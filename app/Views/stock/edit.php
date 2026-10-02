<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Stok Düzenle: <?= \App\Core\View::escape($item['name']) ?></h1>
    <p class="page-subtitle" style="color: var(--text-muted);">Stok kartını düzenliyorsunuz</p>
</div>

<div class="card" style="max-width: 900px;">
    <form action="/stock/<?= $item['id'] ?>" method="POST">
        
        <div class="grid grid-2" style="margin-bottom: 24px;">
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Stok Kodu *</label>
                <input type="text" name="stock_code" value="<?= \App\Core\View::escape($item['stock_code']) ?>" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Ürün Adı *</label>
                <input type="text" name="name" value="<?= \App\Core\View::escape($item['name']) ?>" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Kategori *</label>
                <select name="category_id" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                    <option value="">Seçiniz...</option>
                    <?php foreach($categories ?? [] as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $item['category_id'] ? 'selected' : '' ?>><?= \App\Core\View::escape($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Lokasyon / Depo</label>
                <select name="location_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                    <option value="">Seçiniz...</option>
                    <?php foreach($locations ?? [] as $loc): ?>
                        <option value="<?= $loc['id'] ?>" <?= $loc['id'] == $item['location_id'] ? 'selected' : '' ?>><?= \App\Core\View::escape($loc['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Marka</label>
                <input type="text" name="brand" value="<?= \App\Core\View::escape($item['brand'] ?? '') ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Model / Alt Özellik</label>
                <input type="text" name="model" value="<?= \App\Core\View::escape($item['model'] ?? '') ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Birim</label>
                <select name="unit" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                    <option value="piece" <?= $item['unit'] === 'piece' ? 'selected' : '' ?>>Adet (piece)</option>
                    <option value="box" <?= $item['unit'] === 'box' ? 'selected' : '' ?>>Kutu (box)</option>
                    <option value="set" <?= $item['unit'] === 'set' ? 'selected' : '' ?>>Set (set)</option>
                    <option value="kg" <?= $item['unit'] === 'kg' ? 'selected' : '' ?>>Kilogram (kg)</option>
                    <option value="meter" <?= $item['unit'] === 'meter' ? 'selected' : '' ?>>Metre (meter)</option>
                </select>
            </div>
        </div>

        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Stok Seviyeleri</h3>
        <div class="grid grid-3" style="margin-bottom: 24px;">
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Başlangıç Stoğu</label>
                <input type="number" name="initial_stock" value="<?= $item['current_stock'] ?>" disabled style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background:var(--bg-main); cursor:not-allowed; color: var(--text-muted);">
                <p style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Stok miktarı detay sayfasından yönetilir.</p>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Kritik Stok Uyarı Seviyesi</label>
                <input type="number" name="min_stock" value="<?= $item['min_stock'] ?>" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Maksimum Stok Kapasitesi</label>
                <input type="number" name="max_stock" value="<?= $item['max_stock'] ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Açıklama</label>
            <textarea name="description" rows="3" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; resize: vertical;"><?= \App\Core\View::escape($item['description'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <a href="/stock" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </div>
    </form>
</div>
