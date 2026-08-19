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
                Let op!
            </h2>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH ?></b> is niet schrijfbaar.
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH ?></b> is niet schrijfbaar.
                </p>
            <?php endif; ?>
            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>Klik hier</b></a> voor advies over het oplossen van dit probleem.
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
                    Je gebruikt <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
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
                        Je ziet deze pagina omdat je <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> hebt geïnstalleerd in "<b>ONTWIKKELAARSMODUS</b>". Er is geen voorbeeldinhoud gemaakt. Net als bij populaire PHP-frameworks moet je je eigen modules bouwen door te verwijzen naar de functies die worden aangeboden door <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>. Je kunt nog steeds inloggen en je inhoud toevoegen aan de ingebouwde module (<b>CMS</b>, oftewel <b>Content Management Systeem</b>) zoals <b>Blogs</b>, <b>Pagina's</b>, <b>Galerijen</b> en nog veel meer.
                    </p>
                    <p>
                        Deze module bevindt zich in
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR ?>Home</code>.
                    </p>
                    <p>
                        Je kunt deze module <b>overschrijven</b> in
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR ?>Home</code> zonder het origineel te verwijderen.
                    </p>
                    <p>
                        <b>Hoe is dat mogelijk?</b> Omdat je <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> gebruikt!
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    Ga verder
                </h2>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    Documentatie
                </h2>
                <div class="mb-5">
                    <p>
                        De richtlijnen bevatten een introductie, tutorial, een aantal "how-to" gidsen, en vervolgens referentiedocumentatie voor de componenten waaruit <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> bestaat.
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>Bekijk de Documentatie</b></a>!
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    Gemeenschap
                </h2>
                <div class="mb-5">
                    <p>
                        Je kunt discussies openen over functies, bugs of suggesties op het volgende communityforum:
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
                        Het is ook toegestaan om een officiële forumdiscussie met betrekking tot <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> te starten op je favoriete sociale media.
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    Bijdragen
                </h2>
                <div class="mb-5">
                    <p>
                        Je bent welkom om bij te dragen door documentatie te schrijven, modules te maken en geschikte bibliotheken toe te voegen om <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> nog beter te maken. Deze bijdragen worden gedaan in de vorm van <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Issues</b></a> of <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Pull Requests</b></a> in de <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a> repository op <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>.
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    Ondersteuning
                </h2>
                <div class="mb-5">
                    <p>
                        Als onafhankelijk onderzoeker wil ik af en toe genieten van een buitenwereld die ik nog nooit heb verkend. Misschien bedenk ik met een korte vakantie wel een ander briljant idee om toe te passen op mijn onderzoek.
                    </p>
                    <p>
                        Net als de meeste onafhankelijke onderzoekers, aarzel niet om contact met mij op te nemen via de <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara ontwikkelingswebsite</b></a> als je je geholpen voelt door het onderzoek dat ik heb gedaan en morele of materiële steun wilt bieden. Ik zou alle steun ten zeerste waarderen en natuurlijk zal het me meer zelfvertrouwen geven.
                    </p>
                </div>
                <h5 class="text-center">
                    Nogmaals, bedankt voor het proberen van <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                </h5>
                <h5 class="text-center mb-3">
                    We zijn geweldig!
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h2>
            </div>
        </div>
    </div>
</div>
