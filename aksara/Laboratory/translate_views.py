import os

# Base directory
base_dir = '/Users/abydahana/Sites/aksara/themes/default/views/home'
default_index = os.path.join(base_dir, 'index.php')

with open(default_index, 'r', encoding='utf-8') as f:
    content = f.read()

# Translations mapping
translations = {
    'en-pir': {
        'GET STARTED': 'SET SAIL',
        'Trusted By Organizations and Companies': 'Trusted by the Seven Seas',
        'Community & Interaction': 'Crews & Parleys',
        'Daily Visits': 'Daily Loot',
        'Weekly Visits': 'Weekly Booty',
        'Monthly Visits': 'Monthly Treasures',
        'Yearly Visits': 'Yearly Gold',
        'Articles Published': 'Scrolls Penned',
        'Interactions': 'Rumors',
        'Selected Galleries': 'Treasured Maps',
        'See all galleries': 'See all maps',
        'Latest News': 'Latest Tales',
        'See all news': 'See all tales',
        'Build Your Application Today': 'Forge Yer Vessel Today',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'Grab the charts of Aksara CMS and start buildin\' yer own ship without chains.',
        'GET SOURCE CODE': 'GRAB THE CHARTS'
    },
    'es': {
        'GET STARTED': 'EMPEZAR',
        'Trusted By Organizations and Companies': 'Con la confianza de organizaciones y empresas',
        'Community & Interaction': 'Comunidad e interacción',
        'Daily Visits': 'Visitas diarias',
        'Weekly Visits': 'Visitas semanales',
        'Monthly Visits': 'Visitas mensuales',
        'Yearly Visits': 'Visitas anuales',
        'Articles Published': 'Artículos publicados',
        'Interactions': 'Interacciones',
        'Selected Galleries': 'Galerías seleccionadas',
        'See all galleries': 'Ver todas las galerías',
        'Latest News': 'Últimas noticias',
        'See all news': 'Ver todas las noticias',
        'Build Your Application Today': 'Construya su aplicación hoy',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'Obtenga el código fuente completo de Aksara CMS y comience a construir su propia plataforma sin limitaciones.',
        'GET SOURCE CODE': 'OBTENER CÓDIGO FUENTE'
    },
    'fr': {
        'GET STARTED': 'COMMENCER',
        'Trusted By Organizations and Companies': 'Approuvé par les organisations et les entreprises',
        'Community & Interaction': 'Communauté et Interaction',
        'Daily Visits': 'Visites quotidiennes',
        'Weekly Visits': 'Visites hebdomadaires',
        'Monthly Visits': 'Visites mensuelles',
        'Yearly Visits': 'Visites annuelles',
        'Articles Published': 'Articles publiés',
        'Interactions': 'Interactions',
        'Selected Galleries': 'Galeries sélectionnées',
        'See all galleries': 'Voir toutes les galeries',
        'Latest News': 'Dernières nouvelles',
        'See all news': 'Voir toutes les nouvelles',
        'Build Your Application Today': 'Construisez votre application aujourd\'hui',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'Obtenez le code source complet d\'Aksara CMS et commencez à construire votre propre plateforme sans limitations.',
        'GET SOURCE CODE': 'OBTENIR LE CODE SOURCE'
    },
    'de': {
        'GET STARTED': 'LOSLEGEN',
        'Trusted By Organizations and Companies': 'Vertrauen von Organisationen und Unternehmen',
        'Community & Interaction': 'Community & Interaktion',
        'Daily Visits': 'Tägliche Besuche',
        'Weekly Visits': 'Wöchentliche Besuche',
        'Monthly Visits': 'Monatliche Besuche',
        'Yearly Visits': 'Jährliche Besuche',
        'Articles Published': 'Veröffentlichte Artikel',
        'Interactions': 'Interaktionen',
        'Selected Galleries': 'Ausgewählte Galerien',
        'See all galleries': 'Alle Galerien ansehen',
        'Latest News': 'Neueste Nachrichten',
        'See all news': 'Alle Nachrichten ansehen',
        'Build Your Application Today': 'Erstellen Sie noch heute Ihre Anwendung',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'Holen Sie sich den vollständigen Quellcode von Aksara CMS und bauen Sie Ihre eigene Plattform ohne Einschränkungen.',
        'GET SOURCE CODE': 'QUELLCODE ERHALTEN'
    },
    'it': {
        'GET STARTED': 'INIZIA',
        'Trusted By Organizations and Companies': 'Fiducia da organizzazioni e aziende',
        'Community & Interaction': 'Comunità e interazione',
        'Daily Visits': 'Visite giornaliere',
        'Weekly Visits': 'Visite settimanali',
        'Monthly Visits': 'Visite mensili',
        'Yearly Visits': 'Visite annuali',
        'Articles Published': 'Articoli pubblicati',
        'Interactions': 'Interazioni',
        'Selected Galleries': 'Gallerie selezionate',
        'See all galleries': 'Vedi tutte le gallerie',
        'Latest News': 'Ultime notizie',
        'See all news': 'Vedi tutte le notizie',
        'Build Your Application Today': 'Costruisci la tua applicazione oggi',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'Ottieni il codice sorgente completo di Aksara CMS e inizia a costruire la tua piattaforma senza limitazioni.',
        'GET SOURCE CODE': 'OTTIENI IL CODICE SORGENTE'
    },
    'pt': {
        'GET STARTED': 'COMEÇAR',
        'Trusted By Organizations and Companies': 'Confiado por organizações e empresas',
        'Community & Interaction': 'Comunidade e Interação',
        'Daily Visits': 'Visitas diárias',
        'Weekly Visits': 'Visitas semanais',
        'Monthly Visits': 'Visitas mensais',
        'Yearly Visits': 'Visitas anuais',
        'Articles Published': 'Artigos publicados',
        'Interactions': 'Interações',
        'Selected Galleries': 'Galerias selecionadas',
        'See all galleries': 'Ver todas as galerias',
        'Latest News': 'Últimas notícias',
        'See all news': 'Ver todas as notícias',
        'Build Your Application Today': 'Construa sua aplicação hoje',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'Obtenha o código-fonte completo do Aksara CMS e comece a construir sua própria plataforma sem limitações.',
        'GET SOURCE CODE': 'OBTER CÓDIGO FONTE'
    },
    'ms': {
        'GET STARTED': 'MULAKAN',
        'Trusted By Organizations and Companies': 'Dipercayai oleh Organisasi dan Syarikat',
        'Community & Interaction': 'Komuniti & Interaksi',
        'Daily Visits': 'Lawatan Harian',
        'Weekly Visits': 'Lawatan Mingguan',
        'Monthly Visits': 'Lawatan Bulanan',
        'Yearly Visits': 'Lawatan Tahunan',
        'Articles Published': 'Artikel Diterbitkan',
        'Interactions': 'Interaksi',
        'Selected Galleries': 'Galeri Terpilih',
        'See all galleries': 'Lihat semua galeri',
        'Latest News': 'Berita Terkini',
        'See all news': 'Lihat semua berita',
        'Build Your Application Today': 'Bina Aplikasi Anda Hari Ini',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'Dapatkan kod sumber lengkap Aksara CMS dan mula bina platform anda sendiri tanpa sekatan.',
        'GET SOURCE CODE': 'DAPATKAN KOD SUMBER'
    },
    'tr': {
        'GET STARTED': 'BAŞLAYIN',
        'Trusted By Organizations and Companies': 'Kuruluşlar ve Şirketler Tarafından Güvenilen',
        'Community & Interaction': 'Topluluk ve Etkileşim',
        'Daily Visits': 'Günlük Ziyaretler',
        'Weekly Visits': 'Haftalık Ziyaretler',
        'Monthly Visits': 'Aylık Ziyaretler',
        'Yearly Visits': 'Yıllık Ziyaretler',
        'Articles Published': 'Yayınlanan Makaleler',
        'Interactions': 'Etkileşimler',
        'Selected Galleries': 'Seçilmiş Galeriler',
        'See all galleries': 'Tüm galerileri gör',
        'Latest News': 'Son Haberler',
        'See all news': 'Tüm haberleri gör',
        'Build Your Application Today': 'Uygulamanızı Bugün Oluşturun',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'Aksara CMS\'nin tam kaynak kodunu alın ve kendi platformunuzu sınırlama olmaksızyn oluşturmaya başlayın.',
        'GET SOURCE CODE': 'KAYNAK KODU AL'
    },
    'vi': {
        'GET STARTED': 'BẮT ĐẦU',
        'Trusted By Organizations and Companies': 'Được tin dùng bởi các tổ chức và công ty',
        'Community & Interaction': 'Cộng đồng & Tương tác',
        'Daily Visits': 'Lượt truy cập hàng ngày',
        'Weekly Visits': 'Lượt truy cập hàng tuần',
        'Monthly Visits': 'Lượt truy cập hàng tháng',
        'Yearly Visits': 'Lượt truy cập hàng năm',
        'Articles Published': 'Bài viết đã xuất bản',
        'Interactions': 'Tương tác',
        'Selected Galleries': 'Thư viện đã chọn',
        'See all galleries': 'Xem tất cả thư viện',
        'Latest News': 'Tin tức mới nhất',
        'See all news': 'Xem tất cả tin tức',
        'Build Your Application Today': 'Xây dựng ứng dụng của bạn ngay hôm nay',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'Nhận mã nguồn đầy đủ của Aksara CMS và bắt đầu xây dựng nền tảng của riêng bạn mà không có giới hạn.',
        'GET SOURCE CODE': 'NHẬN MÃ NGUỒN'
    },
    'th': {
        'GET STARTED': 'เริ่มต้นใช้งาน',
        'Trusted By Organizations and Companies': 'ได้รับความไว้วางใจจากองค์กรและบริษัทต่างๆ',
        'Community & Interaction': 'ชุมชนและการโต้ตอบ',
        'Daily Visits': 'การเข้าชมรายวัน',
        'Weekly Visits': 'การเข้าชมรายสัปดาห์',
        'Monthly Visits': 'การเข้าชมรายเดือน',
        'Yearly Visits': 'การเข้าชมรายปี',
        'Articles Published': 'บทความที่เผยแพร่',
        'Interactions': 'การโต้ตอบ',
        'Selected Galleries': 'แกลเลอรี่ที่เลือก',
        'See all galleries': 'ดูแกลเลอรี่ทั้งหมด',
        'Latest News': 'ข่าวล่าสุด',
        'See all news': 'ดูข่าวทั้งหมด',
        'Build Your Application Today': 'สร้างแอปพลิเคชันของคุณวันนี้',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'รับซอร์สโค้ดฉบับเต็มของ Aksara CMS และเริ่มสร้างแพลตฟอร์มของคุณเองโดยไม่มีข้อจำกัด',
        'GET SOURCE CODE': 'รับซอร์สโค้ด'
    },
    'ja': {
        'GET STARTED': '今すぐ始める',
        'Trusted By Organizations and Companies': '組織や企業からの信頼',
        'Community & Interaction': 'コミュニティと相互作用',
        'Daily Visits': '今日の訪問者',
        'Weekly Visits': '週間の訪問者',
        'Monthly Visits': '月間の訪問者',
        'Yearly Visits': '年間の訪問者',
        'Articles Published': '公開された記事',
        'Interactions': 'インタラクション',
        'Selected Galleries': '厳選されたギャラリー',
        'See all galleries': 'すべてのギャラリーを見る',
        'Latest News': '最新ニュース',
        'See all news': 'すべてのニュースを見る',
        'Build Your Application Today': '今すぐアプリケーションを構築しましょう',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'Aksara CMSの完全なソースコードを入手して、制限なしで独自のプラットフォームの構築を開始してください。',
        'GET SOURCE CODE': 'ソースコードを入手'
    },
    'ko': {
        'GET STARTED': '시작하기',
        'Trusted By Organizations and Companies': '조직 및 기업의 신뢰',
        'Community & Interaction': '커뮤니티 및 상호 작용',
        'Daily Visits': '일일 방문',
        'Weekly Visits': '주간 방문',
        'Monthly Visits': '월간 방문',
        'Yearly Visits': '연간 방문',
        'Articles Published': '게시된 기사',
        'Interactions': '상호 작용',
        'Selected Galleries': '엄선된 갤러리',
        'See all galleries': '모든 갤러리 보기',
        'Latest News': '최신 뉴스',
        'See all news': '모든 뉴스 보기',
        'Build Your Application Today': '지금 바로 애플리케이션을 구축하세요',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'Aksara CMS의 전체 소스 코드를 다운로드하고 제한 없이 자신만의 플랫폼 구축을 시작하십시오.',
        'GET SOURCE CODE': '소스 코드 받기'
    },
    'zh': {
        'GET STARTED': '开始使用',
        'Trusted By Organizations and Companies': '受组织和公司信赖',
        'Community & Interaction': '社区与互动',
        'Daily Visits': '每日访问',
        'Weekly Visits': '每周访问',
        'Monthly Visits': '每月访问',
        'Yearly Visits': '每年访问',
        'Articles Published': '已发表文章',
        'Interactions': '互动',
        'Selected Galleries': '精选画廊',
        'See all galleries': '查看所有画廊',
        'Latest News': '最新新闻',
        'See all news': '查看所有新闻',
        'Build Your Application Today': '立即构建您的应用程序',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': '获取 Aksara CMS 的完整源代码，开始无限制地构建您自己的平台。',
        'GET SOURCE CODE': '获取源代码'
    },
    'ar': {
        'GET STARTED': 'ابدأ الآن',
        'Trusted By Organizations and Companies': 'موثوق به من قبل المنظمات والشركات',
        'Community & Interaction': 'المجتمع والتفاعل',
        'Daily Visits': 'الزيارات اليومية',
        'Weekly Visits': 'الزيارات الأسبوعية',
        'Monthly Visits': 'الزيارات الشهرية',
        'Yearly Visits': 'الزيارات السنوية',
        'Articles Published': 'المقالات المنشورة',
        'Interactions': 'التفاعلات',
        'Selected Galleries': 'المعارض المختارة',
        'See all galleries': 'شاهد جميع المعارض',
        'Latest News': 'أحدث الأخبار',
        'See all news': 'شاهد جميع الأخبار',
        'Build Your Application Today': 'ابنِ تطبيقك اليوم',
        'Get the complete source code of Aksara CMS and start building your own platform without limitations.': 'احصل على الكود المصدري الكامل لـ Aksara CMS وابدأ في بناء منصتك الخاصة دون قيود.',
        'GET SOURCE CODE': 'احصل على الكود المصدري'
    }
}

for lang, mapping in translations.items():
    lang_dir = os.path.join(base_dir, lang)
    if not os.path.exists(lang_dir):
        os.makedirs(lang_dir)
    
    new_content = content
    for original, translated in mapping.items():
        new_content = new_content.replace(original, translated)
    
    lang_index = os.path.join(lang_dir, 'index.php')
    with open(lang_index, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print(f"Translated and saved {lang_index}")
