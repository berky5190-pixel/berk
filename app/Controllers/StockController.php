<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Models\StockItem;
use App\Services\AuthService;
use App\Services\AuditLogger;

class StockController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();
        
        $search = $_GET['search'] ?? '';

        $query = "
            SELECT s.*, c.name as category_name, l.name as location_name 
            FROM stock_items s
            LEFT JOIN asset_categories c ON s.category_id = c.id
            LEFT JOIN locations l ON s.location_id = l.id
            WHERE 1=1
        ";
        
        $params = [];
        if (!empty($search)) {
            $query .= " AND (s.name LIKE ? OR s.stock_code LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $query .= " ORDER BY s.name ASC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        $this->render('stock/index', [
            'pageTitle' => 'Stok ve Sarf Malzeme',
            'items' => $items,
            'filters' => ['search' => $search]
        ]);
    }

    public function create(): void
    {
        $db = Database::getInstance()->getConnection();
        $categories = $db->query("SELECT * FROM asset_categories ORDER BY name ASC")->fetchAll();
        $locations = $db->query("SELECT * FROM locations ORDER BY name ASC")->fetchAll();

        $this->render('stock/create', [
            'pageTitle' => 'Yeni Stok Kartı',
            'categories' => $categories,
            'locations' => $locations
        ]);
    }

    public function store(): void
    {
        $request = $this->request->post();

        if (empty($request['name']) || empty($request['stock_code']) || empty($request['category_id'])) {
            $this->flash('error', 'Zorunlu alanları doldurunuz.');
            $this->redirect('/stock/create');
            return;
        }

        $stock = new StockItem();
        try {
            $id = $stock->create([
                'category_id' => $request['category_id'],
                'location_id' => !empty($request['location_id']) ? $request['location_id'] : null,
                'stock_code' => $request['stock_code'],
                'name' => $request['name'],
                'brand' => $request['brand'] ?? null,
                'model' => $request['model'] ?? null,
                'unit' => $request['unit'] ?? 'piece',
                'current_stock' => (int)($request['initial_stock'] ?? 0),
                'min_stock' => (int)($request['min_stock'] ?? 5),
                'max_stock' => (int)($request['max_stock'] ?? 1000),
                'description' => $request['description'] ?? null
            ]);
            
            AuditLogger::log('stock_items', 'created', $id, $request['stock_code'] . ' - ' . $request['name']);
            
            $this->flash('success', 'Stok kartı oluşturuldu.');
            $this->redirect('/stock');
        } catch (\Exception $e) {
            $this->flash('error', 'Hata: ' . $e->getMessage());
            $this->redirect('/stock/create');
        }
    }

    public function show($id): void
    {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT s.*, c.name as category_name, l.name as location_name FROM stock_items s LEFT JOIN asset_categories c ON s.category_id = c.id LEFT JOIN locations l ON s.location_id = l.id WHERE s.id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();

        if (!$item) {
            $this->redirect('/stock');
            return;
        }

        // Fetch movements
        $stmt = $db->prepare("SELECT m.*, u.full_name as user_name FROM stock_movements m JOIN users u ON m.user_id = u.id WHERE m.stock_item_id = ? ORDER BY m.created_at DESC LIMIT 50");
        $stmt->execute([$id]);
        $movements = $stmt->fetchAll();

        $this->render('stock/show', [
            'pageTitle' => 'Stok Detayı',
            'item' => $item,
            'movements' => $movements
        ]);
    }

    public function addMovement($id): void
    {
        $request = $this->request->post();
        $type = $request['movement_type'] ?? 'in';
        $quantity = (int)($request['quantity'] ?? 0);
        
        if ($quantity <= 0) {
            $this->flash('error', 'Miktar sıfırdan büyük olmalıdır.');
            $this->redirect("/stock/{$id}");
            return;
        }

        $stock = new StockItem();
        $item = $stock->find((int)$id);
        
        if (!$item) {
            $this->redirect('/stock');
            return;
        }

        $prev = (int)$item['current_stock'];
        $new = $type === 'in' || $type === 'return' ? $prev + $quantity : $prev - $quantity;

        if ($new < 0) {
            $this->flash('error', 'Yetersiz stok. Çıkış yapılamıyor.');
            $this->redirect("/stock/{$id}");
            return;
        }

        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        try {
            $stock->update((int)$id, ['current_stock' => $new]);

            $stmt = $db->prepare("INSERT INTO stock_movements (stock_item_id, user_id, movement_type, quantity, previous_stock, new_stock, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $id,
                AuthService::id() ?: 1,
                $type,
                $quantity,
                $prev,
                $new,
                $request['notes'] ?? null
            ]);

            $db->commit();
            $this->flash('success', 'Stok hareketi başarıyla eklendi.');
        } catch (\Exception $e) {
            $db->rollBack();
            $this->flash('error', 'Hata: ' . $e->getMessage());
        }

        $this->redirect("/stock/{$id}");
    }

    public function edit($id): void
    {
        $stockModel = new StockItem();
        $item = $stockModel->find($id);

        if (!$item) {
            $this->flash('error', 'Stok kartı bulunamadı.');
            $this->redirect('/stock');
            return;
        }

        $db = Database::getInstance()->getConnection();
        $categories = $db->query("SELECT * FROM asset_categories ORDER BY name ASC")->fetchAll();
        $locations = $db->query("SELECT * FROM locations ORDER BY name ASC")->fetchAll();

        $this->render('stock/edit', [
            'pageTitle' => 'Stok Düzenle: ' . $item['name'],
            'item' => $item,
            'categories' => $categories,
            'locations' => $locations
        ]);
    }

    public function update($id): void
    {
        $request = $this->request->post();

        if (empty($request['name']) || empty($request['stock_code']) || empty($request['category_id'])) {
            $this->flash('error', 'Lütfen zorunlu alanları doldurunuz.');
            $this->redirect("/stock/{$id}/edit");
            return;
        }

        $stockModel = new StockItem();
        
        try {
            $stockModel->update($id, [
                'category_id' => $request['category_id'],
                'location_id' => !empty($request['location_id']) ? $request['location_id'] : null,
                'stock_code' => $request['stock_code'],
                'name' => $request['name'],
                'brand' => $request['brand'] ?? null,
                'model' => $request['model'] ?? null,
                'unit' => $request['unit'] ?? 'piece',
                'min_stock' => (int)($request['min_stock'] ?? 5),
                'max_stock' => (int)($request['max_stock'] ?? 1000),
                'description' => $request['description'] ?? null
            ]);

            AuditLogger::log('stock_items', 'updated', $id, $request['stock_code'] . ' - ' . $request['name']);

            $this->flash('success', 'Stok kartı başarıyla güncellendi.');
            $this->redirect("/stock/{$id}");
        } catch (\Exception $e) {
            $this->flash('error', 'Güncelleme sırasında hata: ' . $e->getMessage());
            $this->redirect("/stock/{$id}/edit");
        }
    }

    public function delete($id): void
    {
        $stockModel = new StockItem();
        
        try {
            $item = $stockModel->find($id);
            if ($item) {
                $stockModel->delete($id);
                AuditLogger::log('stock_items', 'deleted', $id, $item['stock_code'] . ' - ' . $item['name']);
                $this->flash('success', 'Stok kartı başarıyla silindi.');
            } else {
                $this->flash('error', 'Kayıt bulunamadı.');
            }
        } catch (\PDOException $e) {
            $this->flash('error', 'Silinemez: Bu stok kartına ait hareketler var.');
        } catch (\Exception $e) {
            $this->flash('error', 'Bir hata oluştu: ' . $e->getMessage());
        }

        $this->redirect('/stock');
    }
}
