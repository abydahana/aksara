<div class="container-fluid">
    <div class="sticky-lg-top bg-body overflow-x-auto py-1 px-3 mx--3">
        <ul class="nav nav-pills nav-pills-dark flex-nowrap">
            <li class="nav-item">
                <a href="<?= go_to() ?>" class="nav-link rounded-pill active text-nowrap --xhr">
                    <i class="mdi mdi-cart"></i> <?= phrase('Market') ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= go_to('themes') ?>" class="nav-link rounded-pill text-nowrap --xhr">
                    <i class="mdi mdi-palette"></i> <?= phrase('Installed Theme') ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= go_to('modules') ?>" class="nav-link rounded-pill text-nowrap --xhr">
                    <i class="mdi mdi-puzzle"></i> <?= phrase('Installed Module') ?>
                </a>
            </li>
        </ul>
    </div>
    <div class="row py-1 mb-3 border-top border-bottom">
        <div class="col-md-4">
            <div class="row g-1">
                <div class="col-6">
                    <a href="<?= service('request')->getGet('addon_type') == 'theme' ? go_to(null, ['addon_type' => null]) : go_to(null, ['addon_type' => 'theme']) ?>" class="btn <?= service('request')->getGet('addon_type') == 'theme' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm rounded-pill d-block mb-2 mb-md-0 --xhr">
                        <?= phrase('Themes') ?>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= service('request')->getGet('addon_type') == 'module' ? go_to(null, ['addon_type' => null]) : go_to(null, ['addon_type' => 'module']) ?>" class="btn <?= service('request')->getGet('addon_type') == 'module' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm rounded-pill d-block mb-2 mb-md-0 --xhr">
                        <?= phrase('Modules') ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 offset-md-2">
            <div class="row g-1">
                <div class="col-6 col-md-3">
                    <a href="<?= go_to(null, ['order' => 'popular']) ?>" class="btn <?= service('request')->getGet('order') == 'popular' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm rounded-pill d-block mb-2 mb-md-0 --xhr">
                        <?= phrase('Popular') ?>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="<?= go_to(null, ['order' => 'latest']) ?>" class="btn <?= service('request')->getGet('order') == 'latest' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm rounded-pill d-block mb-2 mb-md-0 --xhr">
                        <?= phrase('Latest') ?>
                    </a>
                </div>
                <div class="col-12 col-md-6">
                    <form action="<?= go_to(null, ['page' => null]) ?>" method="POST" class="form-horizontal position-relative-form">
                        <div class="input-group input-group-sm">
                            <input type="text" name="q" class="form-control bg-body border-primary border-end-0 rounded-pill rounded-end-0" placeholder="<?= phrase('Search Add-Ons') ?>" value="<?= service('request')->getGet('q') ? htmlspecialchars(service('request')->getGet('q')) : null ?>" />
                            <button type="submit" class="btn bg-body border border-primary border-start-0 rounded-pill rounded-start-0">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row addon-listing">
        <!-- Addon listing will be appended here -->
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            url: '<?= current_page() ?>',
            method: 'POST',
            data: {
                source: 'market',
                addon_type: '<?= service('request')->getGet('addon_type') ? service('request')->getGet('addon_type') : null ?>',
                order: '<?= service('request')->getGet('order') ? service('request')->getGet('order') : null ?>',
                keyword: '<?= service('request')->getGet('q') ? htmlspecialchars(service('request')->getGet('q')) : null ?>'
            },
            beforeSend: function() {
                $('.addon-listing').html(
                    '<div class="col-lg-12">' +
                        '<div class="spinner-border" role="status">' +
                        '</div>' +
                    '</div>'
                )
            },
            context: this
        })
        .done(function(response) {
            if (! response || Object.keys(response).length === 0) {
                $('.addon-listing').html(`
                    <div class="col-lg-12">
                        <div class="alert alert-warning callout">
                            <i class="mdi mdi-information-outline"></i> <?= phrase('No add-ons available for your current Aksara version.') ?>
                        </div>
                    </div>
                `);

                return;
            } else if (typeof response.error !== 'undefined') {
                $('.addon-listing').html(`
                    <div class="col-lg-12">
                        <div class="alert alert-warning callout">
                            <i class="mdi mdi-information-outline"></i> ${ response.error }
                        </div>
                    </div>
                `);

                return;
            }

            $('.addon-listing').html(''),

            $.each(response, function(key, val) {
                var badge = (val.type == 'backend' ? '<span class="badge bg-warning position-absolute top-0 end-0 z-1 m-2"><?= phrase('Backend Theme') ?></span>' : '<span class="badge bg-success position-absolute top-0 end-0 z-1 m-2"><?= phrase('Frontend Theme') ?></span>');
                var install_label = '<?= phrase('Install') ?>';
                var preview_label = '<?= phrase('Preview') ?>';

                if (val.addon_type == 'theme') {
                    $(`
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card rounded-4 mb-3">
                                <div class="card-body p-3">
                                    <div class="position-relative mb-3">
                                        ${ badge }
                                        <div class="ratio ratio-4x3 bg-dark rounded-4 overflow-hidden">
                                            <a href="${ val.detail_url }" class="--modal d-block h-100">
                                                <img src="${ val.thumbnail?.src }" class="img-fluid w-100 h-100 object-fit-cover rounded-4 border" alt="${ val.thumbnail?.alt }" />
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <a href="${ val.detail_url }" class="text-decoration-none text-body --modal">
                                            <b data-bs-toggle="tooltip" title="${ val.name }">
                                                ${ val.name }
                                            </b>
                                        </a>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <a href="${ val.install_url }" class="btn btn-primary d-block btn-xs show-progress">
                                                <i class="mdi mdi-plus"></i> ${ install_label }
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="${ val.demo_url }" class="btn btn-outline-primary d-block btn-xs" target="_blank">
                                                <i class="mdi mdi-magnify"></i> ${ preview_label }
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `)
                    .appendTo('.addon-listing')
                } else if (val.addon_type == 'module') {
                    $(`
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card rounded-4 mb-3">
                                <div class="card-body p-3">
                                    <div class="position-relative mb-3">
                                        <div class="ratio ratio-4x3 bg-dark rounded-4 overflow-hidden">
                                            <a href="${ val.detail_url }" class="--modal d-block h-100">
                                                <img src="${ val.thumbnail?.src }" class="img-fluid w-100 h-100 object-fit-cover rounded-4 border" alt="${ val.thumbnail?.alt }" />
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <a href="${ val.detail_url }" class="text-decoration-none text-body --modal">
                                            <b data-bs-toggle="tooltip" title="${ val.name }">
                                                ${ val.name }
                                            </b>
                                        </a>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <a href="${ val.install_url }" class="btn btn-primary d-block btn-xs show-progress">
                                                <i class="mdi mdi-plus"></i> ${ install_label }
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="${ val.demo_url }" class="btn btn-outline-primary d-block btn-xs" target="_blank">
                                                <i class="mdi mdi-magnify"></i> ${ preview_label }
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `)
                    .appendTo('.addon-listing')
                }
            })
        })
        .fail(function() {
        })
    })
</script>
