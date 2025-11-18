<div class="container-fluid">
    <div class="sticky-top bg-white overflow-x-auto py-1 px-3 mx--3 mb-3 border-bottom">
        <ul class="nav nav-pills nav-pills-dark flex-nowrap">
            <li class="nav-item">
                <a href="<?= go_to(); ?>" class="nav-link rounded-pill active no-wrap --xhr">
                    <i class="mdi mdi-cart"></i>
                    <?= phrase('Market'); ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= go_to('themes'); ?>" class="nav-link rounded-pill no-wrap --xhr">
                    <i class="mdi mdi-palette"></i>
                    <?= phrase('Installed Theme'); ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= go_to('modules'); ?>" class="nav-link rounded-pill no-wrap --xhr">
                    <i class="mdi mdi-puzzle"></i>
                    <?= phrase('Installed Module'); ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= go_to('ftp'); ?>" class="nav-link rounded-pill no-wrap --xhr">
                    <i class="mdi mdi-console-network"></i>
                    <?= phrase('FTP Configuration'); ?>
                </a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="row mb-3">
                <div class="col-6">
                    <a href="<?= go_to(null, ['order' => 'popular']); ?>" class="btn btn-secondary d-block btn-sm --xhr">
                        <?= phrase('Popular'); ?>
                    </a>
                </div>
                <div class="col-6">
                    <a href="<?= go_to(null, ['order' => 'latest']); ?>" class="btn btn-secondary d-block btn-sm --xhr">
                        <?= phrase('Latest'); ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 offset-md-2">
            <form action="<?= go_to(null, ['per_page' => null]); ?>" method="POST" class="form-horizontal position-relative-form mb-3">
                <div class="input-group input-group-sm">
                    <input type="text" name="q" class="form-control" placeholder="<?= phrase('Search Add-Ons'); ?>" value="<?= (service('request')->getGet('q') ? htmlspecialchars(service('request')->getGet('q')) : null); ?>" />
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <hr class="mx--3 mt-0" />
    <div class="row addon-listing">
        <!-- addon listing -->
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            url: '<?= current_page(); ?>',
            method: 'POST',
            data: {
                source: 'market',
                order: '<?= (service('request')->getGet('order') ? service('request')->getGet('order') : null); ?>',
                keyword: '<?= (service('request')->getGet('q') ? htmlspecialchars(service('request')->getGet('q')) : null); ?>'
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
                        <div class="alert alert-warning">
                            <i class="mdi mdi-information-outline"></i> <?= phrase('No add-ons available for your current Aksara version.'); ?>
                        </div>
                    </div>
                `);
                
                return;
            } else if (typeof response.error !== 'undefined') {
                $('.addon-listing').html(`
                    <div class="col-lg-12">
                        <div class="alert alert-warning">
                            <i class="mdi mdi-information-outline"></i> ${ response.error }
                        </div>
                    </div>
                `);
                
                return;
            }
            
            $('.addon-listing').html(''),
            
            $.each(response, function(key, val) {
                if (val.addon_type == 'theme') {
                    $(`
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card rounded-4 mb-3">
                                <div class="card-body p-3">
                                    <div class="position-relative mb-3">
                                        ${ (val.type == 'backend' ? '<span class="badge bg-warning float-end mt-3 me-3"><?= phrase('Backend Theme'); ?></span>' : '<span class="badge bg-success float-end mt-3 me-3"><?= phrase('Frontend Theme'); ?></span>') }
                                        <img src="${ val.thumbnail }" class="img-fluid rounded-4 border" alt="..." />
                                    </div>
                                    <div class="mb-3">
                                        <b data-bs-toggle="tooltip" title="${ val.name }">
                                            ${ val.name }
                                        </b>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <a href="${ val.install_url }" class="btn btn-primary d-block btn-xs show-progress">
                                                <i class="mdi mdi-plus"></i> <?= phrase('Install'); ?>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="${ val.demo_url }" class="btn btn-outline-primary d-block btn-xs" target="_blank">
                                                <i class="mdi mdi-magnify"></i> <?= phrase('Preview'); ?>
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
                                        <img src="${ val.thumbnail }" class="img-fluid rounded-4 border" alt="..." />
                                    </div>
                                    <div class="mb-3">
                                        <b data-bs-toggle="tooltip" title="${ val.name }">
                                            ${ val.name }
                                        </b>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <a href="${ val.install_url }" class="btn btn-primary d-block btn-xs show-progress">
                                                <i class="mdi mdi-plus"></i> <?= phrase('Install'); ?>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="${ val.demo_url }" class="btn btn-outline-primary d-block btn-xs" target="_blank">
                                                <i class="mdi mdi-magnify"></i> <?= phrase('Preview'); ?>
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
