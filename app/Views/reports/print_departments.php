<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Departman Bazlı Rapor</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
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
                margin: 2cm;
            }
        }
    </style>
</head>
<body>
    <div style="margin-bottom: 20px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; background: #6366f1; color: white; border: none; border-radius: 5px; cursor: pointer;">Hemen Yazdır</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; background: #64748b; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Kapat</button>
    </div>

    <div class="header">
        <h1>Genel Envanter Raporu</h1>
        <p>Rapor Tarihi: <?= date('d.m.Y H:i') ?></p>
        <p>Kurumsal Demirbaş & Envanter Yönetim Sistemi</p>
    </div>

    <div style="page-break-inside: avoid;">
        <h2 style="font-size: 18px; margin-bottom: 15px; text-transform: uppercase;">Departman Bazlı Envanter Dağılımı</h2>
        <table>
        <thead>
            <tr>
                <th>Sıra</th>
                <th>Departman Adı</th>
                <th>Personel Sayısı</th>
                <th>Zimmetli Demirbaş Sayısı</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1; $totalEmp = 0; $totalAssets = 0; ?>
            <?php foreach($deptStats as $ds): ?>
                <?php 
                    $totalEmp += $ds['emp_count']; 
                    $totalAssets += $ds['assigned_assets']; 
                ?>
                <tr>
                    <td style="width: 50px; text-align: center;"><?= $i++ ?></td>
                    <td style="font-weight: bold;"><?= htmlspecialchars($ds['name']) ?></td>
                    <td><?= $ds['emp_count'] ?> Kişi</td>
                    <td><?= $ds['assigned_assets'] ?> Adet</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f5f5f5; font-weight: bold;">
                <td colspan="2" style="text-align: right;">GENEL TOPLAM:</td>
                <td><?= $totalEmp ?> Kişi</td>
                <td><?= $totalAssets ?> Adet</td>
            </tr>
        </tfoot>
    </table>
    </div>

    <div style="page-break-inside: avoid;">
        <h2 style="font-size: 18px; margin-bottom: 15px; margin-top: 40px; text-transform: uppercase;">Kategori Bazlı Demirbaş Sayısı</h2>
        <table>
            <thead>
                <tr>
                    <th>Sıra</th>
                    <th>Kategori Adı</th>
                    <th>Kayıtlı Demirbaş Sayısı</th>
                </tr>
            </thead>
            <tbody>
                <?php $j = 1; $totalCatAssets = 0; ?>
                <?php foreach($catStats as $cs): ?>
                    <?php $totalCatAssets += $cs['asset_count']; ?>
                    <tr>
                        <td style="width: 50px; text-align: center;"><?= $j++ ?></td>
                        <td style="font-weight: bold;"><?= htmlspecialchars($cs['name']) ?></td>
                        <td><?= $cs['asset_count'] ?> Adet</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #f5f5f5; font-weight: bold;">
                    <td colspan="2" style="text-align: right;">GENEL TOPLAM:</td>
                    <td><?= $totalCatAssets ?> Adet</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="signature-block" style="page-break-inside: avoid;">
        <div class="signature-box">
            <p style="margin: 0; padding: 0; font-weight: bold;">Raporu Alan / Onaylayan</p>
            <p style="margin: 5px 0 0 0;">Ad Soyad: .......................................</p>
            <br><br><br>
            <p style="margin: 0; padding: 0;">İmza</p>
        </div>
    </div>

    <div class="footer">
        <div>Sayfa 1 / 1</div>
        <div>Bu belge sistem tarafından otomatik oluşturulmuştur. (Tarih: <?= date('d.m.Y') ?>)</div>
    </div>

    <script>
        // Sayfa açıldığında otomatik yazdırma penceresini getirir (isteğe bağlı)
        window.onload = function() {
            // window.print(); 
        }
    </script>
</body>
</html>
