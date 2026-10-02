<?php
$pdo = new PDO('sqlite:database/database.sqlite');
echo "--- ASSIGNMENTS ---\n";
print_r($pdo->query('SELECT a.id, a.assignment_code, a.employee_id, e.first_name, e.last_name, ast.name as asset_name, ast.asset_code, a.status FROM assignments a JOIN employees e ON a.employee_id = e.id JOIN assets ast ON a.asset_id = ast.id LIMIT 10')->fetchAll(PDO::FETCH_ASSOC));
echo "--- ROLES ---\n";
print_r($pdo->query('SELECT * FROM roles')->fetchAll(PDO::FETCH_ASSOC));
