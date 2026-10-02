<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Bildirim Merkezi</h1>
    <p class="page-subtitle" style="color: var(--text-muted);">Sistem uyarıları ve bilgilendirmeler (Aşama 11)</p>
</div>

<div class="card" style="max-width: 800px;">
    <?php if (empty($notifications)): ?>
        <div style="padding: 40px; text-align: center; color: var(--text-muted);">
            <i class="fas fa-bell-slash" style="font-size: 3rem; margin-bottom: 16px; color: #d1d5db;"></i>
            <p>Hiç bildiriminiz bulunmuyor.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <?php foreach ($notifications as $notif): ?>
                <div style="padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: <?= $notif['is_read'] ? 'white' : '#f0fdf4' ?>;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-weight: 700; color: var(--text-main);"><?= \App\Core\View::escape($notif['title']) ?></span>
                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?= date('d.m.Y H:i', strtotime($notif['created_at'])) ?></span>
                    </div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin: 0;"><?= \App\Core\View::escape($notif['message']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
