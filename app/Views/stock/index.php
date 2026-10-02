<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Stok ve Sarf Malzemeler</h1>
        <p class="page-subtitle" style="color: var(--text-muted);">Sarf malzemeleri, tüketilebilir ürünleri ve genel stokları yönetin</p>
    </div>
    <a href="/stock/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Stok Kartı
    </a>
</div>

<div class="card" style="margin-bottom: 24px; padding: 16px 24px;">
    <form method="GET" action="/stock" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px;">Arama</label>
            <input type="text" name="search" value="<?= \App\Core\View::escape($filters['search'] ?? '') ?>" placeholder="Ürün Adı, Stok Kodu..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="padding: 8px 16px;"><i class="fas fa-search"></i> Ara</button>
            <a href="/stock" class="btn btn-secondary" style="padding: 8px 16px;">Temizle</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header" style="margin-bottom: 0;">
        <h2 class="card-title" style="font-size: 1.1rem;">Kayıtlı Stok Kartları (<?= count($items ?? []) ?>)</h2>
    </div>

    <div class="table-responsive" style="overflow-x: auto; margin-top: 16px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--text-muted); background: var(--bg-main);">
                    <th style="padding: 12px; font-weight: 600;">Stok Kodu</th>
                    <th style="padding: 12px; font-weight: 600;">Ürün Adı</th>
                    <th style="padding: 12px; font-weight: 600;">Kategori</th>
                    <th style="padding: 12px; font-weight: 600;">Mevcut Stok</th>
                    <th style="padding: 12px; font-weight: 600;">Durum</th>
                    <th style="padding: 12px; font-weight: 600; text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="6" style="padding: 24px; text-align: center; color: var(--text-muted);">Kayıtlı stok kartı bulunamadı.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr style="border-bottom: 1px solid var(--bg-main); transition: var(--transition);">
                            <td style="padding: 12px; font-weight: 600; color: var(--primary);"><?= \App\Core\View::escape($item['stock_code']) ?></td>
                            <td style="padding: 12px; font-weight: 500;">
                                <?= \App\Core\View::escape($item['name']) ?>
                                <?php if($item['brand']): ?>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= \App\Core\View::escape($item['brand'] . ' ' . $item['model']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px; color: var(--text-muted);">
                                <?= \App\Core\View::escape($item['category_name'] ?? '-') ?>
                            </td>
                            <td style="padding: 12px; font-weight: 700; font-size: 1.05rem;">
                                <?= $item['current_stock'] ?> <span style="font-size: 0.8rem; font-weight: 400; color: var(--text-muted);"><?= \App\Core\View::escape($item['unit']) ?></span>
                            </td>
                            <td style="padding: 12px;">
                                <?php if ($item['current_stock'] <= 0): ?>
                                    <span class="badge" style="background: var(--danger-light); color: var(--danger);">Tükendi</span>
                                <?php elseif ($item['current_stock'] <= $item['min_stock']): ?>
                                    <span class="badge" style="background: var(--warning-light); color: var(--warning);">Kritik Seviye</span>
                                <?php else: ?>
                                    <span class="badge" style="background: var(--success-light); color: var(--success);">Yeterli</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px; text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="/stock/<?= $item['id'] ?>" class="btn btn-secondary" style="padding: 4px 12px; font-size: 0.8rem; background: var(--info-light); color: var(--info); border: none;" title="Detay / Stok Ekle">
                                        <i class="fas fa-boxes"></i>
                                    </a>
                                    <a href="/stock/<?= $item['id'] ?>/edit" class="btn btn-secondary" style="padding: 4px 12px; font-size: 0.8rem; background: var(--warning-light); color: var(--warning); border: none;" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="/stock/<?= $item['id'] ?>/delete" method="POST" data-confirm="Bu stok kartını silmek istediğinize emin misiniz? (Geçmiş hareketler varsa silinemez)" style="display: inline;">
                                        <button type="submit" class="btn btn-danger" style="padding: 4px 12px; font-size: 0.8rem; border: none;" title="Sil">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
