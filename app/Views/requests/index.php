<?php
$isStaff = $isStaff ?? false;
$statusFilter = $statusFilter ?? '';
$typeFilter = $typeFilter ?? '';
?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">
            <?= $isStaff ? 'Personel Talep ve Arıza Yönetimi' : 'Taleplerim & Arıza Bildirimleri' ?>
        </h1>
        <p class="page-subtitle" style="color: var(--text-muted);">
            <?= $isStaff ? 'Personellerden gelen arıza, değişim ve ekipman taleplerini inceleyin ve sonuçlandırın' : 'Arıza, donanım değişimi veya yeni sarf malzeme taleplerinizi buradan iletin ve takip edin' ?>
        </p>
    </div>
    <div>
        <button onclick="openNewRequestModal()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus-circle"></i> Yeni Talep Oluştur
        </button>
    </div>
</div>

<!-- Özet Kartları -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="padding: 18px; border-left: 4px solid var(--primary); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Toplam Talep</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: var(--text-main); margin-top: 4px;"><?= $stats['total'] ?? 0 ?></div>
        </div>
        <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
            <i class="fas fa-clipboard-list"></i>
        </div>
    </div>

    <div class="card" style="padding: 18px; border-left: 4px solid #f59e0b; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Bekleyenler</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #d97706; margin-top: 4px;"><?= $stats['pending'] ?? 0 ?></div>
        </div>
        <div style="width: 44px; height: 44px; border-radius: 10px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
            <i class="fas fa-hourglass-half"></i>
        </div>
    </div>

    <div class="card" style="padding: 18px; border-left: 4px solid var(--success); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Onaylanan & Tamamlanan</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: var(--success); margin-top: 4px;"><?= ($stats['approved'] ?? 0) + ($stats['completed'] ?? 0) ?></div>
        </div>
        <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--success-light); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>

    <div class="card" style="padding: 18px; border-left: 4px solid var(--danger); display: flex; align-items: center; justify-content: space-between;">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Reddedilen</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: var(--danger); margin-top: 4px;"><?= $stats['rejected'] ?? 0 ?></div>
        </div>
        <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--danger-light); color: var(--danger); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
            <i class="fas fa-times-circle"></i>
        </div>
    </div>
</div>

<!-- Filtreleme Alanı -->
<div class="card" style="margin-bottom: 24px; padding: 16px 20px;">
    <form method="GET" action="/requests" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <div style="min-width: 180px; flex: 1;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; color: var(--text-muted);">Durum Filtresi</label>
            <select name="status" class="form-control" style="width: 100%;">
                <option value="">Tüm Durumlar</option>
                <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>⏳ Beklemede</option>
                <option value="in_review" <?= $statusFilter === 'in_review' ? 'selected' : '' ?>>🔍 İnceleniyor</option>
                <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>✅ Onaylandı</option>
                <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>🎉 Tamamlandı</option>
                <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>❌ Reddedildi</option>
            </select>
        </div>
        <div style="min-width: 180px; flex: 1;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; color: var(--text-muted);">Talep Türü</label>
            <select name="type" class="form-control" style="width: 100%;">
                <option value="">Tüm Türler</option>
                <option value="malfunction" <?= $typeFilter === 'malfunction' ? 'selected' : '' ?>>🛠️ Arıza / Onarım Bildirimi</option>
                <option value="exchange" <?= $typeFilter === 'exchange' ? 'selected' : '' ?>>🔄 Cihaz Değişim Talebi</option>
                <option value="equipment" <?= $typeFilter === 'equipment' ? 'selected' : '' ?>>📦 Yeni Ekipman / Sarf Malzeme</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="padding: 10px 18px;"><i class="fas fa-filter"></i> Filtrele</button>
            <a href="/requests" class="btn btn-secondary" style="padding: 10px 18px;">Temizle</a>
        </div>
    </form>
</div>

<!-- Talep Listesi -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 class="card-title" style="font-size: 1.1rem; font-weight: 700; color: var(--text-main);">
            <i class="fas fa-list-alt" style="color: var(--primary); margin-right: 8px;"></i>
            Talep Kayıtları (<?= count($requests) ?>)
        </h2>
    </div>

    <div class="table-responsive" style="overflow-x: auto; margin-top: 12px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left; color: var(--text-muted); background: var(--bg-main);">
                    <th style="padding: 12px; font-weight: 600;">Talep Kodu</th>
                    <?php if ($isStaff): ?>
                        <th style="padding: 12px; font-weight: 600;">Personel</th>
                    <?php endif; ?>
                    <th style="padding: 12px; font-weight: 600;">Tür</th>
                    <th style="padding: 12px; font-weight: 600;">Başlık & Cihaz</th>
                    <th style="padding: 12px; font-weight: 600;">Aciliyet</th>
                    <th style="padding: 12px; font-weight: 600;">Durum</th>
                    <th style="padding: 12px; font-weight: 600;">Tarih</th>
                    <th style="padding: 12px; font-weight: 600; text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="<?= $isStaff ? 8 : 7 ?>" style="padding: 36px; text-align: center; color: var(--text-muted);">
                            <i class="fas fa-inbox" style="font-size: 2.5rem; color: var(--border-color); margin-bottom: 12px; display: block;"></i>
                            Henüz kayıtlı bir talep bulunmuyor.
                            <div style="margin-top: 12px;">
                                <button onclick="openNewRequestModal()" class="btn btn-primary" style="font-size: 0.85rem;">
                                    <i class="fas fa-plus-circle"></i> İlk Talebinizi Oluşturun
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                        <?php
                            // Type Badges
                            $typeMeta = [
                                'malfunction' => ['label' => 'Arıza Bildirimi', 'icon' => 'fa-wrench', 'bg' => 'var(--danger-light)', 'color' => 'var(--danger)'],
                                'exchange' => ['label' => 'Cihaz Değişimi', 'icon' => 'fa-sync-alt', 'bg' => '#fef3c7', 'color' => '#d97706'],
                                'equipment' => ['label' => 'Yeni Ekipman', 'icon' => 'fa-box', 'bg' => 'var(--primary-light)', 'color' => 'var(--primary)']
                            ];
                            $tm = $typeMeta[$r['request_type']] ?? ['label' => $r['request_type'], 'icon' => 'fa-tag', 'bg' => 'var(--bg-main)', 'color' => 'var(--text-muted)'];

                            // Status Badges
                            $statusMeta = [
                                'pending' => ['label' => 'Beklemede', 'bg' => '#fef3c7', 'color' => '#d97706', 'icon' => 'fa-clock'],
                                'in_review' => ['label' => 'İnceleniyor', 'bg' => 'var(--info-light)', 'color' => 'var(--info)', 'icon' => 'fa-search'],
                                'approved' => ['label' => 'Onaylandı', 'bg' => 'var(--success-light)', 'color' => 'var(--success)', 'icon' => 'fa-check'],
                                'rejected' => ['label' => 'Reddedildi', 'bg' => 'var(--danger-light)', 'color' => 'var(--danger)', 'icon' => 'fa-times'],
                                'completed' => ['label' => 'Tamamlandı', 'bg' => '#e0e7ff', 'color' => '#4338ca', 'icon' => 'fa-flag-checkered']
                            ];
                            $sm = $statusMeta[$r['status']] ?? ['label' => $r['status'], 'bg' => 'var(--bg-main)', 'color' => 'var(--text-muted)', 'icon' => 'fa-circle'];

                            // Urgency Badges
                            $urgencyMeta = [
                                'low' => ['label' => 'Düşük', 'color' => '#6b7280'],
                                'normal' => ['label' => 'Normal', 'color' => 'var(--primary)'],
                                'high' => ['label' => 'Yüksek', 'color' => '#d97706'],
                                'urgent' => ['label' => 'Kritik / Acil', 'color' => 'var(--danger)']
                            ];
                            $um = $urgencyMeta[$r['urgency']] ?? ['label' => $r['urgency'], 'color' => '#6b7280'];
                        ?>
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background-color 0.2s;">
                            <td style="padding: 12px; font-weight: 700; color: var(--primary); font-family: monospace;">
                                <?= \App\Core\View::escape($r['request_code']) ?>
                            </td>

                            <?php if ($isStaff): ?>
                                <td style="padding: 12px;">
                                    <div style="font-weight: 600; color: var(--text-main);">
                                        <?= \App\Core\View::escape(($r['first_name'] ? ($r['first_name'] . ' ' . $r['last_name']) : $r['user_fullname'])) ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        <?= \App\Core\View::escape($r['department_name'] ?? 'Birim Belirtilmemiş') ?>
                                    </div>
                                </td>
                            <?php endif; ?>

                            <td style="padding: 12px;">
                                <span class="badge" style="background: <?= $tm['bg'] ?>; color: <?= $tm['color'] ?>; font-size: 0.75rem; padding: 4px 8px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas <?= $tm['icon'] ?>"></i> <?= $tm['label'] ?>
                                </span>
                            </td>

                            <td style="padding: 12px; max-width: 320px;">
                                <div style="font-weight: 600; color: var(--text-main); margin-bottom: 2px;">
                                    <?= \App\Core\View::escape($r['title']) ?>
                                </div>
                                <?php if (!empty($r['asset_name'])): ?>
                                    <div style="font-size: 0.78rem; color: var(--text-muted);">
                                        <i class="fas fa-laptop" style="margin-right: 4px;"></i>
                                        <?= \App\Core\View::escape($r['asset_name']) ?> (<?= \App\Core\View::escape($r['asset_code']) ?>)
                                    </div>
                                <?php endif; ?>
                                <div style="font-size: 0.75rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 280px; margin-top: 2px;">
                                    <?= \App\Core\View::escape($r['description']) ?>
                                </div>
                                <?php if (!empty($r['admin_notes'])): ?>
                                    <div style="margin-top: 6px; font-size: 0.75rem; background: var(--bg-main); padding: 4px 8px; border-radius: 4px; border-left: 3px solid var(--primary); color: var(--text-main);">
                                        <strong>IT Yanıtı:</strong> <?= \App\Core\View::escape($r['admin_notes']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td style="padding: 12px;">
                                <span style="font-size: 0.8rem; font-weight: 600; color: <?= $um['color'] ?>;">
                                    <?= $um['label'] ?>
                                </span>
                            </td>

                            <td style="padding: 12px;">
                                <span class="badge" style="background: <?= $sm['bg'] ?>; color: <?= $sm['color'] ?>; font-size: 0.75rem; padding: 4px 8px; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fas <?= $sm['icon'] ?>"></i> <?= $sm['label'] ?>
                                </span>
                            </td>

                            <td style="padding: 12px; font-size: 0.8rem; color: var(--text-muted);">
                                <?= date('d.m.Y H:i', strtotime($r['created_at'])) ?>
                            </td>

                            <td style="padding: 12px; text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <!-- Detay Butonu -->
                                    <button onclick='viewRequestDetails(<?= json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;" title="Detayları İncele">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- Yönetici İşlem Butonu -->
                                    <?php if ($isStaff): ?>
                                        <button onclick='openManageModal(<?= json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="btn btn-primary" style="padding: 5px 10px; font-size: 0.8rem;" title="Talebi Değerlendir / Yanıtla">
                                            <i class="fas fa-edit"></i> Değerlendir
                                        </button>
                                    <?php endif; ?>

                                    <!-- Personel İptal Butonu (Sadece beklemedeyken) -->
                                    <?php if (!$isStaff && $r['status'] === 'pending'): ?>
                                        <form method="POST" action="/requests/<?= $r['id'] ?>/cancel" style="display: inline;" onsubmit="return confirm('Bu talebi iptal etmek istediğinize emin misiniz?');">
                                            <?= \App\Helpers\CsrfHelper::field() ?>
                                            <button type="submit" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; color: var(--danger);" title="Talebi İptal Et">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL: YENİ TALEP OLUŞTUR ================= -->
<div id="newRequestModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 16px;">
    <div class="card" style="width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; box-shadow: var(--shadow-xl); border: 1px solid var(--border-color); animation: modalFadeIn 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 16px; margin-bottom: 20px;">
            <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus-circle" style="color: var(--primary);"></i> Yeni Talep / Arıza Bildirimi
            </h3>
            <button onclick="closeNewRequestModal()" style="background: none; border: none; font-size: 1.2rem; color: var(--text-muted); cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>

        <form method="POST" action="/requests">
            <?= \App\Helpers\CsrfHelper::field() ?>

            <!-- Talep Türü -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">
                    Talep Türü <span style="color: var(--danger);">*</span>
                </label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    <label style="border: 2px solid var(--border-color); border-radius: var(--radius-md); padding: 12px 8px; text-align: center; cursor: pointer; transition: all 0.2s;" id="labelTypeMalfunction">
                        <input type="radio" name="request_type" value="malfunction" checked onchange="toggleAssetSelection()" style="display: none;">
                        <i class="fas fa-wrench" style="font-size: 1.4rem; color: var(--danger); display: block; margin-bottom: 6px;"></i>
                        <span style="font-size: 0.8rem; font-weight: 700; display: block;">Arıza / Onarım</span>
                    </label>

                    <label style="border: 2px solid var(--border-color); border-radius: var(--radius-md); padding: 12px 8px; text-align: center; cursor: pointer; transition: all 0.2s;" id="labelTypeExchange">
                        <input type="radio" name="request_type" value="exchange" onchange="toggleAssetSelection()" style="display: none;">
                        <i class="fas fa-sync-alt" style="font-size: 1.4rem; color: #d97706; display: block; margin-bottom: 6px;"></i>
                        <span style="font-size: 0.8rem; font-weight: 700; display: block;">Cihaz Değişimi</span>
                    </label>

                    <label style="border: 2px solid var(--border-color); border-radius: var(--radius-md); padding: 12px 8px; text-align: center; cursor: pointer; transition: all 0.2s;" id="labelTypeEquipment">
                        <input type="radio" name="request_type" value="equipment" onchange="toggleAssetSelection()" style="display: none;">
                        <i class="fas fa-box-open" style="font-size: 1.4rem; color: var(--primary); display: block; margin-bottom: 6px;"></i>
                        <span style="font-size: 0.8rem; font-weight: 700; display: block;">Yeni Ekipman</span>
                    </label>
                </div>
            </div>

            <!-- Zimmetli Cihaz Seçimi (Arıza veya Değişim için) -->
            <div id="assetSelectionGroup" style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-main);">
                    İlgili Zimmetli Demirbaşınız
                </label>
                <select name="asset_id" id="requestAssetSelect" class="form-control" style="width: 100%;">
                    <?php if (empty($myAssets)): ?>
                        <option value="">-- Üzerinize kayıtlı aktif zimmet bulunmuyor --</option>
                    <?php else: ?>
                        <option value="">-- Zimmetli bir cihaz seçin --</option>
                        <?php foreach ($myAssets as $ast): ?>
                            <option value="<?= $ast['id'] ?>">
                                <?= \App\Core\View::escape($ast['name']) ?> (<?= \App\Core\View::escape($ast['asset_code']) ?>) - <?= \App\Core\View::escape($ast['brand'] . ' ' . $ast['model']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px; display: block;">
                    Arıza veya değişim bildiriminde bulunduğunuz cihazı seçiniz.
                </small>
            </div>

            <!-- Başlık -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-main);">
                    Talep Konusu / Başlık <span style="color: var(--danger);">*</span>
                </label>
                <input type="text" name="title" id="requestTitleInput" class="form-control" placeholder="Örn: Laptop ekranında çizgiler çıkıyor veya 2. Monitör Talebi" required style="width: 100%;">
            </div>

            <!-- Detaylı Açıklama -->
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-main);">
                    Açıklama / Belirtiler / Gerekçe <span style="color: var(--danger);">*</span>
                </label>
                <textarea name="description" class="form-control" rows="4" placeholder="Lütfen sorunu veya ihtiyacınızı detaylı şekilde açıklayınız..." required style="width: 100%;"></textarea>
            </div>

            <!-- Aciliyet -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-main);">
                    Aciliyet Derecesi
                </label>
                <select name="urgency" class="form-control" style="width: 100%;">
                    <option value="low">Düşük (İşim aksamıyor, uygun bir zamanda incelenebilir)</option>
                    <option value="normal" selected>Normal (Standart talep)</option>
                    <option value="high">Yüksek (İşlerimi kısmen engelliyor)</option>
                    <option value="urgent">Kritik / Acil (Cihaz çalışmıyor, iş yapamıyorum)</option>
                </select>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 16px;">
                <button type="button" onclick="closeNewRequestModal()" class="btn btn-secondary">Vazgeç</button>
                <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-paper-plane"></i> Talebi İlet
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL: TALEP İNCELEME & YANITLAMA (YÖNETİCİ) ================= -->
<?php if ($isStaff): ?>
<div id="manageModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 16px;">
    <div class="card" style="width: 100%; max-width: 550px; box-shadow: var(--shadow-xl); border: 1px solid var(--border-color); animation: modalFadeIn 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 14px; margin-bottom: 16px;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main);">
                <i class="fas fa-tasks" style="color: var(--primary);"></i> Talebi Değerlendir & Durum Güncelle
            </h3>
            <button onclick="closeManageModal()" style="background: none; border: none; font-size: 1.2rem; color: var(--text-muted); cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>

        <form method="POST" id="manageForm" action="">
            <?= \App\Helpers\CsrfHelper::field() ?>

            <div style="background: var(--bg-main); padding: 12px; border-radius: var(--radius-sm); margin-bottom: 16px;">
                <div style="font-size: 0.8rem; color: var(--text-muted);">Talep Eden: <strong id="mEmpName" style="color: var(--text-main);"></strong></div>
                <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-top: 4px;" id="mTitle"></div>
                <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;" id="mDesc"></div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-main);">
                    Yeni Durum <span style="color: var(--danger);">*</span>
                </label>
                <select name="status" id="mStatusSelect" class="form-control" style="width: 100%; font-weight: 600;" required>
                    <option value="pending">⏳ Beklemede</option>
                    <option value="in_review">🔍 İnceleniyor</option>
                    <option value="approved">✅ Onaylandı (İşlem Başlatıldı)</option>
                    <option value="completed">🎉 Tamamlandı / Teslim Edildi</option>
                    <option value="rejected">❌ Reddedildi</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-main);">
                    Yönetici / IT Notu (Personele İletilecek Mesaj)
                </label>
                <textarea name="admin_notes" id="mAdminNotes" class="form-control" rows="3" placeholder="Örn: Cihazınız garanti servisine gönderildi veya Depodan yeni klavyenizi teslim alabilirsiniz." style="width: 100%;"></textarea>
                <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 4px; display: block;">
                    Bu not güncellendiğinde personele anında sistem içi bildirim gönderilecektir.
                </small>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 14px;">
                <button type="button" onclick="closeManageModal()" class="btn btn-secondary">Vazgeç</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Güncelle ve Bildir
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- ================= MODAL: DETAY GÖRÜNTÜLEME ================= -->
<div id="detailsModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 16px;">
    <div class="card" style="width: 100%; max-width: 520px; box-shadow: var(--shadow-xl); border: 1px solid var(--border-color); animation: modalFadeIn 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 14px; margin-bottom: 16px;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-info-circle" style="color: var(--primary);"></i> Talep Detayları
            </h3>
            <button onclick="closeDetailsModal()" style="background: none; border: none; font-size: 1.2rem; color: var(--text-muted); cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px; font-size: 0.9rem;">
            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                <span style="color: var(--text-muted);">Talep Kodu:</span>
                <strong id="dCode" style="color: var(--primary); font-family: monospace;"></strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                <span style="color: var(--text-muted);">Talep Türü:</span>
                <span id="dType" style="font-weight: 600;"></span>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                <span style="color: var(--text-muted);">Aciliyet:</span>
                <span id="dUrgency" style="font-weight: 600;"></span>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                <span style="color: var(--text-muted);">Durum:</span>
                <span id="dStatus" style="font-weight: 600;"></span>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px;">
                <span style="color: var(--text-muted);">İlgili Cihaz:</span>
                <span id="dAsset" style="font-weight: 600;"></span>
            </div>
            <div>
                <span style="color: var(--text-muted); display: block; margin-bottom: 4px; font-size: 0.8rem; font-weight: 600;">Açıklama:</span>
                <div id="dDesc" style="background: var(--bg-main); padding: 10px; border-radius: var(--radius-sm); color: var(--text-main); font-size: 0.85rem; line-height: 1.5;"></div>
            </div>
            <div id="dAdminGroup">
                <span style="color: var(--primary); display: block; margin-bottom: 4px; font-size: 0.8rem; font-weight: 700;">Bilgi İşlem / Yönetici Yanıtı:</span>
                <div id="dAdminNotes" style="background: var(--primary-light); padding: 10px; border-radius: var(--radius-sm); color: var(--text-main); font-size: 0.85rem; line-height: 1.5; border-left: 3px solid var(--primary);"></div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 20px; border-top: 1px solid var(--border-color); padding-top: 12px;">
            <button onclick="closeDetailsModal()" class="btn btn-secondary">Kapat</button>
        </div>
    </div>
</div>

<style>
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    input[type="radio"]:checked + i + span,
    input[type="radio"]:checked ~ span {
        color: var(--primary);
    }
</style>

<script>
    function openNewRequestModal(preselectedAssetId = null) {
        const modal = document.getElementById('newRequestModal');
        modal.style.display = 'flex';
        updateTypeStyles();
        if (preselectedAssetId) {
            const select = document.getElementById('requestAssetSelect');
            if (select) select.value = preselectedAssetId;
        }
    }

    function closeNewRequestModal() {
        document.getElementById('newRequestModal').style.display = 'none';
    }

    function toggleAssetSelection() {
        updateTypeStyles();
    }

    function updateTypeStyles() {
        const radios = document.getElementsByName('request_type');
        let selectedValue = 'malfunction';
        for (const r of radios) {
            const label = r.closest('label');
            if (r.checked) {
                selectedValue = r.value;
                label.style.borderColor = 'var(--primary)';
                label.style.backgroundColor = 'var(--primary-light)';
            } else {
                label.style.borderColor = 'var(--border-color)';
                label.style.backgroundColor = 'transparent';
            }
        }

        const assetGroup = document.getElementById('assetSelectionGroup');
        const titleInput = document.getElementById('requestTitleInput');
        if (selectedValue === 'equipment') {
            assetGroup.style.display = 'none';
            if (!titleInput.value) titleInput.placeholder = 'Örn: 2. Monitör, Kablosuz Mouse, HDMI Kablosu...';
        } else {
            assetGroup.style.display = 'block';
            if (!titleInput.value) titleInput.placeholder = selectedValue === 'malfunction' ? 'Örn: Ekran kırıldı / Cihaz açılmıyor' : 'Örn: Cihaz eski ve yavaş, değişim talep ediyorum';
        }
    }

    function viewRequestDetails(req) {
        document.getElementById('dCode').innerText = req.request_code;
        
        const typeLabels = { malfunction: '🛠️ Arıza / Onarım', exchange: '🔄 Cihaz Değişimi', equipment: '📦 Yeni Ekipman' };
        document.getElementById('dType').innerText = typeLabels[req.request_type] || req.request_type;

        const urgencyLabels = { low: 'Düşük', normal: 'Normal', high: 'Yüksek', urgent: 'Kritik / Acil' };
        document.getElementById('dUrgency').innerText = urgencyLabels[req.urgency] || req.urgency;

        const statusLabels = { pending: '⏳ Beklemede', in_review: '🔍 İnceleniyor', approved: '✅ Onaylandı', rejected: '❌ Reddedildi', completed: '🎉 Tamamlandı' };
        document.getElementById('dStatus').innerText = statusLabels[req.status] || req.status;

        document.getElementById('dAsset').innerText = req.asset_name ? `${req.asset_name} (${req.asset_code})` : 'Cihaz Seçilmedi / Sarf Malzeme';
        document.getElementById('dDesc').innerText = req.description;

        const adminGroup = document.getElementById('dAdminGroup');
        if (req.admin_notes && req.admin_notes.trim() !== '') {
            adminGroup.style.display = 'block';
            document.getElementById('dAdminNotes').innerText = req.admin_notes;
        } else {
            adminGroup.style.display = 'none';
        }

        document.getElementById('detailsModal').style.display = 'flex';
    }

    function closeDetailsModal() {
        document.getElementById('detailsModal').style.display = 'none';
    }

    <?php if ($isStaff): ?>
    function openManageModal(req) {
        document.getElementById('manageForm').action = '/requests/' + req.id + '/status';
        document.getElementById('mEmpName').innerText = (req.first_name ? (req.first_name + ' ' + req.last_name) : req.user_fullname) + (req.department_name ? ' (' + req.department_name + ')' : '');
        document.getElementById('mTitle').innerText = req.title + ' (' + req.request_code + ')';
        document.getElementById('mDesc').innerText = req.description;
        document.getElementById('mStatusSelect').value = req.status;
        document.getElementById('mAdminNotes').value = req.admin_notes || '';
        document.getElementById('manageModal').style.display = 'flex';
    }

    function closeManageModal() {
        document.getElementById('manageModal').style.display = 'none';
    }
    <?php endif; ?>

    // Otomatik modal açma (URL query params ile geldiyse ?asset_id=...)
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const assetId = urlParams.get('asset_id');
        if (assetId) {
            openNewRequestModal(assetId);
        }
    });
</script>
