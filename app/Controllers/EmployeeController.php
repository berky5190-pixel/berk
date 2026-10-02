<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Employee;
use App\Models\Department;
use App\Services\AuditLogger;

class EmployeeController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();
        
        // Fetch employees with department names
        $stmt = $db->query("
            SELECT e.*, d.name as department_name 
            FROM employees e 
            LEFT JOIN departments d ON e.department_id = d.id 
            ORDER BY e.first_name ASC, e.last_name ASC
        ");
        $employees = $stmt->fetchAll();

        $this->render('employees/index', [
            'pageTitle' => 'Personel Yönetimi',
            'employees' => $employees
        ]);
    }

    public function create(): void
    {
        $db = Database::getInstance()->getConnection();
        $departments = $db->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();

        $this->render('employees/create', [
            'pageTitle' => 'Yeni Personel Ekle',
            'departments' => $departments
        ]);
    }

    public function store(): void
    {
        $request = $this->request->post();

        // Basic validation
        if (empty($request['first_name']) || empty($request['last_name']) || empty($request['registration_no'])) {
            $_SESSION['flash']['error'] = 'Lütfen zorunlu alanları (Ad, Soyad, Sicil No) doldurunuz.';
            $this->redirect('/employees/create');
            return;
        }

        $employee = new Employee();
        
        try {
            $employee->create([
                'department_id' => $request['department_id'] ?: 1, // Fallback for testing
                'registration_no' => $request['registration_no'],
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'email' => $request['email'] ?? null,
                'phone' => $request['phone'] ?? null,
                'title' => $request['title'] ?? 'Personel',
                'hire_date' => !empty($request['hire_date']) ? $request['hire_date'] : null,
                'status' => $request['status'] ?? 'active',
                'notes' => $request['notes'] ?? null,
            ]);

            $_SESSION['flash']['success'] = 'Personel başarıyla eklendi.';
            $this->redirect('/employees');
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Kayıt sırasında hata oluştu: ' . $e->getMessage();
            $this->redirect('/employees/create');
        }
    }

    public function show($id): void
    {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            SELECT e.*, d.name as department_name 
            FROM employees e 
            LEFT JOIN departments d ON e.department_id = d.id 
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        $employee = $stmt->fetch();

        if (!$employee) {
            $_SESSION['flash']['error'] = 'Personel bulunamadı.';
            $this->redirect('/employees');
            return;
        }

        // Fetch active assignments
        $stmt = $db->prepare("
            SELECT a.*, ast.name as asset_name, ast.asset_code, ast.photo_path
            FROM assignments a
            JOIN assets ast ON a.asset_id = ast.id
            WHERE a.employee_id = ? AND a.status = 'active'
            ORDER BY a.assignment_date DESC
        ");
        $stmt->execute([$id]);
        $active_assignments = $stmt->fetchAll();

        // Fetch history assignments
        $stmt = $db->prepare("
            SELECT a.*, ast.name as asset_name, ast.asset_code
            FROM assignments a
            JOIN assets ast ON a.asset_id = ast.id
            WHERE a.employee_id = ? AND a.status != 'active'
            ORDER BY a.actual_return_date DESC, a.assignment_date DESC
        ");
        $stmt->execute([$id]);
        $history_assignments = $stmt->fetchAll();

        $this->render('employees/show', [
            'pageTitle' => $employee['first_name'] . ' ' . $employee['last_name'] . ' Detayları',
            'employee' => $employee,
            'active_assignments' => $active_assignments,
            'history_assignments' => $history_assignments
        ]);
    }

    public function edit($id): void
    {
        $employee = (new Employee())->find($id);

        if (!$employee) {
            $_SESSION['flash']['error'] = 'Personel bulunamadı.';
            $this->redirect('/employees');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $departments = $db->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();

        $this->render('employees/edit', [
            'pageTitle' => 'Personel Düzenle',
            'employee' => $employee,
            'departments' => $departments
        ]);
    }

    public function update($id): void
    {
        $request = $this->request->post();

        if (empty($request['first_name']) || empty($request['last_name']) || empty($request['registration_no'])) {
            $_SESSION['flash']['error'] = 'Lütfen zorunlu alanları (Ad, Soyad, Sicil No) doldurunuz.';
            $this->redirect("/employees/{$id}/edit");
            return;
        }

        $employeeModel = new Employee();
        $employee = $employeeModel->find($id);
        
        if (!$employee) {
            $_SESSION['flash']['error'] = 'Personel bulunamadı.';
            $this->redirect('/employees');
            return;
        }

        try {
            $employeeModel->update($id, [
                'department_id' => $request['department_id'] ?: 1,
                'registration_no' => $request['registration_no'],
                'first_name' => $request['first_name'],
                'last_name' => $request['last_name'],
                'email' => $request['email'] ?? null,
                'phone' => $request['phone'] ?? null,
                'title' => $request['title'] ?? 'Personel',
                'hire_date' => !empty($request['hire_date']) ? $request['hire_date'] : null,
                'status' => $request['status'] ?? 'active',
                'notes' => $request['notes'] ?? null,
            ]);

            $_SESSION['flash']['success'] = 'Personel bilgileri güncellendi.';
            $this->redirect("/employees/{$id}");
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Güncelleme sırasında hata oluştu: ' . $e->getMessage();
            $this->redirect("/employees/{$id}/edit");
        }
    }

    public function delete($id): void
    {
        $employeeModel = new Employee();
        
        try {
            $employee = $employeeModel->find($id);
            if ($employee) {
                $employeeModel->delete($id);
                AuditLogger::log('employees', 'deleted', $id, $employee['first_name'] . ' ' . $employee['last_name'], $employee);
                $_SESSION['flash']['success'] = 'Personel başarıyla silindi.';
            } else {
                $_SESSION['flash']['error'] = 'Personel bulunamadı.';
            }
        } catch (\PDOException $e) {
            $_SESSION['flash']['error'] = 'Silinemez: Bu personel üzerinde aktif bir işlem (örn: zimmet) var. Lütfen önce ilişkili kayıtları silin veya kaldırın.';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Bir hata oluştu: ' . $e->getMessage();
        }

        $this->redirect('/employees');
    }
}
