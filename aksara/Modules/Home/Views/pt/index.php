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
                Aviso!
            </h2>
            <?php if (!$permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH ?></b> não é gravável.
                </p>
            <?php endif; ?>

            <?php if (!$permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH ?></b> não é gravável.
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>Clique aqui</b></a> para obter conselhos sobre como resolver esse problema.
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
                    Você está usando <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
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
                        Você está vendo esta página porque instalou o <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> no "<b>MODO DESENVOLVEDOR</b>". Nenhum conteúdo de exemplo foi criado. Assim como os frameworks PHP populares, você deve construir seus próprios módulos referenciando as funções fornecidas pelo <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>. Você ainda pode fazer login e adicionar seu conteúdo ao módulo integrado (<b>CMS</b> ou <b>Sistema de Gerenciamento de Conteúdo</b>) como <b>Blogs</b>, <b>Páginas</b>, <b>Galerias</b> e muitos mais.
                    </p>
                    <p>
                        Este módulo está localizado em
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR ?>Home</code>.
                    </p>
                    <p>
                        Você pode <b>sobrescrever</b> este módulo em
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR ?>Home</code> sem remover o original.
                    </p>
                    <p>
                        <b>Como isso pode ser feito?</b> Porque você está usando o <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    Vá Além
                </h2>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    Documentação
                </h2>
                <div class="mb-5">
                    <p>
                        As diretrizes contêm uma introdução, tutorial, uma série de guias de "como fazer" e documentação de referência para os componentes que formam o <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>Verifique a Documentação</b></a>!
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    Comunidade
                </h2>
                <div class="mb-5">
                    <p>
                        Você pode iniciar discussões relacionadas a recursos, bugs ou sugestões no fórum da comunidade a seguir:
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
                        Você também tem permissão para criar discussões oficiais sobre o <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> em suas redes sociais favoritas.
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    Contribuir
                </h2>
                <div class="mb-5">
                    <p>
                        Você pode contribuir escrevendo documentação, criando módulos e adicionando bibliotecas adequadas para tornar o <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> ainda melhor. Essas contribuições são feitas na forma de <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Issues</b></a> ou <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Pull Request</b></a> no repositório do <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a> no <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>.
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    Apoio
                </h2>
                <div class="mb-5">
                    <p>
                        Como um pesquisador independente (single fighter), de vez em quando eu quero aproveitar um mundo exterior que nunca explorei. Talvez com algumas férias curtas, eu possa ter outra ideia brilhante para aplicar em minha pesquisa.
                    </p>
                    <p>
                        Como a maioria dos pesquisadores independentes, se você sentiu que a pesquisa que fiz ajudou e deseja fornecer apoio moral ou material, não hesite em entrar em contato comigo através do <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>site de desenvolvimento do Aksara</b></a>. Eu agradeceria muito qualquer que fosse o seu apoio, e claro, isso me dará mais confiança.
                    </p>
                </div>
                <h5 class="text-center">
                    Mais uma vez, obrigado por tentar o <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                </h5>
                <h5 class="text-center mb-3">
                    Nós somos incríveis!
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h2>
            </div>
        </div>
    </div>
</div>
