<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Asset;
use App\Services\AuditLogger;

class AssetController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();
        
        // Basic filtering setup
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $category = $_GET['category'] ?? '';

        $query = "
            SELECT a.*, ac.name as category_name, l.name as location_name 
            FROM assets a 
            LEFT JOIN asset_categories ac ON a.category_id = ac.id 
            LEFT JOIN locations l ON a.location_id = l.id 
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (a.name LIKE ? OR a.asset_code LIKE ? OR a.serial_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($status)) {
            $query .= " AND a.status = ?";
            $params[] = $status;
        }

        if (!empty($category)) {
            $query .= " AND a.category_id = ?";
            $params[] = $category;
        }

        $query .= " ORDER BY a.id DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $assets = $stmt->fetchAll();

        $categories = $db->query("SELECT * FROM asset_categories ORDER BY name ASC")->fetchAll();

        $this->render('assets/index', [
            'pageTitle' => 'Demirbaş Yönetimi',
            'assets' => $assets,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'category' => $category
            ]
        ]);
    }

    public function printList(): void
    {
        $db = Database::getInstance()->getConnection();
        
        // Fetch all assets with categories and locations for the report
        $query = "
            SELECT a.*, ac.name as category_name, l.name as location_name 
            FROM assets a 
            LEFT JOIN asset_categories ac ON a.category_id = ac.id 
            LEFT JOIN locations l ON a.location_id = l.id 
            ORDER BY a.asset_code ASC
        ";
        
        $stmt = $db->query($query);
        $assets = $stmt->fetchAll();

        require_once dirname(__DIR__) . '/Views/assets/print.php';
    }

    public function create(): void
    {
        $db = Database::getInstance()->getConnection();
        $categories = $db->query("SELECT * FROM asset_categories ORDER BY name ASC")->fetchAll();
        $locations = $db->query("SELECT * FROM locations ORDER BY name ASC")->fetchAll();

        $this->render('assets/create', [
            'pageTitle' => 'Yeni Demirbaş Ekle',
            'categories' => $categories,
            'locations' => $locations
        ]);
    }

    public function store(): void
    {
        $request = $this->request->post();

        if (empty($request['name']) || empty($request['asset_code']) || empty($request['category_id'])) {
            $_SESSION['flash']['error'] = 'Lütfen zorunlu alanları (Demirbaş Adı, Kodu, Kategori) doldurunuz.';
            $this->redirect('/assets/create');
            return;
        }

        $asset = new Asset();
        
        try {
            $asset->create([
                'category_id' => $request['category_id'],
                'location_id' => !empty($request['location_id']) ? $request['location_id'] : null,
                'asset_code' => $request['asset_code'],
                'name' => $request['name'],
                'brand' => $request['brand'] ?? '',
                'model' => $request['model'] ?? '',
                'serial_number' => $request['serial_number'] ?? null,
                'barcode' => $request['barcode'] ?? null,
                'purchase_date' => !empty($request['purchase_date']) ? $request['purchase_date'] : null,
                'purchase_price' => !empty($request['purchase_price']) ? $request['purchase_price'] : 0,
                'warranty_start' => !empty($request['warranty_start']) ? $request['warranty_start'] : null,
                'warranty_end' => !empty($request['warranty_end']) ? $request['warranty_end'] : null,
                'status' => $request['status'] ?? 'in_stock',
                'description' => $request['description'] ?? null,
            ]);

            $_SESSION['flash']['success'] = 'Demirbaş başarıyla eklendi.';
            $this->redirect('/assets');
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Kayıt sırasında hata: ' . $e->getMessage();
            $this->redirect('/assets/create');
        }
    }

    public function show($id): void
    {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            SELECT a.*, ac.name as category_name, l.name as location_name 
            FROM assets a 
            LEFT JOIN asset_categories ac ON a.category_id = ac.id 
            LEFT JOIN locations l ON a.location_id = l.id 
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        $asset = $stmt->fetch();

        if (!$asset) {
            $_SESSION['flash']['error'] = 'Demirbaş bulunamadı.';
            $this->redirect('/assets');
            return;
        }

        // Fetch assignment history for this asset
        $stmt = $db->prepare("
            SELECT a.*, e.first_name, e.last_name, e.registration_no 
            FROM assignments a 
            JOIN employees e ON a.employee_id = e.id 
            WHERE a.asset_id = ? 
            ORDER BY a.assignment_date DESC
        ");
        $stmt->execute([$id]);
        $assignments = $stmt->fetchAll();

        // Fetch maintenance history
        $stmt = $db->prepare("SELECT * FROM asset_maintenances WHERE asset_id = ? ORDER BY created_at DESC");
        $stmt->execute([$id]);
        $maintenances = $stmt->fetchAll();

        $this->render('assets/show', [
            'pageTitle' => 'Demirbaş Detayı: ' . $asset['asset_code'],
            'asset' => $asset,
            'assignments' => $assignments,
            'maintenances' => $maintenances
        ]);
    }

    public function edit($id): void
    {
        $assetModel = new Asset();
        $asset = $assetModel->find($id);

        if (!$asset) {
            $_SESSION['flash']['error'] = 'Demirbaş bulunamadı.';
            $this->redirect('/assets');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $categories = $db->query("SELECT * FROM asset_categories ORDER BY name ASC")->fetchAll();
        $locations = $db->query("SELECT * FROM locations ORDER BY name ASC")->fetchAll();

        $this->render('assets/edit', [
            'pageTitle' => 'Demirbaş Düzenle',
            'asset' => $asset,
            'categories' => $categories,
            'locations' => $locations
        ]);
    }

    public function update($id): void
    {
        $request = $this->request->post();

        if (empty($request['name']) || empty($request['asset_code']) || empty($request['category_id'])) {
            $_SESSION['flash']['error'] = 'Lütfen zorunlu alanları doldurunuz.';
            $this->redirect("/assets/{$id}/edit");
            return;
        }

        $assetModel = new Asset();
        
        try {
            $assetModel->update($id, [
                'category_id' => $request['category_id'],
                'location_id' => !empty($request['location_id']) ? $request['location_id'] : null,
                'asset_code' => $request['asset_code'],
                'name' => $request['name'],
                'brand' => $request['brand'] ?? '',
                'model' => $request['model'] ?? '',
                'serial_number' => $request['serial_number'] ?? null,
                'barcode' => $request['barcode'] ?? null,
                'purchase_date' => !empty($request['purchase_date']) ? $request['purchase_date'] : null,
                'purchase_price' => !empty($request['purchase_price']) ? $request['purchase_price'] : 0,
                'warranty_start' => !empty($request['warranty_start']) ? $request['warranty_start'] : null,
                'warranty_end' => !empty($request['warranty_end']) ? $request['warranty_end'] : null,
                'status' => $request['status'] ?? 'in_stock',
                'description' => $request['description'] ?? null,
            ]);

            $_SESSION['flash']['success'] = 'Demirbaş başarıyla güncellendi.';
            $this->redirect("/assets/{$id}");
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Güncelleme sırasında hata: ' . $e->getMessage();
            $this->redirect("/assets/{$id}/edit");
        }
    }

    public function delete($id): void
    {
        $assetModel = new Asset();
        
        try {
            $asset = $assetModel->find($id);
            if ($asset) {
                $assetModel->delete($id);
                AuditLogger::log('assets', 'deleted', $id, $asset['asset_code'] . ' - ' . $asset['name'], $asset);
                $_SESSION['flash']['success'] = 'Demirbaş başarıyla silindi.';
            } else {
                $_SESSION['flash']['error'] = 'Demirbaş bulunamadı.';
            }
        } catch (\PDOException $e) {
            $_SESSION['flash']['error'] = 'Silinemez: Bu demirbaş üzerinde aktif bir işlem (örn: zimmet) var. Lütfen önce ilişkili kayıtları silin veya kaldırın.';
        } catch (\Exception $e) {
            $_SESSION['flash']['error'] = 'Bir hata oluştu: ' . $e->getMessage();
        }

        $this->redirect('/assets');
    }

    public function sendToMaintenance($id): void
    {
        $request = $this->request->post();
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("INSERT INTO asset_maintenances (asset_id, company_name, start_date, notes) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $id,
            $request['company_name'] ?? 'Bilinmeyen Servis',
            date('Y-m-d'),
            $request['notes'] ?? ''
        ]);
        
        $db->prepare("UPDATE assets SET status = 'in_service' WHERE id = ?")->execute([$id]);
        
        $this->flash('success', 'Demirbaş teknik servise gönderildi.');
        $this->redirect("/assets/{$id}");
    }

    public function completeMaintenance($id): void
    {
        $request = $this->request->post();
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("UPDATE asset_maintenances SET end_date = ?, cost = ?, status = 'completed' WHERE asset_id = ? AND status = 'in_progress'");
        $stmt->execute([
            date('Y-m-d'),
            $request['cost'] ?? 0,
            $id
        ]);
        
        $newStatus = $request['new_status'] ?? 'in_stock';
        $db->prepare("UPDATE assets SET status = ? WHERE id = ?")->execute([$newStatus, $id]);
        
        $this->flash('success', 'Bakım/Onarım tamamlandı, demirbaş durumu güncellendi.');
        $this->redirect("/assets/{$id}");
    }
}
