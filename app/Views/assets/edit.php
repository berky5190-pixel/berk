<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Demirbaş Düzenle</h1>
    <p class="page-subtitle" style="color: var(--text-muted);"><?= \App\Core\View::escape($asset['asset_code'] . ' - ' . $asset['name']) ?></p>
</div>

<div class="card" style="max-width: 1000px;">
    <form action="/assets/<?= $asset['id'] ?>" method="POST">
        
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Temel Bilgiler</h3>
        <div class="grid grid-3" style="margin-bottom: 24px;">
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Demirbaş Kodu / Etiket No *</label>
                <input type="text" name="asset_code" value="<?= \App\Core\View::escape($asset['asset_code']) ?>" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            
            <div style="grid-column: span 2;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Ürün Adı *</label>
                <input type="text" name="name" value="<?= \App\Core\View::escape($asset['name']) ?>" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Kategori *</label>
                <select name="category_id" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                    <option value="">Seçiniz...</option>
                    <?php foreach($categories ?? [] as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $asset['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= \App\Core\View::escape($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Marka</label>
                <input type="text" name="brand" value="<?= \App\Core\View::escape($asset['brand']) ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Model</label>
                <input type="text" name="model" value="<?= \App\Core\View::escape($asset['model']) ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Seri No</label>
                <input type="text" name="serial_number" value="<?= \App\Core\View::escape($asset['serial_number'] ?? '') ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Barkod</label>
                <input type="text" name="barcode" value="<?= \App\Core\View::escape($asset['barcode'] ?? '') ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Durum</label>
                <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                    <?php
                    $statuses = [
                        'in_stock' => 'Stokta',
                        'assigned' => 'Zimmetli',
                        'in_use' => 'Kullanımda',
                        'defective' => 'Arızalı',
                        'in_service' => 'Serviste',
                        'scrapped' => 'Hurda',
                        'lost' => 'Kayıp'
                    ];
                    foreach ($statuses as $val => $label):
                    ?>
                        <option value="<?= $val ?>" <?= $asset['status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Satın Alma ve Garanti</h3>
        <div class="grid grid-3" style="margin-bottom: 24px;">
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Satın Alma Tarihi</label>
                <input type="date" name="purchase_date" value="<?= $asset['purchase_date'] ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Fiyat</label>
                <input type="number" step="0.01" name="purchase_price" value="<?= $asset['purchase_price'] ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            <div>
                <!-- Spacer -->
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Garanti Başlangıç</label>
                <input type="date" name="warranty_start" value="<?= $asset['warranty_start'] ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Garanti Bitiş</label>
                <input type="date" name="warranty_end" value="<?= $asset['warranty_end'] ?>" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Açıklama / Notlar</label>
            <textarea name="description" rows="3" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; resize: vertical;"><?= \App\Core\View::escape($asset['description'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <a href="/assets" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
        </div>
    </form>
</div>
