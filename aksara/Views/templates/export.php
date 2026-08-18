<?php

$thead = null;
$tbody = null;
$singlePrint = false;
$method = $method ?? null;
$pagination = $pagination ?? new stdClass();
$path = service('uri')->getPath();
$cleanPath = trim(preg_replace('#/(export|print|pdf)$#i', '', trim($path, '/')), '/');
$sessionHidden = get_userdata('hidden_cols_' . md5($cleanPath)) ?? get_userdata('hidden_cols_' . md5($path)) ?? [];
$getHiddenCols = service('request')->getGet('hidden_cols');
$getSelectedCols = service('request')->getGet('selected_cols');
$hiddenColumns = is_array($sessionHidden) ? $sessionHidden : [];
$selectedColumns = [];

if ($getSelectedCols) {
    $selectedColumns = is_array($getSelectedCols) ? $getSelectedCols : array_filter(array_map('trim', explode(',', $getSelectedCols)));
}

if ($getHiddenCols) {
    $getHiddenArray = is_array($getHiddenCols) ? $getHiddenCols : array_filter(array_map('trim', explode(',', $getHiddenCols)));
    $hiddenColumns = array_merge($hiddenColumns, $getHiddenArray);
}

$hiddenColumns = array_unique(array_filter($hiddenColumns));

if (isset($results->table_data)) {
    foreach ($results->table_data as $key => $row) {
        $rows = null;

        foreach ($row->field_data as $fields => $params) {
            $label = $params->label ?? null;
            $fieldName = $params->name ?? $fields;

            if (
                ! empty($params->hidden) ||
                ($hiddenColumns && (in_array($fields, $hiddenColumns) || in_array($fieldName, $hiddenColumns) || ($label && in_array($label, $hiddenColumns)))) ||
                ($selectedColumns && ! in_array($fields, $selectedColumns) && ! in_array($fieldName, $selectedColumns) && (! $label || ! in_array($label, $selectedColumns)))
            ) {
                continue;
            }

            $params->label = null; // Remove label

            if (0 == $key) {
                $thead .= '<th class="bordered">' . $label . '</th>';
            }

            $rows .= '<td class="bordered">' . form_read($params) . '</td>';
        }

        $tbody .= '<tr>' . $rows . '</tr>';
    }
} elseif (isset($results->field_data)) {
    $singlePrint = true;
    $filteredFields = [];

    foreach ($results->field_data as $field => $params) {
        $label = $params->label ?? null;
        $fieldName = $params->name ?? $field;

        if (
            ! empty($params->hidden) ||
            ($hiddenColumns && (in_array($field, $hiddenColumns) || in_array($fieldName, $hiddenColumns) || ($label && in_array($label, $hiddenColumns)))) ||
            ($selectedColumns && ! in_array($field, $selectedColumns) && ! in_array($fieldName, $selectedColumns) && (! $label || ! in_array($label, $selectedColumns)))
        ) {
            continue;
        }
        $filteredFields[$field] = $params;
    }

    $totalFields = count($filteredFields);
    $currentIndex = 0;

    foreach ($filteredFields as $field => $params) {
        $currentIndex++;
        $label = $params->label ?? null; // Backup label
        $params->label = null; // Remove label
        $isLast = ($currentIndex === $totalFields);

        $tbody .= '
            <tr>
                <td class="text-muted text-uppercase text-end">
                    ' . $label . '
                </td>
                <td width="70%"' . (! $isLast ? ' class="border-between"' : null) . '>
                    ' . form_read($params) . '
                </td>
            </tr>
        ';
    }
} else {
    exit(phrase('No results could be rendered!'));
} ?>

<html>
    <head>
        <title><?= $meta->title ?? get_setting('app_name') ?></title>
        <link rel="icon" type="image/x-icon" href="<?= get_image('settings', get_setting('app_icon'), 'icon') ?>" />
        <style type="text/css">
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
            @page {
                sheet-size: <?= $singlePrint ? '8.5in 13.5in' : '13.5in 8.5in' ?>;
                footer: html_footer;
                margin: 10mm
            }
            body {
                font-family: 'bookos', Tahoma
            }
            label,
            h4 {
                display: block
            }
            a,
            a:hover,
            a:focus,
            a:visited,
            a:link {
                text-decoration: none;
                color: #000
            }
            hr {
                border-top: 1px solid #999;
                border-bottom: 0;
                margin-bottom: 15px
            }
            .separator {
                border-bottom: 1px solid <?= $singlePrint ? '#888' : '#fff' ?>;
                padding: 1px;
                margin: 1rem 0;
            }
            .text-sm {
                font-size: 10px
            }
            .text-uppercase {
                text-transform: uppercase
            }
            .text-muted {
                color: #888
            }
            .text-sm-start {
                text-align: left!important
            }
            .text-center {
                text-align: center
            }
            .text-end {
                text-align: right
            }
            table {
                width: 100%
            }
            th {
                text-align:center;
                font-weight: bold
            }
            td {
                padding: 5px;
                vertical-align: top
            }
            .letterhead td {
                vertical-align: middle!important
            }
            .table {
                border-collapse: collapse
            }
            .table th.bordered,
            .table td.bordered {
                border: 1px solid #aaa
            }
            .table .table th.bordered:first-child,
            .table .table td.bordered:first-child {
                border-left: 0
            }
            .table .table th.bordered:last-child,
            .table .table td.bordered:last-child {
                border-right: 0
            }
            img,
            .table img,
            td img {
                max-width: 80px;
                width: 80px;
                height: auto;
            }
            .col-sm-6 {
                width: 50%;
                float: left;
                margin: 12px 0;

            }
            input {
                border: 1px solid #aaa!important;
                width: 60px!important
            }
            .pagination {
                margin: 0;
                padding: 0;
                list-style-type: none;
                display: inline;
                float: right;
                line-height: 1.5
            }
            nav > form {
                margin: 0;
                display: inline;
                float: right;
                line-height: 1.5;
                margin-right: 15px
            }
            nav > form > .input-group > input,
            nav > form > .input-group > .input-group-append {
                display: inline;
                padding: 3px
            }
            .pagination li {
                display: inline-block;
                margin: 0
            }
            .pagination li a,
            .pagination li input {
                padding: 2px 10px;
                border: 1px solid #aaa
            }
            .btn-sm {
                padding: 2px
            }
            .no-padding {
                padding: 0;
                border: 0
            }
            .no-margin {
                margin: 0
            }
            .border-between,
            td.border-between {
                border-bottom: 1px solid #aaa;
            }
        </style>
    </head>
    <body>
        <table class="letterhead">
            <tbody>
                <tr>
                    <td width="100" valign="middle">
                        <img src="<?= get_image('settings', get_setting('app_icon'), 'icon') ?>" alt="<?= get_setting('app_name') ?>" width="80" />
                    </td>
                    <td valign="middle">
                        <h3 class="no-margin"><?= get_setting('app_name') ?></h3>
                        <h2 class="no-margin"><?= get_setting('office_name') ?></h2>
                        <p class="text-sm no-margin"><?= get_setting('office_address') ?></p>
                        <p class="text-sm no-margin"><?= phrase('Phone') ?>: <?= get_setting('office_phone') ?> / <?= get_setting('office_email') ?></p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="separator"></div>

        <table class="table">
            <thead>
                <tr><?= $thead ?></tr>
            </thead>
            <tbody><?= $tbody ?></tbody>
        </table>

        <?php if ('pdf' == $method): ?>
            <htmlpagefooter name="footer">
                <table>
                    <tfoot>
                        <tr>
                            <td class="text-muted text-sm">
                                <i>
                                    <?= phrase('The document was generated from {{app_name}} at {{datetime}}', [
                                        'app_name' => get_setting('app_name'),
                                        'datetime' => date('Y-m-d H:i:s'),
                                    ]) ?>
                                </i>
                            </td>
                            <td class="text-muted text-sm text-end">
                                <?= phrase('Page') ?> {PAGENO} <?= phrase('of') ?> {nb}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </htmlpagefooter>
        <?php endif; ?>

        <?php if ('print' == $method): ?>
            <div class="no-print">
                <?= pagination($pagination) ?>
            </div>
            <script type="text/javascript">
                window.print()
            </script>
        <?php endif; ?>
    </body>
</html>
