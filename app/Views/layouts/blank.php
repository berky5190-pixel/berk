<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::escape($pageTitle ?? 'Sistem Durumu') ?></title>
    <link rel="stylesheet" href="/static/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body, html {
            margin: 0; padding: 0;
            background: #020617; /* Dark background */
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        .login-btn {
            position: fixed;
            top: 24px;
            right: 24px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 10px 24px;
            border-radius: 100px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .login-btn:hover {
            background: #38bdf8;
            color: #0f172a;
            border-color: #38bdf8;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(56, 189, 248, 0.4);
        }
    </style>
    <style>
        .back-btn {
            position: fixed;
            top: 24px;
            left: 24px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94a3b8;
            padding: 10px 24px;
            border-radius: 100px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .back-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php if (\App\Services\AuthService::check()): ?>
        <a href="/" class="back-btn"><i class="fas fa-arrow-left"></i> Dashboard'a Dön</a>
    <?php else: ?>
        <a href="/" class="back-btn"><i class="fas fa-arrow-left"></i> Ana Sayfaya Dön</a>
        <a href="/login" class="login-btn"><i class="fas fa-sign-in-alt"></i> Kurumsal Giriş</a>
    <?php endif; ?>

    <div class="container">
        <?= $content ?? '' ?>
    </div>
</body>
</html>
