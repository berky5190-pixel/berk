<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Kullanıcı Yönetimi</h1>
    <p class="page-subtitle" style="color: var(--text-muted);">Sisteme yeni admin veya kullanıcı ekleyin, yetkilerini belirleyin.</p>
</div>

<div class="grid grid-2" style="margin-bottom: 24px; align-items: start;">
    <!-- Yeni Kullanıcı Ekleme Formu -->
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
            <i class="fas fa-user-plus" style="color: var(--primary);"></i> Yeni Kullanıcı / Admin Ekle
        </h3>
        <form action="/users" method="POST">
            <?= \App\Helpers\CsrfHelper::field() ?>
            <div class="form-group">
                <label class="form-label">Ad Soyad</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Kullanıcı Adı</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">E-posta</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Şifre</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Yetki / Rol</label>
                    <select name="role_id" class="form-control" required>
                        <?php foreach($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= \App\Core\View::escape($r['name']) ?> - (<?= \App\Core\View::escape($r['description']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">İlişkili Personel (Zimmet & Self-Service Portalı İçin)</label>
                <select name="employee_id" class="form-control">
                    <option value="">-- Personel Seçilmedi (Yalnızca Sistem Hesabı) --</option>
                    <?php foreach($employees ?? [] as $emp): ?>
                        <option value="<?= $emp['id'] ?>">
                            <?= \App\Core\View::escape($emp['first_name'] . ' ' . $emp['last_name']) ?> (Sicil: <?= \App\Core\View::escape($emp['registration_no']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: var(--text-muted); font-size: 0.75rem;">Standart kullanıcılar sisteme girdiğinde bu personele ait zimmetleri ve talepleri görür.</small>
            </div>
            <div style="text-align: right; margin-top: 12px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Kullanıcı Ekle</button>
            </div>
        </form>
    </div>

    <!-- Mevcut Kullanıcılar -->
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
            <i class="fas fa-users" style="color: var(--success);"></i> Sistem Kullanıcıları
        </h3>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach($users as $u): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-main);">
                    <div>
                        <div style="font-weight: 600; font-size: 0.95rem; color: var(--text-main);">
                            <?= \App\Core\View::escape($u['full_name']) ?> 
                            <span style="font-weight: 400; color: var(--text-muted);">(@<?= \App\Core\View::escape($u['username']) ?>)</span>
                        </div>
                        <div style="font-size: 0.75rem; margin-top: 4px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                            <?php if($u['role_id'] == 1): ?>
                                <span class="badge badge-danger"><?= \App\Core\View::escape($u['role_name']) ?></span>
                            <?php elseif($u['role_id'] == 2): ?>
                                <span class="badge badge-warning"><?= \App\Core\View::escape($u['role_name']) ?></span>
                            <?php else: ?>
                                <span class="badge badge-secondary"><?= \App\Core\View::escape($u['role_name']) ?></span>
                            <?php endif; ?>
                            <span style="color: var(--text-muted);"><?= \App\Core\View::escape($u['email']) ?></span>
                            <?php if(!empty($u['first_name'])): ?>
                                <span class="badge" style="background: var(--primary-light); color: var(--primary); font-weight: 600;">
                                    <i class="fas fa-user-check"></i> <?= \App\Core\View::escape($u['first_name'] . ' ' . $u['last_name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <a href="/users/<?= $u['id'] ?>/edit" class="btn" style="background: var(--warning-light); color: var(--warning); padding: 6px 10px; border: none; border-radius: var(--radius-sm);" title="Düzenle">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if($u['id'] != 1): ?>
                        <form action="/users/<?= $u['id'] ?>/delete" method="POST" data-confirm="Kullanıcıyı silmek istediğinize emin misiniz?">
                            <button type="submit" class="btn" style="background: var(--danger-light); color: var(--danger); padding: 6px 10px; border: none; border-radius: var(--radius-sm);" title="Sil">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
