<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Demirbaş Teslim Tutanağı - <?= \App\Core\View::escape($assignment['assignment_code']) ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #000;
            margin: 0;
            padding: 40px;
            background: #fff;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #555;
            font-size: 14px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .info-table th, .info-table td {
            border: 1px solid #000;
            padding: 10px;
            font-size: 14px;
            text-align: left;
        }
        .info-table th {
            background-color: #f0f0f0;
            width: 30%;
        }
        .contract-text {
            font-size: 14px;
            text-align: justify;
            margin-bottom: 40px;
            border: 1px dashed #000;
            padding: 20px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .sign-box {
            width: 40%;
            text-align: center;
        }
        .sign-box p {
            margin: 0 0 5px 0;
            font-weight: bold;
        }
        .sign-box .line {
            border-bottom: 1px solid #000;
            height: 60px;
            margin-bottom: 10px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align:right;">
        <button onclick="window.print()" style="padding:10px 20px; background:#2563eb; color:white; border:none; cursor:pointer; font-size:16px;">Yazdır</button>
    </div>

    <div class="header">
        <h1>Demirbaş Teslim Tutanağı</h1>
        <p>Zimmet Formu No: <?= \App\Core\View::escape($assignment['assignment_code']) ?></p>
    </div>

    <table class="info-table">
        <tr>
            <th>Demirbaş Adı</th>
            <td><?= \App\Core\View::escape($assignment['asset_name']) ?></td>
        </tr>
        <tr>
            <th>Demirbaş Kodu / Seri No</th>
            <td><?= \App\Core\View::escape($assignment['asset_code']) ?></td>
        </tr>
        <tr>
            <th>Teslim Tarihi</th>
            <td><?= date('d.m.Y', strtotime($assignment['assignment_date'])) ?></td>
        </tr>
        <tr>
            <th>Teslim Edilen Personel</th>
            <td><?= \App\Core\View::escape($assignment['first_name'] . ' ' . $assignment['last_name']) ?> (Sicil No: <?= \App\Core\View::escape($assignment['registration_no']) ?>)</td>
        </tr>
        <tr>
            <th>İşlemi Yapan / Teslim Eden</th>
            <td><?= \App\Core\View::escape($assignment['assigned_by_name'] ?? 'Sistem') ?></td>
        </tr>
    </table>

    <div class="contract-text">
        <p><strong>TAAHHÜTNAME</strong></p>
        <p>
            Yukarıda özellikleri belirtilen demirbaşı çalışır, tam ve eksiksiz durumda teslim aldım. 
            Kullanım sürem boyunca cihazın/malzemenin başına gelebilecek her türlü kırılma, kaybolma, sıvı teması, hırsızlık ve kullanıcı hatasından kaynaklanan tüm hasar ve zararlardan şahsımın sorumlu olacağını beyan ve taahhüt ederim. İşten ayrılmam veya ürünün iadesi istendiği takdirde aynı gün sağlam bir şekilde kuruma iade edeceğimi kabul ederim.
        </p>
    </div>

    <div class="signatures">
        <div class="sign-box">
            <p>Teslim Eden</p>
            <p style="font-weight: normal; font-size: 14px;"><?= \App\Core\View::escape($assignment['assigned_by_name'] ?? 'Sistem') ?></p>
            <div class="line"></div>
            <p>İmza</p>
        </div>
        <div class="sign-box">
            <p>Teslim Alan</p>
            <p style="font-weight: normal; font-size: 14px;"><?= \App\Core\View::escape($assignment['first_name'] . ' ' . $assignment['last_name']) ?></p>
            <div class="line"></div>
            <p>İmza</p>
        </div>
    </div>

</body>
</html>
