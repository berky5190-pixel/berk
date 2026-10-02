-- ============================================================================
-- DEMİRBAŞ VE ZİMMET YÖNETİM SİSTEMİ
-- BAŞLANGIÇ TOHUMLAMA VERİLERİ (SEEDERS)
-- ============================================================================

-- 1. ROLLER
INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'Sistem Yöneticisi', 'admin', 'Tüm sistem ayarları, kullanıcılar, loglar ve işlemler üzerinde tam yetki.', NOW()),
(2, 'Birim Sorumlusu', 'manager', 'Demirbaş, personel, zimmet ve stok operasyonlarını yönetir. Sistem ayarlarını değiştiremez.', NOW()),
(3, 'Standart Kullanıcı', 'user', 'Kendi zimmetlerini ve kamuya açık demirbaş kataloğunu görüntüler.', NOW());

-- 2. YETKİLER
INSERT INTO `permissions` (`id`, `name`, `slug`, `module`, `description`) VALUES
-- Demirbaş Yetkileri
(1, 'Demirbaşları Listele', 'assets.view', 'assets', 'Demirbaş listesini görüntüleyebilir.'),
(2, 'Demirbaş Ekle', 'assets.create', 'assets', 'Yeni demirbaş kaydı açabilir.'),
(3, 'Demirbaş Düzenle', 'assets.edit', 'assets', 'Demirbaş bilgilerini güncelleyebilir.'),
(4, 'Demirbaş Sil', 'assets.delete', 'assets', 'Demirbaş kaydını silebilir.'),
-- Zimmet Yetkileri
(5, 'Zimmetleri Listele', 'assignments.view', 'assignments', 'Zimmet kayıtlarını görüntüleyebilir.'),
(6, 'Zimmet Oluştur', 'assignments.create', 'assignments', 'Personele demirbaş zimmetleyebilir.'),
(7, 'Zimmet İade Al', 'assignments.return', 'assignments', 'Zimmetli demirbaşı iade alabilir.'),
(8, 'Zimmet Belgesi Yükle', 'assignments.upload_doc', 'assignments', 'Zimmet tutanağı veya belge yükleyebilir.'),
(9, 'Zimmet Belgesi İndir', 'assignments.download_doc', 'assignments', 'Zimmet belgelerini indirebilir.'),
-- Personel Yetkileri
(10, 'Personelleri Listele', 'employees.view', 'employees', 'Personel listesini görüntüleyebilir.'),
(11, 'Personel Ekle/Düzenle', 'employees.manage', 'employees', 'Personel ekleyip düzenleyebilir.'),
-- Stok Yetkileri
(12, 'Stokları Yönet', 'stock.manage', 'stock', 'Sarf stok kartları ve hareketlerini yönetebilir.'),
-- Rapor & Log Yetkileri
(13, 'Raporları Görüntüle', 'reports.view', 'reports', 'Sistem raporlarını görüntüleyip dışa aktarabilir.'),
(14, 'İşlem Geçmişini İncele', 'audit.view', 'audit', 'Audit log kayıtlarını inceleyebilir.'),
-- Yönetim Yetkileri
(15, 'Kullanıcıları Yönet', 'users.manage', 'users', 'Kullanıcı hesaplarını yönetebilir.'),
(16, 'Sistem Ayarları', 'settings.manage', 'settings', 'Sistem ayarlarını değiştirebilir.');

-- 3. ROL - YETKİ EŞLEŞTİRMELERİ
-- Admin: Tüm Yetkiler (1 - 16)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM `permissions`;

-- Manager: Operasyonel Yetkiler (1 - 14)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 2, id FROM `permissions` WHERE id <= 14;

-- User: Salt Okunur ve İndirme Yetkileri (1, 5, 9)
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3, 1), (3, 5), (3, 9);

-- 4. DEPARTMANLAR
INSERT INTO `departments` (`id`, `name`, `code`, `description`, `status`) VALUES
(1, 'Bilgi İşlem Daire Başkanlığı', 'DEP-IT', 'Yazılım, donanım, ağ ve altyapı yönetimi', 'active'),
(2, 'İnsan Kaynakları', 'DEP-HR', 'Personel özlük, bordro ve işe alım', 'active'),
(3, 'Muhasebe ve Finans', 'DEP-ACC', 'Mali işler ve bütçe planlama', 'active'),
(4, 'İdari ve Mali İşler', 'DEP-ADM', 'Bina, lojistik ve genel hizmetler', 'active');

-- 5. LOKASYONLAR
INSERT INTO `locations` (`id`, `name`, `code`, `building`, `room_number`, `description`) VALUES
(1, 'Merkez Bina - Bilgi İşlem', 'LOC-IT-ROOM', 'Genel Merkez A Blok', 'Kat 2 - No: 204', 'IT Operasyon Odası'),
(2, 'Merkez Depo', 'LOC-MAIN-DEPOT', 'Lojistik B Blok', 'Zemin Kat - No: 01', 'Ana Demirbaş ve Sarf Deposu'),
(3, 'İdari Ofisler', 'LOC-ADM-FLOOR', 'Genel Merkez A Blok', 'Kat 1', 'Açık Ofis Alanı');

-- 6. DEMİRBAŞ KATEGORİLERİ
INSERT INTO `asset_categories` (`id`, `parent_id`, `name`, `code`, `description`) VALUES
(1, NULL, 'Bilgisayar & Donanım', 'CAT-COMP', 'Dizüstü, masaüstü bilgisayarlar ve sunucular'),
(2, 1, 'Dizüstü Bilgisayar (Laptop)', 'CAT-LAPTOP', 'Taşınabilir bilgisayarlar'),
(3, 1, 'Masaüstü Bilgisayar (PC)', 'CAT-DESKTOP', 'İş istasyonları ve masaüstü kasalar'),
(4, NULL, 'Monitör & Ekran', 'CAT-MONITOR', 'Ofis ve tasarım monitörleri'),
(5, NULL, 'Yazıcı & Tarayıcı', 'CAT-PRINT', 'Lazer yazıcılar, çok fonksiyonlu cihazlar'),
(6, NULL, 'Ağ ve Güvenlik Cihazları', 'CAT-NET', 'Switch, router, firewall ve access pointler'),
(7, NULL, 'Ofis Mobilyası', 'CAT-FURN', 'Çalışma masaları, ergonomik koltuklar, dolaplar'),
(8, NULL, 'Sarf Malzeme & Aksesuar', 'CAT-ACC', 'Klavye, mouse, kablolar, adaptörler');

-- 7. İLK YÖNETİCİ KULLANICISI (Admin)
-- Parola: Admin123!
INSERT INTO `users` (`id`, `role_id`, `employee_id`, `username`, `email`, `password_hash`, `full_name`, `status`, `created_at`) VALUES
(1, 1, NULL, 'admin', 'admin@kurum.com', '$2y$10$F005jmLibEgjEhP8/MxfzuZqW6mliVpxqYAsYoVSsypVDo5ccYJXO', 'Sistem Yöneticisi', 'active', NOW());

-- 8. SİSTEM AYARLARI
INSERT INTO `settings` (`key`, `value`, `group`, `description`) VALUES
('company_name', 'T.C. Kurumsal Demirbaş ve Envanter Yönetimi', 'general', 'Sistemde ve PDF belgelerinde görünecek resmi kurum adı'),
('company_email', 'bilgi@kurum.gov.tr', 'general', 'Kurumsal iletişim e-posta adresi'),
('company_phone', '0 (212) 555 00 00', 'general', 'Kurum irtibat telefonu'),
('company_address', 'Merkez Mah. İstiklal Cad. No:100 Çankaya / ANKARA', 'general', 'Kurum resmi açık adresi'),
('assignment_code_prefix', 'ZMT-', 'assignment', 'Zimmet numaralandırma ön eki'),
('default_return_days', '365', 'assignment', 'Standart planlanan iade süresi (gün)'),
('critical_stock_threshold', '5', 'stock', 'Kritik stok seviye uyarı eşiği'),
('assignment_pdf_title', 'DEMİRBAŞ TESLİM VE TESELLÜM TUTANAĞI', 'assignment', 'PDF çıktısı başlığı');
