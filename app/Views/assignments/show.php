<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Zimmet Detayı</h1>
        <p class="page-subtitle" style="color: var(--text-muted);">Zimmet No: <?= \App\Core\View::escape($assignment['assignment_code']) ?></p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="/assignments" class="btn btn-secondary">Listeye Dön</a>
        <?php if($assignment['status'] === 'active'): ?>
        <a href="/assignments/<?= $assignment['id'] ?>/return" class="btn btn-primary" style="background: var(--success); color: white; border: none;">
            <i class="fas fa-undo"></i> İade Al
        </a>
        <?php endif; ?>
        <a href="/assignments/<?= $assignment['id'] ?>/print" target="_blank" class="btn btn-secondary" style="background: var(--primary-light); color: var(--primary); border: none;">
            <i class="fas fa-print"></i> Yazdır / PDF
        </a>
    </div>
</div>

<div class="grid grid-2" style="margin-bottom: 24px;">
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Demirbaş Bilgileri</h3>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; justify-content: space-between; font-size: 0.95rem;">
                <span style="color: var(--text-muted);">Ürün Adı:</span>
                <span style="font-weight: 600;"><a href="/assets/<?= $assignment['asset_id'] ?>" style="color: var(--primary); text-decoration: none;"><?= \App\Core\View::escape($assignment['asset_name']) ?></a></span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.95rem;">
                <span style="color: var(--text-muted);">Demirbaş Kodu:</span>
                <span style="font-weight: 500;"><?= \App\Core\View::escape($assignment['asset_code']) ?></span>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Personel Bilgileri</h3>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; justify-content: space-between; font-size: 0.95rem;">
                <span style="color: var(--text-muted);">Ad Soyad:</span>
                <span style="font-weight: 600;"><a href="/employees/<?= $assignment['employee_id'] ?>" style="color: var(--primary); text-decoration: none;"><?= \App\Core\View::escape($assignment['first_name'] . ' ' . $assignment['last_name']) ?></a></span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.95rem;">
                <span style="color: var(--text-muted);">Sicil No:</span>
                <span style="font-weight: 500;"><?= \App\Core\View::escape($assignment['registration_no']) ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.95rem;">
                <span style="color: var(--text-muted);">Unvan:</span>
                <span style="font-weight: 500;"><?= \App\Core\View::escape($assignment['title'] ?? '-') ?></span>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Zimmet Durumu & Tarihler</h3>
    <div class="grid grid-3">
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="font-size: 0.95rem;">
                <span style="color: var(--text-muted); display: block; margin-bottom: 4px;">Zimmet Durumu:</span>
                <?php
                $statusColors = [
                    'active' => ['bg' => 'var(--primary-light)', 'color' => 'var(--primary)', 'label' => 'Aktif Zimmet'],
                    'returned' => ['bg' => 'var(--success-light)', 'color' => 'var(--success)', 'label' => 'İade Edildi'],
                    'pending_return' => ['bg' => 'var(--warning-light)', 'color' => 'var(--warning)', 'label' => 'İade Bekliyor'],
                    'lost' => ['bg' => '#f3f4f6', 'color' => '#374151', 'label' => 'Kayıp'],
                    'damaged' => ['bg' => 'var(--danger-light)', 'color' => 'var(--danger)', 'label' => 'Hasarlı'],
                ];
                $st = $statusColors[$assignment['status']] ?? $statusColors['active'];
                ?>
                <span class="badge" style="background: <?= $st['bg'] ?>; color: <?= $st['color'] ?>; font-size: 1rem; padding: 6px 12px;"><?= $st['label'] ?></span>
            </div>
            <div style="font-size: 0.95rem; margin-top: 12px;">
                <span style="color: var(--text-muted); display: block; margin-bottom: 4px;">İşlemi Yapan:</span>
                <span style="font-weight: 500;"><?= \App\Core\View::escape($assignment['assigned_by_name'] ?? 'Sistem') ?></span>
            </div>
        </div>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="font-size: 0.95rem;">
                <span style="color: var(--text-muted); display: block; margin-bottom: 4px;">Veriliş Tarihi:</span>
                <span style="font-weight: 500; font-size: 1.05rem;"><?= date('d.m.Y', strtotime($assignment['assignment_date'])) ?></span>
            </div>
            <div style="font-size: 0.95rem; margin-top: 12px;">
                <span style="color: var(--text-muted); display: block; margin-bottom: 4px;">Planlanan İade:</span>
                <span style="font-weight: 500;"><?= $assignment['planned_return_date'] ? date('d.m.Y', strtotime($assignment['planned_return_date'])) : 'Süresiz' ?></span>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="font-size: 0.95rem;">
                <span style="color: var(--text-muted); display: block; margin-bottom: 4px;">Gerçekleşen İade Tarihi:</span>
                <span style="font-weight: 500; font-size: 1.05rem;"><?= $assignment['actual_return_date'] ? date('d.m.Y', strtotime($assignment['actual_return_date'])) : '-' ?></span>
            </div>
            <?php if($assignment['return_condition']): ?>
            <div style="font-size: 0.95rem; margin-top: 12px;">
                <span style="color: var(--text-muted); display: block; margin-bottom: 4px;">İade Durumu (Fiziksel):</span>
                <span style="font-weight: 500;"><?= \App\Core\View::escape($assignment['return_condition']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if(!empty($assignment['notes']) || !empty($assignment['return_notes'])): ?>
<div class="card" style="margin-bottom: 24px;">
    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Notlar ve Açıklamalar</h3>
    
    <?php if(!empty($assignment['notes'])): ?>
        <div style="margin-bottom: 16px;">
            <h4 style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 8px;">Veriliş Notu:</h4>
            <p style="font-size: 0.9rem; background: var(--bg-main); padding: 12px; border-radius: var(--radius-sm);"><?= nl2br(\App\Core\View::escape($assignment['notes'])) ?></p>
        </div>
    <?php endif; ?>

    <?php if(!empty($assignment['return_notes'])): ?>
        <div>
            <h4 style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 8px;">İade Notu:</h4>
            <p style="font-size: 0.9rem; background: var(--warning-light); color: #854d0e; padding: 12px; border-radius: var(--radius-sm);"><?= nl2br(\App\Core\View::escape($assignment['return_notes'])) ?></p>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="grid grid-2" style="margin-bottom: 24px;">
    <!-- Yüklenen Belgeler -->
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
            <i class="fas fa-file-alt" style="color: var(--primary);"></i> Zimmet Belgeleri
        </h3>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php if (empty($documents)): ?>
                <p style="color: var(--text-muted); font-size: 0.85rem; text-align: center; padding: 20px 0;">Henüz belge yüklenmemiş.</p>
            <?php else: ?>
                <?php foreach ($documents as $doc): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-main);">
                        <div>
                            <div style="font-weight: 600; font-size: 0.9rem;"><i class="fas fa-file-pdf" style="color: var(--danger);"></i> <?= \App\Core\View::escape($doc['original_filename']) ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px;"><?= date('d.m.Y H:i', strtotime($doc['uploaded_at'])) ?> - <?= round($doc['file_size'] / 1024) ?> KB</div>
                        </div>
                        <a href="/assignments/document/<?= $doc['id'] ?>" class="btn btn-secondary" style="font-size: 0.8rem; padding: 6px 12px;" target="_blank">İndir</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Belge Yükle -->
    <div class="card">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
            <i class="fas fa-upload" style="color: var(--success);"></i> Yeni Belge Yükle
        </h3>
        <form action="/assignments/<?= $assignment['id'] ?>/document" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Belge Türü</label>
                <select name="document_type" class="form-control" required>
                    <option value="initial_protocol">Teslim Tutanağı (Veriliş)</option>
                    <option value="return_protocol">İade Tutanağı (Dönüş)</option>
                    <option value="damage_report">Hasar/Arıza Raporu</option>
                    <option value="other">Diğer</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Dosya (PDF, Word, JPG, PNG)</label>
                <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
            </div>
            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Yükle</button>
            </div>
        </form>
    </div>
</div>
