<?php
    $thead = null;
    $tbody = null;
    $single_print = false;

    if (isset($results->table_data)) {
        foreach($results->table_data as $key => $row) {
            $rows = null;

            foreach($row->field_data as $fields => $params) {
                if($params->hidden) continue;

                // Backup label
                $label = $params->label;

                // Remove label
                $params->label = null;

                if(0 == $key) {
                    $thead .= '<th class="bordered">' . $label . '</th>';
                }

                $rows .= '<td class="bordered">' . form_read($params) . '</td>';
            }

            $tbody .= '
                <tr>
                    ' . $rows . '
                </tr>
            ';
        }
    } else if(isset($results->field_data)) {
        $single_print = true;

        foreach($results->field_data as $field => $params) {
            // Backup label
            $label = $params->label;

            // Remove label
            $params->label = null;

            $tbody .= '
                <tr>
                    <td class="text-muted text-uppercase text-end">
                        ' . $label . '
                    </td>
                    <td width="70%">
                        ' . form_read($params) . '
                        <hr />
                    </td>
                </tr>
            ';
        }
    } else {
        exit(phrase('No result could be rendered!'));
    }
?>
<html>
    <head>
        <title><?= $meta->title; ?></title>
        <link rel="icon" type="image/x-icon" href="<?= get_image('settings', get_setting('app_icon'), 'icon'); ?>" />
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
                sheet-size: <?= ($single_print ? '8.5in 13.5in' : '13.5in 8.5in'); ?>;;
                footer: html_footer
            }
            * {
                font-family: Tahoma
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
                border-top: 1px solid #999999;
                border-bottom: 0;
                margin-bottom: 15px
            }
            .separator {
                border-top: 3px solid #000000;
                border-bottom: 1px solid #000000;
                padding: 1px;
                margin-bottom: 30px
            }
            .text-sm {
                font-size: 10px
            }
            .text-uppercase {
                text-transform: uppercase
            }
            .text-muted {
                color: #888888
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
            .table {
                border-collapse: collapse
            }
            .table th.bordered,
            .table td.bordered {
                border: 1px solid #000
            }
            .table .table th.bordered:first-child,
            .table .table td.bordered:first-child {
                border-left: 0
            }
            .table .table th.bordered:last-child,
            .table .table td.bordered:last-child {
                border-right: 0
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
        </style>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>
                        <img src="<?= get_image('settings', get_setting('app_icon'), 'icon'); ?>" alt="..." />
                    </th>
                    <th>
                        <h3 class="no-margin">
                            <?= get_setting('app_name'); ?>
                        </h3>
                        <h2 class="no-margin">
                            <?= get_setting('office_name'); ?>
                        </h2>
                        <p class="text-sm no-margin">
                            <?= get_setting('office_address'); ?>
                        </p>
                        <p class="text-sm no-margin">
                            <?= phrase('Phone'); ?>: <?= get_setting('office_phone'); ?>
                            /
                            <?= phrase('Fax'); ?>: <?= get_setting('office_fax'); ?>
                            /
                            <?= get_setting('office_email'); ?>
                        </p>
                    </th>
                </tr>
            </thead>
        </table>

        <div class="separator"></div>

        <table class="table">
            <thead>
                <tr>
                    <?= $thead; ?>
                </tr>
            </thead>
            <tbody>
                <?= $tbody; ?>
            </tbody>
        </table>

        <?php if ($method == 'pdf'): ?>
            <htmlpagefooter name="footer" class="print">
                <table>
                    <tfoot>
                        <tr>
                            <td class="text-muted text-sm">
                                <i>
                                    <?= phrase('The document was generated from {{app_name}} at {{datetime}}', ['app_name' => get_setting('app_name'), 'datetime' => date('Y-m-d H:i:s')]); ?>
                                </i>
                            </td>
                            <td class="text-muted text-sm text-end">
                                <?= phrase('Page'); ?> {PAGENO} <?= phrase('of'); ?> {nb}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </htmlpagefooter>
        <?php elseif ($method == 'print'): ?>
            <div class="no-print">
                <?= pagination($pagination); ?>
            </div>
            <script type="text/javascript">
                window.print()
            </script>
        <?php endif; ?>
    </body>
</html>
