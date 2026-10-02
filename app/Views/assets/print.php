<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Demirbaş Listesi</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #666;
        }
        .signature-block {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
            text-align: center;
        }
        .signature-box {
            width: 200px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            @page {
                margin: 1.5cm;
                size: landscape; /* Listeler geniş olduğu için yatay baskı daha uygundur */
            }
        }
    </style>
</head>
<body>
    <div style="margin-bottom: 20px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; background: #6366f1; color: white; border: none; border-radius: 5px; cursor: pointer;">Hemen Yazdır</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; background: #64748b; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Kapat</button>
        <span style="margin-left: 15px; color: #d97706; font-size: 13px;">* En iyi görünüm için yazdırırken "Yatay (Landscape)" düzeni seçmeniz önerilir.</span>
    </div>

    <div class="header">
        <h1>Tüm Demirbaşlar Listesi</h1>
        <p>Rapor Tarihi: <?= date('d.m.Y H:i') ?></p>
        <p>Kurumsal Demirbaş & Envanter Yönetim Sistemi</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">#</th>
                <th>Kategori</th>
                <th>Demirbaş Kodu</th>
                <th>Demirbaş Adı</th>
                <th>Marka / Model</th>
                <th>Seri No</th>
                <th>Konum</th>
                <th>Durum</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $i = 1;
                $statusMap = [
                    'in_stock' => 'Stokta',
                    'assigned' => 'Zimmetli',
                    'defective' => 'Arızalı',
                    'in_service' => 'Serviste',
                    'scrapped' => 'Hurda'
                ];
            ?>
            <?php foreach($assets as $asset): ?>
                <tr>
                    <td style="text-align: center;"><?= $i++ ?></td>
                    <td><?= htmlspecialchars($asset['category_name'] ?? 'Belirtilmemiş') ?></td>
                    <td style="font-weight: bold;"><?= htmlspecialchars($asset['asset_code']) ?></td>
                    <td><?= htmlspecialchars($asset['name']) ?></td>
                    <td><?= htmlspecialchars($asset['brand'] . ' ' . $asset['model']) ?></td>
                    <td><?= htmlspecialchars($asset['serial_number'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($asset['location_name'] ?? '-') ?></td>
                    <td><?= $statusMap[$asset['status']] ?? $asset['status'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f5f5f5; font-weight: bold;">
                <td colspan="7" style="text-align: right;">TOPLAM KAYITLI DEMİRBAŞ:</td>
                <td><?= count($assets) ?> Adet</td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-block" style="page-break-inside: avoid;">
        <div class="signature-box">
            <p style="margin: 0; padding: 0; font-weight: bold;">Envanter / Rapor Sorumlusu</p>
            <p style="margin: 5px 0 0 0;">Ad Soyad: .......................................</p>
            <br><br><br>
            <p style="margin: 0; padding: 0;">İmza</p>
        </div>
    </div>

    <div class="footer">
        <div>Demirbaş & Envanter Yönetim Sistemi</div>
        <div>Bu belge sistem tarafından otomatik oluşturulmuştur. (Tarih: <?= date('d.m.Y') ?>)</div>
    </div>

    <script>
        // Sayfa açıldığında yazdırma ekranı gelsin isteniyorsa:
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
