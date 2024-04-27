<h4>
    <?= phrase('Congratulations!'); ?>
</h4>
<p>
    <?= phrase('Aksara has been successfully installed on your system!'); ?>
</p>
<div class="text-muted opacity-50">
    <hr class="row" />
</div>
<p class="mb-0">
    <?= phrase('You can login as superuser using following credential:'); ?>:
</p>
<div class="row">
    <div class="col-4">
        <b>
            <?= phrase('Username'); ?>
        </b>
    </div>
    <div class="col-8">
        <?= session()->get('username'); ?>
    </div>
</div>
<div class="row form-group mb-3">
    <div class="col-4">
        <b>
            <?= phrase('Password'); ?>
        </b>
    </div>
    <div class="col-8">
        <?= session()->get('password'); ?>
    </div>
</div>
<div class="text-muted opacity-50">
    <hr />
</div>
<div class="row">
    <div class="col-md-4">
        <img src="assets/like-a-boss.png" class="img-fluid" alt="Like a boss..." />
    </div>
    <div class="col-md-8">
        <p>
            <?= phrase('Follow our updates to get our other works if you find this useful.'); ?>
        </p>
        <p>
            <?= phrase('Just to remind you, we also collect donations from people like you to support our research.'); ?>
        </p>
        <p>
            <?= phrase('Regardless of the amount will be very useful.'); ?>
        </p>
        <p>
            <?= phrase('Cheers'); ?>,
            <br />
            <a href="//abydahana.github.io" class="text-primary text-decoration-none" target="_blank">
                <b>Aby Dahana</b>
            </a>
        </p>
    </div>
</div>
<div class="text-muted opacity-50">
    <hr class="row" />
</div>
<div class="row">
    <div class="col-sm-6">
        &nbsp;
    </div>
    <div class="col-sm-6">
        <div class="d-grid">
            <a href="<?= site_url('xhr/boot'); ?>" class="btn btn-outline-success btn-sm rounded-pill fw-bold">
                <i class="mdi mdi-rocket"></i>
                <?= phrase('Launch Site'); ?>
            </a>
        </div>
    </div>
</div>
