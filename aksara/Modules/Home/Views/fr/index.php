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
                Avis !
            </h2>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH ?></b> n'est pas accessible en écriture.
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH ?></b> n'est pas accessible en écriture.
                </p>
            <?php endif; ?>
            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>Cliquez ici</b></a> pour obtenir des conseils sur la façon de résoudre ce problème.
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
                    Vous utilisez <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> !
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
                        Vous voyez cette page parce que vous avez installé <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> en "<b>MODE DÉVELOPPEUR</b>". Aucun contenu d'exemple n'a été créé. Tout comme les frameworks PHP populaires, vous devez construire vos propres modules en référençant les fonctions servies par <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>. Vous pouvez toujours vous connecter et ajouter votre contenu au module intégré (<b>CMS</b> ou <b>Système de gestion de contenu</b>) comme les <b>Blogs</b>, <b>Pages</b>, <b>Galeries</b> et bien d'autres.
                    </p>
                    <p>
                        Ce module est situé dans
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR ?>Home</code>.
                    </p>
                    <p>
                        Vous pouvez <b>remplacer</b> ce module dans
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR ?>Home</code> sans supprimer l'original.
                    </p>
                    <p>
                        <b>Comment est-ce possible ?</b> Parce que vous utilisez <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> !
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    Allez plus loin
                </h2>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    Documentation
                </h2>
                <div class="mb-5">
                    <p>
                        Les directives contiennent une introduction, un tutoriel, un certain nombre de guides pratiques, puis une documentation de référence pour les composants qui constituent <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>Consultez la Documentation</b></a> !
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    Communauté
                </h2>
                <div class="mb-5">
                    <p>
                        Vous pouvez ouvrir une discussion liée aux fonctionnalités, bugs ou suggestions sur le forum communautaire suivant :
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
                        Il vous est également permis de créer une discussion de forum officielle liée à <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> sur vos réseaux sociaux préférés.
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    Contribuer
                </h2>
                <div class="mb-5">
                    <p>
                        Vous êtes autorisé à contribuer en écrivant de la documentation, en créant des modules et en ajoutant des bibliothèques appropriées pour rendre <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> encore meilleur. Ces contributions se font sous forme de <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Problèmes (Issues)</b></a> ou <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Demandes d'extraction (Pull Request)</b></a> sur le dépôt <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a> sur <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>.
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    Soutien
                </h2>
                <div class="mb-5">
                    <p>
                        En tant que chercheur indépendant (single fighter), j'ai parfois envie de profiter d'un monde extérieur que je n'ai jamais exploré. Peut-être qu'avec quelques petites vacances, je pourrai trouver une autre idée brillante à appliquer à mes recherches.
                    </p>
                    <p>
                        Comme la plupart des chercheurs indépendants, si vous vous sentez aidé par les recherches que j'ai menées et que vous souhaitez apporter un soutien moral ou matériel, n'hésitez pas à me contacter via le <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>site de développement d'Aksara</b></a>. J'apprécierais vraiment tout soutien de votre part, et bien sûr, cela me donnera plus de confiance.
                    </p>
                </div>
                <h5 class="text-center">
                    Encore une fois, merci d'avoir essayé <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                </h5>
                <h5 class="text-center mb-3">
                    Nous sommes géniaux !
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h2>
            </div>
        </div>
    </div>
</div>
