<?php

/**
 * @var string $title
 * @var object $header
 * @var array $results
 * @var string $pageSize
 */
$posts = $results['posts'];
$period = date('d F Y', strtotime($header->date_start)) . ' - ' . date('d F Y', strtotime($header->date_end));
$pageSize = $pageSize ?? 'folio';
$columnMode = $columnMode ?? '1';
$isLandscape = ('2' == $columnMode);

$paperSizes = [
    'folio' => ['width' => 8.5, 'height' => 13, 'unit' => 'in'],
    'a4' => ['width' => 210, 'height' => 297, 'unit' => 'mm'],
    'a5' => ['width' => 148, 'height' => 210, 'unit' => 'mm'],
    'b5' => ['width' => 176, 'height' => 250, 'unit' => 'mm'],
    'letter' => ['width' => 8.5, 'height' => 11, 'unit' => 'in'],
    'executive' => ['width' => 7.25, 'height' => 10.5, 'unit' => 'in']
];
$paperSize = $paperSizes[strtolower($pageSize)] ?? $paperSizes['folio'];
$pageWidth = $isLandscape ? $paperSize['height'] : $paperSize['width'];
$pageHeight = $isLandscape ? $paperSize['width'] : $paperSize['height'];
$sheetSize = number_format($pageWidth, 3, '.', '') . $paperSize['unit'] . ' ' . number_format($pageHeight, 3, '.', '') . $paperSize['unit'];

$margin = $isLandscape ? '36px 40px' : '60px 54px';
if ('a4' === $pageSize) {
    $margin = $isLandscape ? '34px 38px' : '54px 48px';
} elseif ('a5' === $pageSize) {
    $margin = $isLandscape ? '28px 30px' : '44px 38px';
} elseif ('b5' === $pageSize) {
    $margin = $isLandscape ? '32px 34px' : '50px 44px';
} elseif ('letter' === $pageSize) {
    $margin = $isLandscape ? '34px 38px' : '54px 48px';
} elseif ('executive' === $pageSize) {
    $margin = $isLandscape ? '34px 36px' : '54px 48px';
}

$pageHeightIn = 'mm' === $paperSize['unit'] ? ($pageHeight / 25.4) : $pageHeight;
$verticalMargin = $isLandscape ? (
    'a5' === $pageSize ? 56 : ('b5' === $pageSize ? 64 : ('executive' === $pageSize ? 68 : 72))
) : (
    'a5' === $pageSize ? 88 : ('b5' === $pageSize ? 100 : ('executive' === $pageSize ? 108 : 120))
);
$coverHeight = max(1, $pageHeightIn - ($verticalMargin / 96));

$clean = static function ($content) {
    $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', (string) $content);
    $content = preg_replace('/(width|height)="\d*"\s?/i', '', $content);
    $content = str_replace('MsoNormalTable', 'table table-bordered', $content);
    $content = preg_replace('~<p[^>]*>~i', '<p>', $content);
    $content = preg_replace('/<img[^>]*src="(.*?)"[^>]*>/i', '<figure><img src="$1" class="article-image" /></figure>', $content);

    return $content;
};

$author = static function ($post) {
    $name = trim(($post->first_name ?? '') . ' ' . ($post->last_name ?? ''));

    return $name ?: ($post->username ?: '-');
};

$getFeatured = static function ($post) {
    if (! empty($post->featured_image) && 'placeholder.png' !== $post->featured_image) {
        return get_image('blogs', $post->featured_image);
    }

    return null;
};
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title); ?></title>
    <link rel="icon" type="image/x-icon" href="<?= get_image('settings', get_setting('app_icon'), 'icon'); ?>" />
    <style type="text/css">
        @page {
            footer: html_footer;
            sheet-size: <?= $sheetSize; ?>;
            margin: <?= $margin; ?>;
        }

        body {
            margin: 0;
            color: #111;
            font-family: 'bookos', Georgia, serif;
            font-size: <?= $isLandscape ? '11.5pt' : '12pt'; ?>;
            line-height: 1.55;
            background: #fff;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: Arial, Helvetica, sans-serif;
            page-break-after: avoid;
        }

        h1 {
            font-size: <?= $isLandscape ? '21pt' : '26pt'; ?>;
        }

        h2 {
            font-size: <?= $isLandscape ? '16pt' : '20pt'; ?>;
        }

        h3 {
            font-size: <?= $isLandscape ? '13.5pt' : '16pt'; ?>;
        }

        p {
            margin: 0 0 8px;
        }

        a {
            color: #111;
            text-decoration: none;
            text-transform: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            vertical-align: top;
            padding: 5px;
        }

        /* Cover Page */
        .cover {
            text-align: center;
            page-break-inside: avoid;
            height: <?= number_format($coverHeight, 3, '.', ''); ?>in;
        }

        .cover-table {
            height: <?= number_format($coverHeight, 3, '.', ''); ?>in;
            page-break-inside: avoid;
        }

        .cover-row,
        .cover-cell {
            height: <?= number_format($coverHeight, 3, '.', ''); ?>in;
        }

        .cover-cell {
            vertical-align: middle;
        }

        .cover-title {
            font-size: <?= $isLandscape ? '26pt' : '34pt'; ?>;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            line-height: 1.1;
            margin-bottom: 10px;
            color: #111;
        }

        .cover-subtitle {
            font-family: 'bookos', Georgia, serif;
            font-size: <?= $isLandscape ? '11pt' : '14pt'; ?>;
            color: #4f463c;
            font-style: italic;
        }

        .divider {
            border-top: 3px solid #111;
            border-bottom: 1px solid #111;
            padding: 1px;
            margin: 18px 0;
        }

        .page-break {
            page-break-before: always;
        }

        /* Table of Contents */
        .toc {
            margin-top: 14px;
        }

        .toc tr {
            page-break-inside: avoid;
        }

        .toc td {
            padding: 6px 4px;
        }

        .mpdf_toc {
            background: #fff;
        }

        .mpdf_toc_level_0 {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.45;
            padding: 6px 0;
            padding-left: 2em;
            text-indent: -2em;
            background: #fff;
        }

        .mpdf_toc_t_level_0 {
            font-weight: bold;
        }

        .mpdf_toc_p_level_0 {
            font-weight: bold;
        }

        .mpdf_toc_a {
            color: #111;
            text-decoration: none;
        }

        .meta {
            color: #666;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .text-center {
            text-align: center;
        }

        /* Centered Chapter Header */
        .chapter-header {
            text-align: center;
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .chapter-header .chapter-badge {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #b71c1c;
            margin-bottom: 4px;
        }

        .chapter-header h1 {
            font-family: Georgia, "bookos", serif;
            font-size: <?= $isLandscape ? '21pt' : '26pt'; ?>;
            margin: 4px 0 8px;
            line-height: 1.2;
            page-break-after: avoid;
        }

        .chapter-header .header-divider {
            width: 50px;
            height: 2px;
            background: #111;
            margin: 10px auto 0;
        }

        .featured {
            width: 100%;
            object-fit: cover;
            margin: 10px 0 14px;
            page-break-inside: avoid;
        }

        .article-image,
        figure {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
            page-break-inside: avoid;
        }

        .excerpt,
        blockquote {
            font-size: 11.5pt;
            line-height: 1.5;
            color: #4f463c;
            border-left: 4px solid #b71c1c;
            background: #f5efe4;
            padding: 8px 12px;
            margin: 10px 0 14px;
            page-break-inside: avoid;
        }

        .tags {
            margin-top: 12px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            page-break-inside: avoid;
        }

        .tag-chip {
            display: inline-block;
            margin-right: 4px;
            padding: 2px 7px;
            background-color: #f0e9dd;
            color: #b71c1c;
            font-size: 7.5pt;
            font-weight: bold;
            line-height: 1;
            text-transform: uppercase;
            white-space: nowrap;
            border-radius: 3px;
        }

        /* Modern Premium Footer Styling */
        .footer-table {
            width: 100%;
            border-top: 1px solid #b71c1c;
            padding-top: 8px;
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7.5pt;
        }

        .footer-table td {
            padding: 0;
            border: 0;
            vertical-align: middle;
        }

        .footer-brand {
            font-weight: 800;
            color: #111;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .footer-dot {
            color: #b71c1c;
            font-weight: bold;
            margin: 0 4px;
        }

        .footer-title {
            color: #555;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .footer-pill-ch {
            display: inline-block;
            background-color: #b71c1c;
            color: #ffffff;
            font-weight: bold;
            font-size: 6.5pt;
            letter-spacing: 0.8px;
            padding: 2px 7px;
            border-radius: 3px;
            text-transform: uppercase;
            margin-right: 4px;
        }

        .footer-pill-pg {
            display: inline-block;
            background-color: #111111;
            color: #ffffff;
            font-weight: bold;
            font-size: 6.5pt;
            letter-spacing: 0.8px;
            padding: 2px 7px;
            border-radius: 3px;
            text-transform: uppercase;
        }

        .article-body {
            text-align: justify;
        }

        .article-body p {
            margin: 0 0 8px;
            text-indent: 1.5em;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px auto;
        }
        .table td {
            padding: 3px
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <?php if ($isLandscape): ?>
        <!-- 2 COLUMNS LANDSCAPE BOOKLET COVER -->
        <section class="cover">
            <table class="cover-table" cellpadding="0" cellspacing="0">
                <tr class="cover-row" style="height:<?= number_format($coverHeight, 3, '.', ''); ?>in;">
                    <td class="cover-cell" width="50%" height="<?= number_format($coverHeight, 3, '.', ''); ?>in" style="height:<?= number_format($coverHeight, 3, '.', ''); ?>in; border-right:1px dashed #aaa; vertical-align:middle; text-align:center;">
                        <div style="font-style:italic; font-size:16pt; color:#444; line-height:1.6">
                            "<?= phrase('{{app_name}} Blog Compilation', ['app_name' => get_setting('app_name')]); ?> &middot; <?= phrase('Curated stories, published articles, and insights.'); ?>"
                        </div>
                        <div class="divider" style="margin:20px 30px;"></div>
                        <br />
                        <p class="meta" style="font-size:8pt; color:#777;">
                            <?= phrase('Official Report') ?><br>
                            <?= htmlspecialchars($period); ?>
                        </p>
                    </td>
                    <td class="cover-cell" height="<?= number_format($coverHeight, 3, '.', ''); ?>in" style="height:<?= number_format($coverHeight, 3, '.', ''); ?>in; border-left:1px dashed #aaa; vertical-align:middle; text-align:center;">
                        <div>
                            <img src="<?= get_image('settings', get_setting('app_icon'), 'thumb'); ?>" alt="<?= phrase('Application Icon'); ?>" width="84" />
                        </div>
                        <h1 class="cover-title"><?= htmlspecialchars(get_setting('app_name')); ?></h1>
                        <h2 style="font-family: 'bookos', Georgia, serif; font-size:36pt; font-weight:bold; margin:16px 0; color:#111;"><?= htmlspecialchars($title); ?></h2>
                        <?php if (! empty($header->category) || ! empty($header->author)): ?>
                            <table class="table">
                                <?php if (! empty($header->category)): ?>
                                <tr>
                                    <td class="text-right" width="49%"><?= phrase('Category') ?></td>
                                    <td width="2%">:</td>
                                    <td class="text-left"><?= htmlspecialchars($header->category) ?></td>
                                </tr>
                                <?php endif; ?>

                                <?php if (! empty($header->author)): ?>
                                <tr>
                                    <td class="text-right" width="49%"><?= phrase('Author') ?></td>
                                    <td width="2%">:</td>
                                    <td class="text-left"><?= htmlspecialchars($header->author) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        <?php endif; ?>

                        <p class="cover-subtitle" style="font-size:11pt;"><?= htmlspecialchars($period); ?></p>
                        <div class="divider" style="margin:14px 0;"></div>
                        <p class="meta" style="font-size:8.5pt;">
                            <?= number_format(count($posts)); ?> <?= phrase('articles'); ?>
                            &middot;
                            <?= phrase('Generated at') . ' ' . date('d F Y', strtotime($header->generated_at)); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </section>

        <!-- TABLE OF CONTENTS (2 COLUMNS LANDSCAPE) -->
        <tocpagebreak
            links="on"
            paging="on"
            outdent="1.2em"
            toc-prehtml="<?= htmlspecialchars('<h2 style="border-bottom:2px solid #111; padding-bottom:4px; margin-top:0; margin-bottom:12px; text-align:left; font-size:14pt; background:#fff;">' . phrase('Table of Contents') . '</h2><columns column-count="2" column-gap="10" />', ENT_QUOTES); ?>"
            toc-posthtml="<?= htmlspecialchars('<columns column-count="1" />', ENT_QUOTES); ?>"
        />

        <!-- CHAPTERS (2 COLUMNS LANDSCAPE BOOKLET) -->
        <?php foreach ($posts as $key => $post): ?>
            <?php $featured = $getFeatured($post); ?>
            <htmlpagefooter name="footer_ch_l_<?= $key + 1; ?>">
                <table class="footer-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="text-align:left;">
                            <span class="footer-brand"><?= htmlspecialchars(get_setting('app_name')) ?></span>
                            <span class="footer-dot">&bull;</span>
                            <span class="footer-title"><?= htmlspecialchars($title) ?></span>
                        </td>
                        <td style="text-align:right;">
                            <span class="footer-pill-ch">CHAPTER <?= sprintf('%02d', $key + 1); ?> OF <?= sprintf('%02d', count($posts)); ?></span>
                            <span class="footer-pill-pg">PAGE {PAGENO} OF {nbpg}</span>
                        </td>
                    </tr>
                </table>
            </htmlpagefooter>
            <sethtmlpagefooter name="footer_ch_l_<?= $key + 1; ?>" value="on" />

            <article class="page-break">
                <columns column-count="2" column-gap="8" />
                <tocentry content="<?= htmlspecialchars(sprintf('%02d. %s', $key + 1, $post->title), ENT_QUOTES); ?>" level="0" />

                <div class="chapter-header">
                    <div class="chapter-badge">&mdash; <?= phrase('Chapter') . ' ' . sprintf('%02d', $key + 1); ?> &mdash;</div>
                    <h1><?= htmlspecialchars($post->title); ?></h1>
                    <p class="meta">
                        <?= htmlspecialchars($post->category ?: phrase('Uncategorized')); ?>
                        &middot;
                        <?= date('d F Y', strtotime($post->created_at)); ?>
                        &middot;
                        <?= phrase('By') . ' ' . htmlspecialchars($author($post)); ?>
                    </p>
                    <div class="header-divider"></div>
                </div>

                <?php if ($featured): ?>
                    <img src="<?= $featured; ?>" alt="<?= htmlspecialchars($post->title); ?>" class="featured" />
                <?php endif; ?>

                <?php if ($post->excerpt): ?>
                    <div class="excerpt"><?= htmlspecialchars($post->excerpt); ?></div>
                <?php endif; ?>

                <div class="article-body">
                    <?= $clean($post->content); ?>

                    <?php if ($post->tags): ?>
                        <div class="tags">
                            <strong><?= phrase('Tags') ?>:</strong>
                            <?php foreach (array_filter(array_map('trim', explode(',', (string) $post->tags))) as $tag): ?>
                                <span class="tag-chip">&nbsp;<?= htmlspecialchars($tag) ?>&nbsp;</span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- 1 COLUMN PORTRAIT STANDARD COVER -->
        <section class="cover">
            <table class="cover-table" cellpadding="0" cellspacing="0">
                <tr class="cover-row" style="height:<?= number_format($coverHeight, 3, '.', ''); ?>in;">
                    <td class="cover-cell" height="<?= number_format($coverHeight, 3, '.', ''); ?>in" style="height:<?= number_format($coverHeight, 3, '.', ''); ?>in; vertical-align:middle;">
                        <div>
                            <img src="<?= get_image('settings', get_setting('app_icon'), 'thumb'); ?>" alt="<?= phrase('Application Icon'); ?>" width="84" />
                        </div>
                        <h1 class="cover-title"><?= htmlspecialchars(get_setting('app_name')); ?></h1>
                        <h2><?= htmlspecialchars($title); ?></h2>
                        <?php if (! empty($header->category) || ! empty($header->author)): ?>
                            <div style="margin:12px 0;">
                                <?php if (! empty($header->category)): ?>
                                    <span style="display:inline-block; padding:4px 12px; font-size:10pt; font-weight:bold; color:#b71c1c; border:1px solid #b71c1c; border-radius:4px; text-transform:uppercase; letter-spacing:1px; margin:3px;"><?= phrase('Category') . ': ' . htmlspecialchars($header->category); ?></span>
                                <?php endif; ?>

                                <?php if (! empty($header->author)): ?>
                                    <span style="display:inline-block; padding:4px 12px; font-size:10pt; font-weight:bold; color:#333; border:1px solid #888; border-radius:4px; margin:3px;"><?= phrase('Author') . ': ' . htmlspecialchars($header->author); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <p class="cover-subtitle"><?= htmlspecialchars($period); ?></p>
                        <div class="divider"></div>
                        <p class="meta">
                            <?= number_format(count($posts)); ?> <?= phrase('articles'); ?>
                            &middot;
                            <?= phrase('Generated at') . ' ' . date('d F Y - H:i:s', strtotime($header->generated_at)); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </section>

        <!-- TABLE OF CONTENTS (1 COLUMN PORTRAIT) -->
        <tocpagebreak
            links="on"
            paging="on"
            outdent="2em"
            toc-prehtml="<?= htmlspecialchars('<h2 style="border-bottom:2px solid #111; padding-bottom:6px; background:#fff;">' . phrase('Table of Contents') . '</h2>', ENT_QUOTES); ?>"
        />

        <!-- CHAPTERS (1 COLUMN PORTRAIT) -->
        <?php foreach ($posts as $key => $post): ?>
            <?php $featured = $getFeatured($post); ?>
            <htmlpagefooter name="footer_ch_p_<?= $key + 1; ?>">
                <table class="footer-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="text-align:left;">
                            <span class="footer-brand"><?= htmlspecialchars(get_setting('app_name')) ?></span>
                            <span class="footer-dot">&bull;</span>
                            <span class="footer-title"><?= htmlspecialchars($title) ?></span>
                        </td>
                        <td style="text-align:right;">
                            <span class="footer-pill-ch">CHAPTER <?= sprintf('%02d', $key + 1); ?> OF <?= sprintf('%02d', count($posts)); ?></span>
                            <span class="footer-pill-pg">PAGE {PAGENO} OF {nbpg}</span>
                        </td>
                    </tr>
                </table>
            </htmlpagefooter>
            <sethtmlpagefooter name="footer_ch_p_<?= $key + 1; ?>" value="on" />

            <article class="page-break">
                <tocentry content="<?= htmlspecialchars(sprintf('%02d. %s', $key + 1, $post->title), ENT_QUOTES); ?>" level="0" />
                <div class="chapter-header">
                    <div class="chapter-badge">&mdash; <?= phrase('Chapter') . ' ' . sprintf('%02d', $key + 1); ?> &mdash;</div>
                    <h1><?= htmlspecialchars($post->title); ?></h1>
                    <p class="meta">
                        <?= htmlspecialchars($post->category ?: phrase('Uncategorized')); ?>
                        &middot;
                        <?= date('d F Y', strtotime($post->created_at)); ?>
                        &middot;
                        <?= phrase('By') . ' ' . htmlspecialchars($author($post)); ?>
                    </p>
                    <div class="header-divider"></div>
                </div>

                <?php if ($featured): ?>
                    <img src="<?= $featured; ?>" alt="<?= htmlspecialchars($post->title); ?>" class="featured" />
                <?php endif; ?>

                <?php if ($post->excerpt): ?>
                    <div class="excerpt"><?= htmlspecialchars($post->excerpt); ?></div>
                <?php endif; ?>

                <div class="article-body">
                    <?= $clean($post->content); ?>

                    <?php if ($post->tags): ?>
                        <div class="tags">
                            <strong><?= phrase('Tags') ?>:</strong>
                            <?php foreach (array_filter(array_map('trim', explode(',', (string) $post->tags))) as $tag): ?>
                                <span class="tag-chip">&nbsp;<?= htmlspecialchars($tag) ?>&nbsp;</span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>

    <htmlpagefooter name="footer">
        <table class="footer-table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="text-align:left;">
                    <span class="footer-brand"><?= htmlspecialchars(get_setting('app_name')) ?></span>
                    <span class="footer-dot">&bull;</span>
                    <span class="footer-title"><?= htmlspecialchars($title) ?></span>
                </td>
                <td style="text-align:right;">
                    <span class="footer-pill-pg">PAGE {PAGENO} OF {nbpg}</span>
                </td>
            </tr>
        </table>
    </htmlpagefooter>
</body>

</html>
