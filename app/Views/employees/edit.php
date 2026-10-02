<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Personel Düzenle</h1>
    <p class="page-subtitle" style="color: var(--text-muted);"><?= \App\Core\View::escape($employee['first_name'] . ' ' . $employee['last_name']) ?></p>
</div>

<div class="card" style="max-width: 900px;">
    <form action="/employees/<?= $employee['id'] ?>" method="POST">
        <div class="grid grid-2">
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Ad *</label>
                <input type="text" name="first_name" value="<?= \App\Core\View::escape($employee['first_name']) ?>" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Soyad *</label>
                <input type="text" name="last_name" value="<?= \App\Core\View::escape($employee['last_name']) ?>" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Sicil No *</label>
                <input type="text" name="registration_no" value="<?= \App\Core\View::escape($employee['registration_no']) ?>" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Departman</label>
                <select name="department_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                    <option value="">Seçiniz...</option>
                    <?php foreach($departments ?? [] as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= ($employee['department_id'] == $dept['id']) ? 'selected' : '' ?>>
                            <?= \App\Core\View::escape($dept['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Görev / Unvan</label>
                <input type="text" name="title" value="<?= \App\Core\View::escape($employee['title']) ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Durum</label>
                <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                    <option value="active" <?= ($employee['status'] === 'active') ? 'selected' : '' ?>>Aktif</option>
                    <option value="passive" <?= ($employee['status'] === 'passive') ? 'selected' : '' ?>>Pasif</option>
                </select>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">E-posta</label>
                <input type="email" name="email" value="<?= \App\Core\View::escape($employee['email'] ?? '') ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Telefon</label>
                <input type="text" name="phone" value="<?= \App\Core\View::escape($employee['phone'] ?? '') ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Açıklama / Notlar</label>
            <textarea name="notes" rows="4" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; resize: vertical;"><?= \App\Core\View::escape($employee['notes'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <a href="/employees" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
        </div>
    </form>
</div>
