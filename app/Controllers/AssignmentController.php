<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Assignment;
use App\Models\Asset;
use App\Services\AuthService;

class AssignmentController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();
        
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';

        $query = "
            SELECT a.*, asst.asset_code, asst.name as asset_name, 
                   e.first_name, e.last_name, e.registration_no 
            FROM assignments a
            JOIN assets asst ON a.asset_id = asst.id
            JOIN employees e ON a.employee_id = e.id
            WHERE 1=1
        ";
        
        $params = [];
        
        // Standart personel ise sadece kendi üzerine zimmetli olanları görür
        if (!AuthService::isStaff()) {
            $empId = AuthService::employeeId();
            if ($empId) {
                $query .= " AND a.employee_id = ?";
                $params[] = $empId;
            } else {
                $query .= " AND 1=0";
            }
        }

        if (!empty($status)) {
            $query .= " AND a.status = ?";
            $params[] = $status;
        }

        if (!empty($search)) {
            $query .= " AND (asst.asset_code LIKE ? OR asst.name LIKE ? OR e.first_name LIKE ? OR e.last_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $query .= " ORDER BY a.assignment_date DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $assignments = $stmt->fetchAll();

        $this->render('assignments/index', [
            'pageTitle' => AuthService::isStaff() ? 'Zimmet Yönetimi' : 'Zimmetli Cihazlarım',
            'assignments' => $assignments,
            'filters' => ['status' => $status, 'search' => $search],
            'isStaff' => AuthService::isStaff()
        ]);
    }

    public function create(): void
    {
        if (!AuthService::isStaff()) {
            $this->flash('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
            $this->redirect('/assignments');
            return;
        }
        $db = Database::getInstance()->getConnection();
        
        $asset_id = $_GET['asset_id'] ?? null;
        $employee_id = $_GET['employee_id'] ?? null;

        // Sadece "Stokta" olan demirbaşlar zimmetlenebilir
        $assets = $db->query("SELECT id, asset_code, name FROM assets WHERE status = 'in_stock' ORDER BY name ASC")->fetchAll();
        $employees = $db->query("SELECT id, registration_no, first_name, last_name FROM employees WHERE status = 'active' ORDER BY first_name ASC")->fetchAll();

        $this->render('assignments/create', [
            'pageTitle' => 'Yeni Zimmet İşlemi',
            'assets' => $assets,
            'employees' => $employees,
            'selected_asset' => $asset_id,
            'selected_employee' => $employee_id
        ]);
    }

    public function quickCreate(): void
    {
        if (!AuthService::isStaff()) {
            $this->flash('error', 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
            $this->redirect('/assignments');
            return;
        }
        $db = Database::getInstance()->getConnection();
        $employees = $db->query("SELECT id, registration_no, first_name, last_name FROM employees WHERE status = 'active' ORDER BY first_name ASC")->fetchAll();
        
        $this->render('assignments/quick', [
            'pageTitle' => 'Hızlı Zimmet (QR) Modülü',
            'employees' => $employees
        ]);
    }

    public function apiQuickStore(): void
    {
        header('Content-Type: application/json');
        if (!AuthService::isStaff()) {
            echo json_encode(['success' => false, 'message' => 'Yetkisiz işlem.']);
            return;
        }
        $request = json_decode(file_get_contents('php://input'), true);

        if (empty($request['asset_code']) || empty($request['employee_code'])) {
            echo json_encode(['success' => false, 'message' => 'Eksik bilgi gönderildi.']);
            return;
        }

        $db = Database::getInstance()->getConnection();

        // 1. Demirbaşı bul (URL id si veya direkt kod olabilir)
        $assetCode = $request['asset_code'];
        $stmtAsset = $db->prepare("SELECT id, status FROM assets WHERE asset_code = ? OR id = ? LIMIT 1");
        $stmtAsset->execute([$assetCode, $assetCode]);
        $asset = $stmtAsset->fetch();

        if (!$asset) {
            echo json_encode(['success' => false, 'message' => 'Demirbaş bulunamadı.']);
            return;
        }
        if ($asset['status'] !== 'in_stock') {
            echo json_encode(['success' => false, 'message' => 'Bu demirbaş stokta değil (Zimmetli veya Serviste).']);
            return;
        }

        // 2. Personeli bul
        $empCode = $request['employee_code'];
        $stmtEmp = $db->prepare("SELECT id, status FROM employees WHERE registration_no = ? OR id = ? LIMIT 1");
        $stmtEmp->execute([$empCode, $empCode]);
        $employee = $stmtEmp->fetch();

        if (!$employee) {
            echo json_encode(['success' => false, 'message' => 'Personel bulunamadı.']);
            return;
        }
        if ($employee['status'] !== 'active') {
            echo json_encode(['success' => false, 'message' => 'Personel aktif değil.']);
            return;
        }

        // 3. Zimmetle
        try {
            $db->beginTransaction();

            $assignmentCode = 'ZMT-' . date('Ymd-His') . rand(10, 99);
            
            $stmt = $db->prepare("INSERT INTO assignments (assignment_code, asset_id, employee_id, assignment_date, assigned_by_user_id, status, notes) VALUES (?, ?, ?, ?, ?, 'active', ?)");
            $stmt->execute([
                $assignmentCode,
                $asset['id'],
                $employee['id'],
                date('Y-m-d H:i:s'),
                $_SESSION['user_id'] ?? 1,
                'Hızlı QR Zimmet modülü ile atandı.'
            ]);
            $assignmentId = $db->lastInsertId();

            $db->prepare("UPDATE assets SET status = 'assigned' WHERE id = ?")->execute([$asset['id']]);

            $db->commit();
            echo json_encode(['success' => true, 'assignment_id' => $assignmentId]);
        } catch (\Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Kayıt sırasında veritabanı hatası oluştu.']);
        }
    }

    public function store(): void
    {
        $request = $this->request->post();

        if (empty($request['asset_id']) || empty($request['employee_id']) || empty($request['assignment_date'])) {
            $_SESSION['flash']['error'] = 'Lütfen zorunlu alanları doldurunuz.';
            $this->redirect('/assignments/create');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        try {
            // Generate unique assignment code
            $code = 'ZMT-' . date('Ymd') . '-' . rand(1000, 9999);

            $assignment = new Assignment();
            $assignment->create([
                'assignment_code' => $code,
                'employee_id' => $request['employee_id'],
                'asset_id' => $request['asset_id'],
                'assigned_by_user_id' => AuthService::id() ?: 1,
                'assignment_date' => $request['assignment_date'],
                'planned_return_date' => !empty($request['planned_return_date']) ? $request['planned_return_date'] : null,
                'status' => 'active',
                'notes' => $request['notes'] ?? null
            ]);

            // Update asset status to 'assigned'
            $assetModel = new Asset();
            $assetModel->update((int)$request['asset_id'], ['status' => 'assigned']);

            $db->commit();
            $_SESSION['flash']['success'] = 'Zimmet işlemi başarıyla kaydedildi.';
            $this->redirect('/assignments');
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['flash']['error'] = 'Zimmet kaydı sırasında hata: ' . $e->getMessage();
            $this->redirect('/assignments/create');
        }
    }

    public function show($id): void
    {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            SELECT a.*, asst.asset_code, asst.name as asset_name, 
                   e.first_name, e.last_name, e.registration_no, e.title,
                   u.full_name as assigned_by_name
            FROM assignments a
            JOIN assets asst ON a.asset_id = asst.id
            JOIN employees e ON a.employee_id = e.id
            LEFT JOIN users u ON a.assigned_by_user_id = u.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        $assignment = $stmt->fetch();

        if (!$assignment) {
            $_SESSION['flash']['error'] = 'Zimmet kaydı bulunamadı.';
            $this->redirect('/assignments');
            return;
        }

        $stmt = $db->prepare("SELECT * FROM assignment_documents WHERE assignment_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$id]);
        $documents = $stmt->fetchAll();

        $this->render('assignments/show', [
            'pageTitle' => 'Zimmet Detayı: ' . $assignment['assignment_code'],
            'assignment' => $assignment,
            'documents' => $documents
        ]);
    }

    public function printDocument($id): void
    {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            SELECT a.*, asst.asset_code, asst.name as asset_name, 
                   e.first_name, e.last_name, e.registration_no, e.title,
                   u.full_name as assigned_by_name
            FROM assignments a
            JOIN assets asst ON a.asset_id = asst.id
            JOIN employees e ON a.employee_id = e.id
            LEFT JOIN users u ON a.assigned_by_user_id = u.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        $assignment = $stmt->fetch();

        if (!$assignment) {
            $_SESSION['flash']['error'] = 'Zimmet kaydı bulunamadı.';
            $this->redirect('/assignments');
            return;
        }

        // Just include the raw view file instead of using render() so it doesn't include the main layout
        require_once __DIR__ . '/../Views/assignments/print.php';
    }

    public function returnForm($id): void
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT a.*, asst.name as asset_name, e.first_name, e.last_name FROM assignments a JOIN assets asst ON a.asset_id = asst.id JOIN employees e ON a.employee_id = e.id WHERE a.id = ?");
        $stmt->execute([$id]);
        $assignment = $stmt->fetch();

        if (!$assignment || $assignment['status'] !== 'active') {
            $_SESSION['flash']['error'] = 'Geçerli bir aktif zimmet bulunamadı.';
            $this->redirect('/assignments');
            return;
        }

        $this->render('assignments/return', [
            'pageTitle' => 'Zimmet İade Al',
            'assignment' => $assignment
        ]);
    }

    public function processReturn($id): void
    {
        $request = $this->request->post();
        
        if (empty($request['actual_return_date']) || empty($request['return_condition'])) {
            $_SESSION['flash']['error'] = 'İade tarihi ve durumu zorunludur.';
            $this->redirect("/assignments/{$id}/return");
            return;
        }

        $assignmentModel = new Assignment();
        $assignment = $assignmentModel->find((int)$id);

        if (!$assignment || $assignment['status'] !== 'active') {
            $this->redirect('/assignments');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        try {
            // Update Assignment
            $assignmentModel->update((int)$id, [
                'actual_return_date' => $request['actual_return_date'],
                'status' => 'returned',
                'return_condition' => $request['return_condition'],
                'return_notes' => $request['return_notes'] ?? null
            ]);

            // Update Asset status based on return condition
            $newAssetStatus = 'in_stock';
            if ($request['return_condition'] === 'defective' || $request['return_condition'] === 'damaged') {
                $newAssetStatus = 'defective';
            } elseif ($request['return_condition'] === 'scrapped') {
                $newAssetStatus = 'scrapped';
            }

            $assetModel = new Asset();
            $assetModel->update((int)$assignment['asset_id'], ['status' => $newAssetStatus]);

            $db->commit();
            $_SESSION['flash']['success'] = 'Zimmet başarıyla iade alındı.';
            $this->redirect("/assignments/{$id}");
        } catch (\Exception $e) {
            $db->rollBack();
            $_SESSION['flash']['error'] = 'İade işlemi sırasında hata: ' . $e->getMessage();
            $this->redirect("/assignments/{$id}/return");
        }
    }
    public function uploadDocument($id): void
    {
        if (empty($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash']['error'] = 'Lütfen geçerli bir dosya seçin.';
            $this->redirect("/assignments/{$id}");
            return;
        }

        $file = $_FILES['document'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

        if (!in_array($ext, $allowed)) {
            $_SESSION['flash']['error'] = 'Sadece PDF, Word veya Resim dosyaları yüklenebilir.';
            $this->redirect("/assignments/{$id}");
            return;
        }

        $uploadDir = dirname(__DIR__, 2) . '/storage/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newFileName = 'ZMT_' . $id . '_' . time() . '.' . $ext;
        $destination = $uploadDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO assignment_documents (assignment_id, document_type, original_filename, stored_filename, file_path, mime_type, file_size, uploaded_by_user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $id,
                $_POST['document_type'] ?? 'other',
                $file['name'],
                $newFileName,
                'storage/documents/' . $newFileName,
                $file['type'],
                $file['size'],
                AuthService::id() ?: 1
            ]);
            $_SESSION['flash']['success'] = 'Belge başarıyla yüklendi.';
        } else {
            $_SESSION['flash']['error'] = 'Dosya yükleme sırasında bir hata oluştu.';
        }

        $this->redirect("/assignments/{$id}");
    }

    public function downloadDocument($docId): void
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM assignment_documents WHERE id = ?");
        $stmt->execute([$docId]);
        $doc = $stmt->fetch();

        if (!$doc) {
            die("Belge bulunamadı.");
        }

        $filePath = dirname(__DIR__, 2) . '/' . $doc['file_path'];
        if (!file_exists($filePath)) {
            die("Dosya fiziksel olarak sunucuda bulunamadı.");
        }

        header('Content-Type: ' . $doc['mime_type']);
        header('Content-Disposition: attachment; filename="' . $doc['original_filename'] . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}
