<?php

/**
 * @var mixed $permission
 * @var mixed $meta
 * @var mixed $error
 */
if (isset($permission) && (! $permission->uploads || ! $permission->writable)): ?>
    <div class="alert alert-danger rounded-0 border-0 mb-0">
        <div class="container">
            <h4>
                注意！
            </h2>
            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH; ?></b> 不可寫入。
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH; ?></b> 不可寫入。
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>點擊這裡</b></a> 獲取關於如何解決此問題的建議。
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
                        您看到此頁面是因為您以「<b>開發者模式</b>」安裝了 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>。目前尚未建立任何範例內容。就像流行的 PHP 框架一樣，您必須透過引用 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 提供的功能來建構自己的模組。您仍然可以登入並將您的內容新增至內建模組（<b>CMS</b>，又名<b>內容管理系統</b>），如<b>部落格</b>、<b>頁面</b>、<b>畫廊</b>等。
                    </p>
                    <p>
                        此模組位於
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR; ?>Home</code>。
                    </p>
                    <p>
                        您可以將此模組<b>覆蓋</b>到
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR; ?>Home</code>，而無需刪除原始模組。
                    </p>
                    <p>
                        <b>這是怎麼做到的？</b> 因為您使用的是 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>！
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    走得更遠
                </h2>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    文件
                </h2>
                <div class="mb-5">
                    <p>
                        該指南包含簡介、教學課程、一些「操作方法」指南，以及構成 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 的組件的參考文件。
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>查看文件</b></a>！
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    社群
                </h2>
                <div class="mb-5">
                    <p>
                        您可以在以下社群論壇中發起有關功能、錯誤或建議的討論：
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
                        您還可以在您最喜歡的社群媒體上建立與 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 相關的官方論壇討論。
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    貢獻
                </h2>
                <div class="mb-5">
                    <p>
                        您可以編寫文件、建立模組並加入合適的庫來使 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 變得更好。這些貢獻可以以 <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Issues（問題）</b></a> 或 <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Pull Request（拉取請求）</b></a> 的形式在 <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a> 上的 <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a> 儲存庫中進行。
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    支持
                </h2>
                <div class="mb-5">
                    <p>
                        作為一名獨立研究員（單打獨鬥者），我偶爾想去享受一下我從未探索過的外部世界。也許透過一個小長假，我可以想出另一個絕妙的主意來應用到我的研究中。
                    </p>
                    <p>
                        就像大多數獨立研究員一樣，如果您覺得我做的研究對您有幫助，並且想提供精神或物質上的支持，請隨時透過 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara 開發網站</b></a> 與我聯絡。我非常感謝您的任何支持，當然，這會讓我更有信心。
                    </p>
                </div>
                <h5 class="text-center">
                    再次感謝您嘗試 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>。
                </h5>
                <h5 class="text-center mb-3">
                    我們太棒了！
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h2>
            </div>
        </div>
    </div>
</div>
