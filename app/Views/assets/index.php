<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Demirbaş Yönetimi</h1>
        <p class="page-subtitle" style="color: var(--text-muted);">Sistemde kayıtlı demirbaşları yönetin</p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="/assets/print" target="_blank" class="btn btn-secondary" style="background: white;">
            <i class="fas fa-print"></i> Listeyi Yazdır
        </a>
        <a href="/assets/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Demirbaş Ekle
        </a>
    </div>
</div>

<div class="card" style="margin-bottom: 24px; padding: 16px 24px;">
    <form method="GET" action="/assets" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px;">Arama</label>
            <input type="text" name="search" value="<?= \App\Core\View::escape($filters['search'] ?? '') ?>" placeholder="Ürün Adı, Kodu, Seri No..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
        </div>
        <div style="width: 200px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px;">Durum</label>
            <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                <option value="">Tümü</option>
                <option value="in_stock" <?= ($filters['status'] ?? '') === 'in_stock' ? 'selected' : '' ?>>Stokta</option>
                <option value="assigned" <?= ($filters['status'] ?? '') === 'assigned' ? 'selected' : '' ?>>Zimmetli</option>
                <option value="defective" <?= ($filters['status'] ?? '') === 'defective' ? 'selected' : '' ?>>Arızalı</option>
                <option value="in_service" <?= ($filters['status'] ?? '') === 'in_service' ? 'selected' : '' ?>>Serviste</option>
                <option value="scrapped" <?= ($filters['status'] ?? '') === 'scrapped' ? 'selected' : '' ?>>Hurda</option>
                <option value="lost" <?= ($filters['status'] ?? '') === 'lost' ? 'selected' : '' ?>>Kayıp</option>
            </select>
        </div>
        <div style="width: 200px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px;">Kategori</label>
            <select name="category" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                <option value="">Tümü</option>
                <?php foreach($categories ?? [] as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($filters['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= \App\Core\View::escape($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="padding: 8px 16px;"><i class="fas fa-search"></i> Filtrele</button>
            <a href="/assets" class="btn btn-secondary" style="padding: 8px 16px;">Temizle</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header" style="margin-bottom: 0;">
        <h2 class="card-title" style="font-size: 1.1rem;">Kayıtlı Demirbaşlar (<?= count($assets ?? []) ?>)</h2>
    </div>

    <div class="table-responsive" style="overflow-x: auto; margin-top: 16px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--text-muted); background: var(--bg-main);">
                    <th style="padding: 12px; font-weight: 600;">Demirbaş Kodu</th>
                    <th style="padding: 12px; font-weight: 600;">Ürün Adı</th>
                    <th style="padding: 12px; font-weight: 600;">Kategori</th>
                    <th style="padding: 12px; font-weight: 600;">Marka / Model</th>
                    <th style="padding: 12px; font-weight: 600;">Durum</th>
                    <th style="padding: 12px; font-weight: 600; text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($assets)): ?>
                    <tr><td colspan="6" style="padding: 24px; text-align: center; color: var(--text-muted);">Demirbaş bulunamadı.</td></tr>
                <?php else: ?>
                    <?php foreach ($assets as $asset): ?>
                        <tr style="border-bottom: 1px solid var(--bg-main); transition: var(--transition);">
                            <td style="padding: 12px; font-weight: 600; color: var(--primary);"><?= \App\Core\View::escape($asset['asset_code']) ?></td>
                            <td style="padding: 12px; font-weight: 500;">
                                <?= \App\Core\View::escape($asset['name']) ?>
                                <?php if($asset['serial_number']): ?>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">SN: <?= \App\Core\View::escape($asset['serial_number']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px; color: var(--text-muted);">
                                <?= \App\Core\View::escape($asset['category_name'] ?? '-') ?>
                            </td>
                            <td style="padding: 12px;">
                                <?= \App\Core\View::escape($asset['brand'] ?? '-') ?> <br>
                                <span style="font-size: 0.8rem; color: var(--text-muted);"><?= \App\Core\View::escape($asset['model'] ?? '-') ?></span>
                            </td>
                            <td style="padding: 12px;">
                                <?php
                                $statusColors = [
                                    'in_stock' => ['bg' => 'var(--success-light)', 'color' => 'var(--success)', 'label' => 'Stokta'],
                                    'assigned' => ['bg' => 'var(--primary-light)', 'color' => 'var(--primary)', 'label' => 'Zimmetli'],
                                    'in_use' => ['bg' => 'var(--info-light)', 'color' => 'var(--info)', 'label' => 'Kullanımda'],
                                    'defective' => ['bg' => 'var(--danger-light)', 'color' => 'var(--danger)', 'label' => 'Arızalı'],
                                    'in_service' => ['bg' => 'var(--warning-light)', 'color' => 'var(--warning)', 'label' => 'Serviste'],
                                    'scrapped' => ['bg' => 'var(--secondary-light)', 'color' => 'var(--secondary)', 'label' => 'Hurda'],
                                    'lost' => ['bg' => '#f3f4f6', 'color' => '#374151', 'label' => 'Kayıp'],
                                ];
                                $st = $statusColors[$asset['status']] ?? $statusColors['in_stock'];
                                ?>
                                <span class="badge" style="background: <?= $st['bg'] ?>; color: <?= $st['color'] ?>;"><?= $st['label'] ?></span>
                            </td>
                            <td style="padding: 12px; text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="/assets/<?= $asset['id'] ?>" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem; background: var(--info-light); color: var(--info); border: none;" title="Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/assets/<?= $asset['id'] ?>/edit" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem; background: var(--warning-light); color: var(--warning); border: none;" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="/assets/<?= $asset['id'] ?>/delete" method="POST" data-confirm="Bu demirbaşı silmek istediğinize emin misiniz?" style="display: inline;">
                                        <button type="submit" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem; background: var(--danger-light); color: var(--danger); border: none;" title="Sil">
                                            <i class="fas fa-trash"></i>
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
