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
                알림!
            </h4>
            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH; ?></b> 는 쓰기 가능하지 않습니다.
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH; ?></b> 는 쓰기 가능하지 않습니다.
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>여기를 클릭</b></a> 하여 이 문제를 해결하는 방법에 대한 조언을 얻으십시오.
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
                    당신은 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 를 사용하고 있습니다!
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
                        <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 를 "<b>개발자 모드</b>"로 설치했기 때문에 이 페이지를 보고 있습니다. 생성된 예제 콘텐츠가 없습니다. 인기 있는 PHP 프레임워크와 마찬가지로, <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 가 제공하는 기능을 참조하여 자체 모듈을 구축해야 합니다. <b>블로그</b>, <b>페이지</b>, <b>갤러리</b> 등과 같은 내장 모듈(<b>CMS</b> 또는 <b>콘텐츠 관리 시스템</b>)에 여전히 로그인하여 콘텐츠를 추가할 수 있습니다.
                    </p>
                    <p>
                        이 모듈은 다음 위치에 있습니다
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR; ?>Home</code>.
                    </p>
                    <p>
                        원본을 제거하지 않고 이 모듈을 다음 위치로 <b>재정의(override)</b> 할 수 있습니다
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR; ?>Home</code>.
                    </p>
                    <p>
                        <b>어떻게 그럴 수 있나요?</b> 당신이 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 를 사용하고 있기 때문입니다!
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    더 알아보기
                </h3>
                <h4 class="mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    문서화
                </h4>
                <div class="mb-5">
                    <p>
                        가이드라인에는 소개, 튜토리얼, 수많은 "사용 방법" 가이드 및 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 를 구성하는 구성 요소에 대한 참조 문서가 포함되어 있습니다.
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>문서를 확인하세요</b></a>!
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    커뮤니티
                </h4>
                <div class="mb-5">
                    <p>
                        다음 커뮤니티 포럼에서 기능, 버그 또는 제안과 관련된 토론을 열 수 있습니다:
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
                        또한 즐겨 사용하는 소셜 미디어에서 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 와 관련된 공식 포럼 토론을 만드는 것도 허용됩니다.
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    기여
                </h4>
                <div class="mb-5">
                    <p>
                        문서를 작성하고, 모듈을 만들고, 적합한 라이브러리를 추가하여 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 를 더욱 개선하는 데 기여할 수 있습니다. 이러한 기여는 <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a> 의 <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a> 리포지토리에서 <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>이슈(Issues)</b></a> 또는 <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>풀 리퀘스트(Pull Request)</b></a> 의 형태로 이루어집니다.
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    지원
                </h4>
                <div class="mb-5">
                    <p>
                        단일 연구자(싱글 파이터)로서, 저는 때때로 탐험해보지 않은 외부 세계를 즐기고 싶습니다. 약간의 휴가를 통해 제 연구에 적용할 수 있는 또 다른 빛나는 아이디어를 떠올릴 수 있을지도 모릅니다.
                    </p>
                    <p>
                        대부분의 단일 연구자와 마찬가지로, 제가 수행한 연구가 도움이 되었다고 생각하여 도덕적 또는 물질적 지원을 제공하고 싶으시다면 주저하지 말고 <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara 개발 웹사이트</b></a> 에서 연락해 주십시오. 여러분의 어떠한 지원이라도 정말 감사하게 생각하며, 물론 그것은 저에게 더 많은 자신감을 줄 것입니다.
                    </p>
                </div>
                <h5 class="text-center">
                    다시 한 번, <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> 를 사용해 주셔서 감사합니다.
                </h5>
                <h5 class="text-center mb-3">
                    우리는 훌륭합니다!
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h4>
            </div>
        </div>
    </div>
</div>
