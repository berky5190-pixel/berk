<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Yeni Zimmet Fişi</h1>
    <p class="page-subtitle" style="color: var(--text-muted);">Bir personelin üzerine demirbaş zimmetleyin</p>
</div>

<div class="card" style="max-width: 800px;">
    <form action="/assignments" method="POST">
        
        <div class="grid grid-2" style="margin-bottom: 24px;">
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Personel Seçin *</label>
                <select name="employee_id" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                    <option value="">Seçiniz...</option>
                    <?php foreach($employees ?? [] as $emp): ?>
                        <option value="<?= $emp['id'] ?>" <?= $selected_employee == $emp['id'] ? 'selected' : '' ?>>
                            <?= \App\Core\View::escape($emp['first_name'] . ' ' . $emp['last_name'] . ' (' . $emp['registration_no'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Demirbaş Seçin *</label>
                <select name="asset_id" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                    <option value="">Seçiniz (Sadece stoktakiler)...</option>
                    <?php foreach($assets ?? [] as $asset): ?>
                        <option value="<?= $asset['id'] ?>" <?= $selected_asset == $asset['id'] ? 'selected' : '' ?>>
                            <?= \App\Core\View::escape($asset['asset_code'] . ' - ' . $asset['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Veriliş Tarihi *</label>
                <input type="date" name="assignment_date" value="<?= date('Y-m-d') ?>" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Planlanan İade Tarihi</label>
                <input type="date" name="planned_return_date" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
                <small style="color: var(--text-muted);">Süresiz ise boş bırakın</small>
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Açıklama / Şartlar</label>
            <textarea name="notes" rows="4" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; resize: vertical;" placeholder="Teslim edilirkenki durum vb. notlar..."></textarea>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <a href="/assignments" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Zimmetle</button>
        </div>
    </form>
</div>
