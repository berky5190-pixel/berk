<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Personel Detayı</h1>
        <p class="page-subtitle" style="color: var(--text-muted);">Sicil No: <?= \App\Core\View::escape($employee['registration_no']) ?></p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="/employees" class="btn btn-secondary">Listeye Dön</a>
        <a href="/employees/<?= $employee['id'] ?>/edit" class="btn btn-primary">
            <i class="fas fa-edit"></i> Düzenle
        </a>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom: 24px;">
    <!-- Sol Panel: Profil -->
    <div class="card" style="grid-column: span 1;">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--primary-light); color: var(--primary); font-size: 3rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fas fa-user"></i>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 700;"><?= \App\Core\View::escape($employee['first_name'] . ' ' . $employee['last_name']) ?></h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 8px;"><?= \App\Core\View::escape($employee['title'] ?? '-') ?></p>
            
            <?php if ($employee['status'] === 'active'): ?>
                <span class="badge badge-success">Aktif</span>
            <?php else: ?>
                <span class="badge badge-secondary">Pasif</span>
            <?php endif; ?>
        </div>
        
        <div style="border-top: 1px solid var(--border-color); padding-top: 16px;">
            <h3 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 12px; color: var(--text-muted); text-transform: uppercase;">İletişim Bilgileri</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; gap: 12px; align-items: center; font-size: 0.9rem;">
                    <i class="fas fa-envelope" style="color: var(--text-light); width: 16px;"></i>
                    <span><?= \App\Core\View::escape($employee['email'] ?? 'Belirtilmemiş') ?></span>
                </div>
                <div style="display: flex; gap: 12px; align-items: center; font-size: 0.9rem;">
                    <i class="fas fa-phone" style="color: var(--text-light); width: 16px;"></i>
                    <span><?= \App\Core\View::escape($employee['phone'] ?? 'Belirtilmemiş') ?></span>
                </div>
                <div style="display: flex; gap: 12px; align-items: center; font-size: 0.9rem;">
                    <i class="fas fa-building" style="color: var(--text-light); width: 16px;"></i>
                    <span><?= \App\Core\View::escape($employee['department_name'] ?? 'Departman Yok') ?></span>
                </div>
            </div>
        </div>

        <?php if (!empty($employee['notes'])): ?>
        <div style="border-top: 1px solid var(--border-color); padding-top: 16px; margin-top: 16px;">
            <h3 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 8px; color: var(--text-muted); text-transform: uppercase;">Açıklama</h3>
            <p style="font-size: 0.85rem; color: var(--text-main);"><?= nl2br(\App\Core\View::escape($employee['notes'])) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sağ Panel: Zimmetler -->
    <div style="grid-column: span 2; display: flex; flex-direction: column; gap: 24px;">
        
        <!-- Aktif Zimmetler -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Üzerindeki Zimmetler (<?= count($active_assignments ?? []) ?>)</h2>
                <a href="/assignments/create?employee_id=<?= $employee['id'] ?>" class="btn btn-primary" style="padding: 4px 10px; font-size: 0.75rem;">
                    <i class="fas fa-plus"></i> Yeni Zimmet
                </a>
            </div>
            
            <div class="table-responsive" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--text-muted);">
                            <th style="padding: 12px; font-weight: 600;">Demirbaş Kodu</th>
                            <th style="padding: 12px; font-weight: 600;">Ürün Adı</th>
                            <th style="padding: 12px; font-weight: 600;">Zimmet Tarihi</th>
                            <th style="padding: 12px; font-weight: 600; text-align: right;">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($active_assignments)): ?>
                            <tr><td colspan="4" style="padding: 24px; text-align: center; color: var(--text-muted);">Üzerinde aktif zimmet bulunmuyor.</td></tr>
                        <?php else: ?>
                            <?php foreach ($active_assignments as $asg): ?>
                                <tr style="border-bottom: 1px solid var(--bg-main);">
                                    <td style="padding: 12px; font-weight: 500;"><a href="/assets/<?= $asg['asset_id'] ?>"><?= \App\Core\View::escape($asg['asset_code']) ?></a></td>
                                    <td style="padding: 12px;"><?= \App\Core\View::escape($asg['asset_name']) ?></td>
                                    <td style="padding: 12px;"><?= date('d.m.Y', strtotime($asg['assignment_date'])) ?></td>
                                    <td style="padding: 12px; text-align: right;">
                                        <a href="/assignments/<?= $asg['id'] ?>" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem;">Detay</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Geçmiş Zimmetler -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Geçmiş Zimmetler (<?= count($history_assignments ?? []) ?>)</h2>
            </div>
            
            <div class="table-responsive" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--text-muted);">
                            <th style="padding: 12px; font-weight: 600;">Demirbaş</th>
                            <th style="padding: 12px; font-weight: 600;">Zimmet Tarihi</th>
                            <th style="padding: 12px; font-weight: 600;">İade Tarihi</th>
                            <th style="padding: 12px; font-weight: 600;">Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history_assignments)): ?>
                            <tr><td colspan="4" style="padding: 24px; text-align: center; color: var(--text-muted);">Geçmiş zimmet kaydı bulunmuyor.</td></tr>
                        <?php else: ?>
                            <?php foreach ($history_assignments as $asg): ?>
                                <tr style="border-bottom: 1px solid var(--bg-main);">
                                    <td style="padding: 12px;">
                                        <div style="font-weight: 500;"><?= \App\Core\View::escape($asg['asset_name']) ?></div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?= \App\Core\View::escape($asg['asset_code']) ?></div>
                                    </td>
                                    <td style="padding: 12px;"><?= date('d.m.Y', strtotime($asg['assignment_date'])) ?></td>
                                    <td style="padding: 12px;"><?= $asg['actual_return_date'] ? date('d.m.Y', strtotime($asg['actual_return_date'])) : '-' ?></td>
                                    <td style="padding: 12px;">
                                        <span class="badge badge-secondary"><?= \App\Core\View::escape($asg['status']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
