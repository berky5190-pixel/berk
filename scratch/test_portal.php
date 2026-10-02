<?php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) require_once $file;
});

// Simulate Ahmet login
\App\Helpers\SessionHelper::start();
$uModel = new \App\Models\User();
$u = $uModel->findByUsernameOrEmail('ahmet');
echo "User from DB:\n";
print_r($u);
$res = $auth->attempt('ahmet', 'User123!');
echo "Login result: " . ($res ? 'SUCCESS' : 'FAILED') . "\n";
$user = \App\Services\AuthService::user();
echo "Logged in user: " . $user['username'] . " (Role: " . $user['role_slug'] . ", Emp ID: " . \App\Services\AuthService::employeeId() . ")\n";
echo "isStaff: " . (\App\Services\AuthService::isStaff() ? 'YES' : 'NO') . "\n";

$employee = \App\Services\AuthService::employee();
echo "Employee: " . ($employee['first_name'] ?? '') . ' ' . ($employee['last_name'] ?? '') . ' - ' . ($employee['department_name'] ?? '') . "\n";
