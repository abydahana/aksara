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
                ¡Aviso!
            </h2>
            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH; ?></b> no tiene permisos de escritura.
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH; ?></b> no tiene permisos de escritura.
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>Haga clic aquí</b></a> para obtener consejos sobre cómo resolver este problema.
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
                    ¡Estás usando <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
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
                        Estás viendo esta página porque instalaste <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> en "<b>MODO DESARROLLADOR</b>". No se ha creado ningún contenido de ejemplo. Al igual que los populares frameworks PHP, debes construir tus propios módulos haciendo referencia a las funciones proporcionadas por <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>. Todavía puedes iniciar sesión y agregar tu contenido al módulo integrado (<b>CMS</b>, también conocido como <b>Sistema de Gestión de Contenido</b>), como <b>Blogs</b>, <b>Páginas</b>, <b>Galerías</b> y muchos más.
                    </p>
                    <p>
                        Este módulo se encuentra en
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR; ?>Home</code>.
                    </p>
                    <p>
                        Puedes <b>sobrescribir</b> este módulo en
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR; ?>Home</code> sin eliminar el original.
                    </p>
                    <p>
                        <b>¿Cómo se puede hacer eso?</b> ¡Porque estás usando <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    Ve más allá
                </h2>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    Documentación
                </h2>
                <div class="mb-5">
                    <p>
                        Las directrices contienen una introducción, un tutorial, varias guías de "cómo hacer" y luego la documentación de referencia para los componentes que conforman <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>¡Revisa la Documentación!</b></a>
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    Comunidad
                </h2>
                <div class="mb-5">
                    <p>
                        Puedes abrir discusiones relacionadas con las funciones, errores o sugerencias en el siguiente foro de la comunidad:
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
                        También tienes permitido crear una discusión oficial en el foro relacionada con <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> en tus redes sociales favoritas.
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    Contribuir
                </h2>
                <div class="mb-5">
                    <p>
                        Se te permite contribuir escribiendo documentación, creando módulos y agregando bibliotecas adecuadas para hacer que <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> sea aún mejor. Estas contribuciones se realizan en forma de <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Problemas (Issues)</b></a> o <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Solicitudes de Extracción (Pull Request)</b></a> en el repositorio de <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a> en <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>.
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    Apoyo
                </h2>
                <div class="mb-5">
                    <p>
                        Como investigador independiente (single fighter), de vez en cuando quiero disfrutar de un mundo exterior que nunca he explorado. Quizás con unas pequeñas vacaciones, pueda encontrar otra idea brillante para aplicarla a mi investigación.
                    </p>
                    <p>
                        Como la mayoría de los investigadores independientes, si sientes que la investigación que realicé te ayudó y deseas brindar apoyo moral o material, no dudes en contactarme desde el <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>sitio web de desarrollo de Aksara</b></a>. Apreciaría mucho cualquiera que sea tu apoyo y, por supuesto, me dará más confianza.
                    </p>
                </div>
                <h5 class="text-center">
                    Una vez más, gracias por probar <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                </h5>
                <h5 class="text-center mb-3">
                    ¡Somos increíbles!
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h2>
            </div>
        </div>
    </div>
</div>
