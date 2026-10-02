<div class="dashboard-header" style="margin-bottom: 32px; padding: 40px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); border-radius: var(--radius-lg); color: white; box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3); display: flex; justify-content: space-between; align-items: center; position: relative; overflow: hidden;">
    <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; filter: blur(20px);"></div>
    <div style="position: absolute; bottom: -30px; right: 100px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%; filter: blur(15px);"></div>
    
    <div style="position: relative; z-index: 2;">
        <h1 class="page-title" style="font-size: 2.2rem; font-weight: 800; margin-bottom: 8px; color: white;">Hoş Geldiniz, <?= \App\Core\View::escape($_SESSION['full_name'] ?? 'Yönetici') ?> 👋</h1>
        <p class="page-subtitle" style="color: rgba(255,255,255,0.8); font-size: 1.05rem;">Kurumsal Demirbaş & Envanter Yönetim Sistemi Özeti</p>
    </div>
    <div style="position: relative; z-index: 2;">
        <div style="background: rgba(255,255,255,0.2); padding: 12px 24px; border-radius: var(--radius-md); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
            <div style="font-size: 0.85rem; color: rgba(255,255,255,0.8); text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Tarih</div>
            <div style="font-size: 1.2rem; font-weight: 700;"><?= date('d F Y') ?></div>
        </div>
    </div>
</div>

<!-- TOP STATS -->
<div class="grid grid-4" style="margin-bottom: 24px;">
    <div class="stat-card" style="padding: 24px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px; transition: var(--transition-smooth);">
        <div class="stat-icon" style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; box-shadow: 0 8px 15px rgba(99, 102, 241, 0.3);">
            <i class="fas fa-box-open"></i>
        </div>
        <div>
            <p style="font-size: 0.9rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Toplam Demirbaş</p>
            <h3 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-top: 4px;"><?= $stats['total_assets'] ?? 0 ?></h3>
        </div>
    </div>
    
    <div class="stat-card" style="padding: 24px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px;">
        <div class="stat-icon" style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, var(--success) 0%, #059669 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; box-shadow: 0 8px 15px rgba(16, 185, 129, 0.3);">
            <i class="fas fa-hands-helping"></i>
        </div>
        <div>
            <p style="font-size: 0.9rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Zimmetli</p>
            <h3 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-top: 4px;"><?= $stats['assigned_assets'] ?? 0 ?></h3>
        </div>
    </div>

    <div class="stat-card" style="padding: 24px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px;">
        <div class="stat-icon" style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; box-shadow: 0 8px 15px rgba(245, 158, 11, 0.3);">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <p style="font-size: 0.9rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Arızalı/Servis</p>
            <h3 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-top: 4px;"><?= ($stats['defective_assets'] ?? 0) + ($stats['service_assets'] ?? 0) ?></h3>
        </div>
    </div>

    <div class="stat-card" style="padding: 24px; border-radius: var(--radius-lg); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px;">
        <div class="stat-icon" style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, var(--info) 0%, #0284c7 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; box-shadow: 0 8px 15px rgba(14, 165, 233, 0.3);">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <p style="font-size: 0.9rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Personel</p>
            <h3 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-top: 4px;"><?= $stats['total_employees'] ?? 0 ?></h3>
        </div>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom: 24px;">
    <!-- Zimmet Durumu -->
    <div class="card" style="grid-column: span 2; display: flex; flex-direction: column;">
        <div class="card-header">
            <h2 class="card-title">Zimmet & Stok Özeti</h2>
        </div>
        <div class="grid grid-3" style="gap: 24px; flex: 1; height: 100%;">
            <div style="display: flex; flex-direction: column; justify-content: center; text-align: center; padding: 40px 20px; background: var(--bg-main); border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); height: 100%; min-height: 200px;">
                <div style="font-size: 4rem; font-weight: 800; color: var(--primary); line-height: 1; margin-bottom: 12px;"><?= $stats['active_assignments'] ?? 0 ?></div>
                <div style="font-size: 1.1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Aktif Zimmet</div>
            </div>
            <div style="display: flex; flex-direction: column; justify-content: center; text-align: center; padding: 40px 20px; background: var(--bg-main); border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); height: 100%; min-height: 200px;">
                <div style="font-size: 4rem; font-weight: 800; color: var(--warning); line-height: 1; margin-bottom: 12px;"><?= $stats['pending_return'] ?? 0 ?></div>
                <div style="font-size: 1.1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">İade Bekleyen</div>
            </div>
            <div style="display: flex; flex-direction: column; justify-content: center; text-align: center; padding: 40px 20px; background: var(--bg-main); border-radius: var(--radius-lg); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); height: 100%; min-height: 200px;">
                <div style="font-size: 4rem; font-weight: 800; color: var(--danger); line-height: 1; margin-bottom: 12px;"><?= $stats['critical_stock'] ?? 0 ?></div>
                <div style="font-size: 1.1rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Kritik Stok</div>
            </div>
        </div>
    </div>

    <!-- Bildirimler -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Bildirimler</h2>
            <span class="badge badge-danger"><?= count($notifications ?? []) ?> Yeni</span>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php if (empty($notifications)): ?>
                <p style="color: var(--text-muted); font-size: 0.9rem; text-align: center; padding: 20px 0;">Bildirim bulunmuyor.</p>
            <?php else: ?>
                <?php foreach ($notifications as $notif): ?>
                    <div style="display: flex; gap: 12px; align-items: flex-start; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--primary); margin-top: 6px;"></div>
                        <div>
                            <div style="font-size: 0.85rem; font-weight: 600;"><?= \App\Core\View::escape($notif['title']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?= \App\Core\View::escape($notif['message']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Son İşlemler (Audit Logs) -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Son İşlemler</h2>
        <a href="/audit-logs" class="btn btn-secondary" style="font-size: 0.75rem; padding: 4px 10px;">Tümünü Gör</a>
    </div>
    
    <div class="table-responsive" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--text-muted);">
                    <th style="padding: 12px; font-weight: 600;">Tarih</th>
                    <th style="padding: 12px; font-weight: 600;">Kullanıcı</th>
                    <th style="padding: 12px; font-weight: 600;">Modül</th>
                    <th style="padding: 12px; font-weight: 600;">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_actions)): ?>
                    <tr><td colspan="4" style="padding: 24px; text-align: center; color: var(--text-muted);">Henüz işlem kaydı bulunmuyor.</td></tr>
                <?php else: ?>
                    <?php foreach ($recent_actions as $log): ?>
                        <tr style="border-bottom: 1px solid var(--bg-main);">
                            <td style="padding: 12px; color: var(--text-muted);"><?= date('d.m.Y H:i', strtotime($log['created_at'])) ?></td>
                            <td style="padding: 12px; font-weight: 500;"><?= \App\Core\View::escape($log['user_name'] ?? 'Sistem') ?></td>
                            <td style="padding: 12px;">
                                <span class="badge badge-secondary"><?= \App\Core\View::escape($log['module']) ?></span>
                            </td>
                            <td style="padding: 12px;">
                                <?= \App\Core\View::escape($log['action']) ?>: <strong><?= \App\Core\View::escape($log['details'] ?? '') ?></strong>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
