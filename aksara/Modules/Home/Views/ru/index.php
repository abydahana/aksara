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
                Внимание!
            </h4>
            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH; ?></b> недоступен для записи.
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH; ?></b> недоступен для записи.
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>Нажмите здесь</b></a>, чтобы получить совет, как решить эту проблему.
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
                    Вы используете <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
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
                        Вы видите эту страницу, потому что установили <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> в "<b>РЕЖИМЕ РАЗРАБОТЧИКА</b>". Примерный контент еще не создан. Как и в случае с популярными фреймворками PHP, вы должны создавать свои собственные модули, ссылаясь на функции, предоставляемые <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>. Вы по-прежнему можете войти в систему и добавить свой контент во встроенный модуль (<b>CMS</b> или <b>Система управления контентом</b>), такой как <b>Блоги</b>, <b>Страницы</b>, <b>Галереи</b> и многое другое.
                    </p>
                    <p>
                        Этот модуль находится в
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR; ?>Home</code>.
                    </p>
                    <p>
                        Вы можете <b>переопределить</b> этот модуль в
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR; ?>Home</code> без удаления оригинала.
                    </p>
                    <p>
                        <b>Как это возможно?</b> Потому что вы используете <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    Идите дальше
                </h3>
                <h4 class="mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    Документация
                </h4>
                <div class="mb-5">
                    <p>
                        Руководства содержат введение, учебник, ряд руководств "как сделать", а затем справочную документацию для компонентов, составляющих <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>Проверить документацию</b></a>!
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    Сообщество
                </h4>
                <div class="mb-5">
                    <p>
                        Вы можете открыть обсуждение, связанное с функциями, ошибками или предложениями, на следующем форуме сообщества:
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
                        Вам также разрешено создавать официальные обсуждения, связанные с <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>, в ваших любимых социальных сетях.
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    Сделать вклад
                </h4>
                <div class="mb-5">
                    <p>
                        Вам разрешено вносить вклад, написав документацию, создав модули и добавив подходящие библиотеки, чтобы сделать <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> еще лучше. Эти вклады осуществляются в виде <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Issues</b></a> (вопросов) или <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Pull Request</b></a> (запросов на извлечение) в репозитории <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a> на <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>.
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    Поддержка
                </h4>
                <div class="mb-5">
                    <p>
                        Как независимый исследователь (одинокий боец), я иногда хочу насладиться внешним миром, который никогда не исследовал. Возможно, с небольшим отпуском мне придет в голову еще одна блестящая идея, которую можно применить в моем исследовании.
                    </p>
                    <p>
                        Как и большинство независимых исследователей, если вы чувствуете, что мое исследование помогло вам, и хотите оказать моральную или материальную поддержку, не стесняйтесь обращаться ко мне на <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>сайте разработки Aksara</b></a>. Я буду очень признателен за любую вашу поддержку, и, конечно же, это придаст мне больше уверенности.
                    </p>
                </div>
                <h5 class="text-center">
                    Еще раз спасибо, что попробовали <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                </h5>
                <h5 class="text-center mb-3">
                    Мы потрясающие!
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h4>
            </div>
        </div>
    </div>
</div>
