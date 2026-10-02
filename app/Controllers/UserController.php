<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class UserController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();
        
        $users = $db->query("
            SELECT u.*, r.name as role_name, e.first_name, e.last_name, e.registration_no 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            LEFT JOIN employees e ON u.employee_id = e.id
            ORDER BY u.created_at DESC
        ")->fetchAll();
        
        $roles = $db->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();
        $employees = $db->query("SELECT id, first_name, last_name, registration_no FROM employees WHERE status = 'active' ORDER BY first_name ASC")->fetchAll();

        $this->render('users/index', [
            'pageTitle' => 'Kullanıcı ve Yetki Yönetimi',
            'users' => $users,
            'roles' => $roles,
            'employees' => $employees
        ]);
    }

    public function store(): void
    {
        $request = $this->request->post();
        
        if (empty($request['username']) || empty($request['email']) || empty($request['password']) || empty($request['role_id'])) {
            $this->flash('error', 'Lütfen zorunlu alanları doldurun.');
            $this->redirect('/users');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $employeeId = !empty($request['employee_id']) ? (int)$request['employee_id'] : null;
        
        try {
            $stmt = $db->prepare("INSERT INTO users (role_id, employee_id, username, email, password_hash, full_name, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
            $stmt->execute([
                $request['role_id'],
                $employeeId,
                $request['username'],
                $request['email'],
                password_hash($request['password'], PASSWORD_DEFAULT),
                $request['full_name'] ?? $request['username']
            ]);
            
            $this->flash('success', 'Yeni kullanıcı başarıyla eklendi.');
        } catch (\Exception $e) {
            $this->flash('error', 'Kullanıcı eklenirken hata oluştu (kullanıcı adı veya e-posta kullanılıyor olabilir).');
        }
        
        $this->redirect('/users');
    }

    public function delete($id): void
    {
        if ($id == 1) {
            $this->flash('error', 'Sistem yöneticisi silinemez.');
            $this->redirect('/users');
            return;
        }

        $db = Database::getInstance()->getConnection();
        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $this->flash('success', 'Kullanıcı başarıyla silindi.');
        } catch (\Exception $e) {
            $this->flash('error', 'Silinemez, kullanıcının işlem geçmişi var.');
        }
        
        $this->redirect('/users');
    }
    public function edit($id): void
    {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if (!$user) {
            $this->flash('error', 'Kullanıcı bulunamadı.');
            $this->redirect('/users');
            return;
        }

        $roles = $db->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();
        $employees = $db->query("SELECT id, first_name, last_name, registration_no FROM employees WHERE status = 'active' ORDER BY first_name ASC")->fetchAll();

        $this->render('users/edit', [
            'pageTitle' => 'Kullanıcı Düzenle',
            'user' => $user,
            'roles' => $roles,
            'employees' => $employees
        ]);
    }

    public function update($id): void
    {
        $request = $this->request->post();
        $db = Database::getInstance()->getConnection();
        $employeeId = !empty($request['employee_id']) ? (int)$request['employee_id'] : null;

        try {
            if (!empty($request['password'])) {
                // Şifre güncelleniyorsa
                $stmt = $db->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, role_id = ?, employee_id = ?, password_hash = ? WHERE id = ?");
                $stmt->execute([
                    $request['full_name'],
                    $request['username'],
                    $request['email'],
                    $request['role_id'],
                    $employeeId,
                    password_hash($request['password'], PASSWORD_DEFAULT),
                    $id
                ]);
            } else {
                // Şifre boş bırakıldıysa güncellemeyelim
                $stmt = $db->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, role_id = ?, employee_id = ? WHERE id = ?");
                $stmt->execute([
                    $request['full_name'],
                    $request['username'],
                    $request['email'],
                    $request['role_id'],
                    $employeeId,
                    $id
                ]);
            }
            $this->flash('success', 'Kullanıcı bilgileri başarıyla güncellendi.');
        } catch (\Exception $e) {
            $this->flash('error', 'Güncelleme hatası. Kullanıcı adı veya e-posta zaten kullanımda olabilir.');
        }

        $this->redirect('/users');
    }
}
