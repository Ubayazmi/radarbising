<?php
// Database connection (SQLite local / PostgreSQL production)
require_once __DIR__ . '/includes/Database.php';
$pdo = Database::getConnection();

// Load settings for meta tags
$stmtSettings = $pdo->query("SELECT * FROM settings LIMIT 1");
$siteSettings = $stmtSettings->fetch();
if (!$siteSettings) {
    $siteSettings = [
        'site_name' => 'Radar Bising',
        'site_description' => 'Platform Artikel & Video Musik Indonesia',
        'meta_title_template' => '{title} | Radar Bising',
        'meta_description_default' => '',
        'og_image_default' => '',
        'google_analytics_id' => '',
    ];
}

// Helper function
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Build meta title from template
function buildMetaTitle(string $title, string $template, string $siteName): string {
    if ($title === '') return $siteName;
    return str_replace('{title}', $title, $template);
}

// Category color function (global)
$catColor = function(string $cat): string {
    $colors = [
        'Ulasan' => 'bg-indigo-600',
        'Wawancara' => 'bg-fuchsia-600',
        'Agenda' => 'bg-orange-500',
        'Musik' => 'bg-emerald-600',
        'Budaya' => 'bg-sky-500',
        'Teknologi' => 'bg-amber-500',
    ];
    return $colors[$cat] ?? 'bg-neutral-600';
};

// Determine page
$page = $_GET['page'] ?? 'home';
$slug = $_GET['slug'] ?? null;
$catFilter = $_GET['cat'] ?? null;
$search = $_GET['q'] ?? null;
$pageTitle = '';

// Page-specific title logic
if ($page === 'article_detail' && $slug) {
    $stmtDetail = $pdo->prepare("SELECT * FROM articles WHERE slug = ? AND is_draft = 0");
    $stmtDetail->execute([$slug]);
    $artDetail = $stmtDetail->fetch();
    $pageTitle = $artDetail ? $artDetail['title'] : '';
} elseif ($page === 'video_detail' && $slug) {
    $stmtDetail = $pdo->prepare("SELECT * FROM videos WHERE slug = ? AND is_draft = 0");
    $stmtDetail->execute([$slug]);
    $vidDetail = $stmtDetail->fetch();
    $pageTitle = $vidDetail ? $vidDetail['title'] : '';
} elseif ($page === 'articles') {
    $pageTitle = 'Artikel';
} elseif ($page === 'videos') {
    $pageTitle = 'Video';
} elseif ($page === 'exclusive') {
    $pageTitle = 'Exclusive Interview';
}
?><!DOCTYPE html>
<html lang="id" class="<?= (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light') ? '' : 'dark' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(buildMetaTitle($pageTitle, $siteSettings['meta_title_template'], $siteSettings['site_name'])) ?></title>
    <meta name="description" content="<?= e($pageTitle ? '' : $siteSettings['meta_description_default']) ?>">
    <meta property="og:title" content="<?= e(buildMetaTitle($pageTitle, $siteSettings['meta_title_template'], $siteSettings['site_name'])) ?>">
    <meta property="og:description" content="<?= e($pageTitle ? '' : $siteSettings['meta_description_default']) ?>">
    <?php if ($siteSettings['og_image_default']): ?>
    <meta property="og:image" content="<?= e($siteSettings['og_image_default']) ?>">
    <?php endif; ?>
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e(buildMetaTitle($pageTitle, $siteSettings['meta_title_template'], $siteSettings['site_name'])) ?>">
    <meta name="twitter:description" content="<?= e($pageTitle ? '' : $siteSettings['meta_description_default']) ?>">
    <?php if ($siteSettings['google_analytics_id']): ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($siteSettings['google_analytics_id']) ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= e($siteSettings['google_analytics_id']) ?>');
    </script>
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class'
            }
        </script>
        <script>
            // Theme toggle - runs before render to avoid FOUC
            // Check both cookie (for server-side) and localStorage (client-side)
            (function() {
                var theme = localStorage.getItem('theme');
                var cookieTheme = document.cookie.split('; ').find(function(row) { return row.startsWith('theme='); });
                if (cookieTheme) theme = cookieTheme.split('=')[1];
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (theme === 'light' || (!theme && !prefersDark)) {
                    document.documentElement.classList.remove('dark');
                } else {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
    <style>
            :root {
                --accent-start: #6366f1;
                --accent-mid: #d946ef;
                --accent-end: #f97316;
            }
            .gradient-accent { background: linear-gradient(135deg, var(--accent-start), var(--accent-mid), var(--accent-end)); }
            .gradient-accent-alt { background: linear-gradient(135deg, var(--accent-end), var(--accent-mid), var(--accent-start)); }
            .gradient-text { background: linear-gradient(135deg, var(--accent-start), var(--accent-mid), var(--accent-end)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
            .font-serif { font-family: Georgia, 'Times New Roman', serif; }
            .section-border { border-left: 3px solid; border-image: linear-gradient(135deg, var(--accent-start), var(--accent-mid), var(--accent-end)) 1; }
            .card-glow { box-shadow: 0 0 0 1px transparent; transition: box-shadow 0.3s, border-color 0.3s; }
            .card-glow:hover { box-shadow: 0 0 30px -5px rgba(99, 102, 241, 0.3), 0 0 30px -5px rgba(217, 70, 239, 0.2); }
            @media (prefers-color-scheme: dark) {
                .card-glow:hover { box-shadow: 0 0 30px -5px rgba(99, 102, 241, 0.4), 0 0 30px -5px rgba(217, 70, 239, 0.3); }
            }
            /* Sans-serif satu keluarga, beratnya disesuaikan per tempat:
               - serif : heading besar (700) — tetap sans, beda bobot
               - body  : 400 (normal) / 300 (light) untuk kontras visual */
            h1, h2, h3, h4, .font-serif { font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-weight: 700; letter-spacing: -0.025em; }
            body { font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-weight: 400; font-feature-settings: "cv02", "cv03", "cv04", "cv11"; }
            .font-light { font-weight: 300; }
            /* Card text content area - fixed height for aligned separator line */
                    .text-content-area { min-height: 5.5rem; padding: 0 1.25rem; }
                    .text-content-area h3 { line-height: 1.25; }
                    .text-content-area p { line-height: 1.5; }
        </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-white via-indigo-50/40 to-white dark:from-[#0e0e1c] dark:via-[#131328] dark:to-[#0e0e1c]">
    <!-- HEADER -->
    <header class="border-b border-neutral-200 dark:border-neutral-800 bg-white/80 dark:bg-[#0e0e1c]/80 backdrop-blur-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                                        <!-- Logo: switches between dark (white) and light (black) -->
                                        <a href="index.php" class="flex items-center gap-3" aria-label="Radar Bising - Home">
                                            <span class="logo-wrapper" data-dark-src="/radarbising/assets/logo-white.png" data-light-src="/radarbising/assets/logo.png">
                                                <img src="/radarbising/assets/logo.png" alt="<?= e($siteSettings['site_name']) ?>" class="h-10 w-auto logo-img" loading="lazy">
                                            </span>
                                        </a>
                                    </div>
            <nav class="hidden md:flex items-center gap-8 text-sm font-sans">
                                        <a href="index.php" class="font-bold uppercase tracking-wider transition-colors <?= $page === 'home' ? 'text-fuchsia-600 dark:text-fuchsia-400' : 'text-neutral-600 dark:text-neutral-400 hover:text-fuchsia-500' ?>">HOME</a>
                                        <a href="index.php?page=articles" class="font-bold uppercase tracking-wider transition-colors <?= $page === 'articles' ? 'text-fuchsia-600 dark:text-fuchsia-400' : 'text-neutral-600 dark:text-neutral-400 hover:text-fuchsia-500' ?>">ARTIKEL</a>
                                        <a href="index.php?page=videos" class="font-bold uppercase tracking-wider transition-colors <?= $page === 'videos' ? 'text-fuchsia-600 dark:text-fuchsia-400' : 'text-neutral-600 dark:text-neutral-400 hover:text-fuchsia-500' ?>">VIDEO</a>
                                        <a href="index.php?page=exclusive" class="font-bold uppercase tracking-wider transition-colors <?= $page === 'exclusive' ? 'text-fuchsia-600 dark:text-fuchsia-400' : 'text-neutral-600 dark:text-neutral-400 hover:text-fuchsia-500' ?>">EXCLUSIVE</a>
                                    </nav>
            <div class="flex items-center gap-4">
                            <button id="theme-toggle" class="p-2 rounded-lg bg-neutral-100 dark:bg-neutral-800 hover:bg-neutral-200 dark:hover:bg-neutral-700 transition-colors" aria-label="Toggle theme">
                                <svg id="icon-sun" class="w-5 h-5 hidden text-neutral-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <svg id="icon-moon" class="w-5 h-5 text-neutral-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            </button>
                        </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-12">
        <!-- PAGE ROUTING -->
        <?php if ($page === 'home'): ?>
            <!-- HOME PAGE -->
            <?php
            // Headline article (latest headline)
                        $stmtHeadline = $pdo->prepare("SELECT * FROM articles WHERE is_headline = 1 AND is_draft = 0 ORDER BY published_at DESC LIMIT 1");
                        $stmtHeadline->execute();
                        $headlineArticle = $stmtHeadline->fetch();

                        // Articles Terbaru (4 items)
                        $stmtSec = $pdo->prepare("SELECT * FROM articles WHERE is_headline = 0 AND is_draft = 0 ORDER BY published_at DESC LIMIT 4");
                        $stmtSec->execute();
                        $secondaryArticles = $stmtSec->fetchAll();

                        // Videos Pilihan (4 items)
                        $stmtVideos = $pdo->prepare("SELECT * FROM videos WHERE is_draft = 0 ORDER BY published_at DESC LIMIT 4");
                        $stmtVideos->execute();
                        $secondaryVideos = $stmtVideos->fetchAll();
                        ?>

            <!-- 1. HEADLINE ARTICLE -->
                                                <section class="mb-16">
                                                    <div class="flex justify-between items-center mb-8">
                                                        <h2 class="text-2xl sm:text-3xl font-sans uppercase tracking-widest font-bold section-border pl-4 text-neutral-900 dark:text-white">HEADLINE UTAMA</h2>
                                                    </div>
                <?php if ($headlineArticle): ?>
                <a href="index.php?page=article_detail&slug=<?= urlencode($headlineArticle['slug']) ?>" class="group block">
                    <div class="aspect-video overflow-hidden rounded-3xl mb-6 bg-neutral-200 dark:bg-neutral-900 relative">
                        <img src="<?= e($headlineArticle['image']) ?>" alt="<?= e($headlineArticle['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black/80 via-black/40 to-transparent">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-white text-xs font-bold uppercase <?= $catColor($headlineArticle['category']) ?>"><?= e($headlineArticle['category']) ?></span>
                            <h1 class="text-3xl sm:text-5xl font-bold uppercase tracking-tight mt-3 text-white group-hover:text-fuchsia-300 transition-colors"><?= e($headlineArticle['title']) ?></h1>
                        </div>
                    </div>
                    <p class="text-neutral-600 dark:text-neutral-400 text-lg leading-relaxed max-w-3xl"><?= e($headlineArticle['excerpt']) ?></p>
                </a>
                <?php else: ?>
                <div class="text-center py-20">
                    <p class="font-bold text-xl text-neutral-500">Belum ada headline artikel.</p>
                </div>
                <?php endif; ?>
            </section>

            <!-- 2. ARTIKEL TERBARU (4 items) -->
                                                            <section>
                                                                <h2 class="text-2xl sm:text-3xl font-sans uppercase tracking-widest font-bold section-border pl-4 mb-8 text-neutral-900 dark:text-white">ARTIKEL TERBARU</h2>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                                            <?php foreach ($secondaryArticles as $index => $art): ?>
                                                                                                                            <a href="index.php?page=article_detail&slug=<?= urlencode($art['slug']) ?>" class="group block shadow-[0_2px_8px_rgba(0,0,0,0.08),0_1px_3px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.12),0_4px_12px_rgba(0,0,0,0.08)] transition-shadow duration-300 dark:shadow-[0_4px_16px_rgba(0,0,0,0.4),0_2px_8px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_20px_48px_rgba(0,0,0,0.5),0_8px_24px_rgba(0,0,0,0.4)] dark:shadow-fuchsia-900/20">
                                                                                                                                <div class="aspect-[4/5] overflow-hidden rounded-3xl mb-4 bg-neutral-200 dark:bg-neutral-900">
                                                                                        <img src="<?= e($art['image']) ?>" alt="<?= e($art['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                                                                    </div>
                                                                                    <div class="text-content-area min-h-[5.5rem] mb-4">
                                                                                        <h3 class="text-lg sm:text-xl font-bold uppercase tracking-tight group-hover:text-fuchsia-500 transition-colors text-neutral-900 dark:text-white line-clamp-2 mb-1.5 leading-snug">
                                                                                                                                <?= e($art['title']) ?>
                                                                                                                            </h3>
                                                                                        <p class="text-neutral-600 dark:text-neutral-400 text-sm leading-relaxed line-clamp-2">
                                                                                                                                <?= e($art['excerpt']) ?>
                                                                                                                            </p>
                                                                                    </div>
                                                                                    <div class="flex items-center gap-2 pt-4 pb-4 border-t border-neutral-100 dark:border-neutral-800 text-xs text-neutral-500 px-5">
                                                                                                                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-white text-[10px] font-bold uppercase <?= $catColor($art['category']) ?>"><?= e($art['category']) ?></span>
                                                                                                                                                            <span class="ml-auto flex items-center gap-1">
                                                                                                                                                                <?= e($art['published_at']) ?>
                                                                                                                                                            </span>
                                                                                                                                                        </div>
                                                                                </a>
                                                                            <?php endforeach; ?>
                                                                                            </div>
                                                                                            <!-- Show More centered below grid -->
                                                                                            <div class="text-center mt-10">
                                                                                                <a href="index.php?page=articles" class="inline-flex items-center space-x-2 text-xs font-sans uppercase tracking-widest px-6 py-2.5 border border-neutral-300 text-neutral-700 rounded-full hover:gradient-accent-alt hover:text-white hover:border-fuchsia-400 transition-all">
                                                                                                    <span>Show More</span>
                                                                                                </a>
                                                                                            </div>
                                                                                        </section>

                                                                            <!-- 3. VIDEO PILIHAN (4 items) -->
                                                                                                                                                                <h2 class="text-2xl sm:text-3xl font-sans uppercase tracking-widest font-bold section-border pl-4 mb-8 text-neutral-900 dark:text-white">VIDEO PILIHAN</h2>
                                                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                                                                            <?php foreach ($secondaryVideos as $index => $vid): ?>
                                                                                                                                                                                                                                        <a href="index.php?page=video_detail&slug=<?= urlencode($vid['slug']) ?>" class="group block shadow-[0_2px_8px_rgba(0,0,0,0.08),0_1px_3px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.12),0_4px_12px_rgba(0,0,0,0.08)] transition-shadow duration-300 dark:shadow-[0_4px_16px_rgba(0,0,0,0.4),0_2px_8px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_20px_48px_rgba(0,0,0,0.5),0_8px_24px_rgba(0,0,0,0.4)] dark:shadow-fuchsia-900/20">
                                                                                                                                                                                                                                            <div class="aspect-[4/5] overflow-hidden rounded-3xl mb-4 bg-black relative">
                                                                                                                                                                                                    <img src="<?= e($vid['thumbnail']) ?>" alt="<?= e($vid['title']) ?>" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700">
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <div class="text-content-area min-h-[5.5rem] mb-4">
                                                                                                                                                                                                    <h3 class="text-lg sm:text-xl font-bold uppercase tracking-tight group-hover:text-fuchsia-500 transition-colors text-neutral-900 dark:text-white line-clamp-2 mb-1.5 leading-snug"><?= e($vid['title']) ?></h3>
                                                                                                                                                                                                    <p class="text-neutral-600 dark:text-neutral-400 text-sm leading-relaxed line-clamp-2"><?= e($vid['description']) ?></p>
                                                                                                                                                                                                </div>
                                                                                                                                                                                                <div class="flex items-center gap-2 pt-4 pb-4 border-t border-neutral-100 dark:border-neutral-800 text-xs text-neutral-500 px-5">
                                                                                                                                                                                                                                                                                                                                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-white text-[10px] font-bold uppercase <?= $catColor($vid['category']) ?>"><?= e($vid['category']) ?></span>
                                                                                                                                                                                                                                                                                                                                                                            <span class="ml-auto flex items-center gap-1">
                                                                                                                                                                                                                                                                                                                                                                                <?= e($vid['published_at']) ?>
                                                                                                                                                                                                                                                                                                                                                                            </span>
                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                            </a>
                                                                                                                                                                                        <?php endforeach; ?>
                                                                                                            </div>
                                                                                                            <!-- Show More centered below grid -->
                                                                                                                                                        <div class="text-center mt-10">
                                                                                                                                                            <a href="index.php?page=videos" class="inline-flex items-center space-x-2 text-xs font-sans uppercase tracking-widest px-6 py-2.5 border border-neutral-300 text-neutral-700 rounded-full hover:gradient-accent-alt hover:text-white hover:border-fuchsia-400 transition-all">
                                                                                                                                                                <span>Show More</span>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                        </section>

                                        <?php elseif ($page === 'articles'): ?>
            <!-- PAGE: ARTICLES (FULL) — 6 per page + pagination -->
            <?php
                        $perPageArt = 6;
                        $pageArt = max(1, (int)($_GET['p'] ?? 1));

                        $sql = "SELECT * FROM articles WHERE is_draft = 0";
                        $params = [];
                        if ($catFilter) {
                            $sql .= " AND category = ?";
                            $params[] = $catFilter;
                        }
                        if ($search) {
                            $sql .= " AND (title LIKE ? OR content LIKE ?)";
                            $params[] = "%$search%";
                            $params[] = "%$search%";
                        }
            $stmtCount = $pdo->prepare($sql . " AND 1=1");
            $stmtCount->execute($params);
            $totalArt = (int) $stmtCount->fetchColumn();
            $maxPageArt = max(1, (int) ceil($totalArt / $perPageArt));
            $pageArt = min($pageArt, $maxPageArt);

            $sql .= " ORDER BY published_at DESC LIMIT $perPageArt OFFSET " . (($pageArt - 1) * $perPageArt);
            $stmtAllArt = $pdo->prepare($sql);
            $stmtAllArt->execute($params);
            $allArticles = $stmtAllArt->fetchAll();
            ?>
                        <section class="space-y-8">
                                        <div class="border-b border-neutral-200 dark:border-neutral-800 pb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
                                            <div>
                                                <h1 class="text-2xl sm:text-3xl font-sans uppercase tracking-widest font-bold section-border pl-4 mb-2 text-neutral-900 dark:text-white">
                                                    <?php if ($search): ?>HASIL PENCARIAN: "<?= e($search) ?>"
                                                    <?php else: ?>SEMUA ARTIKEL
                                                    <?php endif; ?>
                                                </h1>
                                            </div>
                                        </div>

                <?php if (!$allArticles): ?>
                                    <div class="text-center py-20">
                                        <p class="font-bold text-xl text-neutral-500 mb-2">Belum ada artikel<?= $catFilter ? ' di kategori ' . e($catFilter) : '' ?><?= $search ? ' yang cocok dengan pencarian' : '' ?>.</p>
                                                                <a href="index.php?page=articles" class="text-xs font-sans uppercase tracking-wider text-fuchsia-600 hover:underline">BACA ARTIKEL MENARIK LAINNYA</a>
                                    </div>
                <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php foreach ($allArticles as $art): ?>
                                                                <a href="index.php?page=article_detail&slug=<?= urlencode($art['slug']) ?>" class="group block shadow-[0_2px_8px_rgba(0,0,0,0.08),0_1px_3px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.12),0_4px_12px_rgba(0,0,0,0.08)] transition-shadow duration-300 dark:shadow-[0_4px_16px_rgba(0,0,0,0.4),0_2px_8px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_20px_48px_rgba(0,0,0,0.5),0_8px_24px_rgba(0,0,0,0.4)] dark:shadow-fuchsia-900/20">
                                                                    <div class="aspect-[4/5] overflow-hidden rounded-3xl mb-4 bg-neutral-200 dark:bg-neutral-900">
                                                    <img src="<?= e($art['image']) ?>" alt="<?= e($art['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                                </div>
                                                <div class="text-content-area min-h-[5.5rem] mb-4">
                                                    <h3 class="text-base sm:text-lg font-bold uppercase tracking-tight group-hover:text-fuchsia-500 leading-snug mb-1.5 transition-colors text-neutral-900 dark:text-white line-clamp-2">
                                                                                <?= e($art['title']) ?>
                                                                            </h3>
                                                    <p class="text-neutral-600 dark:text-neutral-400 text-sm leading-relaxed line-clamp-2">
                                                        <?= e($art['excerpt']) ?>
                                                    </p>
                                                </div>
                                                <div class="flex items-center gap-2 pt-4 pb-4 border-t border-neutral-100 dark:border-neutral-800 text-xs text-neutral-500 px-5">
                                                                                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-white text-[10px] font-bold uppercase <?= $catColor($art['category']) ?>"><?= e($art['category']) ?></span>
                                                                                                                        <span class="ml-auto flex items-center gap-1">
                                                                                                                            <?= e($art['published_at']) ?>
                                                                                                                        </span>
                                                                                                                    </div>
                                            </a>
                                        <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($totalArt > 0): ?>
                                <!-- Pagination Articles -->
                                <?php
                                $artQuery = [];
                                if ($catFilter) $artQuery['cat'] = $catFilter;
                                if ($search) $artQuery['q'] = $search;
                                $artPageUrl = function (int $p) use ($artQuery): string {
                                    $q = ['page' => 'articles', 'p' => $p] + $artQuery;
                                    return 'index.php?' . http_build_query($q);
                                };
                                ?>
                                <div class="flex justify-center items-center space-x-2 pt-8 text-xs font-sans">
                                                                    <?php if ($pageArt > 1): ?>
                                                                        <a href="<?= $artPageUrl($pageArt - 1) ?>" class="px-4 py-2.5 border border-neutral-300 rounded hover:gradient-accent-alt hover:text-white hover:border-fuchsia-400 transition-all">
                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                                                        </a>
                                                                    <?php else: ?>
                                                                        <span class="px-4 py-2.5 border border-neutral-200 rounded text-neutral-400 cursor-not-allowed">
                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                    <?php for ($i = 1; $i <= $maxPageArt; $i++): ?>
                                                                        <a href="<?= $artPageUrl($i) ?>" class="w-10 h-10 flex items-center justify-center border rounded <?= $i === $pageArt ? 'gradient-accent text-white font-bold border-transparent' : 'border-neutral-300 hover:border-fuchsia-400' ?>"><?= $i ?></a>
                                                                    <?php endfor; ?>
                                                                    <?php if ($pageArt < $maxPageArt): ?>
                                                                        <a href="<?= $artPageUrl($pageArt + 1) ?>" class="px-4 py-2.5 border border-neutral-300 rounded hover:gradient-accent-alt hover:text-white hover:border-fuchsia-400 transition-all">
                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                                        </a>
                                                                    <?php else: ?>
                                                                        <span class="px-4 py-2.5 border border-neutral-200 rounded text-neutral-400 cursor-not-allowed">
                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                                        </span>
                                                                    <?php endif; ?>
                                                                </div>
                                <?php endif; ?>
            </section>

        <?php elseif ($page === 'videos'): ?>
            <!-- PAGE: VIDEOS (FULL) — 6 per page + pagination -->
            <?php
            $perPageVid = 6;
            $pageVid = max(1, (int)($_GET['p'] ?? 1));

            $sqlVid = "SELECT * FROM videos WHERE is_draft = 0";
            $paramsVid = [];
            if ($catFilter) {
                $sqlVid .= " AND category = ?";
                $paramsVid[] = $catFilter;
            }
            if ($search) {
                $sqlVid .= " AND (title LIKE ? OR description LIKE ?)";
                $paramsVid[] = "%$search%";
                $paramsVid[] = "%$search%";
            }
            $stmtCount = $pdo->prepare($sqlVid);
            $stmtCount->execute($paramsVid);
            $totalVid = (int) $stmtCount->fetchColumn();
            $maxPageVid = max(1, (int) ceil($totalVid / $perPageVid));
            $pageVid = min($pageVid, $maxPageVid);

            $sqlVid .= " ORDER BY published_at DESC LIMIT $perPageVid OFFSET " . (($pageVid - 1) * $perPageVid);
            $stmtAllVid = $pdo->prepare($sqlVid);
            $stmtAllVid->execute($paramsVid);
            $allVideos = $stmtAllVid->fetchAll();
            ?>
                        <section class="space-y-8">
                                        <div class="border-b border-neutral-200 dark:border-neutral-800 pb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
                                            <div>
                                                <h1 class="text-2xl sm:text-3xl font-sans uppercase tracking-widest font-bold section-border pl-4 mb-2 text-neutral-900 dark:text-white">
                                                    <?php if ($search): ?>HASIL PENCARIAN: "<?= e($search) ?>"
                                                    <?php else: ?>KUMPULAN VIDEO
                                                    <?php endif; ?>
                                                </h1>
                                            </div>
                                        </div>

                <?php if (!$allVideos): ?>
                                    <div class="text-center py-20">
                                                                            <p class="font-bold text-xl text-neutral-500 mb-2">Belum ada video<?= $catFilter ? ' di kategori ' . e($catFilter) : '' ?><?= $search ? ' yang cocok dengan pencarian' : '' ?>.</p>
                                                                            <a href="index.php?page=videos" class="text-xs font-sans uppercase tracking-wider text-fuchsia-600 hover:underline">LIHAT VIDEO MENARIK LAINNYA</a>
                                                                        </div>
                <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php foreach ($allVideos as $vid): ?>
                                                                                    <a href="index.php?page=video_detail&slug=<?= urlencode($vid['slug']) ?>" class="group block shadow-[0_2px_8px_rgba(0,0,0,0.08),0_1px_3px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.12),0_4px_12px_rgba(0,0,0,0.08)] transition-shadow duration-300 dark:shadow-[0_4px_16px_rgba(0,0,0,0.4),0_2px_8px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_20px_48px_rgba(0,0,0,0.5),0_8px_24px_rgba(0,0,0,0.4)] dark:shadow-fuchsia-900/20">
                                                                                        <div class="aspect-[4/5] overflow-hidden rounded-3xl mb-4 bg-black relative">
                                                                        <img src="<?= e($vid['thumbnail']) ?>" alt="<?= e($vid['title']) ?>" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700">
                                                                    </div>
                                                                    <div class="text-content-area min-h-[5.5rem] mb-4">
                                                                        <h3 class="text-base sm:text-lg font-bold uppercase tracking-tight group-hover:text-fuchsia-500 transition-colors text-neutral-900 dark:text-white line-clamp-2 mb-1.5 leading-snug"><?= e($vid['title']) ?></h3>
                                                                        <p class="text-neutral-600 dark:text-neutral-400 text-sm leading-relaxed line-clamp-2"><?= e($vid['description']) ?></p>
                                                                    </div>
                                                                    <div class="flex items-center gap-2 pt-4 pb-4 border-t border-neutral-100 dark:border-neutral-800 text-xs text-neutral-500 px-5">
                                                                                                                                                                                                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-white text-[10px] font-bold uppercase <?= $catColor($vid['category']) ?>"><?= e($vid['category']) ?></span>
                                                                                                                                                                                                                                                <span class="ml-auto flex items-center gap-1">
                                                                                                                                                                                                                                                    <?= e($vid['published_at']) ?>
                                                                                                                                                                                                                                                </span>
                                                                                                                                                                                                                                            </div>
                                                                </a>
                                                            <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($totalVid > 0): ?>
                <!-- Pagination -->
                <?php
                $vidQuery = [];
                if ($catFilter) $vidQuery['cat'] = $catFilter;
                if ($search) $vidQuery['q'] = $search;
                $vidPageUrl = function (int $p) use ($vidQuery): string {
                    $q = ['page' => 'videos', 'p' => $p] + $vidQuery;
                    return 'index.php?' . http_build_query($q);
                };
                ?>
                <div class="flex justify-center items-center space-x-2 pt-8 text-xs font-sans">
                                <?php if ($pageVid > 1): ?>
                                    <a href="<?= $vidPageUrl($pageVid - 1) ?>" class="px-4 py-2.5 border border-neutral-300 rounded hover:gradient-accent-alt hover:text-white hover:border-fuchsia-400 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </a>
                                <?php else: ?>
                                    <span class="px-4 py-2.5 border border-neutral-200 rounded text-neutral-400 cursor-not-allowed">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                    </span>
                                <?php endif; ?>
                                <?php for ($i = 1; $i <= $maxPageVid; $i++): ?>
                                    <a href="<?= $vidPageUrl($i) ?>" class="w-10 h-10 flex items-center justify-center border rounded <?= $i === $pageVid ? 'gradient-accent text-white font-bold border-transparent' : 'border-neutral-300 hover:border-fuchsia-400' ?>"><?= $i ?></a>
                                <?php endfor; ?>
                                <?php if ($pageVid < $maxPageVid): ?>
                                    <a href="<?= $vidPageUrl($pageVid + 1) ?>" class="px-4 py-2.5 border border-neutral-300 rounded hover:gradient-accent-alt hover:text-white hover:border-fuchsia-400 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                <?php else: ?>
                                    <span class="px-4 py-2.5 border border-neutral-200 rounded text-neutral-400 cursor-not-allowed">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </span>
                                <?php endif; ?>
                            </div>
                <?php endif; ?>
            </section>

        <?php elseif ($page === 'article_detail'): ?>
            <!-- ARTICLE DETAIL -->
            <?php
            $stmtDetail = $pdo->prepare("SELECT * FROM articles WHERE slug = ? AND is_draft = 0");
            $stmtDetail->execute([$slug]);
            $artDetail = $stmtDetail->fetch();
            ?>
            <?php if ($artDetail): ?>
                <?php
                // Artikel lainnya (maks 3, bukan artikel ini)
                                $stmtMore = $pdo->prepare("SELECT * FROM articles WHERE id != ? AND is_draft = 0 ORDER BY published_at DESC LIMIT 3");
                                $stmtMore->execute([$artDetail['id']]);
                                $moreArticles = $stmtMore->fetchAll();
                ?>
                <article class="max-w-3xl mx-auto space-y-8">
                                    <div>
                                        <div class="flex items-center space-x-3 text-xs font-sans uppercase text-neutral-500 mb-4">
                                            <a href="index.php?page=articles&cat=<?= urlencode($artDetail['category']) ?>" class="gradient-accent text-white px-2.5 py-1 rounded font-bold hover:opacity-80 transition-opacity"><?= e($artDetail['category']) ?></a>
                                            <span>•</span>
                                            <span><?= e($artDetail['published_at']) ?></span>
                                            <span>•</span>
                                            <span>Oleh <?= e($artDetail['author']) ?></span>
                                        </div>
                                        <h1 class="text-3xl sm:text-5xl font-bold uppercase tracking-tight leading-tight gradient-text">
                                            <?= e($artDetail['title']) ?>
                                        </h1>
                                    </div>

                                    <div class="aspect-video rounded overflow-hidden bg-neutral-200 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800">
                                        <img src="<?= e($artDetail['image']) ?>" alt="<?= e($artDetail['title']) ?>" class="w-full h-full object-cover">
                                    </div>

                                    <p class="text-xl font-bold italic text-neutral-700 dark:text-neutral-300 border-l-4 border-fuchsia-500 pl-4 py-1">
                                        <?= e($artDetail['excerpt']) ?>
                                    </p>

                                    <div class="prose prose-neutral dark:prose-invert max-w-none text-neutral-800 dark:text-neutral-200 leading-relaxed space-y-6 text-lg font-light">
                                        <?= $artDetail['content'] ?>
                                    </div>

                                    <div class="pt-8 border-t border-neutral-200 flex justify-between font-sans text-xs">
                                                                                                                <a href="index.php?page=articles" class="text-neutral-700 hover:text-fuchsia-500 font-bold">BACA ARTIKEL MENARIK LAINNYA</a>
                                                                                                            </div>
                                </article>

                <?php if ($moreArticles): ?>
                <section class="max-w-5xl mx-auto pt-4">
                    <h2 class="text-xl font-sans uppercase tracking-widest font-bold section-border pl-3 mb-8 text-neutral-900 dark:text-white">ARTIKEL LAINNYA</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($moreArticles as $art): ?>
                                                                            <a href="index.php?page=article_detail&slug=<?= urlencode($art['slug']) ?>" class="group block shadow-[0_2px_8px_rgba(0,0,0,0.08),0_1px_3px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.12),0_4px_12px_rgba(0,0,0,0.08)] transition-shadow duration-300 dark:shadow-[0_4px_16px_rgba(0,0,0,0.4),0_2px_8px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_20px_48px_rgba(0,0,0,0.5),0_8px_24px_rgba(0,0,0,0.4)] dark:shadow-fuchsia-900/20">
                                                                                <div class="aspect-[4/5] overflow-hidden rounded-3xl mb-4 bg-neutral-200 dark:bg-neutral-900">
                                                            <img src="<?= e($art['image']) ?>" alt="<?= e($art['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                                        </div>
                                                        <div class="text-content-area min-h-[5.5rem] mb-4">
                                                            <h3 class="text-base font-bold uppercase tracking-tight group-hover:text-fuchsia-500 leading-snug mb-1.5 transition-colors text-neutral-900 dark:text-white line-clamp-2">
                                                                                            <?= e($art['title']) ?>
                                                                                        </h3>
                                                            <p class="text-neutral-600 dark:text-neutral-400 text-sm leading-relaxed line-clamp-2">
                                                                <?= e($art['excerpt']) ?>
                                                            </p>
                                                        </div>
                                                        <div class="flex items-center gap-2 pt-4 pb-4 border-t border-neutral-100 dark:border-neutral-800 text-xs text-neutral-500 px-5">
                                                                                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-white text-[10px] font-bold uppercase <?= $catColor($art['category']) ?>"><?= e($art['category']) ?></span>
                                                                                                                                <span class="ml-auto flex items-center gap-1">
                                                                                                                                    <?= e($art['published_at']) ?>
                                                                                                                                </span>
                                                                                                                            </div>
                                                    </a>
                                                <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>
            <?php else: ?>
                                        <div class="text-center py-20">
                                            <p class="font-bold text-2xl text-neutral-500 mb-2">Artikel tidak ditemukan.</p>
                                            <a href="index.php?page=articles" class="text-xs font-sans uppercase tracking-wider text-fuchsia-600 hover:underline">BACA ARTIKEL MENARIK LAINNYA</a>
                                        </div>
                                    <?php endif; ?>

        <?php elseif ($page === 'video_detail'): ?>
            <!-- VIDEO DETAIL -->
            <?php
            $stmtDetail = $pdo->prepare("SELECT * FROM videos WHERE slug = ? AND is_draft = 0");
            $stmtDetail->execute([$slug]);
            $vidDetail = $stmtDetail->fetch();
            ?>
            <?php if ($vidDetail): ?>
                <?php
                // Video lainnya (maks 2, bukan video ini)
                $stmtMore = $pdo->prepare("SELECT * FROM videos WHERE id != ? AND is_draft = 0 ORDER BY published_at DESC LIMIT 2");
                $stmtMore->execute([$vidDetail['id']]);
                $moreVideos = $stmtMore->fetchAll();
                ?>
                <article class="max-w-4xl mx-auto space-y-8">
                                    <div>
                                        <div class="flex items-center space-x-3 text-xs font-sans uppercase text-neutral-500 mb-4">
                                            <a href="index.php?page=videos&cat=<?= urlencode($vidDetail['category']) ?>" class="gradient-accent text-white px-2.5 py-1 rounded font-bold hover:opacity-80 transition-opacity"><?= e($vidDetail['category']) ?></a>
                                            <span>•</span>
                                            <span>Dipublikasikan: <?= e($vidDetail['published_at']) ?></span>
                                        </div>
                                        <h1 class="text-3xl sm:text-4xl font-bold uppercase tracking-tight leading-tight text-neutral-900 dark:text-white">
                                                                                    <?= e($vidDetail['title']) ?>
                                                                                </h1>
                                    </div>

                                    <div class="aspect-video rounded overflow-hidden bg-black border border-neutral-200 dark:border-neutral-800 shadow-xl">
                                        <iframe class="w-full h-full" src="<?= e($vidDetail['embed_url']) ?>" title="<?= e($vidDetail['title']) ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>

                                    <div class="text-neutral-800 dark:text-neutral-200 leading-relaxed space-y-6 text-lg font-light">
                                        <p><?= nl2br(e($vidDetail['description'])) ?></p>
                                    </div>

                                    <div class="pt-8 border-t border-neutral-200 flex justify-between font-sans text-xs">
                                                                                                                <a href="index.php?page=videos" class="text-neutral-700 hover:text-fuchsia-500 font-bold">LIHAT VIDEO MENARIK LAINNYA</a>
                                                                                                            </div>
                                </article>

                <?php if ($moreVideos): ?>
                <section class="max-w-5xl mx-auto pt-4">
                    <h2 class="text-xl font-sans uppercase tracking-widest font-bold section-border pl-3 mb-8 text-neutral-900 dark:text-white">VIDEO LAINNYA</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                        <?php foreach ($moreVideos as $vid): ?>
                                                    <a href="index.php?page=video_detail&slug=<?= urlencode($vid['slug']) ?>" class="group block shadow-[0_2px_8px_rgba(0,0,0,0.08),0_1px_3px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.12),0_4px_12px_rgba(0,0,0,0.08)] transition-shadow duration-300 dark:shadow-[0_4px_16px_rgba(0,0,0,0.4),0_2px_8px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_20px_48px_rgba(0,0,0,0.5),0_8px_24px_rgba(0,0,0,0.4)] dark:shadow-fuchsia-900/20">
                                                        <div class="aspect-[4/5] overflow-hidden rounded-3xl mb-4 bg-black relative">
                                                            <img src="<?= e($vid['thumbnail']) ?>" alt="<?= e($vid['title']) ?>" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700">
                                                        </div>
                                                        <div class="text-content-area min-h-[5.5rem] mb-4">
                                                            <h3 class="text-base font-bold uppercase tracking-tight group-hover:text-fuchsia-500 transition-colors text-neutral-900 dark:text-white line-clamp-2 mb-1.5 leading-snug"><?= e($vid['title']) ?></h3>
                                                            <p class="text-neutral-600 dark:text-neutral-400 text-sm leading-relaxed line-clamp-2"><?= e($vid['description']) ?></p>
                                                        </div>
                                                        <div class="flex items-center gap-2 pt-4 pb-4 border-t border-neutral-100 dark:border-neutral-800 text-xs text-neutral-500 px-5">
                                                                                                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-white text-[10px] font-bold uppercase <?= $catColor($vid['category']) ?>"><?= e($vid['category']) ?></span>
                                                                                                                                                <span class="ml-auto flex items-center gap-1">
                                                                                                                                                    <?= e($vid['published_at']) ?>
                                                                                                                                                </span>
                                                                                                                                            </div>
                                                    </a>
                                                <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>
            <?php else: ?>
                                        <div class="text-center py-20">
                                            <p class="font-bold text-2xl text-neutral-500 mb-2">Video tidak ditemukan.</p>
                                            <a href="index.php?page=videos" class="text-xs font-sans uppercase tracking-wider text-fuchsia-600 hover:underline">LIHAT VIDEO MENARIK LAINNYA</a>
                                        </div>
                                    <?php endif; ?>

        <?php elseif ($page === 'exclusive'): ?>
                    <!-- PAGE: EXCLUSIVE INTERVIEW (card grid layout like articles/videos) -->
                    <?php
                    $perPage = 8;
                    $pageExclusive = max(1, (int)($_GET['p'] ?? 1));

                    // Gabungkan artikel & video kategori Wawancara, urut terbaru
                                        $sql = "
                                            SELECT id AS content_id, 'article' AS type, title, category, slug, image AS thumb, excerpt AS summary, published_at FROM articles WHERE category = 'Wawancara' AND is_draft = 0
                                            UNION ALL
                                            SELECT id AS content_id, 'video' AS type, title, category, slug, thumbnail AS thumb, description AS summary, published_at FROM videos WHERE category = 'Wawancara' AND is_draft = 0
                                            ORDER BY published_at DESC
                                        ";
                    $stmtAll = $pdo->query($sql);
                    $allExclusive = $stmtAll->fetchAll();
                    $totalExclusive = count($allExclusive);
                    $maxPage = max(1, (int) ceil($totalExclusive / $perPage));
                    $pageExclusive = min($pageExclusive, $maxPage);
                    $offset = ($pageExclusive - 1) * $perPage;
                    $exclusiveItems = array_slice($allExclusive, $offset, $perPage);
                    ?>
                                        <section class="space-y-8">
                                                                <div class="border-b border-neutral-200 dark:border-neutral-800 pb-6">
                                                                    <h1 class="text-2xl sm:text-3xl font-sans uppercase tracking-widest font-bold section-border pl-4 mb-2 text-neutral-900 dark:text-white">EXCLUSIVE INTERVIEW</h1>
                                                                </div>

                        <?php if (!$exclusiveItems): ?>
                            <div class="text-center py-20">
                                <p class="font-bold text-xl text-neutral-500">Belum ada konten Exclusive Interview.</p>
                            </div>
                        <?php else: ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                            <?php foreach ($exclusiveItems as $item): ?>
                                                                                                                    <?php $href = 'index.php?page=' . ($item['type'] === 'article' ? 'article_detail' : 'video_detail') . '&slug=' . urlencode($item['slug']); ?>
                                                                                                                    <a href="<?= $href ?>" class="group block shadow-[0_2px_8px_rgba(0,0,0,0.08),0_1px_3px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.12),0_4px_12px_rgba(0,0,0,0.08)] transition-shadow duration-300 dark:shadow-[0_4px_16px_rgba(0,0,0,0.4),0_2px_8px_rgba(0,0,0,0.3)] dark:hover:shadow-[0_20px_48px_rgba(0,0,0,0.5),0_8px_24px_rgba(0,0,0,0.4)] dark:shadow-fuchsia-900/20">
                                                                                                                        <div class="aspect-[4/5] overflow-hidden rounded-3xl mb-4 relative">
                                                                                                <img src="<?= e($item['thumb']) ?>" alt="<?= e($item['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                                                                            </div>
                                                                                            <div class="text-content-area min-h-[5.5rem] mb-4">
                                                                                                <h3 class="text-base font-bold uppercase tracking-tight group-hover:text-fuchsia-500 leading-snug mb-1.5 transition-colors text-neutral-900 dark:text-white line-clamp-2"><?= e($item['title']) ?></h3>
                                                                                                <p class="text-neutral-600 dark:text-neutral-400 text-sm leading-relaxed line-clamp-2"><?= e($item['summary']) ?></p>
                                                                                            </div>
                                                                                            <div class="flex items-center gap-2 pt-4 pb-4 border-t border-neutral-100 dark:border-neutral-800 text-xs text-neutral-500 px-5">
                                                                                                                                                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-white text-[10px] font-bold uppercase <?= $catColor($item['category']) ?>"><?= e($item['category']) ?></span>
                                                                                                                                                                                                    <span class="ml-auto flex items-center gap-1">
                                                                                                                                                                                                        <?= e($item['published_at']) ?>
                                                                                                                                                                                                    </span>
                                                                                                                                                                                                </div>
                                                            </a>
                                                        <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($totalExclusive > 0): ?>
                                                <!-- Pagination Exclusive -->
                                                                <div class="flex justify-center items-center space-x-2 pt-8 text-xs font-sans">
                                                                                                                                    <?php if ($pageExclusive > 1): ?>
                                                                                                                                        <a href="index.php?page=exclusive&p=<?= $pageExclusive - 1 ?>" class="px-4 py-2.5 border border-neutral-300 rounded hover:gradient-accent-alt hover:text-white hover:border-fuchsia-400 transition-all">
                                                                                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                                                                                                                        </a>
                                                                                                                                    <?php else: ?>
                                                                                                                                        <span class="px-4 py-2.5 border border-neutral-200 rounded text-neutral-400 cursor-not-allowed">
                                                                                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                                                                                                                        </span>
                                                                                                                                    <?php endif; ?>
                                                                                                                                    <?php for ($i = 1; $i <= $maxPage; $i++): ?>
                                                                                                                                        <a href="index.php?page=exclusive&p=<?= $i ?>" class="w-10 h-10 flex items-center justify-center border rounded <?= $i === $pageExclusive ? 'gradient-accent text-white font-bold border-transparent' : 'border-neutral-300 hover:border-fuchsia-400' ?>"><?= $i ?></a>
                                                                                                                                    <?php endfor; ?>
                                                                                                                                    <?php if ($pageExclusive < $maxPage): ?>
                                                                                                                                        <a href="index.php?page=exclusive&p=<?= $pageExclusive + 1 ?>" class="px-4 py-2.5 border border-neutral-300 rounded hover:gradient-accent-alt hover:text-white hover:border-fuchsia-400 transition-all">
                                                                                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                                                                                                        </a>
                                                                                                                                    <?php else: ?>
                                                                                                                                        <span class="px-4 py-2.5 border border-neutral-200 rounded text-neutral-400 cursor-not-allowed">
                                                                                                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                                                                                                        </span>
                                                                                                                                    <?php endif; ?>
                                                                                                                                </div>
                                                <?php endif; ?>
                    </section>

        <?php endif; ?>
    </main>

    <!-- FOOTER -->
    <footer class="border-t border-neutral-200 dark:border-neutral-800 bg-gradient-to-t from-white via-indigo-50/40 to-white dark:from-[#0e0e1c] dark:via-[#131328] dark:to-[#0e0e1c] py-16 mt-20">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-10 text-sm">
                <div class="space-y-4">
                                    <h4 class="font-bold text-xl tracking-wider uppercase text-neutral-900 dark:text-white"><?= e(strtoupper($siteSettings['site_name'])) ?></h4>
                                    <p class="text-neutral-500 text-xs leading-relaxed">
                                        Media digital independen yang merekam, meresensi, dan merayakan dinamika skena musik serta kultur pop Indonesia terkini.
                                    </p>
                                </div>
                <div class="space-y-4">
                    <h4 class="font-sans font-bold text-xs tracking-widest uppercase text-neutral-400">JELAJAH</h4>
                    <ul class="space-y-2 text-xs font-sans text-neutral-600 dark:text-neutral-400">
                        <li><a href="index.php?page=articles" class="hover:text-fuchsia-500 transition-colors">Semua Artikel</a></li>
                        <li><a href="index.php?page=videos" class="hover:text-fuchsia-500 transition-colors">Semua Video</a></li>
                        <li><a href="index.php?page=exclusive" class="hover:text-fuchsia-500 transition-colors">Exclusive Interview</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h4 class="font-sans font-bold text-xs tracking-widest uppercase text-neutral-400">KATEGORI</h4>
                    <ul class="space-y-2 text-xs font-sans text-neutral-600 dark:text-neutral-400">
                        <li><a href="index.php?page=articles&cat=Ulasan" class="hover:text-fuchsia-500 transition-colors">Ulasan</a></li>
                        <li><a href="index.php?page=articles&cat=Wawancara" class="hover:text-fuchsia-500 transition-colors">Wawancara</a></li>
                        <li><a href="index.php?page=articles&cat=Agenda" class="hover:text-fuchsia-500 transition-colors">Agenda</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h4 class="font-sans font-bold text-xs tracking-widest uppercase text-neutral-400">REDAKSI</h4>
                    <ul class="space-y-2 text-xs font-sans text-neutral-600 dark:text-neutral-400">
                        <li><a href="#" class="hover:text-fuchsia-500 transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-fuchsia-500 transition-colors">Kontak & Kirim Rilisan</a></li>
                        <li><a href="#" class="hover:text-fuchsia-500 transition-colors">Iklan & Kerja Sama</a></li>
                    </ul>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-6 mt-12 pt-6 border-t border-neutral-100 dark:border-neutral-900 flex flex-col sm:flex-row items-center justify-between text-xs font-sans text-neutral-500">
                            <div class="text-neutral-600 dark:text-neutral-400">© <?= date('Y') ?> <?= e($siteSettings['site_name']) ?>. HAK CIPTA DILINDUNGI.</div>
                            <div class="flex space-x-4 mt-2 sm:mt-0">
                                <span class="text-neutral-600 dark:text-neutral-400">JAKARTA, INDONESIA</span>
                            </div>
                        </div>
        </footer>

        <script>
            // Theme toggle - robust implementation
            (function() {
                const toggle = document.getElementById('theme-toggle');
                const sun = document.getElementById('icon-sun');
                const moon = document.getElementById('icon-moon');
                const html = document.documentElement;
                const logoImg = document.querySelector('.logo-img');
                const logoWrapper = document.querySelector('.logo-wrapper');

                // Defensive: exit if critical elements missing
                if (!toggle || !html) {
                    console.warn('Theme toggle: critical elements not found');
                    return;
                }

                function updateIcons() {
                    if (!sun || !moon) return;
                    if (html.classList.contains('dark')) {
                        sun.classList.add('hidden');
                        moon.classList.remove('hidden');
                    } else {
                        sun.classList.remove('hidden');
                        moon.classList.add('hidden');
                    }
                }

                function updateLogo() {
                    if (!logoImg || !logoWrapper) return;
                    const darkSrc = logoWrapper.getAttribute('data-dark-src');
                    const lightSrc = logoWrapper.getAttribute('data-light-src');
                    if (darkSrc && lightSrc) {
                        logoImg.src = html.classList.contains('dark') ? darkSrc : lightSrc;
                    }
                }

                // Sync theme from cookie/localStorage on load (safety net)
                function syncThemeOnLoad() {
                    // Priority: localStorage > cookie > system preference > default (dark)
                    var theme = localStorage.getItem('theme');
                    if (!theme) {
                        var cookieTheme = document.cookie.split('; ').find(function(row) { return row.startsWith('theme='); });
                        if (cookieTheme) theme = cookieTheme.split('=')[1];
                    }
                    if (!theme) {
                        var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                        theme = prefersDark ? 'dark' : 'light';
                    }
                    // Apply theme
                    if (theme === 'light') {
                        html.classList.remove('dark');
                    } else {
                        html.classList.add('dark');
                    }
                }

                // Initialize
                syncThemeOnLoad();
                updateIcons();
                updateLogo();

                // Toggle click handler
                toggle.addEventListener('click', function() {
                    html.classList.toggle('dark');
                    var theme = html.classList.contains('dark') ? 'dark' : 'light';
                    localStorage.setItem('theme', theme);
                    document.cookie = 'theme=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
                    updateIcons();
                    updateLogo();
                });

                // Sync with system preference changes (only if no user preference stored)
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                    if (!localStorage.getItem('theme')) {
                        html.classList.toggle('dark', e.matches);
                        updateIcons();
                        updateLogo();
                    }
                });
            })();
        </script>
    </body>
    </html>