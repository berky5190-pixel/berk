<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Kullanıcı Düzenle</h1>
        <p class="page-subtitle" style="color: var(--text-muted);">Sistem yetkilisi veya kullanıcı bilgilerini güncelleyin.</p>
    </div>
    <a href="/users" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Geri Dön
    </a>
</div>

<div class="card" style="max-width: 800px;">
    <form action="/users/<?= $user['id'] ?>" method="POST">
        <?= \App\Helpers\CsrfHelper::field() ?>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label" style="font-weight: 600;">Ad Soyad</label>
            <input type="text" name="full_name" class="form-control" value="<?= \App\Core\View::escape($user['full_name']) ?>" required>
        </div>

        <div class="grid grid-2" style="margin-bottom: 20px;">
            <div class="form-group">
                <label class="form-label" style="font-weight: 600;">Kullanıcı Adı</label>
                <input type="text" name="username" class="form-control" value="<?= \App\Core\View::escape($user['username']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label" style="font-weight: 600;">E-posta</label>
                <input type="email" name="email" class="form-control" value="<?= \App\Core\View::escape($user['email']) ?>" required>
            </div>
        </div>

        <div class="grid grid-2" style="margin-bottom: 20px;">
            <div class="form-group">
                <label class="form-label" style="font-weight: 600;">Yeni Şifre <span style="font-weight:normal; font-size: 0.8rem; color: var(--text-muted);">(Değiştirmek istemiyorsanız boş bırakın)</span></label>
                <input type="password" name="password" class="form-control" placeholder="****">
            </div>
            <div class="form-group">
                <label class="form-label" style="font-weight: 600;">Yetki / Rol</label>
                <select name="role_id" class="form-control" required>
                    <?php foreach($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= $r['id'] == $user['role_id'] ? 'selected' : '' ?>>
                            <?= \App\Core\View::escape($r['name']) ?> - (<?= \App\Core\View::escape($r['description']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label" style="font-weight: 600;">İlişkili Personel (Zimmet & Self-Service Portalı)</label>
            <select name="employee_id" class="form-control">
                <option value="">-- Personel Seçilmedi (Yalnızca Sistem Hesabı) --</option>
                <?php foreach($employees ?? [] as $emp): ?>
                    <option value="<?= $emp['id'] ?>" <?= ($user['employee_id'] ?? null) == $emp['id'] ? 'selected' : '' ?>>
                        <?= \App\Core\View::escape($emp['first_name'] . ' ' . $emp['last_name']) ?> (Sicil: <?= \App\Core\View::escape($emp['registration_no']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <small style="color: var(--text-muted); font-size: 0.75rem;">Bu kullanıcı standart personel rolünde giriş yaptığında yalnızca bu personele ait zimmet ve talepleri yönetebilir.</small>
        </div>

        <div style="border-top: 1px solid var(--border-color); padding-top: 20px; text-align: right;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Değişiklikleri Kaydet</button>
        </div>
    </form>
</div>
