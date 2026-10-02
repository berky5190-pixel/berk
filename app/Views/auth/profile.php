<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <div>
            <h1 class="card-title" style="margin-bottom: 4px;">Kullanıcı Profili</h1>
            <p style="font-size: 0.875rem; color: var(--text-muted);">Aktif oturum ve yetki ayrıntıları</p>
        </div>
        <span class="badge badge-primary"><?= \App\Core\View::escape($user['role_name'] ?? 'Kullanıcı') ?></span>
    </div>

    <!-- User Information Grid -->
    <div class="grid grid-2" style="margin-bottom: 24px;">
        <div style="background: var(--bg-main); padding: 16px; border-radius: var(--radius-md);">
            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Ad Soyad</span>
            <p style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-top: 4px;">
                <?= \App\Core\View::escape($user['full_name'] ?? '') ?>
            </p>
        </div>

        <div style="background: var(--bg-main); padding: 16px; border-radius: var(--radius-md);">
            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Kullanıcı Adı</span>
            <p style="font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin-top: 4px;">
                @<?= \App\Core\View::escape($user['username'] ?? '') ?>
            </p>
        </div>

        <div style="background: var(--bg-main); padding: 16px; border-radius: var(--radius-md);">
            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">E-posta</span>
            <p style="font-size: 1rem; font-weight: 600; color: var(--text-main); margin-top: 4px;">
                <?= \App\Core\View::escape($user['email'] ?? '') ?>
            </p>
        </div>

        <div style="background: var(--bg-main); padding: 16px; border-radius: var(--radius-md);">
            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">Hesap Durumu</span>
            <p style="margin-top: 6px;">
                <span class="badge badge-success"><span class="status-dot active"></span> Aktif</span>
            </p>
        </div>
    </div>

    <!-- Permissions List -->
    <div style="margin-bottom: 28px;">
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 12px;">Tanımlı İzinler & Yetkiler</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            <?php if (!empty($user['permissions'])): ?>
                <?php foreach ($user['permissions'] as $perm): ?>
                    <span style="background: #eef2ff; color: #4338ca; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-family: monospace; border: 1px solid #c7d2fe;">
                        <?= \App\Core\View::escape($perm) ?>
                    </span>
                <?php endforeach; ?>
            <?php else: ?>
                <span style="color: var(--text-muted); font-size: 0.875rem;">Yönetici rolü (Tüm izinlere erişim açık)</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 20px;">
        <a href="/" class="btn btn-secondary">← Ana Sayfaya Dön</a>
        <a href="/logout" class="btn btn-danger">Güvenli Çıkış Yap</a>
    </div>
</div>
