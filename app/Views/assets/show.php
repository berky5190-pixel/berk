<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Demirbaş Detayı</h1>
        <p class="page-subtitle" style="color: var(--text-muted);">Demirbaş Kodu: <?= \App\Core\View::escape($asset['asset_code']) ?></p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="/assets" class="btn btn-secondary">Listeye Dön</a>
        <a href="/assets/<?= $asset['id'] ?>/edit" class="btn btn-primary">
            <i class="fas fa-edit"></i> Düzenle
        </a>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom: 24px;">
    <!-- Sol Panel: Bilgiler -->
    <div class="card" style="grid-column: span 1;">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 120px; height: 120px; border-radius: 12px; background: var(--primary-light); color: var(--primary); font-size: 3rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                <i class="fas fa-box-open"></i>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 700;"><?= \App\Core\View::escape($asset['name']) ?></h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 8px;"><?= \App\Core\View::escape($asset['category_name'] ?? 'Kategori Yok') ?></p>
            
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
        </div>
        
        <div style="border-top: 1px solid var(--border-color); padding-top: 16px;">
            <h3 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 12px; color: var(--text-muted); text-transform: uppercase;">Kimlik Bilgileri</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Marka:</span>
                    <span style="font-weight: 500;"><?= \App\Core\View::escape($asset['brand'] ?: '-') ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Model:</span>
                    <span style="font-weight: 500;"><?= \App\Core\View::escape($asset['model'] ?: '-') ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Seri No:</span>
                    <span style="font-weight: 500;"><?= \App\Core\View::escape($asset['serial_number'] ?: '-') ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Barkod:</span>
                    <span style="font-weight: 500;"><?= \App\Core\View::escape($asset['barcode'] ?: '-') ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Lokasyon:</span>
                    <span style="font-weight: 500;"><?= \App\Core\View::escape($asset['location_name'] ?: '-') ?></span>
                </div>
            </div>
        </div>

        <div style="border-top: 1px solid var(--border-color); padding-top: 16px; margin-top: 16px;">
            <h3 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 12px; color: var(--text-muted); text-transform: uppercase;">Satın Alma & Garanti</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Fiyat:</span>
                    <span style="font-weight: 500;"><?= number_format($asset['purchase_price'], 2) ?> <?= $asset['currency'] ?? 'TRY' ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Alım Tarihi:</span>
                    <span style="font-weight: 500;"><?= $asset['purchase_date'] ? date('d.m.Y', strtotime($asset['purchase_date'])) : '-' ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: var(--text-muted);">Garanti Bitiş:</span>
                    <span style="font-weight: 500;"><?= $asset['warranty_end'] ? date('d.m.Y', strtotime($asset['warranty_end'])) : '-' ?></span>
                </div>
            </div>
        </div>

        <?php if (!empty($asset['description'])): ?>
        <div style="border-top: 1px solid var(--border-color); padding-top: 16px; margin-top: 16px;">
            <h3 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 8px; color: var(--text-muted); text-transform: uppercase;">Açıklama</h3>
            <p style="font-size: 0.85rem; color: var(--text-main);"><?= nl2br(\App\Core\View::escape($asset['description'])) ?></p>
        </div>
        <?php endif; ?>

        <!-- QR KOD BÖLÜMÜ -->
        <div style="border-top: 1px solid var(--border-color); padding-top: 24px; margin-top: 24px; text-align: center;">
            <h3 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 16px; color: var(--text-muted); text-transform: uppercase;"><i class="fas fa-qrcode"></i> Demirbaş QR Kodu</h3>
            <?php
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $domainName = $_SERVER['HTTP_HOST'];
            $assetUrl = $protocol . $domainName . '/assets/' . $asset['id'];
            $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=" . urlencode($assetUrl);
            ?>
            <div id="qr-code-print-area" style="background: white; padding: 16px; border-radius: var(--radius-sm); display: inline-block; border: 1px dashed var(--border-color); margin-bottom: 16px;">
                <img src="<?= $qrApiUrl ?>" alt="QR Code" style="width: 150px; height: 150px; display: block;">
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-main); margin-top: 8px; text-align: center; font-family: monospace;">
                    <?= \App\Core\View::escape($asset['asset_code']) ?>
                </div>
            </div>
            <div>
                <button onclick="printQRCode()" class="btn btn-secondary" style="font-size: 0.8rem; padding: 6px 12px;">
                    <i class="fas fa-print"></i> QR Kod Yazdır
                </button>
            </div>
        </div>

        <script>
        function printQRCode() {
            var printContents = document.getElementById('qr-code-print-area').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = '<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; text-align: center;">' + printContents + '</div>';
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload(); // Reattach events
        }
        </script>
    </div>

    <!-- Sağ Panel: Zimmet Geçmişi -->
    <div style="grid-column: span 2; display: flex; flex-direction: column; gap: 24px;">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Zimmet Geçmişi (<?= count($assignments ?? []) ?>)</h2>
                <?php if($asset['status'] === 'in_stock'): ?>
                <a href="/assignments/create?asset_id=<?= $asset['id'] ?>" class="btn btn-primary" style="padding: 4px 10px; font-size: 0.75rem;">
                    <i class="fas fa-handshake"></i> Zimmetle
                </a>
                <?php endif; ?>
            </div>
            
            <div class="table-responsive" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--text-muted);">
                            <th style="padding: 12px; font-weight: 600;">Personel</th>
                            <th style="padding: 12px; font-weight: 600;">Zimmet Tarihi</th>
                            <th style="padding: 12px; font-weight: 600;">İade Tarihi</th>
                            <th style="padding: 12px; font-weight: 600;">Durum</th>
                            <th style="padding: 12px; font-weight: 600; text-align: right;">Detay</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($assignments)): ?>
                            <tr><td colspan="5" style="padding: 24px; text-align: center; color: var(--text-muted);">Bu demirbaş henüz kimseye zimmetlenmemiş.</td></tr>
                        <?php else: ?>
                            <?php foreach ($assignments as $asg): ?>
                                <tr style="border-bottom: 1px solid var(--bg-main);">
                                    <td style="padding: 12px;">
                                        <div style="font-weight: 500;"><a href="/employees/<?= $asg['employee_id'] ?>"><?= \App\Core\View::escape($asg['first_name'] . ' ' . $asg['last_name']) ?></a></div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">Sicil: <?= \App\Core\View::escape($asg['registration_no']) ?></div>
                                    </td>
                                    <td style="padding: 12px;"><?= date('d.m.Y', strtotime($asg['assignment_date'])) ?></td>
                                    <td style="padding: 12px;"><?= $asg['actual_return_date'] ? date('d.m.Y', strtotime($asg['actual_return_date'])) : '-' ?></td>
                                    <td style="padding: 12px;">
                                        <?php if ($asg['status'] === 'active'): ?>
                                            <span class="badge badge-primary">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?= \App\Core\View::escape($asg['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px; text-align: right;">
                                        <a href="/assignments/<?= $asg['id'] ?>" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem;">İncele</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bakım / Teknik Servis Bölümü -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-tools" style="color: var(--warning);"></i> Teknik Servis / Bakım Geçmişi</h2>
            </div>
            
            <?php if($asset['status'] === 'in_service'): ?>
                <?php 
                    $activeMaint = array_filter($maintenances, fn($m) => $m['status'] === 'in_progress');
                    $activeMaint = reset($activeMaint);
                ?>
                <div style="background: var(--warning-light); padding: 16px; border-radius: var(--radius-sm); border: 1px solid #fcd34d; margin-bottom: 20px;">
                    <h4 style="color: #92400e; margin-bottom: 8px;">Cihaz Şu An Teknik Serviste</h4>
                    <p style="font-size: 0.9rem; color: #92400e; margin-bottom: 12px;"><strong>Servis Firması:</strong> <?= \App\Core\View::escape($activeMaint['company_name'] ?? '-') ?></p>
                    <form action="/assets/<?= $asset['id'] ?>/maintenance/complete" method="POST" style="display: flex; gap: 12px; align-items: flex-end;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600;">Servis Masrafı (TRY)</label>
                            <input type="number" step="0.01" name="cost" class="form-control" style="padding: 6px 10px;" required>
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600;">Yeni Durumu</label>
                            <select name="new_status" class="form-control" style="padding: 6px 10px;">
                                <option value="in_stock">Tamir Edildi (Stokta)</option>
                                <option value="defective">Yapılamadı (Arızalı)</option>
                                <option value="scrapped">Hurda / Çöp</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding: 6px 16px;">Servisten Döndü</button>
                    </form>
                </div>
            <?php else: ?>
                <form action="/assets/<?= $asset['id'] ?>/maintenance" method="POST" style="margin-bottom: 24px; padding: 16px; border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                    <h4 style="font-size: 0.95rem; margin-bottom: 12px;">Servise / Bakıma Gönder</h4>
                    <div class="grid grid-2" style="gap: 12px;">
                        <div>
                            <label class="form-label">Servis / Firma Adı</label>
                            <input type="text" name="company_name" class="form-control" required>
                        </div>
                        <div>
                            <label class="form-label">Arıza Detayı / Not</label>
                            <input type="text" name="notes" class="form-control">
                        </div>
                    </div>
                    <div style="margin-top: 12px; text-align: right;">
                        <button type="submit" class="btn btn-secondary" style="background: var(--warning-light); color: #92400e; border-color: #fcd34d;">Servise Gönder</button>
                    </div>
                </form>
            <?php endif; ?>

            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--text-muted);">
                            <th style="padding: 10px;">Tarih</th>
                            <th style="padding: 10px;">Firma</th>
                            <th style="padding: 10px;">Tutar</th>
                            <th style="padding: 10px;">Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($maintenances)): ?>
                            <tr><td colspan="4" style="padding: 16px; text-align: center; color: var(--text-muted);">Bakım geçmişi bulunmuyor.</td></tr>
                        <?php else: ?>
                            <?php foreach ($maintenances as $m): ?>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <td style="padding: 10px;"><?= date('d.m.Y', strtotime($m['start_date'])) ?> <?= $m['end_date'] ? ' - ' . date('d.m.Y', strtotime($m['end_date'])) : '' ?></td>
                                    <td style="padding: 10px;"><?= \App\Core\View::escape($m['company_name']) ?></td>
                                    <td style="padding: 10px;"><?= $m['cost'] > 0 ? number_format($m['cost'], 2) . ' ₺' : '-' ?></td>
                                    <td style="padding: 10px;">
                                        <?php if ($m['status'] === 'completed'): ?>
                                            <span class="badge badge-success">Tamamlandı</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Serviste</span>
                                        <?php endif; ?>
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
