<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--danger);">Son Silinenler (Çöp Kutusu)</h1>
    <p class="page-subtitle" style="color: var(--text-muted);">Sistemden silinen kayıtların işlem geçmişi</p>
</div>

<div class="grid grid-3" style="gap: 24px; align-items: start;">
    
    <!-- Silinen Personeller -->
    <div class="card" style="border-top: 4px solid var(--info);">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); margin-bottom: 16px; padding-bottom: 12px;">
            <h2 class="card-title" style="font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-users" style="color: var(--info);"></i> Silinen Personeller
            </h2>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php if (empty($deletedEmployees)): ?>
                <p style="color: var(--text-muted); font-size: 0.85rem; text-align: center; padding: 20px 0;">Silinmiş personel kaydı yok.</p>
            <?php else: ?>
                <?php foreach ($deletedEmployees as $item): ?>
                    <div style="padding: 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-main);">
                        <div style="font-weight: 600; color: var(--text-main); margin-bottom: 4px;"><?= \App\Core\View::escape($item['details']) ?></div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--text-muted);">
                            <div>
                                <span><i class="fas fa-user"></i> <?= \App\Core\View::escape($item['user']) ?></span>
                                <span style="margin-left: 8px;"><?= date('d.m.Y H:i', strtotime($item['date'])) ?></span>
                            </div>
                            <?php if (!empty($item['can_restore'])): ?>
                                <form action="/trash/<?= $item['id'] ?>/restore" method="POST" style="margin: 0;" data-confirm="Bu personeli geri yüklemek istediğinize emin misiniz?">
                                    <button type="submit" class="btn btn-sm btn-outline" style="padding: 2px 8px; font-size: 0.7rem;">
                                        <i class="fas fa-undo"></i> Geri Yükle
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Silinen Demirbaşlar -->
    <div class="card" style="border-top: 4px solid var(--primary);">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); margin-bottom: 16px; padding-bottom: 12px;">
            <h2 class="card-title" style="font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-box" style="color: var(--primary);"></i> Silinen Demirbaşlar
            </h2>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php if (empty($deletedAssets)): ?>
                <p style="color: var(--text-muted); font-size: 0.85rem; text-align: center; padding: 20px 0;">Silinmiş demirbaş kaydı yok.</p>
            <?php else: ?>
                <?php foreach ($deletedAssets as $item): ?>
                    <div style="padding: 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-main);">
                        <div style="font-weight: 600; color: var(--text-main); margin-bottom: 4px;"><?= \App\Core\View::escape($item['details']) ?></div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--text-muted);">
                            <div>
                                <span><i class="fas fa-user"></i> <?= \App\Core\View::escape($item['user']) ?></span>
                                <span style="margin-left: 8px;"><?= date('d.m.Y H:i', strtotime($item['date'])) ?></span>
                            </div>
                            <?php if (!empty($item['can_restore'])): ?>
                                <form action="/trash/<?= $item['id'] ?>/restore" method="POST" style="margin: 0;" data-confirm="Bu demirbaşı geri yüklemek istediğinize emin misiniz?">
                                    <button type="submit" class="btn btn-sm btn-outline" style="padding: 2px 8px; font-size: 0.7rem;">
                                        <i class="fas fa-undo"></i> Geri Yükle
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Silinen Zimmetler -->
    <div class="card" style="border-top: 4px solid var(--warning);">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); margin-bottom: 16px; padding-bottom: 12px;">
            <h2 class="card-title" style="font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-clipboard-list" style="color: var(--warning);"></i> İptal/Silinen Zimmetler
            </h2>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php if (empty($deletedAssignments)): ?>
                <p style="color: var(--text-muted); font-size: 0.85rem; text-align: center; padding: 20px 0;">İptal edilmiş/silinmiş zimmet kaydı yok.</p>
            <?php else: ?>
                <?php foreach ($deletedAssignments as $item): ?>
                    <div style="padding: 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-main);">
                        <div style="font-weight: 600; color: var(--text-main); margin-bottom: 4px;"><?= \App\Core\View::escape($item['details']) ?></div>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--text-muted);">
                            <div>
                                <span><i class="fas fa-user"></i> <?= \App\Core\View::escape($item['user']) ?></span>
                                <span style="margin-left: 8px;"><?= date('d.m.Y H:i', strtotime($item['date'])) ?></span>
                            </div>
                            <?php if (!empty($item['can_restore'])): ?>
                                <form action="/trash/<?= $item['id'] ?>/restore" method="POST" style="margin: 0;" data-confirm="Bu kaydı geri yüklemek istediğinize emin misiniz?">
                                    <button type="submit" class="btn btn-sm btn-outline" style="padding: 2px 8px; font-size: 0.7rem;">
                                        <i class="fas fa-undo"></i> Geri Yükle
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
