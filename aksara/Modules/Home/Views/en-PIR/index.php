<?php

/**
 * @var mixed $permission
 * @var mixed $meta
 * @var mixed $error
 */
if (! $permission->uploads || ! $permission->writable): ?>
    <div class="alert alert-danger rounded-0 border-0 mb-0">
        <div class="container">
            <h4>
                Avast ye!
            </h4>
            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH; ?></b> be not writable.
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH; ?></b> be not writable.
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>Click 'ere</b></a> to get some advice 'ow to solve this here issue.
        </div>
    </div>
<?php endif; ?>

<div class="py-3 py-md-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <h1 class="text-center">
                    <?= $meta->title; ?>
                </h1>
                <p class="fs-5 text-center">
                    <?= truncate($meta->description, 256); ?>
                </p>
                <h3 class="mb-3 text-center">
                    Ye be sailin' with <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
                </h3>
            </div>
        </div>
    </div>
</div>
<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="mb-5">
                    <p>
                        Ye be spyin' this page 'cause ye rigged <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> in "<b>DEVELOPER MODE</b>". There be no booty or example content crafted yet. Just like them popular PHP frameworks, ye must build yer own modules by referencin' the functions served by <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>. Ye can still board and add yer content to the built-in module (<b>CMS</b> a.k.a <b>Content Management System</b>) such as <b>Blogs</b>, <b>Pages</b>, <b>Galleries</b> and many more.
                    </p>
                    <p>
                        This here module be located in
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR; ?>Home</code>.
                    </p>
                    <p>
                        Ye can <b>mutiny and override</b> this module into
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR; ?>Home</code> without sinkin' the original one.
                    </p>
                    <p>
                        <b>How could that be done?</b> Because ye be usin' <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>, matey!
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    Sail Further
                </h3>
                <h4 class="mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    The Treasure Map (Documentation)
                </h4>
                <div class="mb-5">
                    <p>
                        The Guidelines contain an introduction, tutorial, a number of "how to" guides, and then reference maps for the components that make up the <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>Check the Documentation</b></a>!
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    The Crew (Community)
                </h4>
                <div class="mb-5">
                    <p>
                        Ye can open discussion related to the features, bugs or suggestions to the followin' crew forum:
                    </p>
                    <p class="mb-1">
                        <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank">
                            https://github.com/abydahana/aksara/issues<i class="mdi mdi-open-in-new"></i>
                        </a>
                    </p>
                    <p class="mb-1">
                        <a href="https://github.com/abydahana/aksara/discussions" class="text-primary" target="blank">
                            https://github.com/abydahana/aksara/discussions<i class="mdi mdi-open-in-new"></i>
                        </a>
                    </p>
                    <p>
                        Ye also be permitted to make an official tavern discussion related to <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> in yer favourite social media.
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    Chip In (Contribute)
                </h4>
                <div class="mb-5">
                    <p>
                        Ye be allowed to contribute by writin' documentation, creatin' modules and addin' suitable libraries to make <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> even better. These contributions be made in the form of <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Issues</b></a> or <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Pull Request</b></a> on the <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> repository on <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>.
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    Support
                </h4>
                <div class="mb-5">
                    <p>
                        As a lone scallywag researcher, I occasionally want to enjoy an outside world that I've never explored. Maybe with a little vacation, I can come up with another bright idea to apply to me research.
                    </p>
                    <p>
                        Like most lone researchers, if ye feel helped by the research I did and want to provide moral or material doubloons, don't hesitate to contact me from the <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara development website</b></a>. I would really appreciate whatever yer support be, and of course it will give me more confidence.
                    </p>
                </div>
                <h5 class="text-center">
                    Once again, thank ye for tryin' <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                </h5>
                <h5 class="text-center mb-3">
                    We be fearsome!
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h4>
            </div>
        </div>
    </div>
</div>
