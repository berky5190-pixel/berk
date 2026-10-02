<?php

/**
 * Web Routes
 * @var App\Core\Router $router
 */

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\CsrfMiddleware;

use App\Controllers\EmployeeController;
use App\Controllers\AssetController;
use App\Controllers\AssignmentController;
use App\Controllers\StockController;
use App\Controllers\ReportController;
use App\Controllers\SettingsController;
use App\Controllers\NotificationController;
use App\Controllers\AuditLogController;
use App\Controllers\TrashController;
use App\Controllers\UserController;
use App\Controllers\InventoryAuditController;
use App\Controllers\RequestController;

// ── Public Routes ──────────────────────────────────────
$router->get('/', [HomeController::class, 'index']);
$router->get('/health', [HomeController::class, 'health']);

// ── Auth: Guest Only (redirect to / if already logged in) ──
$router->get('/login', [AuthController::class, 'showLoginForm'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [CsrfMiddleware::class]);

// ── Auth: Logged In Only (Protected Routes) ─────────────────
$router->group(['middleware' => [AuthMiddleware::class, CsrfMiddleware::class]], function ($router) {
    
    $router->get('/logout', [AuthController::class, 'logout']);
    $router->get('/profile', [AuthController::class, 'profile']);

    // Employee Routes
    $router->get('/employees', [EmployeeController::class, 'index']);
    $router->get('/employees/create', [EmployeeController::class, 'create']);
    $router->post('/employees', [EmployeeController::class, 'store']);
    $router->get('/employees/{id}', [EmployeeController::class, 'show']);
    $router->get('/employees/{id}/edit', [EmployeeController::class, 'edit']);
    $router->post('/employees/{id}', [EmployeeController::class, 'update']);
    $router->post('/employees/{id}/delete', [EmployeeController::class, 'delete']);

    // Asset Routes
    $router->get('/assets', [AssetController::class, 'index']);
    $router->get('/assets/print', [AssetController::class, 'printList']);
    $router->get('/assets/create', [AssetController::class, 'create']);
    $router->post('/assets', [AssetController::class, 'store']);
    $router->get('/assets/{id}', [AssetController::class, 'show']);
    $router->get('/assets/{id}/edit', [AssetController::class, 'edit']);
    $router->post('/assets/{id}', [AssetController::class, 'update']);
    $router->post('/assets/{id}/delete', [AssetController::class, 'delete']);
    $router->post('/assets/{id}/maintenance', [AssetController::class, 'sendToMaintenance']);
    $router->post('/assets/{id}/maintenance/complete', [AssetController::class, 'completeMaintenance']);

    // Assignment Routes
    $router->get('/assignments', [AssignmentController::class, 'index']);
    $router->get('/assignments/create', [AssignmentController::class, 'create']);
    $router->get('/assignments/quick', [AssignmentController::class, 'quickCreate']);
    $router->post('/api/assignments/quick', [AssignmentController::class, 'apiQuickStore']);
    $router->post('/assignments', [AssignmentController::class, 'store']);
    $router->get('/assignments/{id}', [AssignmentController::class, 'show']);
    $router->get('/assignments/{id}/print', [AssignmentController::class, 'printDocument']);
    $router->get('/assignments/{id}/return', [AssignmentController::class, 'returnForm']);
    $router->post('/assignments/{id}/return', [AssignmentController::class, 'processReturn']);
    $router->post('/assignments/{id}/document', [AssignmentController::class, 'uploadDocument']);
    $router->get('/assignments/document/{docId}', [AssignmentController::class, 'downloadDocument']);

    // Stock Routes
    $router->get('/stock', [StockController::class, 'index']);
    $router->get('/stock/create', [StockController::class, 'create']);
    $router->post('/stock', [StockController::class, 'store']);
    $router->get('/stock/{id}', [StockController::class, 'show']);
    $router->post('/stock/{id}/movement', [StockController::class, 'addMovement']);
    $router->get('/stock/{id}/edit', [StockController::class, 'edit']);
    $router->post('/stock/{id}', [StockController::class, 'update']);
    $router->post('/stock/{id}/delete', [StockController::class, 'delete']);

    // Report Routes
    $router->get('/reports', [ReportController::class, 'index']);
    $router->get('/reports/export/assets', [ReportController::class, 'exportAssets']);
    $router->get('/reports/export/assignments', [ReportController::class, 'exportAssignments']);
    $router->get('/reports/print/departments', [ReportController::class, 'printDepartments']);

    // System Routes
    $router->get('/settings', [SettingsController::class, 'index']);
    $router->post('/settings', [SettingsController::class, 'update']);

    $router->get('/inventory-audit', [InventoryAuditController::class, 'index']);
    $router->get('/api/inventory-audit/expected', [InventoryAuditController::class, 'getExpectedAssets']);
    $router->get('/notifications', [NotificationController::class, 'index']);
    $router->post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    $router->get('/audit-logs', [AuditLogController::class, 'index']);
    $router->get('/trash', [TrashController::class, 'index']);
    $router->post('/trash/{id}/restore', [TrashController::class, 'restore']);
    // Request & Malfunction Routes (Personel Talep ve Arıza Modülü)
    $router->get('/requests', [RequestController::class, 'index']);
    $router->post('/requests', [RequestController::class, 'store']);
    $router->post('/requests/{id}/status', [RequestController::class, 'updateStatus']);
    $router->post('/requests/{id}/cancel', [RequestController::class, 'cancel']);

    $router->get('/users', [UserController::class, 'index']);
    $router->post('/users', [UserController::class, 'store']);
    $router->get('/users/{id}/edit', [UserController::class, 'edit']);
    $router->post('/users/{id}', [UserController::class, 'update']);
    $router->post('/users/{id}/delete', [UserController::class, 'delete']);
});

// ── Dynamic Route Test ─────────────────────────────────
$router->get('/test/{name}', function ($request, $response, $name) {
    return $response->json([
        'message' => "Merhaba, {$name}! Router dinamik parametreleri başarıyla çalışıyor.",
        'method' => $request->getMethod(),
        'uri' => $request->getUri()
    ]);
});
