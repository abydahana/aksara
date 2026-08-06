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
                Hinweis!
            </h4>
            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH; ?></b> ist nicht beschreibbar.
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH; ?></b> ist nicht beschreibbar.
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>Klicken Sie hier</b></a>, um Ratschläge zur Lösung dieses Problems zu erhalten.
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
                    Sie verwenden <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
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
                        Sie sehen diese Seite, weil Sie <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> im "<b>ENTWICKLERMODUS</b>" installiert haben. Es wurden keine Beispielinhalte erstellt. Wie bei beliebten PHP-Frameworks müssen Sie Ihre eigenen Module erstellen, indem Sie auf die von <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> bereitgestellten Funktionen verweisen. Sie können sich weiterhin anmelden und Ihre Inhalte dem integrierten Modul (<b>CMS</b> oder <b>Content Management System</b>) hinzufügen, wie z.B. <b>Blogs</b>, <b>Seiten</b>, <b>Galerien</b> und vieles mehr.
                    </p>
                    <p>
                        Dieses Modul befindet sich in
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR; ?>Home</code>.
                    </p>
                    <p>
                        Sie können dieses Modul <b>überschreiben</b> nach
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR; ?>Home</code>, ohne das Original zu entfernen.
                    </p>
                    <p>
                        <b>Wie ist das möglich?</b> Weil Sie <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> verwenden!
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    Gehen Sie weiter
                </h3>
                <h4 class="mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    Dokumentation
                </h4>
                <div class="mb-5">
                    <p>
                        Die Richtlinien enthalten eine Einführung, Tutorials, eine Reihe von "How-to"-Anleitungen und dann Referenzdokumentationen für die Komponenten, aus denen <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> besteht.
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>Sehen Sie sich die Dokumentation an</b></a>!
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    Community
                </h4>
                <div class="mb-5">
                    <p>
                        Sie können Diskussionen über Funktionen, Fehler oder Vorschläge im folgenden Community-Forum eröffnen:
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
                        Sie dürfen auch offizielle Forumsdiskussionen zu <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> in Ihren bevorzugten sozialen Medien erstellen.
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    Beitragen
                </h4>
                <div class="mb-5">
                    <p>
                        Sie können dazu beitragen, indem Sie Dokumentationen schreiben, Module erstellen und geeignete Bibliotheken hinzufügen, um <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> noch besser zu machen. Diese Beiträge erfolgen in Form von <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Issues</b></a> oder <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Pull Requests</b></a> im <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a>-Repository auf <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>.
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    Unterstützung
                </h4>
                <div class="mb-5">
                    <p>
                        Als Einzelkämpfer-Forscher möchte ich gelegentlich eine Außenwelt genießen, die ich noch nie erkundet habe. Vielleicht fällt mir mit ein wenig Urlaub eine weitere brillante Idee ein, die ich in meiner Forschung anwenden kann.
                    </p>
                    <p>
                        Wie bei den meisten Einzelkämpfern: Wenn Sie sich durch meine Forschung unterstützt fühlen und moralische oder materielle Unterstützung leisten möchten, zögern Sie nicht, mich über die <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara-Entwicklungswebsite</b></a> zu kontaktieren. Ich würde mich über jede Art von Unterstützung sehr freuen, was mir natürlich mehr Selbstvertrauen geben wird.
                    </p>
                </div>
                <h5 class="text-center">
                    Nochmals vielen Dank, dass Sie <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> ausprobiert haben.
                </h5>
                <h5 class="text-center mb-3">
                    Wir sind großartig!
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h4>
            </div>
        </div>
    </div>
</div>
