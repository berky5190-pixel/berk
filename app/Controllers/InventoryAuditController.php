<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class InventoryAuditController extends Controller
{
    public function index(): void
    {
        $this->render('audit/index', [
            'pageTitle' => 'Fiziksel Sayım & Karekod Okuyucu'
        ]);
    }

    public function getExpectedAssets(): void
    {
        $db = Database::getInstance()->getConnection();
        // Fetch all active/in_stock/assigned assets that should be in the inventory
        $stmt = $db->query("SELECT id, asset_code, name, status FROM assets WHERE status IN ('assigned', 'in_stock', 'in_service')");
        $assets = $stmt->fetchAll();
        
        header('Content-Type: application/json');
        echo json_encode($assets);
    }
}
