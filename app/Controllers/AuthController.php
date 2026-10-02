<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Helpers\SessionHelper;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->authService = new AuthService();
    }

    public function showLoginForm(): void
    {
        $this->render('auth.login', [
            'pageTitle' => 'Kullanıcı Girişi - Demirbaş ve Zimmet Yönetimi'
        ], 'auth');
    }

    public function login(): void
    {
        $identifier = trim((string)$this->request->input('identifier'));
        $password = (string)$this->request->input('password');

        if (empty($identifier) || empty($password)) {
            $this->flash('error', 'Kullanıcı adı/e-posta ve şifre zorunludur.');
            $this->redirect('/login');
            return;
        }

        if ($this->authService->attempt($identifier, $password)) {
            $user = AuthService::user();
            $this->flash('success', "Hoş geldiniz, Sn. {$user['full_name']}. Sisteme başarıyla giriş yaptınız.");
            
            $intended = SessionHelper::get('intended_url', '/');
            SessionHelper::remove('intended_url');

            $this->redirect($intended);
            return;
        }

        if (!SessionHelper::getFlash('error')) {
            $this->flash('error', 'Girdiğiniz kullanıcı adı veya şifre hatalıdır.');
        }

        $this->redirect('/login');
    }

    public function logout(): void
    {
        AuthService::logout();
        $this->flash('success', 'Oturumunuz güvenli bir şekilde sonlandırıldı.');
        $this->redirect('/login');
    }

    public function profile(): void
    {
        $user = AuthService::user();
        $this->render('auth.profile', [
            'pageTitle' => 'Kullanıcı Profili - ' . ($user['full_name'] ?? ''),
            'user' => $user
        ]);
    }
}
