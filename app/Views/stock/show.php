<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Stok Detayı ve Hareketleri</h1>
        <p class="page-subtitle" style="color: var(--text-muted);">Stok Kodu: <?= \App\Core\View::escape($item['stock_code']) ?></p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="/stock" class="btn btn-secondary">Listeye Dön</a>
        <a href="/stock/<?= $item['id'] ?>/edit" class="btn btn-primary" style="background: var(--warning); border: none;">
            <i class="fas fa-edit"></i> Düzenle
        </a>
        <form action="/stock/<?= $item['id'] ?>/delete" method="POST" data-confirm="Bu stok kartını silmek istediğinize emin misiniz? (Geçmiş hareketler varsa silinemez)" style="display: inline;">
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash-alt"></i> Sil
            </button>
        </form>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom: 24px;">
    <!-- Sol Panel: Ürün Bilgileri -->
    <div class="card" style="grid-column: span 1;">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 100px; height: 100px; border-radius: 12px; background: var(--info-light); color: var(--info); font-size: 2.5rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fas fa-boxes"></i>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 4px;"><?= \App\Core\View::escape($item['name']) ?></h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 12px;"><?= \App\Core\View::escape($item['category_name'] ?? 'Kategori Yok') ?></p>
            
            <div style="background: var(--bg-main); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 4px;">Mevcut Stok</div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--text-main);">
                    <?= $item['current_stock'] ?> <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);"><?= \App\Core\View::escape($item['unit']) ?></span>
                </div>
            </div>
        </div>
        
        <div style="border-top: 1px solid var(--border-color); padding-top: 16px;">
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Marka/Model:</span>
                    <span style="font-weight: 500;"><?= \App\Core\View::escape($item['brand'] . ' ' . $item['model']) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Lokasyon:</span>
                    <span style="font-weight: 500;"><?= \App\Core\View::escape($item['location_name'] ?: '-') ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Kritik Eşik:</span>
                    <span style="font-weight: 500; color: var(--danger);"><?= $item['min_stock'] ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sağ Panel: Hareket Girişi ve Geçmiş -->
    <div style="grid-column: span 2; display: flex; flex-direction: column; gap: 24px;">
        
        <!-- Yeni Hareket Formu -->
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Yeni Stok Hareketi Ekle</h3>
            <form action="/stock/<?= $item['id'] ?>/movement" method="POST" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 150px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">İşlem Türü</label>
                    <select name="movement_type" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                        <option value="in">Giriş (Ekle)</option>
                        <option value="out">Çıkış (Sarf/Kullanım)</option>
                        <option value="return">İade Girişi</option>
                        <option value="adjustment">Düzeltme Çıkışı</option>
                    </select>
                </div>
                <div style="width: 120px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Miktar</label>
                    <input type="number" name="quantity" min="1" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
                </div>
                <div style="flex: 2; min-width: 200px;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Açıklama (Kime verildi vb.)</label>
                    <input type="text" name="notes" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 16px; height: 38px;">Kaydet</button>
                </div>
            </form>
        </div>

        <!-- Hareket Geçmişi Tablosu -->
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px;">Hareket Geçmişi</h3>
            <div class="table-responsive" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--text-muted);">
                            <th style="padding: 12px; font-weight: 600;">Tarih</th>
                            <th style="padding: 12px; font-weight: 600;">İşlem</th>
                            <th style="padding: 12px; font-weight: 600;">Miktar</th>
                            <th style="padding: 12px; font-weight: 600;">Yeni Stok</th>
                            <th style="padding: 12px; font-weight: 600;">Açıklama</th>
                            <th style="padding: 12px; font-weight: 600;">İşlem Yapan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($movements)): ?>
                            <tr><td colspan="6" style="padding: 24px; text-align: center; color: var(--text-muted);">Hareket kaydı bulunamadı.</td></tr>
                        <?php else: ?>
                            <?php foreach ($movements as $mov): ?>
                                <tr style="border-bottom: 1px solid var(--bg-main);">
                                    <td style="padding: 12px; color: var(--text-muted);"><?= date('d.m.Y H:i', strtotime($mov['created_at'])) ?></td>
                                    <td style="padding: 12px;">
                                        <?php if(in_array($mov['movement_type'], ['in', 'return'])): ?>
                                            <span style="color: var(--success); font-weight: 600;"><i class="fas fa-arrow-down"></i> Giriş</span>
                                        <?php else: ?>
                                            <span style="color: var(--danger); font-weight: 600;"><i class="fas fa-arrow-up"></i> Çıkış</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px; font-weight: 700;"><?= $mov['quantity'] ?></td>
                                    <td style="padding: 12px; font-weight: 700; color: var(--text-main);"><?= $mov['new_stock'] ?></td>
                                    <td style="padding: 12px;"><?= \App\Core\View::escape($mov['notes'] ?? '-') ?></td>
                                    <td style="padding: 12px; font-size: 0.8rem; color: var(--text-muted);"><?= \App\Core\View::escape($mov['user_name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
