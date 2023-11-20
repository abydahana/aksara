<form action="<?= site_url('run'); ?>" method="POST" class="--validate-form">
    <h4>
        <?= phrase('The installer was interrupted!'); ?>
    </h4>
    <p>
        <?= phrase('The installer was unable to write the configuration file.') . ' ' . phrase('Please follow the instructions below to continue.'); ?>
    </p>
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <p class="mb-0">
        <b>
            <?= phrase('Problem Found'); ?>:
        </b>
    </p>
    <p class="text-break-word">
        <?= phrase('Unable to create or write file'); ?>
    </p>
    <p class="mb-0">
        <b>
            <?= phrase('Solution'); ?>:
        </b>
    </p>
    <p class="text-break-word">
        <?= phrase('Please download the configuration file below and upload or paste it manually under the following directory') . ': <code>' . substr(ROOTPATH, 0, strrpos(ROOTPATH, '/public')) . '</code>'; ?>
        <br />
        <a href="<?= site_url('run'); ?>?download=1" target="_blank" class="btn btn-success btn-sm">
            <i class="mdi mdi-download"></i>
            <?= phrase('Download Configuration'); ?>
        </a>
    </p>
    <p>
        <?= phrase('Once the configuration file were uploaded, please click the refresh button to continue the installation.'); ?>
    </p>
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <div class="--validation-callback"></div>
    <div class="row">
        <div class="col-6">
            <div class="d-grid">
                <a href="<?= site_url('system'); ?>" class="btn btn-light btn-sm rounded-pill --xhr">
                    <i class="mdi mdi-arrow-left"></i>
                    <?= phrase('Back'); ?>
                </a>
            </div>
        </div>
        <div class="col-6 text-right">
            <div class="d-grid">
                <input type="hidden" name="_token" value="<?= sha1(time()); ?>" />
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">
                    <i class="mdi mdi-reload"></i>
                    <?= phrase('Refresh'); ?>
                </button>
            </div>
        </div>
    </div>
</form>
