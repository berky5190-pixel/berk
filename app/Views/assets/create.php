<div class="dashboard-header" style="margin-bottom: 24px;">
    <h1 class="page-title" style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">Yeni Demirbaş Ekle</h1>
    <p class="page-subtitle" style="color: var(--text-muted);">Sisteme yeni bir demirbaş kaydedin</p>
</div>

<div class="card" style="max-width: 1000px;">
    <form action="/assets" method="POST">
        
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Temel Bilgiler</h3>
        <div class="grid grid-3" style="margin-bottom: 24px;">
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Demirbaş Kodu / Etiket No *</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" name="asset_code" id="asset_code_input" required style="flex: 1; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
                    <button type="button" class="btn btn-secondary" onclick="startQRScanner('asset_code_input')" style="padding: 10px; flex-shrink: 0;" title="Kameradan Tara">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
            </div>
            
            <div style="grid-column: span 2;">
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Ürün Adı *</label>
                <input type="text" name="name" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Kategori *</label>
                <select name="category_id" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                    <option value="">Seçiniz...</option>
                    <?php foreach($categories ?? [] as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= \App\Core\View::escape($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Marka</label>
                <input type="text" name="brand" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Model</label>
                <input type="text" name="model" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Seri No</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" name="serial_number" id="serial_number_input" style="flex: 1; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
                    <button type="button" class="btn btn-secondary" onclick="startQRScanner('serial_number_input')" style="padding: 10px; flex-shrink: 0;" title="Kameradan Tara">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
            </div>
            
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Barkod</label>
                <input type="text" name="barcode" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Durum</label>
                <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; background: white;">
                    <option value="in_stock">Stokta</option>
                    <option value="assigned">Zimmetli</option>
                    <option value="in_use">Kullanımda</option>
                    <option value="defective">Arızalı</option>
                    <option value="in_service">Serviste</option>
                    <option value="scrapped">Hurda</option>
                    <option value="lost">Kayıp</option>
                </select>
            </div>
        </div>

        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Satın Alma ve Garanti</h3>
        <div class="grid grid-3" style="margin-bottom: 24px;">
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Satın Alma Tarihi</label>
                <input type="date" name="purchase_date" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Fiyat</label>
                <input type="number" step="0.01" name="purchase_price" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            <div>
                <!-- Spacer -->
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Garanti Başlangıç</label>
                <input type="date" name="warranty_start" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Garanti Bitiş</label>
                <input type="date" name="warranty_end" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none;">
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px;">Açıklama / Notlar</label>
            <textarea name="description" rows="3" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: var(--radius-sm); outline: none; resize: vertical;"></textarea>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 20px;">
            <a href="/assets" class="btn btn-secondary">İptal</a>
            <button type="submit" class="btn btn-primary">Kaydet</button>
        </div>
    </form>
</div>

<!-- QR Scanner Modal Overlay -->
<div id="qrModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="card" style="width: 100%; max-width: 500px; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.1rem; font-weight: 700;"><i class="fas fa-camera"></i> Barkod / QR Kod Tara</h3>
            <button type="button" onclick="stopQRScanner()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div id="qr-reader" style="width: 100%;"></div>
        <p style="text-align: center; font-size: 0.85rem; color: var(--text-muted); margin-top: 16px;">Kameranızı açarak QR kodu okutunuz.</p>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrcodeScanner = null;
    let targetInputId = null;

    function startQRScanner(inputId) {
        targetInputId = inputId;
        document.getElementById('qrModal').style.display = 'flex';
        
        html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: {width: 250, height: 250} }, false);
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Handle the decoded text
        // If it's a URL (like our generated QR codes), we might want to extract the ID, 
        // but since we are scanning standard barcodes or our own asset codes:
        // Actually, our generated QR code has a URL: http://domain/assets/ID
        // But if they scan a manufacturer barcode, it's just a string.
        // Let's just put the raw decoded text into the input field.
        document.getElementById(targetInputId).value = decodedText;
        stopQRScanner();
    }

    function onScanFailure(error) {
        // handle scan failure, usually better to ignore and keep scanning
    }

    function stopQRScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
        document.getElementById('qrModal').style.display = 'none';
        targetInputId = null;
    }
</script>
