<?php
$pdo = new PDO('sqlite:database/database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query("SELECT a.id as assignment_id, a.asset_id, ast.name as asset_name, ast.asset_code FROM assignments a JOIN assets ast ON a.asset_id = ast.id WHERE a.employee_id = 4");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Ahmet's assets:\n";
print_r($rows);

// Insert a sample request if empty
$reqCount = $pdo->query("SELECT COUNT(*) FROM asset_requests")->fetchColumn();
if ($reqCount == 0 && !empty($rows)) {
    $asset = $rows[0];
    $userStmt = $pdo->query("SELECT id FROM users WHERE username = 'ahmet'");
    $userId = $userStmt->fetchColumn() ?: 1;

    $ins = $pdo->prepare("
        INSERT INTO asset_requests 
        (request_code, user_id, employee_id, request_type, asset_id, title, description, urgency, status, created_at, updated_at)
        VALUES 
        ('TLP-20261002-001', ?, 4, 'malfunction', ?, 'Laptop şarj olmuyor ve aşırı ısınıyor', 'Type-C adaptörü taktığımda temassızlık yapıyor ve batarya dolmuyor.', 'high', 'pending', datetime('now', '-2 hours'), datetime('now', '-2 hours')),
        ('TLP-20261002-002', ?, 4, 'equipment', NULL, 'Kablosuz Klavye & Mouse Seti Talebi', 'Mevcut kablolu klavyem arızalandı, ergonomik kablosuz set talep ediyorum.', 'normal', 'approved', datetime('now', '-1 days'), datetime('now', '-1 hours'))
    ");
    $ins->execute([$userId, $asset['asset_id'], $userId]);

    // Add admin note to the approved one
    $pdo->exec("UPDATE asset_requests SET admin_notes = 'Talebiniz onaylandı. Depodan teslim alabilirsiniz.' WHERE request_code = 'TLP-20261002-002'");

    echo "Örnek talepler eklendi.\n";
} else {
    echo "Talepler zaten mevcut (Toplam: $reqCount).\n";
}
