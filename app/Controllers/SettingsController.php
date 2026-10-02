<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class SettingsController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance()->getConnection();
        
        $settingsRaw = $db->query("SELECT * FROM settings ORDER BY `group`, `key`")->fetchAll();
        $settings = [];
        
        foreach ($settingsRaw as $s) {
            $settings[$s['group']][] = $s;
        }

        $this->render('settings/index', [
            'pageTitle' => 'Sistem Ayarları',
            'settingsGrouped' => $settings
        ]);
    }

    public function update(): void
    {
        $request = $this->request->post();
        $db = Database::getInstance()->getConnection();
        
        try {
            $stmt = $db->prepare("UPDATE settings SET value = ? WHERE `key` = ?");
            foreach ($request as $key => $value) {
                if ($key !== 'csrf_token') {
                    $stmt->execute([$value, $key]);
                }
            }
            $this->flash('success', 'Ayarlar başarıyla güncellendi.');
        } catch (\Exception $e) {
            $this->flash('error', 'Hata: ' . $e->getMessage());
        }
        
        $this->redirect('/settings');
    }
}
