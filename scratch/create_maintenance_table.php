<?php
$db = new PDO('sqlite:database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("CREATE TABLE IF NOT EXISTS asset_maintenances (id INTEGER PRIMARY KEY AUTOINCREMENT, asset_id INTEGER NOT NULL, company_name TEXT NOT NULL, cost NUMERIC DEFAULT 0.00, start_date DATE, end_date DATE, notes TEXT, status TEXT DEFAULT 'in_progress', created_at DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (asset_id) REFERENCES assets(id));");
echo "Tablo oluşturuldu.\n";
