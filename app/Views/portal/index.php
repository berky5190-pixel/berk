<?php
$emp = $employee ?? [];
$myAssignments = $myAssignments ?? [];
$myRequests = $myRequests ?? [];
$stats = $stats ?? [];
?>

<!-- Personel Karşılama Kartı -->
<div class="card" style="margin-bottom: 24px; padding: 24px; background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-main) 100%); border-left: 5px solid var(--primary); box-shadow: var(--shadow-md);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div style="display: flex; align-items: center; gap: 18px;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 800; border: 2px solid var(--primary);">
                <?= strtoupper(mb_substr($emp['first_name'] ?? 'P', 0, 1, 'UTF-8')) ?>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main); margin: 0;">
                        Hoş Geldiniz, <?= \App\Core\View::escape(($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? '')) ?>
                    </h1>
                    <span class="badge" style="background: var(--success-light); color: var(--success); font-size: 0.75rem; padding: 3px 8px; border-radius: 12px; font-weight: 600;">
                        <i class="fas fa-check-circle"></i> Aktif Personel
                    </span>
                </div>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 6px; display: flex; gap: 16px; flex-wrap: wrap;">
                    <span><i class="fas fa-id-badge" style="color: var(--primary);"></i> Sicil No: <strong><?= \App\Core\View::escape($emp['registration_no'] ?? '-') ?></strong></span>
                    <span><i class="fas fa-briefcase" style="color: var(--primary);"></i> Görev: <strong><?= \App\Core\View::escape($emp['title'] ?? 'Personel') ?></strong></span>
                    <span><i class="fas fa-building" style="color: var(--primary);"></i> Birim: <strong><?= \App\Core\View::escape($emp['department_name'] ?? '-') ?></strong></span>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="/requests" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus-circle"></i> Yeni Talep Aç
            </a>
            <a href="/assignments" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-clipboard-list"></i> Tüm Zimmetlerim
            </a>
        </div>
    </div>
</div>

<!-- Özet Metrikleri -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="padding: 20px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--primary);">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Üzerimdeki Zimmetler</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-top: 4px;">
                <?= count($myAssignments) ?> <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-muted);">Adet Cihaz</span>
            </div>
        </div>
        <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
            <i class="fas fa-laptop"></i>
        </div>
    </div>

    <div class="card" style="padding: 20px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #f59e0b;">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Açık / Bekleyen Taleplerim</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: #d97706; margin-top: 4px;">
                <?= $stats['pending_requests'] ?? 0 ?> <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-muted);">Talep</span>
            </div>
        </div>
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
            <i class="fas fa-hourglass-half"></i>
        </div>
    </div>

    <div class="card" style="padding: 20px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: space-between; border-left: 4px solid var(--success);">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Sonuçlanan Talepler</div>
            <div style="font-size: 1.8rem; font-weight: 800; color: var(--success); margin-top: 4px;">
                <?= $stats['completed_requests'] ?? 0 ?> <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-muted);">İşlem</span>
            </div>
        </div>
        <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--success-light); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
            <i class="fas fa-check-double"></i>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 24px;">

    <!-- 1. Zimmetli Cihazlarım -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 14px; border-bottom: 1px solid var(--border-color);">
            <div>
                <h2 class="card-title" style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-box" style="color: var(--primary);"></i> Üzerinize Zimmetli Cihazlar & Ekipmanlar
                </h2>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 4px 0 0 0;">
                    Şu an kurum tarafından kullanımınıza tahsis edilmiş aktif demirbaşlar
                </p>
            </div>
            <span class="badge" style="background: var(--primary-light); color: var(--primary); font-weight: 700; font-size: 0.85rem; padding: 4px 10px;">
                <?= count($myAssignments) ?> Cihaz
            </span>
        </div>

        <?php if (empty($myAssignments)): ?>
            <div style="padding: 40px 20px; text-align: center; color: var(--text-muted);">
                <i class="fas fa-shield-alt" style="font-size: 2.8rem; color: var(--border-color); margin-bottom: 12px; display: block;"></i>
                Şu anda üzerinize zimmetlenmiş herhangi bir demirbaş bulunmamaktadır.
                <div style="margin-top: 12px;">
                    <a href="/requests" class="btn btn-primary" style="font-size: 0.85rem;">
                        <i class="fas fa-plus"></i> Ekipman Talebinde Bulun
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive" style="overflow-x: auto; margin-top: 14px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color); text-align: left; color: var(--text-muted); background: var(--bg-main);">
                            <th style="padding: 12px; font-weight: 600;">Demirbaş Kodu</th>
                            <th style="padding: 12px; font-weight: 600;">Cihaz / Ekipman Adı</th>
                            <th style="padding: 12px; font-weight: 600;">Marka & Model</th>
                            <th style="padding: 12px; font-weight: 600;">Seri No</th>
                            <th style="padding: 12px; font-weight: 600;">Zimmet Tarihi</th>
                            <th style="padding: 12px; font-weight: 600; text-align: right;">Hızlı İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($myAssignments as $a): ?>
                            <tr style="border-bottom: 1px solid var(--border-color); transition: background-color 0.2s;">
                                <td style="padding: 14px 12px; font-weight: 700; color: var(--primary); font-family: monospace;">
                                    <?= \App\Core\View::escape($a['asset_code']) ?>
                                </td>
                                <td style="padding: 14px 12px;">
                                    <div style="font-weight: 700; color: var(--text-main);">
                                        <?= \App\Core\View::escape($a['asset_name']) ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        <?= \App\Core\View::escape($a['category_name'] ?? 'Donanım') ?>
                                    </div>
                                </td>
                                <td style="padding: 14px 12px; color: var(--text-main);">
                                    <?= \App\Core\View::escape(($a['brand'] ?? '') . ' ' . ($a['model'] ?? '')) ?>
                                </td>
                                <td style="padding: 14px 12px; font-family: monospace; font-size: 0.85rem; color: var(--text-muted);">
                                    <?= \App\Core\View::escape($a['serial_number'] ?? '-') ?>
                                </td>
                                <td style="padding: 14px 12px; font-size: 0.85rem; color: var(--text-muted);">
                                    <i class="far fa-calendar-alt" style="color: var(--primary); margin-right: 4px;"></i>
                                    <?= date('d.m.Y', strtotime($a['assignment_date'])) ?>
                                </td>
                                <td style="padding: 14px 12px; text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <!-- Arıza Bildir Butonu -->
                                        <a href="/requests?asset_id=<?= $a['asset_id'] ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem; background: var(--danger-light); color: var(--danger); border: none; font-weight: 600;" title="Bu cihaz için arıza bildirimi yap">
                                            <i class="fas fa-wrench"></i> Arıza Bildir
                                        </a>

                                        <!-- Değişim İste Butonu -->
                                        <a href="/requests?asset_id=<?= $a['asset_id'] ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem; background: #fef3c7; color: #d97706; border: none; font-weight: 600;" title="Cihaz değişimi talep et">
                                            <i class="fas fa-sync-alt"></i> Değişim İste
                                        </a>

                                        <!-- Tutanak İncele / Yazdır -->
                                        <a href="/assignments/<?= $a['id'] ?>" class="btn btn-secondary" style="padding: 6px 10px; font-size: 0.8rem;" title="Teslim-Tesellüm Tutanağı">
                                            <i class="fas fa-file-pdf"></i> Tutanak
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- 2. Son Taleplerim -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 14px; border-bottom: 1px solid var(--border-color);">
            <div>
                <h2 class="card-title" style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-history" style="color: var(--primary);"></i> Son Talep ve Arıza Bildirimlerim
                </h2>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 4px 0 0 0;">
                    Bilgi İşlem birimine ilettiğiniz arıza, bakım ve sarf malzeme isteklerinizin güncel durumları
                </p>
            </div>
            <a href="/requests" class="btn btn-secondary" style="font-size: 0.85rem;">
                Tümünü Gör (<?= count($myRequests) ?>) <i class="fas fa-arrow-right" style="margin-left: 4px;"></i>
            </a>
        </div>

        <?php if (empty($myRequests)): ?>
            <div style="padding: 30px 20px; text-align: center; color: var(--text-muted);">
                <i class="fas fa-clipboard-check" style="font-size: 2.4rem; color: var(--border-color); margin-bottom: 10px; display: block;"></i>
                Şu ana kadar herhangi bir talep oluşturmadınız.
                <div style="margin-top: 10px;">
                    <a href="/requests" class="btn btn-primary" style="font-size: 0.85rem;">
                        <i class="fas fa-plus"></i> Yeni Talep Oluştur
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive" style="overflow-x: auto; margin-top: 14px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color); text-align: left; color: var(--text-muted); background: var(--bg-main);">
                            <th style="padding: 12px; font-weight: 600;">Talep Kodu</th>
                            <th style="padding: 12px; font-weight: 600;">Tür</th>
                            <th style="padding: 12px; font-weight: 600;">Başlık & İlgili Cihaz</th>
                            <th style="padding: 12px; font-weight: 600;">Durum</th>
                            <th style="padding: 12px; font-weight: 600;">Tarih</th>
                            <th style="padding: 12px; font-weight: 600; text-align: right;">Detay</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($myRequests, 0, 5) as $req): ?>
                            <?php
                                $typeLabels = [
                                    'malfunction' => ['label' => 'Arıza / Onarım', 'color' => 'var(--danger)', 'bg' => 'var(--danger-light)', 'icon' => 'fa-wrench'],
                                    'exchange' => ['label' => 'Cihaz Değişimi', 'color' => '#d97706', 'bg' => '#fef3c7', 'icon' => 'fa-sync-alt'],
                                    'equipment' => ['label' => 'Yeni Ekipman', 'color' => 'var(--primary)', 'bg' => 'var(--primary-light)', 'icon' => 'fa-box']
                                ];
                                $t = $typeLabels[$req['request_type']] ?? ['label' => $req['request_type'], 'color' => 'var(--text-muted)', 'bg' => 'var(--bg-main)', 'icon' => 'fa-tag'];

                                $statusLabels = [
                                    'pending' => ['label' => 'Beklemede', 'bg' => '#fef3c7', 'color' => '#d97706', 'icon' => 'fa-clock'],
                                    'in_review' => ['label' => 'İnceleniyor', 'bg' => 'var(--info-light)', 'color' => 'var(--info)', 'icon' => 'fa-search'],
                                    'approved' => ['label' => 'Onaylandı', 'bg' => 'var(--success-light)', 'color' => 'var(--success)', 'icon' => 'fa-check'],
                                    'rejected' => ['label' => 'Reddedildi', 'bg' => 'var(--danger-light)', 'color' => 'var(--danger)', 'icon' => 'fa-times'],
                                    'completed' => ['label' => 'Tamamlandı', 'bg' => '#e0e7ff', 'color' => '#4338ca', 'icon' => 'fa-flag-checkered']
                                ];
                                $s = $statusLabels[$req['status']] ?? ['label' => $req['status'], 'bg' => 'var(--bg-main)', 'color' => 'var(--text-muted)', 'icon' => 'fa-circle'];
                            ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 12px; font-weight: 700; color: var(--primary); font-family: monospace;">
                                    <?= \App\Core\View::escape($req['request_code']) ?>
                                </td>
                                <td style="padding: 12px;">
                                    <span class="badge" style="background: <?= $t['bg'] ?>; color: <?= $t['color'] ?>; font-size: 0.75rem; padding: 4px 8px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas <?= $t['icon'] ?>"></i> <?= $t['label'] ?>
                                    </span>
                                </td>
                                <td style="padding: 12px;">
                                    <div style="font-weight: 600; color: var(--text-main);"><?= \App\Core\View::escape($req['title']) ?></div>
                                    <?php if (!empty($req['asset_name'])): ?>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                                            <?= \App\Core\View::escape($req['asset_name']) ?> (<?= \App\Core\View::escape($req['asset_code'] ?? '') ?>)
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($req['admin_notes'])): ?>
                                        <div style="margin-top: 4px; font-size: 0.75rem; color: var(--primary); background: var(--primary-light); padding: 3px 6px; border-radius: 4px; display: inline-block;">
                                            <i class="fas fa-comment-dots"></i> <strong>IT Yanıtı:</strong> <?= \App\Core\View::escape($req['admin_notes']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px;">
                                    <span class="badge" style="background: <?= $s['bg'] ?>; color: <?= $s['color'] ?>; font-size: 0.75rem; padding: 4px 8px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fas <?= $s['icon'] ?>"></i> <?= $s['label'] ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; font-size: 0.8rem; color: var(--text-muted);">
                                    <?= date('d.m.Y H:i', strtotime($req['created_at'])) ?>
                                </td>
                                <td style="padding: 12px; text-align: right;">
                                    <a href="/requests" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem;">
                                        <i class="fas fa-eye"></i> İncele
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>
