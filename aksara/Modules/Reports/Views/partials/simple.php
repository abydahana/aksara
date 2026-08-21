<?php

/**
 * @var string $title
 * @var object $header
 * @var array $summary
 * @var array $sections
 * @var string|null $description
 * @var string|null $orientation
 */
$description ??= null;
$orientation ??= 'portrait';
$sheetSize = ('landscape' === $orientation ? '13in 8.5in' : '8.5in 13in');
?>

<!DOCTYPE html>
<html>

<head>
    <title>
        <?= $title; ?>
    </title>
    <link rel="icon" type="image/x-icon" href="<?= get_image('settings', get_setting('app_icon'), 'icon'); ?>" />

    <style type="text/css">
        @page {
            footer: html_footer;
            sheet-size: <?= $sheetSize; ?>;
            margin: 50px
        }

        .print {
            display: none
        }

        @media print {
            .no-print {
                display: none
            }

            .print {
                display: block
            }
        }

        body {
            font-family: 'bookos', Tahoma;
            font-size: 12px
        }

        .divider {
            display: block;
            border-bottom: 1px solid #888;
            padding: 1px;
            margin-bottom: 15px
        }

        .text-sm {
            font-size: 11px
        }

        .text-uppercase {
            text-transform: uppercase
        }

        .text-body-secondary {
            color: #888
        }

        .text-left {
            text-align: left
        }

        .text-right {
            text-align: right
        }

        .text-center {
            text-align: center
        }

        .letterhead-logo {
            width: 70px;
            vertical-align: middle
        }

        .letterhead-identity {
            vertical-align: middle
        }

        .letterhead-meta {
            width: 230px;
            font-size: 11px;
            line-height: 1.35;
            vertical-align: bottom
        }

        table {
            width: 100%
        }

        th {
            font-weight: bold;
            padding: 4px 5px
        }

        td {
            vertical-align: top;
            padding: 4px 5px
        }

        .table {
            border-collapse: collapse
        }

        .bg-light {
            background: #f2f2f2
        }

        .no-margin {
            margin: 0
        }

        h2 {
            font-size: 16px
        }

        h3 {
            font-size: 14px;
            margin: 15px 0 6px
        }

        p {
            margin: 0
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td class="letterhead-logo">
                <img src="<?= get_image('settings', get_setting('app_icon'), 'thumb'); ?>" alt="<?= phrase('Application Icon'); ?>" width="60" loading="lazy" />
            </td>
            <td class="letterhead-identity">
                <h2 class="no-margin">
                    <?= get_setting('app_name'); ?>
                </h2>
                <h3 class="no-margin">
                    <?= $title; ?>
                </h3>
                <?php if ($description): ?>
                    <p class="text-body-secondary">
                        <?= htmlspecialchars($description); ?>
                    </p>
                <?php endif; ?>
            </td>
            <td class="letterhead-meta text-right">
                <?php if (isset($header->date_start, $header->date_end)): ?>
                    <p>
                        <?= format_date($header->date_start, 'long') . ' - ' . format_date($header->date_end, 'long'); ?>
                    </p>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="text-center">
        <h2 class="no-margin text-uppercase">
            <?= $title; ?>
        </h2>
    </div>

    <?php if ($summary): ?>
        <h3><?= phrase('Summary'); ?></h3>
        <table class="table" border="1">
            <tbody>
                <?php foreach (array_chunk($summary, 3, true) as $chunk): ?>
                    <tr>
                        <?php foreach ($chunk as $label => $value): ?>
                            <th class="bg-light text-left"><?= phrase($label); ?></th>
                            <td class="text-right"><?= is_numeric($value) ? number_format($value) : htmlspecialchars((string) $value); ?></td>
                        <?php endforeach; ?>

                        <?php for ($i = count($chunk); $i < 3; $i++): ?>
                            <th class="bg-light"></th>
                            <td></td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php foreach ($sections as $section): ?>
        <h3><?= phrase($section['title']); ?></h3>
        <table class="table" border="1">
            <thead>
                <tr class="bg-light">
                    <?php foreach ($section['headers'] as $headerLabel): ?>
                        <th><?= phrase($headerLabel); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($section['rows'] as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td class="<?= is_numeric(str_replace(',', '', (string) $cell)) ? 'text-right' : null; ?>">
                                <?= htmlspecialchars((string) $cell); ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>

                <?php if (! $section['rows']): ?>
                    <tr>
                        <td colspan="<?= count($section['headers']); ?>" class="text-center text-body-secondary">
                            <?= phrase('No data available'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <htmlpagefooter name="footer">
        <table class="print">
            <tr>
                <td class="text-body-secondary text-sm">
                    <i>
                        <?= phrase('Document has generated from') . ' ' . get_setting('app_name') . ' ' . phrase('at') . ' ' . date('d F Y - H:i:s'); ?>
                    </i>
                </td>
                <td class="text-body-secondary text-sm text-right">
                    <?= phrase('Page') . ' {PAGENO} ' . phrase('of') . ' {nb}'; ?>
                </td>
            </tr>
        </table>
    </htmlpagefooter>
</body>

</html>
