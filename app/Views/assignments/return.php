<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Zimmet İade İşlemi</h1>
    <p class="page-subtitle" style="color: var(--text-muted);">Zimmet No: <?= \App\Core\View::escape($assignment['assignment_code']) ?></p>
</div>

<div class="card" style="max-width: 800px;">
    
    <div style="background: var(--bg-main); padding: 16px; border-radius: var(--radius-md); margin-bottom: 24px;">
        <h4 style="margin-top: 0; margin-bottom: 8px; font-size: 1rem;">İade Alınacak Demirbaş</h4>
        <div style="display: flex; gap: 20px; align-items: center;">
            <div style="flex: 1;">
                <div style="font-weight: 600;"><?= \App\Core\View::escape($assignment['asset_name']) ?></div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">Personel: <?= \App\Core\View::escape($assignment['first_name'] . ' ' . $assignment['last_name']) ?></div>
            </div>
            <div>
                <span class="badge badge-primary">Aktif Zimmet</span>
            </div>
        </div>
    </div>

    <form action="/assignments/<?= $assignment['id'] ?>/return" method="POST">
        
        <div class="grid grid-2" style="margin-bottom: 24px;">
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">İade Tarihi *</label>
                <input type="date" name="actual_return_date" value="<?= date('Y-m-d') ?>" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">İade Durumu *</label>
                <select name="return_condition" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                    <option value="good">Sağlam / Çalışır Durumda</option>
                    <option value="defective">Arızalı</option>
                    <option value="damaged">Hasarlı (Kullanıcı Hatası)</option>
                    <option value="scrapped">Hurda</option>
                </select>
                <small style="color: var(--text-muted);">Seçilen duruma göre demirbaş stoğu güncellenecektir.</small>
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">İade Notları / Tutanak</label>
            <textarea name="return_notes" rows="4" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; resize: vertical;" placeholder="Eksik parça, hasar detayı vb. notlar..."></textarea>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <a href="/assignments" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn btn-success" style="background: var(--success); color: white; border: none; padding: 10px 20px; border-radius: var(--radius-sm); font-weight: 600; cursor: pointer;"><i class="fas fa-undo"></i> İadeyi Tamamla</button>
        </div>
    </form>
</div>
