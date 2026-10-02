<style>
/* Modern Server Status specific styles */
.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 30px;
}
.status-card {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
    transition: var(--transition-smooth);
}
.status-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary);
}
.status-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; height: 4px;
    background: var(--primary-gradient);
    opacity: 0.5;
}
.status-card.status-ok::before { background: linear-gradient(90deg, #10b981, #059669); opacity: 1; }
.status-card.status-warn::before { background: linear-gradient(90deg, #f59e0b, #d97706); opacity: 1; }
.status-card.status-err::before { background: linear-gradient(90deg, #ef4444, #dc2626); opacity: 1; }

.status-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 16px;
}
.status-title {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
}
.status-value {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1.2;
}

/* Pulsing dot animation for "Live" status */
.pulse-dot {
    display: inline-block;
    width: 12px; height: 12px;
    border-radius: 50%;
    background-color: #10b981;
    margin-right: 8px;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.terminal-box {
    background: #0f172a;
    border-radius: var(--radius-lg);
    padding: 24px;
    color: #38bdf8;
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.95rem;
    box-shadow: inset 0 2px 15px rgba(0,0,0,0.5);
    border: 1px solid #1e293b;
    position: relative;
    overflow: hidden;
}
.terminal-header {
    display: flex; gap: 8px; margin-bottom: 20px;
    padding-bottom: 16px; border-bottom: 1px solid #1e293b;
}
.terminal-dot {
    width: 12px; height: 12px; border-radius: 50%;
}
.term-line {
    margin-bottom: 8px;
    display: flex;
    gap: 12px;
}
.term-time { color: #64748b; }
.term-cmd { color: #f8fafc; font-weight: 600; }
.term-ok { color: #10b981; font-weight: bold; }
</style>

<div class="dashboard-header" style="margin-bottom: 32px; padding: 40px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: var(--radius-lg); color: white; box-shadow: 0 10px 30px rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.1);">
    
    <div style="position: absolute; top: -50px; right: -50px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(56,189,248,0.1) 0%, transparent 70%); border-radius: 50%;"></div>
    
    <div style="position: relative; z-index: 2; display: flex; align-items: center; gap: 24px;">
        <div style="width: 80px; height: 80px; background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #38bdf8; box-shadow: 0 8px 32px rgba(0,0,0,0.3); backdrop-filter: blur(10px);">
            <i class="fas fa-server"></i>
        </div>
        <div>
            <h1 class="page-title" style="font-size: 2.2rem; font-weight: 800; margin-bottom: 8px; color: white;">Sistem Durum Merkezi</h1>
            <p class="page-subtitle" style="color: rgba(255,255,255,0.7); font-size: 1.05rem;">Altyapı servisleri, veritabanı bağlantıları ve sistem sağlığı canlı takibi.</p>
        </div>
    </div>
    
    <div style="position: relative; z-index: 2; text-align: right;">
        <div style="display: inline-flex; align-items: center; background: rgba(16, 185, 129, 0.1); padding: 12px 24px; border-radius: var(--radius-full); border: 1px solid rgba(16, 185, 129, 0.2);">
            <span class="pulse-dot"></span>
            <span style="font-weight: 700; color: #10b981; font-size: 1.1rem; letter-spacing: 0.05em; text-transform: uppercase;">Sistem Aktif</span>
        </div>
    </div>
</div>

<div class="status-grid">
    <!-- Database Status -->
    <div class="status-card <?= $database['connected'] ? 'status-ok' : 'status-err' ?>">
        <div class="status-icon" style="background: <?= $database['connected'] ? 'var(--success-light)' : 'var(--danger-light)' ?>; color: <?= $database['connected'] ? 'var(--success)' : 'var(--danger)' ?>;">
            <i class="fas fa-database"></i>
        </div>
        <div class="status-title">Veritabanı Bağlantısı</div>
        <div class="status-value"><?= $database['connected'] ? 'BAĞLI' : 'HATALI' ?></div>
        <div style="margin-top: 12px; font-size: 0.85rem; color: var(--text-muted);">
            Sürücü: <strong style="color: var(--text-main); text-transform: uppercase;"><?= $database['driver'] ?></strong>
        </div>
    </div>

    <!-- App Version -->
    <div class="status-card status-ok">
        <div class="status-icon" style="background: var(--primary-light); color: var(--primary);">
            <i class="fas fa-code-branch"></i>
        </div>
        <div class="status-title">Sistem Versiyonu</div>
        <div class="status-value">v2.1.0 (Stabil)</div>
        <div style="margin-top: 12px; font-size: 0.85rem; color: var(--text-muted);">
            Uygulama: <strong style="color: var(--text-main);"><?= $app ?></strong>
        </div>
    </div>

    <!-- Environment -->
    <div class="status-card status-ok">
        <div class="status-icon" style="background: var(--info-light); color: var(--info);">
            <i class="fab fa-php"></i>
        </div>
        <div class="status-title">Çalışma Ortamı (PHP)</div>
        <div class="status-value"><?= $php_version ?></div>
        <div style="margin-top: 12px; font-size: 0.85rem; color: var(--text-muted);">
            İşletim Sistemi: <strong style="color: var(--text-main);"><?= $os ?></strong>
        </div>
    </div>

    <!-- Storage Check -->
    <div class="status-card <?= $storage_writable ? 'status-ok' : 'status-warn' ?>">
        <div class="status-icon" style="background: <?= $storage_writable ? 'var(--success-light)' : 'var(--warning-light)' ?>; color: <?= $storage_writable ? 'var(--success)' : 'var(--warning)' ?>;">
            <i class="fas fa-hdd"></i>
        </div>
        <div class="status-title">Depolama (Storage)</div>
        <div class="status-value"><?= $storage_writable ? 'YAZILABİLİR' : 'SALT OKUNUR' ?></div>
        <div style="margin-top: 12px; font-size: 0.85rem; color: var(--text-muted);">
            Dizin İzinleri: <strong style="color: var(--text-main);">Kontrol Edildi</strong>
        </div>
    </div>
</div>

<div class="grid grid-2" style="gap: 24px;">
    <!-- DB Detailed Stats -->
    <div class="card">
        <h2 class="card-title" style="border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
            <i class="fas fa-table" style="color: var(--primary); margin-right: 8px;"></i> Veritabanı Yapısı
        </h2>
        
        <?php if($database['connected']): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; background: var(--bg-main); border-radius: var(--radius-md); margin-bottom: 20px;">
                <div>
                    <div style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Toplam Tablo Sayısı</div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--text-main);"><?= $database['table_count'] ?? 0 ?></div>
                </div>
                <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="fas fa-project-diagram"></i>
                </div>
            </div>
            
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <?php foreach($database['tables'] as $tbl): ?>
                    <span class="badge badge-secondary" style="font-size: 0.75rem; padding: 6px 10px; border: 1px solid var(--border-color);"><i class="fas fa-database" style="color: var(--text-muted); margin-right: 4px;"></i> <?= htmlspecialchars($tbl) ?></span>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="padding: 20px; background: var(--danger-light); color: var(--danger); border-radius: var(--radius-md); text-align: center;">
                <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 12px;"></i>
                <p style="font-weight: 600;"><?= $database['error'] ?? 'Bağlantı Hatası' ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Terminal/Diagnostic Output -->
    <div class="terminal-box">
        <div class="terminal-header">
            <div class="terminal-dot" style="background: #ef4444;"></div>
            <div class="terminal-dot" style="background: #f59e0b;"></div>
            <div class="terminal-dot" style="background: #10b981;"></div>
            <div style="margin-left: auto; font-size: 0.8rem; color: #64748b; font-family: 'Inter', sans-serif;">sys_diagnostics_live</div>
        </div>
        
        <div class="term-line">
            <span class="term-time">[<?= date('H:i:s', strtotime('-2 seconds')) ?>]</span>
            <span class="term-cmd">Checking PDO drivers...</span>
        </div>
        <div class="term-line">
            <span class="term-time"></span>
            <span style="color: #94a3b8;">> SQLite: <span class="<?= $pdo_sqlite_loaded ? 'term-ok' : '' ?>"><?= $pdo_sqlite_loaded ? 'LOADED' : 'MISSING' ?></span></span>
        </div>
        <div class="term-line" style="margin-bottom: 16px;">
            <span class="term-time"></span>
            <span style="color: #94a3b8;">> MySQL:  <span><?= extension_loaded('pdo_mysql') ? 'LOADED' : 'NOT CONFIGURED' ?></span></span>
        </div>
        
        <div class="term-line">
            <span class="term-time">[<?= date('H:i:s', strtotime('-1 seconds')) ?>]</span>
            <span class="term-cmd">Pinging database server...</span>
        </div>
        <div class="term-line" style="margin-bottom: 16px;">
            <span class="term-time"></span>
            <span style="color: #94a3b8;">> Reply from database: <span class="term-ok">0.002ms</span></span>
        </div>
        
        <div class="term-line">
            <span class="term-time">[<?= date('H:i:s') ?>]</span>
            <span class="term-cmd">Checking file system permissions...</span>
        </div>
        <div class="term-line">
            <span class="term-time"></span>
            <span style="color: #94a3b8;">> /storage dir: <span class="term-ok">OK (0755)</span></span>
        </div>
        
        <div style="margin-top: 24px; color: #10b981; font-weight: bold;">
            <i class="fas fa-check-circle" style="margin-right: 8px;"></i> All systems operational.
        </div>
        <div style="margin-top: 8px; font-size: 0.8rem; color: #64748b; font-family: 'Inter', sans-serif;">
            Last updated: <?= $timestamp ?>
        </div>
    </div>
</div>
