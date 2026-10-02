<?php
$pdo = new PDO('sqlite:database/database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "
CREATE TABLE IF NOT EXISTS asset_requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    request_code VARCHAR(50) NOT NULL UNIQUE,
    user_id INTEGER NOT NULL,
    employee_id INTEGER NULL,
    request_type VARCHAR(30) NOT NULL, -- 'malfunction', 'exchange', 'equipment'
    asset_id INTEGER NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    urgency VARCHAR(20) DEFAULT 'normal', -- 'low', 'normal', 'high', 'urgent'
    status VARCHAR(30) DEFAULT 'pending', -- 'pending', 'in_review', 'approved', 'rejected', 'completed'
    admin_notes TEXT NULL,
    resolved_by_user_id INTEGER NULL,
    resolved_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (employee_id) REFERENCES employees(id),
    FOREIGN KEY (asset_id) REFERENCES assets(id)
);
";

$pdo->exec($sql);
echo "asset_requests tablosu başarıyla oluşturuldu.\n";
