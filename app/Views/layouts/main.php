<!DOCTYPE html>
<html lang="tr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::escape($pageTitle ?? 'IT Envanter & Zimmet Sistemi') ?></title>
    <?= \App\Helpers\CsrfHelper::meta() ?>
    <link rel="stylesheet" href="/static/css/style.css">
    <!-- Font Awesome (for icons in sidebar) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Dark Mode Init Script -->
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</head>
<body>
    <div class="app-container with-sidebar">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b4/Flag_of_Turkey.svg" alt="Türk Bayrağı" style="width: 42px; height: 28px; border-radius: 4px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                <span class="brand-text">Bilgi İşlem</span>
                <button class="mobile-menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')" aria-label="Menüyü Aç/Kapat">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <nav class="sidebar-nav">
                <?php if (\App\Services\AuthService::check()): ?>
                    <?php 
                        $uri = $_SERVER['REQUEST_URI'];
                        $isStaffUser = \App\Services\AuthService::isStaff();
                        $dbInstance = \App\Core\Database::getInstance()->getConnection();
                        $pendingRequestsCount = 0;
                        if ($isStaffUser && $dbInstance) {
                            try {
                                $stmt = $dbInstance->query("SELECT COUNT(*) as c FROM asset_requests WHERE status = 'pending'");
                                if ($stmt) {
                                    $pendingRequestsCount = (int)($stmt->fetch()['c'] ?? 0);
                                }
                            } catch (\Throwable $e) {
                                $pendingRequestsCount = 0;
                            }
                        }
                    ?>

                    <?php if ($isStaffUser): ?>
                        <!-- Yönetici & Birim Sorumlusu Menüsü -->
                        <div class="nav-group">Genel Yönetim</div>
                        <a href="/" class="sidebar-link <?= ($uri === '/' || $uri === '/health') ? 'active' : '' ?>">
                            <i class="fas fa-chart-line"></i> Dashboard
                        </a>
                        <a href="/requests" class="sidebar-link <?= str_starts_with($uri, '/requests') ? 'active' : '' ?>" style="display: flex; justify-content: space-between; align-items: center;">
                            <span><i class="fas fa-tasks"></i> Personel Talepleri</span>
                            <?php if ($pendingRequestsCount > 0): ?>
                                <span class="badge" style="background: #f59e0b; color: white; border-radius: 10px; padding: 2px 7px; font-size: 0.7rem; font-weight: 700;"><?= $pendingRequestsCount ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="/assets" class="sidebar-link <?= str_starts_with($uri, '/assets') ? 'active' : '' ?>">
                            <i class="fas fa-box"></i> Demirbaşlar
                        </a>
                        <a href="/stock" class="sidebar-link <?= str_starts_with($uri, '/stock') ? 'active' : '' ?>">
                            <i class="fas fa-layer-group"></i> Stok
                        </a>
                        <a href="/employees" class="sidebar-link <?= str_starts_with($uri, '/employees') ? 'active' : '' ?>">
                            <i class="fas fa-users"></i> Personeller
                        </a>
                        <a href="/assignments" class="sidebar-link <?= $uri === '/assignments' ? 'active' : '' ?>">
                            <i class="fas fa-clipboard-list"></i> Zimmetler
                        </a>
                        <a href="/assignments/quick" class="sidebar-link <?= str_starts_with($uri, '/assignments/quick') ? 'active' : '' ?>">
                            <i class="fas fa-bolt"></i> Hızlı Zimmet (QR)
                        </a>
                        <a href="/reports" class="sidebar-link <?= str_starts_with($uri, '/reports') ? 'active' : '' ?>">
                            <i class="fas fa-chart-pie"></i> Raporlar
                        </a>
                        <a href="/inventory-audit" class="sidebar-link <?= str_starts_with($uri, '/inventory-audit') ? 'active' : '' ?>">
                            <i class="fas fa-qrcode"></i> Fiziksel Sayım
                        </a>
                        
                        <div class="nav-group" style="margin-top: 24px;">Sistem</div>
                        <a href="/users" class="sidebar-link <?= str_starts_with($uri, '/users') ? 'active' : '' ?>">
                            <i class="fas fa-user-shield"></i> Kullanıcı Yönetimi
                        </a>
                        <a href="/settings" class="sidebar-link <?= str_starts_with($uri, '/settings') ? 'active' : '' ?>">
                            <i class="fas fa-cog"></i> Ayarlar
                        </a>
                        <a href="/trash" class="sidebar-link <?= str_starts_with($uri, '/trash') ? 'active' : '' ?>" style="color: var(--danger);">
                            <i class="fas fa-trash-alt"></i> Çöp Kutusu
                        </a>
                    <?php else: ?>
                        <!-- Personel Self-Service Menüsü -->
                        <div class="nav-group">Personel Portalı</div>
                        <a href="/" class="sidebar-link <?= ($uri === '/' || $uri === '/health') ? 'active' : '' ?>">
                            <i class="fas fa-home"></i> Panelim
                        </a>
                        <a href="/assignments" class="sidebar-link <?= str_starts_with($uri, '/assignments') ? 'active' : '' ?>">
                            <i class="fas fa-laptop"></i> Zimmetli Cihazlarım
                        </a>
                        <a href="/requests" class="sidebar-link <?= str_starts_with($uri, '/requests') ? 'active' : '' ?>">
                            <i class="fas fa-clipboard-list"></i> Taleplerim & Arıza
                        </a>

                        <div class="nav-group" style="margin-top: 24px;">Hesap</div>
                        <a href="/notifications" class="sidebar-link <?= str_starts_with($uri, '/notifications') ? 'active' : '' ?>">
                            <i class="fas fa-bell"></i> Bildirimler
                        </a>
                        <a href="/profile" class="sidebar-link <?= str_starts_with($uri, '/profile') ? 'active' : '' ?>">
                            <i class="fas fa-user-circle"></i> Profilim
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="/" class="sidebar-link active">
                        <i class="fas fa-info-circle"></i> Sistem Durumu
                    </a>
                <?php endif; ?>
            </nav>
        </aside>

        <div class="main-wrapper">
            <!-- Top Navbar -->
            <header class="navbar">
                <div class="navbar-inner" style="justify-content: flex-end;">
                    <nav class="nav-links">
                        <?php if (\App\Services\AuthService::check()): ?>
                            <?php $u = \App\Services\AuthService::user(); ?>
                            <!-- Dark Mode Toggle -->
                            <a href="javascript:void(0)" id="darkModeToggle" class="nav-item">
                                <i class="fas fa-moon" id="darkModeIcon"></i>
                            </a>
                            <?php
                                $dbInstance = \App\Core\Database::getInstance()->getConnection();
                                $currentUserId = $u['id'] ?? 1;
                                $unreadCount = $dbInstance->query("SELECT COUNT(*) as c FROM notifications WHERE user_id = {$currentUserId} AND is_read = 0")->fetch()['c'];
                                $latestNotifs = $dbInstance->query("SELECT * FROM notifications WHERE user_id = {$currentUserId} ORDER BY created_at DESC LIMIT 5")->fetchAll();
                            ?>
                            <div class="nav-item notification-dropdown-container" style="position: relative;">
                                <a href="javascript:void(0)" id="notificationToggle" style="color: inherit; text-decoration: none;">
                                    <i class="fas fa-bell"></i>
                                    <?php if ($unreadCount > 0): ?>
                                        <span class="badge" style="position: absolute; top: -5px; right: -8px; background: #ef4444; color: white; border-radius: 50%; padding: 2px 5px; font-size: 0.6rem; font-weight: bold;"><?= $unreadCount ?></span>
                                    <?php endif; ?>
                                </a>
                                <div id="notificationDropdown" style="display: none; position: absolute; top: 35px; right: 0; width: 320px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 1000; overflow: hidden; backdrop-filter: blur(20px);">
                                    <div style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: var(--bg-main);">
                                        <span style="font-weight: 600; font-size: 0.9rem; color: var(--text-main);">Bildirimler</span>
                                        <button id="closeNotification" style="background: none; border: none; cursor: pointer; color: var(--text-muted);"><i class="fas fa-times"></i></button>
                                    </div>
                                    <div style="max-height: 300px; overflow-y: auto;">
                                        <?php if (empty($latestNotifs)): ?>
                                            <div style="padding: 16px; text-align: center; color: var(--text-muted); font-size: 0.85rem;">Yeni bildiriminiz yok.</div>
                                        <?php else: ?>
                                            <?php foreach ($latestNotifs as $notif): ?>
                                                <div style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); <?= $notif['is_read'] == 0 ? 'background: var(--primary-light);' : '' ?>">
                                                    <div style="font-size: 0.85rem; color: var(--text-main); margin-bottom: 4px;"><?= \App\Core\View::escape($notif['message']) ?></div>
                                                    <div style="font-size: 0.7rem; color: var(--text-muted);">
                                                        <?php 
                                                            $utcTime = new DateTime($notif['created_at'], new DateTimeZone('UTC'));
                                                            $utcTime->setTimezone(new DateTimeZone('Europe/Istanbul'));
                                                            echo $utcTime->format('d.m.Y H:i');
                                                        ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div style="padding: 8px; text-align: center; border-top: 1px solid var(--border-color); background: var(--bg-main); display: flex; justify-content: space-between;">
                                        <a href="/notifications" style="font-size: 0.8rem; color: var(--primary); text-decoration: none; font-weight: 600; padding: 4px 8px;">Tümünü Gör</a>
                                        <?php if ($unreadCount > 0): ?>
                                            <button onclick="markAllNotificationsAsRead()" style="font-size: 0.8rem; color: var(--success); background: none; border: none; cursor: pointer; font-weight: 600; padding: 4px 8px;"><i class="fas fa-check-double"></i> Okundu İşaretle</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <script>
                                function markAllNotificationsAsRead() {
                                    fetch('/notifications/read-all', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                            'X_CSRF_TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                                        },
                                        body: '_csrf=' + (document.querySelector('meta[name="csrf-token"]')?.content || '')
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'success') {
                                            location.reload();
                                        }
                                    });
                                }
                            </script>
                            <a href="/profile" class="nav-item">
                                <i class="fas fa-user-circle"></i> <?= \App\Core\View::escape($u['full_name'] ?? 'Profil') ?>
                            </a>
                            <a href="/logout" class="nav-item" style="color: var(--danger);">
                                <i class="fas fa-sign-out-alt"></i> Çıkış
                            </a>
                        <?php else: ?>
                            <a href="/health" class="nav-item">Sağlık Kontrolü</a>
                            <a href="/login" class="btn btn-primary" style="padding: 6px 16px; font-size: 0.85rem;">Giriş Yap</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </header>

            <!-- Flash Messages (SweetAlert2) -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <?php if (!empty($flash['success']) || !empty($flash['error'])): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                            background: '#ffffff',
                            color: '#333333',
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });

                        <?php if (!empty($flash['success'])): ?>
                        Toast.fire({
                            icon: 'success',
                            title: '<?= addslashes(\App\Core\View::escape($flash['success'])) ?>'
                        });
                        <?php endif; ?>

                        <?php if (!empty($flash['error'])): ?>
                        Toast.fire({
                            icon: 'error',
                            title: '<?= addslashes(\App\Core\View::escape($flash['error'])) ?>'
                        });
                        <?php endif; ?>
                    });
                </script>
            <?php endif; ?>

            <!-- Main Content Slot -->
            <main class="main-content">
                <?= $content ?? '' ?>
            </main>

            <!-- Footer -->
            <footer class="footer">
                <p>&copy; <?= date('Y') ?> Bilgi İşlem (IT) Demirbaş ve Zimmet Yönetim Sistemi.</p>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dark Mode Logic
            const darkModeToggle = document.getElementById('darkModeToggle');
            const darkModeIcon = document.getElementById('darkModeIcon');
            
            if (darkModeToggle && darkModeIcon) {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                if (currentTheme === 'dark') {
                    darkModeIcon.classList.remove('fa-moon');
                    darkModeIcon.classList.add('fa-sun');
                }
                
                darkModeToggle.addEventListener('click', function() {
                    const theme = document.documentElement.getAttribute('data-theme');
                    const newTheme = theme === 'dark' ? 'light' : 'dark';
                    
                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    
                    if (newTheme === 'dark') {
                        darkModeIcon.classList.remove('fa-moon');
                        darkModeIcon.classList.add('fa-sun');
                    } else {
                        darkModeIcon.classList.remove('fa-sun');
                        darkModeIcon.classList.add('fa-moon');
                    }
                });
            }

            // Notification Dropdown Logic
            const notifToggle = document.getElementById('notificationToggle');
            const notifDropdown = document.getElementById('notificationDropdown');
            const notifClose = document.getElementById('closeNotification');

            if (notifToggle && notifDropdown) {
                // Toggle dropdown on bell click
                notifToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const isVisible = notifDropdown.style.display === 'block';
                    notifDropdown.style.display = isVisible ? 'none' : 'block';
                });

                // Close on 'x' click
                if (notifClose) {
                    notifClose.addEventListener('click', function(e) {
                        e.preventDefault();
                        notifDropdown.style.display = 'none';
                    });
                }

                // Close when clicking outside
                document.addEventListener('click', function(e) {
                    if (!notifToggle.contains(e.target) && !notifDropdown.contains(e.target)) {
                        notifDropdown.style.display = 'none';
                    }
                });
            }

            // Global confirmation dialog for forms with 'data-confirm'
            const confirmForms = document.querySelectorAll('form[data-confirm]');
            confirmForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent default submission
                    const message = this.getAttribute('data-confirm') || 'Bu işlemi yapmak istediğinize emin misiniz?';
                    
                    Swal.fire({
                        title: 'Emin misiniz?',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Evet, Onaylıyorum',
                        cancelButtonText: 'İptal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Submit if confirmed
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
