<?php
    $selected = service('request')->getGet('group');
    $group_collector = [];
    $access_token = false;
    $method = [];

    if ($permission->groups) {
        $groups = null;
        $privileges = [];

        foreach ($permission->groups as $key => $val)
        {
            $group_collector[] = $val->group_id;
            $actions = null;
            $extract_privileges = json_decode($val->group_privileges);

            if (isset($extract_privileges->$active)) {
                foreach ($extract_privileges->$active as $_key => $_val)
                {
                    $actions .= '<a href="#--method-' . $_val . '"><span class="badge bg-success"><i class="mdi mdi-link"></i> ' . phrase($_val) . '</span></a>&nbsp;';
                }
            }

            if ($val->group_id) {
                $access_token = true;
            }

            $groups .= '<option value="' . $val->group_id . '"' . ($val->group_id == $selected ? ' selected' : null) . '>' . $val->group_name . '</option>';

            $privileges[$selected] = $actions;
        }
    }
?>

<div class="container-fluid py-3">
    <div class="row">
        <div class="col-md-3">
            <div class="sticky-top">
                <div class="pretty-scrollbar">
                    <a href="<?= base_url('apis/documentation'); ?>" class="<?= (! $active ? 'text-primary fw-bold' : null); ?>">
                        <?= phrase('Getting Started'); ?>
                    </a>
                    <br />
                    
                    <?php
                        if ($modules) {
                            foreach ($modules as $key => $val) {
                                echo '
                                    <a href="' . current_page(null, ['slug' => $val, 'group' => null]) . '" class="' . ($val == $active ? ' text-primary fw-bold' : null) . '">
                                        ' . str_replace('/', ' &gt; ', $val) . '
                                    </a>
                                    <br />
                                ';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <?php if ($active): ?>
                <?php if ($permission->groups): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="mb-3 --title">
                                <?= $active; ?>
                            </h4>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>
                                        <?= phrase('Group Name'); ?>
                                    </th>
                                    <th>
                                        <?= phrase('Privileges'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td width="250">
                                        <form action="<?= current_page(); ?>" method="GET">
                                            <select name="group" class="form-control form-control-sm">
                                                <?= $groups; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <?= (isset($privileges[$selected]) ? $privileges[$selected] : null); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <h5 class="mt-3">
                        <?= phrase('Request Method'); ?>
                    </h5>
                <?php endif; ?>
                <?php if ($permission->privileges): ?>
                    <?php foreach ($permission->privileges as $key => $val): ?>
                        <?php $method[] = $val; ?>
                        <div class="mb-3" id="--method-<?= $val; ?>">
                            <h5 class="mb-1">
                                <span class="badge bg-primary bg-md">
                                    <?= (in_array($val, ['create', 'update']) ? 'POST' : (in_array($val, ['delete']) ? 'DELETE' : 'GET')); ?>
                                </span>
                            </h5>
                            <div class="rounded pt-2 pe-3 pb-2 ps-3 bg-dark">
                                <code class="text-light"><?= base_url(('index' !== $val ? $active . '/' . $val : $active)); ?></code>
                            </div>
                        </div>
                        <h5 class="mt-3">
                            <?= phrase('Header'); ?>
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>
                                        <?= phrase('Field'); ?>
                                        </th>
                                        <th>
                                        <?= phrase('Type'); ?>
                                        </th>
                                        <th>
                                        <?= phrase('Description'); ?>
                                        </th>
                                        <th width="100" class="text-center">
                                        <?= phrase('Required'); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <code>
                                                X-API-KEY
                                            </code>
                                        </td>
                                        <td>
                                            String
                                        </td>
                                        <td>
                                        <?= phrase('Valid API Key added in API Service'); ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">
                                            <?= phrase('Required'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php if ($access_token): ?>
                                    <tr>
                                        <td>
                                            <code>
                                                X-ACCESS-TOKEN
                                            </code>
                                        </td>
                                        <td>
                                            String
                                        </td>
                                        <td>
                                        <?= phrase('The given token from authentication response'); ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">
                                            <?= phrase('Required'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center --spinner">
                            <div class="spinner-border" role="status"></div>
                        </div>
                        <div class="--query-<?= $val; ?> d-none">
                            <h5 class="mt-3">
                            <?= phrase('Query String'); ?>
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>
                                            <?= phrase('Field'); ?>
                                            </th>
                                            <th>
                                            <?= phrase('Type'); ?>
                                            </th>
                                            <th>
                                            <?= phrase('Description'); ?>
                                            </th>
                                            <th width="100" class="text-center">
                                            <?= phrase('Required'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="--parameter-<?= $val; ?> d-none">
                            <h5 class="mt-3">
                            <?= phrase('Parameter'); ?>
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>
                                            <?= phrase('Field'); ?>
                                            </th>
                                            <th>
                                            <?= phrase('Type'); ?>
                                            </th>
                                            <th>
                                            <?= phrase('Max Length'); ?>
                                            </th>
                                            <th>
                                            <?= phrase('Description'); ?>
                                            </th>
                                            <th width="100" class="text-center">
                                            <?= phrase('Required'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="--response-success-<?= $val; ?> d-none">
                            <h5 class="mt-3">
                            <?= phrase('Success Response'); ?>
                            </h5>
                            <pre class="language-javascript rounded mt-0"><code>{}</code></pre>
                        </div>
                        <div class="--response-error-<?= $val; ?> d-none">
                            <h5 class="mt-3">
                            <?= phrase('Error Response'); ?>
                            </h5>
                            <pre class="language-javascript rounded mt-0"><code>{}</code></pre>
                        </div>
                        <br />
                        <br />
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <h4 class="mb-3">
                    Pengenalan
                </h4>
                <p>
                    <a href="//www.aksaracms.com" target="_blank"><b class="text-primary">Aksara</b></a> dilengkapi dengan fitur <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> tanpa harus membangun ulang <i>controller</i>. Konsep dan alur kerja implementasi <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> sama dengan cara Anda mengakses aplikasi yang dibangun menggunakan <a href="//www.aksaracms.com" target="_blank"><b class="text-primary">Aksara</b></a>, yang sedang Anda buka saat ini.
                </p>
                <p>
                    Anda tidak perlu lagi memikirkan hal rumit yang membebani pekerjaan Anda. Semua permintaan <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> akan dikirimkan melalui otentikasi (handshake), pengecekan otorisasi, termasuk validasi yang telah Anda tetapkan untuk setiap modul yang ada.
                </p>
                <p>
                    Semudah itukah? Ya, karena ini <a href="//www.aksaracms.com" target="_blank"><b class="text-primary">Aksara</b></a>!
                </p>
                <hr />
                <h4 class="mb-3">
                    Memulai
                </h4>
                <p>
                    Untuk dapat menggunakan fitur API, Anda perlu menambahkan kunci API dari menu <a href="<?= go_to('../services'); ?>" class="text-primary"><b>Pengelola Layanan API</b></a>. Tentukan metode permintaan yang diizinkan, rentang IP yang diizinkan, dan juga tanggal kedaluwarsa kunci API yang dibuat.
                </p>
                <p>
                    Gunakan kunci API yang dihasilkan pada properti HEADER saat membuat permintaan.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>
                                    <?= phrase('Field'); ?>
                                </th>
                                <th>
                                    <?= phrase('Type'); ?>
                                </th>
                                <th>
                                    <?= phrase('Description'); ?>
                                </th>
                                <th width="100" class="text-center">
                                    <?= phrase('Required'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <code>
                                        X-API-KEY
                                    </code>
                                </td>
                                <td>
                                    string
                                </td>
                                <td>
                                    <?= phrase('Valid API Key added in API Service'); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger">
                                        <?= phrase('Required'); ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>
                    Untuk modul yang memerlukan izin sebagaimana ditentukan untuk kelompok pengguna tertentu, tambahkan parameter <code>X-ACCESS-TOKEN</code> ke dalam HEADER dan isi nilai dengan token yang diperoleh dari permintaan otentikasi (log-in).
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>
                                    <?= phrase('Field'); ?>
                                </th>
                                <th>
                                    <?= phrase('Type'); ?>
                                </th>
                                <th>
                                <?= phrase('Description'); ?>
                                </th>
                                <th width="100" class="text-center">
                                <?= phrase('Required'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <code>
                                        X-ACCESS-TOKEN
                                    </code>
                                </td>
                                <td>
                                    string
                                </td>
                                <td>
                                <?= phrase('The given token from authentication response'); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger">
                                    <?= phrase('Required'); ?>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p>
                    Proses otentikasi dapat POST ke <code><?= base_url('auth'); ?></code> dengan menambahkan <code>X-API-KEY</code> pada HEADER, termasuk data formulir (form-data) untuk mengirimkan <code>nama pengguna</code> dan <code>kata sandi</code>.
                </p>
                <hr />
                <h4>
                    Pengambilan Data
                </h4>
                <p>
                    Saat Anda meminta data, anda akan turut mendapatkan properti <i>query string</i> (properti bernama "<code>query_string</code>") yang akan membantu Anda mengambil data untuk dicocokkan dengan kata kunci. Selain itu, terdapat juga parameter <i>query string</i> untuk mengambil hasil dengan <i>filter</i>.
                </p>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>
                                    <?= phrase('Key'); ?>
                                </th>
                                <th>
                                    <?= phrase('Type'); ?>
                                </th>
                                <th>
                                    <?= phrase('Description'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <code>
                                        limit
                                    </code>
                                </td>
                                <td>
                                    int
                                </td>
                                <td>
                                    Menerapkan jumlah data yang dihasilkan
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code>
                                        offset
                                    </code>
                                </td>
                                <td>
                                    int
                                </td>
                                <td>
                                    Menerapkan di baris ke berapa pengambilan data di mulai
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code>
                                        order
                                    </code>
                                </td>
                                <td>
                                    string
                                </td>
                                <td>
                                    Pengurutan berdasarkan nama kolom
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code>
                                        sort
                                    </code>
                                </td>
                                <td>
                                    string <code>ASC|DESC</code>
                                </td>
                                <td>
                                    Pengurutan berdasarkan abjad, angka atau waktu
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code>
                                        q
                                    </code>
                                </td>
                                <td>
                                    string | number | int
                                </td>
                                <td>
                                    Kata kunci untuk menerapkan pencarian
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code>
                                        column
                                    </code>
                                </td>
                                <td>
                                    string
                                </td>
                                <td>
                                    Kolom yang dilakukan pencarian
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        if (UA !== 'mobile' && typeof mCustomScrollbar !== 'undefined') {
            $('.pretty-scrollbar').mCustomScrollbar({
                autoHideScrollbar: true,
                axis: 'y',
                scrollInertia: 170,
                mouseWheelPixels: 170,
                setHeight: $(window).outerHeight(true) - (($('[role=header]').outerHeight(true) ?? 0) + ($('[role=breadcrumb]').outerHeight(true) ?? 0) + ($('[role=meta]').outerHeight(true) ?? 0)),
                advanced: {
                    updateOnContentResize: true
                },
                autoHideScrollbar: false
            })
        }
        
        $.ajax({
            url: '<?= current_page(); ?>',
            context: this,
            method: 'POST',
            data: {
                mode: 'fetch',
                group: '<?= ($selected ? $selected : (isset($group_collector[0]) ? $group_collector[0] : 0)); ?>',
                method: JSON.parse('<?= json_encode($method); ?>')
            },
            beforeSend: function() {
            }
        })
        .done(function(response, status, error) {
            $('.--spinner').remove();
            
            if (response.results) {
                $.each(response.results, function(key, val) {
                    if (typeof val.query_string !== 'undefined') {
                        $.each(val.query_string, function(_key, _val) {
                            if ($('.--query-' + key).hasClass('d-none')) {
                                $('.--query-' + key).removeClass('d-none')
                            }
                            
                            $('<tr><td><code>' + _key + '</code></td><td>mixed</td><td>-</td><td class="text-center"><span class="badge bg-danger"><?= phrase('Required'); ?></span></td></tr>').appendTo('.--query-' + key + ' tbody')
                        })
                    }
                    
                    if (typeof val.parameter !== 'undefined') {
                        $.each(val.parameter, function(_key, _val) {
                            if ($('.--parameter-' + key).hasClass('d-none')) {
                                $('.--parameter-' + key).removeClass('d-none')
                            }
                            
                            $('<tr><td><code>' + _key + '</code></td><td>' + _val.type + '</td><td>' + _val.maxlength + '</td><td>' + _val.label + '</td><td class="text-center">' + (_val.required ? '<span class="badge bg-danger"><?= phrase('Required'); ?></span>' : '') + '</td></tr>').appendTo('.--parameter-' + key + ' tbody')
                        })
                    }
                    
                    if (typeof val.response.success !== 'undefined') {
                        if ($('.--response-success-' + key).hasClass('d-none')) {
                            $('.--response-success-' + key).removeClass('d-none')
                        }
                        
                        $('.--response-success-' + key + ' pre code').text(JSON.stringify(val.response.success, null, 4))
                    }
                    
                    if (typeof val.response.error !== 'undefined') {
                        if ($('.--response-error-' + key).hasClass('d-none')) {
                            $('.--response-error-' + key).removeClass('d-none')
                        }
                        
                        $('.--response-error-' + key + ' pre code').text(JSON.stringify(val.response.error, null, 4))
                    }
                })
            }
        })
    })
</script>
