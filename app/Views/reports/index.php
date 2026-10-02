<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Raporlama Modülü</h1>
    <p class="page-subtitle" style="color: var(--text-muted);">Sistemdeki demirbaş, zimmet ve stok verilerinin genel analizleri</p>
</div>

<div class="grid grid-4" style="margin-bottom: 24px;">
    <div class="stat-card" style="background: white; padding: 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fas fa-box"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Toplam Demirbaş</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);"><?= $summary['assets'] ?></div>
        </div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--info-light); color: var(--info); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Aktif Personel</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);"><?= $summary['employees'] ?></div>
        </div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--success-light); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Aktif Zimmet</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);"><?= $summary['assignments'] ?></div>
        </div>
    </div>
    <div class="stat-card" style="background: white; padding: 20px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 16px;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--warning-light); color: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fas fa-layer-group"></i>
        </div>
        <div>
            <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Stok Çeşidi</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);"><?= $summary['stocks'] ?></div>
        </div>
    </div>
</div>

<div class="grid grid-2" style="margin-bottom: 24px;">
    <!-- Departman Bazlı Dağılım -->
    <div class="card">
        <h2 class="card-title" style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 16px;">
            <i class="fas fa-building" style="color: var(--primary); margin-right: 8px;"></i> Departman Bazlı Rapor
        </h2>
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--text-muted);">
                        <th style="padding: 10px; font-weight: 600;">Departman</th>
                        <th style="padding: 10px; font-weight: 600;">Personel</th>
                        <th style="padding: 10px; font-weight: 600;">Üzerindeki Zimmet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($deptStats as $ds): ?>
                        <tr style="border-bottom: 1px solid var(--bg-main);">
                            <td style="padding: 10px; font-weight: 500;"><?= \App\Core\View::escape($ds['name']) ?></td>
                            <td style="padding: 10px;"><?= $ds['emp_count'] ?> Kişi</td>
                            <td style="padding: 10px; color: var(--primary); font-weight: 600;"><?= $ds['assigned_assets'] ?> Adet</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 16px; text-align: right;">
            <a href="/reports/print/departments" target="_blank" class="btn btn-secondary" style="font-size: 0.8rem; padding: 6px 12px;"><i class="fas fa-print"></i> Yazdır</a>
        </div>
    </div>

    <!-- Kategori Bazlı Envanter -->
    <div class="card">
        <h2 class="card-title" style="font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 16px;">
            <i class="fas fa-tags" style="color: var(--primary); margin-right: 8px;"></i> Kategori Bazlı Demirbaş Sayısı
        </h2>
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color); text-align: left; color: var(--text-muted);">
                        <th style="padding: 10px; font-weight: 600;">Kategori</th>
                        <th style="padding: 10px; font-weight: 600; text-align: right;">Kayıtlı Demirbaş</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($catStats as $cs): ?>
                        <tr style="border-bottom: 1px solid var(--bg-main);">
                            <td style="padding: 10px; font-weight: 500;"><?= \App\Core\View::escape($cs['name']) ?></td>
                            <td style="padding: 10px; text-align: right; font-weight: 600;"><?= $cs['asset_count'] ?> Adet</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card" style="text-align: center; padding: 32px;">
    <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--success-light); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 16px;">
        <i class="fas fa-file-csv"></i>
    </div>
    <h3 style="margin-bottom: 8px;">Dışa Aktar (CSV)</h3>
    <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 24px;">Sistemdeki tüm kayıtları Excel ve diğer tablo programlarıyla uyumlu CSV formatında indirebilirsiniz.</p>
    <div style="display: flex; gap: 12px; justify-content: center;">
        <a href="/reports/export/assets" class="btn btn-primary"><i class="fas fa-box"></i> Demirbaş Listesi İndir</a>
        <a href="/reports/export/assignments" class="btn btn-secondary"><i class="fas fa-clipboard-list"></i> Zimmet Listesi İndir</a>
    </div>
</div>
