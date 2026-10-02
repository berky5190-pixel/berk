<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Hızlı Zimmet (QR)</h1>
    <p class="page-subtitle" style="color: var(--text-muted);">Cihazın kodunu okutup, listeden personeli seçerek saniyeler içinde zimmet işlemini tamamlayın.</p>
</div>

<div class="grid grid-2" style="align-items: start; gap: 24px;">
    
    <!-- Camera / Steps -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-header" style="text-align: center; margin-bottom: 24px;">
            <h2 id="stepTitle" class="card-title" style="font-size: 1.2rem; color: var(--primary);">1. Adım: Demirbaş Okutun</h2>
            <p id="stepSubtitle" style="font-size: 0.85rem; color: var(--text-muted);">Zimmetlenecek cihazın QR veya Barkodunu okutun.</p>
        </div>
        
        <!-- Step 1: Scanner -->
        <div id="step1-container">
            <div id="qr-reader" style="width: 100%; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-color); background: var(--bg-main);"></div>
            
            <div style="margin-top: 16px;">
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px;">Kamera kullanılamıyorsa Kodu/ID'yi elle girin:</p>
                <div style="display: flex; gap: 8px;">
                    <input type="text" id="manualCode" class="form-control" placeholder="Demirbaş kodu veya ID" onkeydown="if(event.key === 'Enter') checkManualCode()">
                    <button class="btn btn-primary" onclick="checkManualCode()"><i class="fas fa-check"></i></button>
                </div>
            </div>
        </div>

        <!-- Step 2: Employee Select -->
        <div id="step2-container" style="display: none;">
            <div class="form-group">
                <label class="form-label" style="font-weight: 600;">Zimmet Yapılacak Personeli Seçin</label>
                <select id="employeeSelect" class="form-control" style="font-size: 1rem; padding: 12px;">
                    <option value="">-- Personel Seçin --</option>
                    <?php foreach($employees as $emp): ?>
                        <option value="<?= $emp['id'] ?>"><?= \App\Core\View::escape($emp['first_name'] . ' ' . $emp['last_name']) ?> (Sicil: <?= \App\Core\View::escape($emp['registration_no']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="margin-top: 24px; text-align: center;">
                <button class="btn btn-success" onclick="assignToEmployee()" style="width: 100%; font-size: 1.1rem; padding: 14px; background: var(--success); color: white; border: none;">
                    <i class="fas fa-handshake"></i> Personeli Seç ve Zimmetle
                </button>
            </div>
        </div>

        <div style="margin-top: 24px; display: flex; gap: 12px; justify-content: center;">
            <button class="btn btn-secondary" onclick="resetProcess()" style="font-size: 0.8rem; padding: 6px 12px;">İşlemi Sıfırla / Başa Dön</button>
        </div>
    </div>

    <!-- Status / Info -->
    <div class="card" style="grid-column: span 1; display: flex; flex-direction: column; gap: 16px;">
        
        <div style="padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-main); display: flex; gap: 16px; align-items: center;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-box"></i>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Seçilen Demirbaş</div>
                <div id="selectedAssetDisplay" style="font-size: 1.1rem; font-weight: 700; color: var(--text-main);">Bekleniyor...</div>
            </div>
        </div>

        <div style="padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-main); display: flex; gap: 16px; align-items: center;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--warning-light); color: var(--warning); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">Teslim Alacak Personel</div>
                <div id="selectedEmployeeDisplay" style="font-size: 1.1rem; font-weight: 700; color: var(--text-main);">Bekleniyor...</div>
            </div>
        </div>

        <div id="successBox" style="display: none; padding: 24px; text-align: center; border-radius: var(--radius-sm); background: var(--success-light); border: 1px solid #86efac; color: var(--success); margin-top: auto;">
            <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 12px;"></i>
            <h3 style="font-size: 1.2rem; font-weight: 800;">Zimmet Başarılı!</h3>
            <p style="font-size: 0.9rem; margin-top: 4px; color: #166534;">İşlem saniyeler içinde tamamlandı.</p>
            <a href="#" id="printDocBtn" target="_blank" class="btn btn-secondary" style="margin-top: 16px; background: white; color: var(--success); border-color: #86efac;">
                <i class="fas fa-print"></i> Zimmet Belgesini Yazdır
            </a>
        </div>

    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let step = 1;
    let scannedAssetCode = null;
    let html5QrcodeScanner = null;

    document.addEventListener('DOMContentLoaded', function() {
        startScanner();
    });

    function startScanner() {
        if(html5QrcodeScanner) {
            try { html5QrcodeScanner.clear(); } catch(e){}
        }
        html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }

    function onScanSuccess(decodedText, decodedResult) {
        let identifier = decodedText;
        if (decodedText.includes('/assets/')) {
            const parts = decodedText.split('/assets/');
            identifier = parts[parts.length - 1]; // get ID
        }
        processAssetScan(identifier);
    }

    function onScanFailure(error) {}

    function checkManualCode() {
        const code = document.getElementById('manualCode').value.trim();
        if(code !== '') {
            processAssetScan(code);
            document.getElementById('manualCode').value = '';
        }
    }

    function processAssetScan(code) {
        if (step !== 1) return;
        
        scannedAssetCode = code;
        document.getElementById('selectedAssetDisplay').innerHTML = `<span style="color:var(--primary);"><i class="fas fa-check"></i> Okundu (${code})</span>`;
        
        // Hide scanner, show employee select
        if(html5QrcodeScanner) {
            try { html5QrcodeScanner.clear(); } catch(e){}
        }
        document.getElementById('step1-container').style.display = 'none';
        document.getElementById('step2-container').style.display = 'block';
        
        step = 2;
        document.getElementById('stepTitle').innerText = "2. Adım: Personel Seçin";
        document.getElementById('stepTitle').style.color = "var(--warning)";
        document.getElementById('stepSubtitle').innerText = "Listeden personeli seçin ve zimmetle butonuna basın.";
        
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Cihaz Seçildi! Şimdi Personel Seçin.', showConfirmButton: false, timer: 2000 });
    }

    // On select change, update the right panel UI
    document.getElementById('employeeSelect').addEventListener('change', function() {
        const select = this;
        if(select.value !== "") {
            const text = select.options[select.selectedIndex].text;
            document.getElementById('selectedEmployeeDisplay').innerHTML = `<span style="color:var(--warning);"><i class="fas fa-check"></i> ${text}</span>`;
        } else {
            document.getElementById('selectedEmployeeDisplay').innerText = "Bekleniyor...";
        }
    });

    function assignToEmployee() {
        const employeeId = document.getElementById('employeeSelect').value;
        if (!employeeId) {
            Swal.fire({ icon: 'warning', title: 'Uyarı', text: 'Lütfen listeden bir personel seçin.' });
            return;
        }

        Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Zimmet işlemi başlatılıyor...', showConfirmButton: false, timer: 1000 });
        
        // Send API Request
        fetch('/api/assignments/quick', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                asset_code: scannedAssetCode,
                employee_code: employeeId // Backend accepts employee ID as well
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('step2-container').style.display = 'none';
                document.getElementById('successBox').style.display = 'block';
                document.getElementById('printDocBtn').href = '/assignments/' + data.assignment_id + '/print';
                
                document.getElementById('stepTitle').innerText = "İşlem Tamamlandı";
                document.getElementById('stepTitle').style.color = "var(--success)";
                document.getElementById('stepSubtitle').innerText = "Bir sonraki işlem için Sıfırla butonuna basın.";
                
                Swal.fire({ icon: 'success', title: 'Başarılı!', text: 'Zimmet saniyeler içinde başarıyla oluşturuldu.' });
            } else {
                Swal.fire({ icon: 'error', title: 'Hata', text: data.message });
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Bağlantı Hatası', text: 'Sunucuya ulaşılamadı.' });
        });
    }

    function resetProcess() {
        step = 1;
        scannedAssetCode = null;
        
        document.getElementById('step1-container').style.display = 'block';
        document.getElementById('step2-container').style.display = 'none';
        document.getElementById('employeeSelect').value = "";
        
        document.getElementById('selectedAssetDisplay').innerText = "Bekleniyor...";
        document.getElementById('selectedEmployeeDisplay').innerText = "Bekleniyor...";
        document.getElementById('successBox').style.display = 'none';
        
        document.getElementById('stepTitle').innerText = "1. Adım: Demirbaş Okutun";
        document.getElementById('stepTitle').style.color = "var(--primary)";
        document.getElementById('stepSubtitle').innerText = "Zimmetlenecek cihazın QR veya Barkodunu okutun.";
        
        startScanner();
    }
</script>
