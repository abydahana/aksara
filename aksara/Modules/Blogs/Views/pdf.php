<?php

/**
 * @var mixed $post
 * @var string $title
 * @var string $qrcode
 */
$authorName = trim(($post->first_name ?? '') . ' ' . ($post->last_name ?? ''));
$authorName = $authorName ?: ($post->username ?? phrase('Author'));
$categorySlug = strtolower((string) $post->category_slug);
$postSlug = strtolower((string) $post->post_slug);
$postUrl = base_url('blogs/' . $categorySlug . '/' . $postSlug);
$categoryUrl = base_url('blogs/' . $categorySlug);
$authorUrl = $post->username ? base_url('user/' . $post->username) : null;
$publishedAt = $post->created_at ? phrase(date('l', strtotime($post->created_at))) . ', ' . date('d F Y H:i', strtotime($post->created_at)) : '-';
$updatedAt = $post->updated_at ? phrase(date('l', strtotime($post->updated_at))) . ', ' . date('d F Y H:i', strtotime($post->updated_at)) : null;
$featuredImage = $post->featured_image && 'placeholder.png' != $post->featured_image ? get_image('blogs', $post->featured_image) : null;
$coverImage = $featuredImage ?: ($post->category_image && 'placeholder.png' != $post->category_image ? get_image('blogs', $post->category_image) : null);
$authorImage = get_image('users', $post->photo, 'thumb');
$tags = array_filter(array_map('trim', explode(',', (string) $post->post_tags)));

$cleanContent = static function ($content) {
    $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', (string) $content);
    $content = preg_replace('/(width|height)="\d*"\s?/i', '', $content);
    $content = str_replace('MsoNormalTable', 'table table-bordered', $content);
    $content = preg_replace('~<p[^>]*>~i', '<p>', $content);
    $content = preg_replace('/<img[^>]*src="(.*?)"[^>]*>/i', '<figure><img src="$1" class="article-image" /></figure>', $content);

    return $content;
};

$content = $cleanContent($post->post_content);
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        @page {
            footer: html_footer;
        }

        body {
            margin: 0;
            color: #211f1c;
            font-family: "bookos", Georgia, serif;
            font-size: 14pt;
            line-height: 1.52;
            background: #fffdf8;
        }

        .masthead {
            border-top: 5px solid #111;
            border-bottom: 2px solid #111;
            padding: 10px 0 8px;
            text-align: center;
            letter-spacing: 2px;
        }

        .masthead-title {
            font-family: 'bookos', Helvetica, sans-serif;
            font-size: 20pt;
            font-weight: 800;
        }

        .masthead-meta {
            margin-top: 4px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7.5pt;
            letter-spacing: 1px;
            color: #69635a;
        }

        h1 {
            margin: 10px 0 8px;
            font-size: 34pt;
            line-height: .96;
            letter-spacing: 0;
        }

        .deck {
            margin: 0 0 14px;
            color: #4f463c;
            font-size: 14pt;
            line-height: 1.35;
            font-style: italic;
        }

        .byline {
            border-top: 1px solid #b8aa98;
            border-bottom: 1px solid #b8aa98;
            padding: 8px 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            text-transform: uppercase;
            page-break-inside: avoid;
        }

        .byline table {
            width: 100%;
            border-collapse: collapse;
        }

        .byline td {
            vertical-align: middle;
        }

        .byline .author-line,
        .byline .date-line {
            border: 0;
            padding: 0;
        }

        .byline .date-line {
            padding-top: 3px;
        }

        .byline .date-value {
            white-space: nowrap;
        }

        .author-link {
            color: #211f1c;
            text-decoration: none;
        }

        a {
            color: #211f1c;
            text-decoration: none;
            text-transform: none;
        }

        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
        }

        .cover {
            width: 100%;
            margin: 16px 0 12px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .cover td {
            border: 0;
            padding: 0;
        }

        .cover-image {
            width: 100%;
            height: auto;
        }

        .cover-caption {
            border-top: 0;
            border-bottom: 5px solid #111;
            padding: 6px 0 8px;
            color: #6f675c;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
        }

        .lead {
            margin: 12px 0 14px;
            padding: 10px 14px;
            border-left: 5px solid #b71c1c;
            background: #f5efe4;
            font-size: 13pt;
            line-height: 1.4;
            page-break-inside: avoid;
        }

        .article {
            column-count: 2;
            column-gap: 22px;
            text-align: justify;
        }

        .article p {
            margin: 0 0 10px;
        }

        .article h2,
        .article h3,
        .article h4 {
            column-span: all;
            margin: 18px 0 8px;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.15;
            page-break-after: avoid;
        }

        .article img,
        .article-image {
            width: 100%;
            height: auto;
            margin: 8px 0;
            page-break-inside: avoid;
        }

        blockquote {
            column-span: all;
            margin: 16px 0;
            padding: 10px 16px;
            border-top: 2px solid #111;
            border-bottom: 2px solid #111;
            color: #9a1d1d;
            font-size: 17pt;
            line-height: 1.25;
            font-weight: bold;
            page-break-inside: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d5c8b6;
            padding: 6px;
        }

        .category-label {
            width: 1%;
            margin-top: 18px;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .category-label td {
            border: 0;
            padding: 5px 10px;
            background-color: #111;
            color: #fff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: 1px;
            line-height: 1;
            vertical-align: middle;
            white-space: nowrap;
        }

        .tags {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 2px solid #111;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            page-break-inside: avoid;
        }

        .tag-chip {
            display: inline-block;
            margin-right: 4px;
            border: 1px solid #111;
            border-radius: 8px;
            padding: 3px 7px;
            background-color: #fffdf8;
            font-size: 8pt;
            font-weight: 700;
            line-height: 1;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .author-box {
            width: 100%;
            margin-top: 16px;
            padding: 12px;
            border: 1px solid #111;
            background: #f7f1e8;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12pt;
            page-break-inside: avoid;
            border-collapse: collapse;
        }

        .author-box td {
            border: 0;
            padding: 0;
            vertical-align: middle;
        }

        .author-box img.avatar,
        .author-box .avatar {
            width: 80px !important;
            height: 80px !important;
        }

        .author-name {
            font-size: 12pt;
            font-weight: 700;
            line-height: 1.2;
        }

        .author-username {
            font-size: 10pt;
            font-weight: 400;
            color: #666666;
        }

        .author-bio {
            margin: 8px 0 0;
            color: #50483f;
            font-size: 9pt;
            font-family: 'bookos';
            line-height: 1.45;
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
    </style>
</head>
<body>
    <div class="masthead">
        <div class="masthead-title"><?= strtoupper(phrase('{{app_name}} Tabloid', ['app_name' => htmlspecialchars(get_setting('app_name'))])) ?></div>
        <div class="masthead-meta"><?= strtoupper(htmlspecialchars($publishedAt)) ?></div>
    </div>

    <table class="category-label" width="1%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="border:0; padding:5px 10px; background-color:#111111; color:#ffffff; font-family:Arial, Helvetica, sans-serif; font-size:8pt; font-weight:bold; letter-spacing:1px; line-height:1; white-space:nowrap;">
                <?= strtoupper(htmlspecialchars($post->category_title)) ?>
            </td>
        </tr>
    </table>

    <h1><?= htmlspecialchars($post->post_title) ?></h1>

    <?php if ($post->post_excerpt): ?>
        <div class="deck"><?= htmlspecialchars($post->post_excerpt) ?></div>
    <?php endif; ?>

    <div class="byline">
        <table>
            <tr>
                <td style="width:52px">
                    <img src="<?= $authorImage ?>" class="avatar" alt="<?= htmlspecialchars($authorName) ?>">
                </td>
                <td>
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="author-line" colspan="<?= $updatedAt ? 3 : 1 ?>">
                                <?= phrase('Written by') ?>
                                <strong><?= htmlspecialchars($authorName) ?></strong>
                                <?php if ($post->username): ?>
                                    &middot; @<?= htmlspecialchars($post->username) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="date-line date-value">
                                <?= phrase('Published at') ?> <?= htmlspecialchars($publishedAt) ?>
                            </td>
                            <?php if ($updatedAt): ?>
                                <td class="date-line" style="width:18px; text-align:center;">
                                    &middot;
                                </td>
                                <td class="date-line date-value">
                                    <?= phrase('Updated at') ?> <?= htmlspecialchars($updatedAt) ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <?php if ($coverImage): ?>
        <table class="cover">
            <tr>
                <td>
                    <img src="<?= $coverImage ?>" width="100%" class="cover-image" alt="<?= htmlspecialchars($post->post_title) ?>">
                </td>
            </tr>
            <tr>
                <td class="cover-caption">
                    <?= htmlspecialchars($post->post_title) ?>
                </td>
            </tr>
        </table>
    <?php endif; ?>

    <?php if ($post->post_excerpt): ?>
        <div class="lead"><?= htmlspecialchars($post->post_excerpt) ?></div>
    <?php endif; ?>

    <div class="article">
        <?= $content ?>
    </div>

    <?php if ($tags): ?>
        <div class="tags">
            <strong><?= phrase('Tags') ?>:</strong>
            <?php foreach ($tags as $tag): ?>
                <span class="tag-chip" style="display:inline-block; margin-right:4px; border:1px solid #111; border-radius:8px; padding:3px 7px; background-color:#fffdf8; font-size:8pt; font-weight:bold; line-height:1; text-transform:uppercase; white-space:nowrap;">&nbsp;<?= htmlspecialchars($tag) ?>&nbsp;</span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <table class="author-box" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:90px">
                <img src="<?= $authorImage ?>" class="avatar" style="width:80px; height:80px;" width="80" height="80" alt="<?= htmlspecialchars($authorName) ?>">
            </td>
            <td>
                <div class="author-name">
                    <b><?= htmlspecialchars($authorName) ?></b>
                    <?php if ($post->username): ?>
                        <span class="author-username">&middot; @<?= htmlspecialchars($post->username) ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($post->bio): ?>
                    <div class="author-bio"><?= nl2br(htmlspecialchars($post->bio)) ?></div>
                <?php endif; ?>
            </td>
            <td style="width:90px; text-align:right; vertical-align:middle;">
                <img src="<?= $qrcode ?>" style="width:80px; height:80px;" alt="QR Code">
            </td>
        </tr>
    </table>
    <htmlpagefooter name="footer">
        <table class="footer-table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="text-align:left;">
                    <span class="footer-brand"><?= htmlspecialchars(get_setting('app_name')) ?></span>
                </td>
                <td style="text-align:right;">
                    <span class="footer-pill-pg">&nbsp; PAGE {PAGENO} OF {nbpg} &nbsp;</span>
                </td>
            </tr>
        </table>
    </htmlpagefooter>
</body>
</html>
