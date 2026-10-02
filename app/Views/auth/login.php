<div style="text-align: left; margin-bottom: 32px;">
    <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%); color: #fff; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: 0 10px 20px rgba(56, 189, 248, 0.3); margin-bottom: 20px;">
        <i class="fas fa-fingerprint"></i>
    </div>
    <h2 style="font-size: 1.8rem; font-weight: 800; color: #f8fafc; margin-bottom: 8px;">Oturum Açın</h2>
    <p style="font-size: 0.9rem; color: #94a3b8;">Lütfen sistem bilgilerinizi giriniz.</p>
</div>

<!-- Flash Alerts -->
<?php if (!empty($flash['error'])): ?>
    <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171; padding: 12px 16px; border-radius: 10px; font-size: 0.875rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-exclamation-circle"></i>
        <span><?= \App\Core\View::escape($flash['error']) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($flash['success'])): ?>
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #34d399; padding: 12px 16px; border-radius: 10px; font-size: 0.875rem; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-check-circle"></i>
        <span><?= \App\Core\View::escape($flash['success']) ?></span>
    </div>
<?php endif; ?>

<form action="/login" method="POST" id="loginForm">
    <?= \App\Helpers\CsrfHelper::field() ?>

    <div style="margin-bottom: 20px;">
        <label for="identifier" style="display: block; font-size: 0.85rem; font-weight: 600; color: #cbd5e1; margin-bottom: 8px;">
            Kullanıcı Adı veya E-posta
        </label>
        <div style="position: relative;">
            <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #64748b;">
                <i class="fas fa-user"></i>
            </div>
            <input 
                type="text" 
                id="identifier" 
                name="identifier" 
                required 
                autofocus
                placeholder="admin"
                style="width: 100%; padding: 14px 14px 14px 42px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 10px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; outline: none; box-sizing: border-box;"
                onfocus="this.style.borderColor='#38bdf8'; this.style.background='rgba(255,255,255,0.05)'"
                onblur="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.background='rgba(255,255,255,0.03)'"
            >
        </div>
    </div>

    <div style="margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label for="password" style="font-size: 0.85rem; font-weight: 600; color: #cbd5e1;">Şifre</label>
        </div>
        <div style="position: relative;">
            <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #64748b;">
                <i class="fas fa-lock"></i>
            </div>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
                placeholder="••••••••"
                style="width: 100%; padding: 14px 42px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 10px; font-size: 0.95rem; font-family: inherit; transition: all 0.2s; outline: none; box-sizing: border-box;"
                onfocus="this.style.borderColor='#38bdf8'; this.style.background='rgba(255,255,255,0.05)'"
                onblur="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.background='rgba(255,255,255,0.03)'"
            >
            <div style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #64748b; cursor: pointer; padding: 5px;" onclick="togglePassword()">
                <i class="fas fa-eye" id="eyeIcon"></i>
            </div>
        </div>
    </div>

    <button type="submit" style="width: 100%; padding: 14px; background: #38bdf8; color: #0f172a; font-weight: bold; font-size: 0.95rem; border: none; border-radius: 10px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 14px rgba(56, 189, 248, 0.4);"
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(56, 189, 248, 0.6)'"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(56, 189, 248, 0.4)'">
        Sisteme Giriş Yap
    </button>
</form>

<script>

function togglePassword() {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        pwd.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
