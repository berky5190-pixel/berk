<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::escape($pageTitle ?? 'Giriş Yap - Demirbaş Yönetim Sistemi') ?></title>
    <?= \App\Helpers\CsrfHelper::meta() ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #38bdf8;
            --primary-dark: #0284c7;
            --bg-dark: #0f172a;
            --bg-darker: #020617;
        }
        body, html {
            margin: 0; padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            height: 100%;
            background: var(--bg-darker);
            overflow: hidden;
            color: #f8fafc;
        }
        .auth-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }
        .auth-left {
            flex: 1;
            position: relative;
            background: linear-gradient(135deg, rgba(15,23,42,0.9) 0%, rgba(2,6,23,1) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 10%;
            overflow: hidden;
            border-right: 1px solid rgba(255,255,255,0.05);
        }
        
        /* Tech Particles Animation */
        .tech-bg {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(56, 189, 248, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 85% 30%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
        }
        .grid-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2;
            background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.5;
            animation: moveGrid 20s linear infinite;
        }
        @keyframes moveGrid {
            0% { transform: translateY(0); }
            100% { transform: translateY(30px); }
        }

        .auth-left-content {
            position: relative;
            z-index: 10;
            max-width: 500px;
        }
        .brand-badge {
            display: inline-flex; align-items: center; gap: 10px;
            background: rgba(56,189,248,0.1); border: 1px solid rgba(56,189,248,0.2);
            color: var(--primary); padding: 8px 16px; border-radius: 100px;
            font-size: 0.85rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;
            margin-bottom: 24px;
        }
        .auth-left-title {
            font-size: 3.5rem; font-weight: 800; line-height: 1.1; margin-bottom: 24px;
            background: linear-gradient(to right, #f8fafc, #94a3b8);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        
        .auth-right {
            width: 480px;
            background: var(--bg-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            box-shadow: -20px 0 50px rgba(0,0,0,0.5);
            z-index: 20;
        }
        .auth-card {
            width: 100%;
            max-width: 360px;
        }
        
        @media (max-width: 900px) {
            .auth-container { flex-direction: column; }
            .auth-left { display: none; }
            .auth-right { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Sol Taraf (Görsel ve Animasyon Alanı) -->
        <div class="auth-left">
            <div class="tech-bg"></div>
            <div class="grid-overlay"></div>
            
            <div class="auth-left-content">
                <div class="brand-badge">
                    <i class="fas fa-microchip"></i> IT CORE SYSTEM v2.1
                </div>
                <h1 class="auth-left-title">Kurumsal Envanter &<br>Zimmet Portalı</h1>
                <p style="font-size: 1.1rem; color: #94a3b8; line-height: 1.6; margin-bottom: 40px;">
                    Tüm şirket demirbaşlarını, IT cihazlarını ve personel zimmet kayıtlarını tek merkezden, güvenli bir şekilde yönetin.
                </p>
                
                <div style="display: flex; gap: 30px;">
                    <div>
                        <div style="font-size: 2rem; font-weight: 800; color: #f8fafc;">%99.9</div>
                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Uptime Sistemi</div>
                    </div>
                    <div>
                        <div style="font-size: 2rem; font-weight: 800; color: #f8fafc;">256-bit</div>
                        <div style="font-size: 0.85rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Şifreli Altyapı</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Taraf (Form Alanı) -->
        <div class="auth-right">
            <div class="auth-card">
                <?= $content ?? '' ?>
            </div>
        </div>
    </div>
</body>
</html>
