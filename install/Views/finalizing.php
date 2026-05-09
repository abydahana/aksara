<form action="<?= site_url('run'); ?>" method="POST" class="--validate-form">
    <h4>
        <?= phrase('All catched up!'); ?>
    </h4>
    <p>
        <?= phrase('Your application is ready to install using the provided configurations.'); ?>
    </p>
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <p>
        <b>
            <?= phrase('Just one more step'); ?>
        </b>
    </p>
    <p class="mb-0 mb-md-5">
        <?= phrase('Make sure what you filled in on the previous form is correct.'); ?>
        <?= phrase('Once you have successfully run the installer, there is no more back button.'); ?>
    </p>
    <p class="mb-0 mb-md-5">
        <?= phrase('Click run installer to applying your configuration.'); ?>
    </p>
    <br />
    <br />
    <br />
    <div class="text-muted opacity-50">
        <hr class="row" />
    </div>
    <div class="--validation-callback"></div>
    <div class="row">
        <div class="col-4 col-sm-6">
            <div class="d-grid">
                <a href="<?= site_url('system'); ?>" class="btn btn-light btn-sm rounded-pill --xhr">
                    <i class="mdi mdi-arrow-left"></i>
                    <?= phrase('Back'); ?>
                </a>
            </div>
        </div>
        <div class="col-8 col-sm-6">
            <div class="d-grid">
                <input type="hidden" name="_token" value="<?= sha1(time()); ?>" />
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill">
                    <i class="mdi mdi-check"></i>
                    <?= phrase('Run Installer'); ?>
                </button>
            </div>
        </div>
    </div>
</form>
