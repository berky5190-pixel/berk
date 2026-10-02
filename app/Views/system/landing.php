<style>
/* Full screen override */
body, html {
    margin: 0; padding: 0;
    overflow: hidden;
    height: 100vh;
}
.container {
    max-width: none !important;
    padding: 0 !important;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-section {
    position: relative;
    width: 100%;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    z-index: 10;
}

/* Abstract Background Shapes */
.shape-1 {
    position: absolute;
    top: -15%;
    left: -10%;
    width: 50vw;
    height: 50vw;
    background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, transparent 60%);
    border-radius: 50%;
    z-index: 1;
    animation: float 20s ease-in-out infinite alternate;
}
.shape-2 {
    position: absolute;
    bottom: -20%;
    right: -10%;
    width: 60vw;
    height: 60vw;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 60%);
    border-radius: 50%;
    z-index: 1;
    animation: float 25s ease-in-out infinite alternate-reverse;
}

@keyframes float {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(50px, 50px) scale(1.1); }
}

.hero-content {
    position: relative;
    z-index: 20;
    text-align: center;
    max-width: 800px;
    padding: 0 20px;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #94a3b8;
    padding: 8px 16px;
    border-radius: 100px;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin-bottom: 24px;
    backdrop-filter: blur(10px);
}
.hero-badge .dot {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 10px #10b981;
    animation: pulse 2s infinite;
}

.hero-title {
    font-size: 4.5rem;
    font-weight: 900;
    line-height: 1.1;
    margin: 0 0 24px 0;
    color: #f8fafc;
    letter-spacing: -0.02em;
}
.hero-title span {
    background: linear-gradient(135deg, #38bdf8 0%, #3b82f6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-subtitle {
    font-size: 1.25rem;
    color: #94a3b8;
    line-height: 1.6;
    margin: 0 auto 40px auto;
    max-width: 600px;
    font-weight: 400;
}

.hero-actions {
    display: flex;
    gap: 16px;
    justify-content: center;
}

.btn-glow {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: #38bdf8;
    color: #0f172a;
    font-size: 1.1rem;
    font-weight: 700;
    padding: 16px 40px;
    border-radius: 100px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    overflow: hidden;
}
.btn-glow:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(56, 189, 248, 0.4);
}
.btn-glow::after {
    content: '';
    position: absolute;
    top: -50%; left: -50%;
    width: 200%; height: 200%;
    background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
    transform: rotate(45deg) translateY(-100%);
    animation: shine 3s infinite;
}
@keyframes shine {
    0% { transform: rotate(45deg) translateY(-100%); }
    20% { transform: rotate(45deg) translateY(100%); }
    100% { transform: rotate(45deg) translateY(100%); }
}

.btn-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: transparent;
    color: #f8fafc;
    border: 1px solid rgba(255,255,255,0.2);
    font-size: 1.1rem;
    font-weight: 600;
    padding: 16px 40px;
    border-radius: 100px;
    text-decoration: none;
    transition: all 0.3s ease;
}
.btn-outline:hover {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.4);
}

/* Subtle grid line at the bottom */
.footer-line {
    position: absolute;
    bottom: 40px;
    left: 0;
    width: 100%;
    text-align: center;
    font-size: 0.8rem;
    color: #475569;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    z-index: 20;
}
</style>

<div class="hero-section">
    <div class="shape-1"></div>
    <div class="shape-2"></div>
    
    <div class="hero-content">
        <div class="hero-badge">
            <span class="dot"></span> Sistem Aktif
        </div>
        
        <h1 class="hero-title">
            Kurumsal <span>Demirbaş</span> Yönetimi
        </h1>
        
        <p class="hero-subtitle">
            Şirketinizin tüm cihaz, ekipman ve donanım altyapısını güvenli, hızlı ve profesyonel bir şekilde tek bir noktadan yönetin.
        </p>
        
        <div class="hero-actions">
            <a href="/login" class="btn-glow">
                Sisteme Giriş Yap <i class="fas fa-arrow-right"></i>
            </a>
            <a href="/health" class="btn-outline">
                <i class="fas fa-server"></i> Sunucu Durumu
            </a>
        </div>
    </div>
    
    <div class="footer-line">
        Kurumsal Bilgi İşlem Portalı v2.0
    </div>
</div>

<script>
    // Giriş butonunu ve geri butonunu ana landing page'de gizle
    document.addEventListener("DOMContentLoaded", function() {
        const topLoginBtn = document.querySelector('.login-btn');
        if (topLoginBtn) {
            topLoginBtn.style.display = 'none';
        }
        
        const topBackBtn = document.querySelector('.back-btn');
        if (topBackBtn) {
            topBackBtn.style.display = 'none';
        }
    });
</script>
