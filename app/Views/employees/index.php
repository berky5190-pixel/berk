<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Personel Yönetimi</h1>
        <p class="page-subtitle" style="color: var(--text-muted);">Sistemde kayıtlı personelleri yönetin</p>
    </div>
    <a href="/employees/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Personel Ekle
    </a>
</div>

<div class="card">
    <div class="card-header" style="margin-bottom: 0;">
        <h2 class="card-title" style="font-size: 1.1rem;">Kayıtlı Personeller (<?= count($employees ?? []) ?>)</h2>
        <div style="display: flex; gap: 8px;">
            <!-- Dummy filter buttons for UI -->
            <button class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.8rem;"><i class="fas fa-filter"></i> Filtrele</button>
        </div>
    </div>

    <div class="table-responsive" style="overflow-x: auto; margin-top: 16px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--text-muted); background: var(--bg-main);">
                    <th style="padding: 12px; font-weight: 600;">Sicil No</th>
                    <th style="padding: 12px; font-weight: 600;">Ad Soyad</th>
                    <th style="padding: 12px; font-weight: 600;">Departman</th>
                    <th style="padding: 12px; font-weight: 600;">Görev</th>
                    <th style="padding: 12px; font-weight: 600;">İletişim</th>
                    <th style="padding: 12px; font-weight: 600;">Durum</th>
                    <th style="padding: 12px; font-weight: 600; text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($employees)): ?>
                    <tr><td colspan="7" style="padding: 24px; text-align: center; color: var(--text-muted);">Kayıtlı personel bulunamadı.</td></tr>
                <?php else: ?>
                    <?php foreach ($employees as $emp): ?>
                        <tr style="border-bottom: 1px solid var(--bg-main); transition: var(--transition);">
                            <td style="padding: 12px; font-weight: 600; color: var(--primary);"><?= \App\Core\View::escape($emp['registration_no']) ?></td>
                            <td style="padding: 12px; font-weight: 500;">
                                <?= \App\Core\View::escape($emp['first_name'] . ' ' . $emp['last_name']) ?>
                            </td>
                            <td style="padding: 12px; color: var(--text-muted);">
                                <?= \App\Core\View::escape($emp['department_name'] ?? 'Bilinmiyor') ?>
                            </td>
                            <td style="padding: 12px;"><?= \App\Core\View::escape($emp['title'] ?? '-') ?></td>
                            <td style="padding: 12px; font-size: 0.85rem;">
                                <?= \App\Core\View::escape($emp['email'] ?? '-') ?><br>
                                <span style="color: var(--text-muted);"><?= \App\Core\View::escape($emp['phone'] ?? '-') ?></span>
                            </td>
                            <td style="padding: 12px;">
                                <?php if ($emp['status'] === 'active'): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Pasif</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 12px; text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="/employees/<?= $emp['id'] ?>" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem; background: var(--info-light); color: var(--info); border: none;" title="Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/employees/<?= $emp['id'] ?>/edit" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem; background: var(--warning-light); color: var(--warning); border: none;" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="/employees/<?= $emp['id'] ?>/delete" method="POST" data-confirm="Bu personeli silmek istediğinize emin misiniz?" style="display: inline;">
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
