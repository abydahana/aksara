<?php

/**
 * @var mixed $permission
 * @var mixed $meta
 * @var mixed $error
 */
if (isset($permission) && (!$permission->uploads || !$permission->writable)): ?>
    <div class="alert alert-danger rounded-0 border-0 mb-0">
        <div class="container">
            <h4>
                注意！
            </h2>
            <?php if (!$permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH ?></b> 不可写。
                </p>
            <?php endif; ?>

            <?php if (!$permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH ?></b> 不可写。
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>点击这里</b></a> 获取关于如何解决此问题的建议。
        </div>
    </div>
<?php endif; ?>

<div class="py-3 py-md-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <h1 class="text-center">
                    <?= $meta->title ?>
                </h1>
                <p class="fs-5 text-center">
                    <?= truncate($meta->description, 256) ?>
                </p>
                <h3 class="mb-3 text-center">
                    您正在使用 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>！
                </h2>
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
                        您看到此页面是因为您以“<b>开发者模式</b>”安装了 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>。目前尚未创建任何示例内容。就像流行的 PHP 框架一样，您必须通过引用 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 提供的功能来构建自己的模块。您仍然可以登录并将您的内容添加到内置模块（<b>CMS</b>，又名<b>内容管理系统</b>），如<b>博客</b>、<b>页面</b>、<b>画廊</b>等。
                    </p>
                    <p>
                        此模块位于
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR ?>Home</code>。
                    </p>
                    <p>
                        您可以将此模块<b>覆盖</b>到
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR ?>Home</code>，而无需删除原始模块。
                    </p>
                    <p>
                        <b>这是怎么做到的？</b> 因为您使用的是 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>！
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    走得更远
                </h2>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    文档
                </h2>
                <div class="mb-5">
                    <p>
                        该指南包含简介、教程、一些“操作方法”指南，以及构成 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 的组件的参考文档。
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>查看文档</b></a>！
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    社区
                </h2>
                <div class="mb-5">
                    <p>
                        您可以在以下社区论坛中发起有关功能、错误或建议的讨论：
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
                        您还可以在您最喜欢的社交媒体上创建与 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 相关的官方论坛讨论。
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    贡献
                </h2>
                <div class="mb-5">
                    <p>
                        您可以编写文档、创建模块并添加合适的库来使 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 变得更好。这些贡献可以以 <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Issues（问题）</b></a> 或 <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Pull Request（拉取请求）</b></a> 的形式在 <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a> 上的 <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a> 存储库中进行。
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    支持
                </h2>
                <div class="mb-5">
                    <p>
                        作为一名独立研究员（单打独斗者），我偶尔想去享受一下我从未探索过的外部世界。也许通过一个小长假，我可以想出另一个绝妙的主意来应用到我的研究中。
                    </p>
                    <p>
                        就像大多数独立研究员一样，如果您觉得我做的研究对您有帮助，并且想提供精神或物质上的支持，请随时通过 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara 开发网站</b></a> 与我联系。我非常感谢您的任何支持，当然，这会让我更有信心。
                    </p>
                </div>
                <h5 class="text-center">
                    再次感谢您尝试 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>。
                </h5>
                <h5 class="text-center mb-3">
                    我们太棒了！
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h2>
            </div>
        </div>
    </div>
</div>
