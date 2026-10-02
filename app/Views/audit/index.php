<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Fiziksel Sayım & Karekod Okuyucu</h1>
    <p class="page-subtitle" style="color: var(--text-muted);">QR kodları okutarak stok denetimi yapın</p>
</div>

<div class="grid grid-3" style="align-items: start; gap: 24px;">
    
    <!-- Camera Section -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-header">
            <h2 class="card-title" style="font-size: 1.1rem;"><i class="fas fa-camera"></i> QR Okuyucu</h2>
        </div>
        <div id="qr-reader" style="width: 100%; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-color); background: var(--bg-main);"></div>
        
        <div style="margin-top: 16px;">
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px;">Kamera çalışmıyorsa Demirbaş Kodunu manuel girin:</p>
            <div style="display: flex; gap: 8px;">
                <input type="text" id="manualCode" class="form-control" placeholder="Örn: IT-LAP-001" onkeydown="if(event.key === 'Enter') checkManualCode()">
                <button class="btn btn-primary" onclick="checkManualCode()"><i class="fas fa-check"></i></button>
            </div>
        </div>

        <div style="margin-top: 24px; padding: 12px; background: var(--info-light); border-radius: var(--radius-sm); color: var(--info);">
            <div style="font-size: 1.5rem; font-weight: 800;" id="scannedCountDisplay">0 / 0</div>
            <div style="font-size: 0.85rem; font-weight: 600;">Demirbaş Sayıldı</div>
        </div>
    </div>

    <!-- Lists Section -->
    <div style="grid-column: span 2; display: flex; flex-direction: column; gap: 24px;">
        
        <!-- Scanned (Güncel Stok) -->
        <div class="card" style="border-top: 4px solid var(--success);">
            <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 16px;">
                <h2 class="card-title" style="font-size: 1.1rem; color: var(--success); margin:0;">
                    <i class="fas fa-check-circle"></i> Güncel Stok (Okutulanlar)
                </h2>
            </div>
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="text-align: left; color: var(--text-muted); background: var(--bg-main);">
                            <th style="padding: 10px;">ID / Kod</th>
                            <th style="padding: 10px;">Demirbaş Adı</th>
                            <th style="padding: 10px;">Durum</th>
                        </tr>
                    </thead>
                    <tbody id="scannedList">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Missing (Eski Stok / Eksikler) -->
        <div class="card" style="border-top: 4px solid var(--danger);">
            <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 16px;">
                <h2 class="card-title" style="font-size: 1.1rem; color: var(--danger); margin:0;">
                    <i class="fas fa-exclamation-circle"></i> Eski Stok (Sistemde Olup Okutulmayanlar)
                </h2>
            </div>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="text-align: left; color: var(--text-muted); background: var(--bg-main);">
                            <th style="padding: 10px;">ID / Kod</th>
                            <th style="padding: 10px;">Demirbaş Adı</th>
                            <th style="padding: 10px;">Durum</th>
                        </tr>
                    </thead>
                    <tbody id="missingList">
                        <!-- Filled by JS -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let expectedAssets = {};
    let scannedAssets = {};
    let totalExpected = 0;
    let totalScanned = 0;
    let html5QrcodeScanner = null;

    document.addEventListener('DOMContentLoaded', function() {
        // 1. Fetch expected assets
        fetch('/api/inventory-audit/expected')
            .then(res => res.json())
            .then(data => {
                data.forEach(asset => {
                    expectedAssets[asset.id] = asset;
                    // also allow lookup by asset_code
                    expectedAssets[asset.asset_code] = asset; 
                });
                totalExpected = data.length;
                updateUI();
                startScanner();
            })
            .catch(err => console.error("Error fetching assets:", err));
    });

    function startScanner() {
        html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Our QR codes have format like: http://domain/assets/ID
        // Let's try to extract ID from URL, or fallback to treating it as asset_code
        let assetIdentifier = decodedText;
        if (decodedText.includes('/assets/')) {
            const parts = decodedText.split('/assets/');
            assetIdentifier = parts[parts.length - 1]; // get the ID part
        }
        processScan(assetIdentifier);
    }

    function onScanFailure(error) {
        // ignore background noise
    }

    function checkManualCode() {
        const code = document.getElementById('manualCode').value.trim();
        if(code !== '') {
            processScan(code);
            document.getElementById('manualCode').value = ''; // clear input
        }
    }

    function processScan(identifier) {
        if (scannedAssets[identifier]) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Bu demirbaş zaten sayıldı.', showConfirmButton: false, timer: 3000 });
            return;
        }

        const asset = expectedAssets[identifier];
        if (asset) {
            // Found! Move it to scanned.
            // Since we indexed by both ID and code, find the real ID to avoid duplicate counting
            const realId = asset.id;
            if (scannedAssets[realId]) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'Bu demirbaş zaten sayıldı.', showConfirmButton: false, timer: 3000 });
                return;
            }

            scannedAssets[realId] = asset;
            scannedAssets[asset.asset_code] = asset; // mark both as scanned
            
            // Remove from expected object to hide from missing list (or just flag it)
            delete expectedAssets[realId];
            delete expectedAssets[asset.asset_code];

            totalScanned++;
            
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Okundu: ' + asset.name, showConfirmButton: false, timer: 2000 });
            updateUI();
        } else {
            // Not found in expected
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'HATA: Sistemde bulunamayan kayıt: ' + identifier, showConfirmButton: false, timer: 4000 });
        }
    }

    function updateUI() {
        const missingBody = document.getElementById('missingList');
        const scannedBody = document.getElementById('scannedList');
        
        missingBody.innerHTML = '';
        scannedBody.innerHTML = '';

        // To avoid listing twice (since we map both ID and Code), we keep track of printed IDs
        const printedMissing = new Set();
        for (const key in expectedAssets) {
            const asset = expectedAssets[key];
            if (!printedMissing.has(asset.id)) {
                missingBody.innerHTML += `
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--primary); font-weight:600;">${asset.asset_code}</td>
                        <td style="padding: 10px;">${asset.name}</td>
                        <td style="padding: 10px;"><span class="badge badge-secondary">${asset.status}</span></td>
                    </tr>
                `;
                printedMissing.add(asset.id);
            }
        }
        
        if (printedMissing.size === 0) {
            missingBody.innerHTML = '<tr><td colspan="3" style="padding: 20px; text-align: center; color: var(--success); font-weight:bold;">Tüm demirbaşlar okutuldu! Eksik yok.</td></tr>';
        }

        const printedScanned = new Set();
        for (const key in scannedAssets) {
            const asset = scannedAssets[key];
            if (!printedScanned.has(asset.id)) {
                scannedBody.innerHTML += `
                    <tr style="border-bottom: 1px solid var(--border-color); background: var(--success-light);">
                        <td style="padding: 10px; color: var(--primary); font-weight:600;">${asset.asset_code}</td>
                        <td style="padding: 10px;">${asset.name}</td>
                        <td style="padding: 10px;"><span class="badge badge-success">Sayıldı</span></td>
                    </tr>
                `;
                printedScanned.add(asset.id);
            }
        }
        
        if (printedScanned.size === 0) {
            scannedBody.innerHTML = '<tr><td colspan="3" style="padding: 20px; text-align: center; color: var(--text-muted);">Henüz demirbaş okutulmadı.</td></tr>';
        }

        document.getElementById('scannedCountDisplay').innerText = `${totalScanned} / ${totalExpected}`;
    }
</script>
