<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);"><?= ($isStaff ?? true) ? 'Zimmet Yönetimi' : 'Zimmetli Cihazlarım' ?></h1>
        <p class="page-subtitle" style="color: var(--text-muted);"><?= ($isStaff ?? true) ? 'Personellere zimmetlenen demirbaşları takip edin' : 'Üzerinize zimmetlenmiş demirbaş ve ekipman listesi' ?></p>
    </div>
    <?php if ($isStaff ?? true): ?>
        <a href="/assignments/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Zimmet Fişi
        </a>
    <?php else: ?>
        <a href="/requests" class="btn btn-primary">
            <i class="fas fa-exclamation-triangle"></i> Arıza / Talep Bildir
        </a>
    <?php endif; ?>
</div>

<div class="card" style="margin-bottom: 24px; padding: 16px 24px;">
    <form method="GET" action="/assignments" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px;">Arama</label>
            <input type="text" name="search" value="<?= \App\Core\View::escape($filters['search'] ?? '') ?>" placeholder="Personel Adı, Demirbaş Kodu..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
        </div>
        <div style="width: 200px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px;">Durum</label>
            <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                <option value="">Tümü</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktif (Zimmetli)</option>
                <option value="returned" <?= ($filters['status'] ?? '') === 'returned' ? 'selected' : '' ?>>İade Edildi</option>
                <option value="pending_return" <?= ($filters['status'] ?? '') === 'pending_return' ? 'selected' : '' ?>>İade Bekliyor</option>
                <option value="lost" <?= ($filters['status'] ?? '') === 'lost' ? 'selected' : '' ?>>Kayıp</option>
                <option value="damaged" <?= ($filters['status'] ?? '') === 'damaged' ? 'selected' : '' ?>>Hasarlı</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="padding: 8px 16px;"><i class="fas fa-search"></i> Filtrele</button>
            <a href="/assignments" class="btn btn-secondary" style="padding: 8px 16px;">Temizle</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header" style="margin-bottom: 0;">
        <h2 class="card-title" style="font-size: 1.1rem;">Zimmet Kayıtları (<?= count($assignments ?? []) ?>)</h2>
    </div>

    <div class="table-responsive" style="overflow-x: auto; margin-top: 16px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--text-muted); background: var(--bg-main);">
                    <th style="padding: 12px; font-weight: 600;">Zimmet No</th>
                    <th style="padding: 12px; font-weight: 600;">Demirbaş</th>
                    <th style="padding: 12px; font-weight: 600;">Personel</th>
                    <th style="padding: 12px; font-weight: 600;">Tarihler</th>
                    <th style="padding: 12px; font-weight: 600;">Durum</th>
                    <th style="padding: 12px; font-weight: 600; text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($assignments)): ?>
                    <tr><td colspan="6" style="padding: 24px; text-align: center; color: var(--text-muted);">Zimmet kaydı bulunamadı.</td></tr>
                <?php else: ?>
                    <?php foreach ($assignments as $asg): ?>
                        <tr style="border-bottom: 1px solid var(--bg-main); transition: var(--transition);">
                            <td style="padding: 12px; font-weight: 600; color: var(--primary);"><?= \App\Core\View::escape($asg['assignment_code']) ?></td>
                            <td style="padding: 12px;">
                                <div style="font-weight: 500;"><a href="/assets/<?= $asg['asset_id'] ?>" style="color: var(--text-main); text-decoration: none;"><?= \App\Core\View::escape($asg['asset_name']) ?></a></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><?= \App\Core\View::escape($asg['asset_code']) ?></div>
                            </td>
                            <td style="padding: 12px;">
                                <div style="font-weight: 500;"><a href="/employees/<?= $asg['employee_id'] ?>" style="color: var(--text-main); text-decoration: none;"><?= \App\Core\View::escape($asg['first_name'] . ' ' . $asg['last_name']) ?></a></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Sicil: <?= \App\Core\View::escape($asg['registration_no']) ?></div>
                            </td>
                            <td style="padding: 12px; font-size: 0.85rem;">
                                <div><span style="color: var(--text-muted);">Veriliş:</span> <?= date('d.m.Y', strtotime($asg['assignment_date'])) ?></div>
                                <?php if($asg['actual_return_date']): ?>
                                    <div><span style="color: var(--text-muted);">İade:</span> <?= date('d.m.Y', strtotime($asg['actual_return_date'])) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px;">
                                <?php
                                $statusColors = [
                                    'active' => ['bg' => 'var(--primary-light)', 'color' => 'var(--primary)', 'label' => 'Aktif'],
                                    'returned' => ['bg' => 'var(--success-light)', 'color' => 'var(--success)', 'label' => 'İade Edildi'],
                                    'pending_return' => ['bg' => 'var(--warning-light)', 'color' => 'var(--warning)', 'label' => 'İade Bekliyor'],
                                    'lost' => ['bg' => '#f3f4f6', 'color' => '#374151', 'label' => 'Kayıp'],
                                    'damaged' => ['bg' => 'var(--danger-light)', 'color' => 'var(--danger)', 'label' => 'Hasarlı'],
                                ];
                                $st = $statusColors[$asg['status']] ?? $statusColors['active'];
                                ?>
                                <span class="badge" style="background: <?= $st['bg'] ?>; color: <?= $st['color'] ?>;"><?= $st['label'] ?></span>
                            </td>
                            <td style="padding: 12px; text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <?php if(($isStaff ?? true) && $asg['status'] === 'active'): ?>
                                        <a href="/assignments/<?= $asg['id'] ?>/return" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem; background: var(--success-light); color: var(--success); border: none;" title="İade Al">
                                            <i class="fas fa-undo"></i> İade Al
                                        </a>
                                    <?php elseif(!($isStaff ?? true) && $asg['status'] === 'active'): ?>
                                        <a href="/requests?asset_id=<?= $asg['asset_id'] ?>" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem; background: var(--warning-light); color: #d97706; border: none;" title="Arıza / Değişim Bildir">
                                            <i class="fas fa-tools"></i> Arıza Bildir
                                        </a>
                                    <?php endif; ?>
                                    <a href="/assignments/<?= $asg['id'] ?>" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem; background: var(--info-light); color: var(--info); border: none;" title="Tutanak / Detay">
                                        <i class="fas fa-eye"></i> Detay
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
