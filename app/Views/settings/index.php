<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Sistem Ayarları</h1>
    <p class="page-subtitle" style="color: var(--text-muted);">Uygulama genel parametrelerini yönetin</p>
</div>

<div class="card" style="max-width: 800px;">
    <form action="/settings" method="POST">
        
        <?php foreach ($settingsGrouped as $group => $settings): ?>
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; margin-top: <?= $group === array_key_first($settingsGrouped) ? '0' : '24px' ?>; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; text-transform: capitalize;">
                <?= \App\Core\View::escape($group) ?> Ayarları
            </h3>
            
            <div class="grid grid-2">
                <?php foreach ($settings as $setting): ?>
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;" title="<?= \App\Core\View::escape($setting['description']) ?>">
                            <?= \App\Core\View::escape($setting['description'] ?: $setting['key']) ?>
                        </label>
                        <input type="text" name="<?= \App\Core\View::escape($setting['key']) ?>" value="<?= \App\Core\View::escape($setting['value']) ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 20px; margin-top: 24px;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Ayarları Kaydet</button>
        </div>
    </form>
</div>
