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
                お知らせ！
            </h2>
            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH; ?></b> は書き込み不可です。
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH; ?></b> は書き込み不可です。
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>ここをクリック</b></a> して、この問題の解決方法に関するアドバイスを取得してください。
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
                    あなたは <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> を使用しています！
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
                        <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> を「<b>開発者モード</b>」でインストールしたため、このページが表示されています。作成されたサンプルコンテンツはありません。人気のあるPHPフレームワークと同様に、<a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> が提供する関数を参照して、独自のモジュールを構築する必要があります。ログインして、<b>ブログ</b>、<b>ページ</b>、<b>ギャラリー</b>などの組み込みモジュール（<b>CMS</b>、別名<b>コンテンツ管理システム</b>）にコンテンツを追加することは引き続き可能です。
                    </p>
                    <p>
                        このモジュールは以下の場所にあります
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR; ?>Home</code>。
                    </p>
                    <p>
                        元のモジュールを削除せずに、このモジュールを以下の場所で<b>オーバーライド</b>できます。
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR; ?>Home</code>。
                    </p>
                    <p>
                        <b>どうやってそれが可能なのですか？</b> それはあなたが <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> を使っているからです！
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    さらに先へ
                </h2>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    ドキュメンテーション
                </h2>
                <div class="mb-5">
                    <p>
                        ガイドラインには、紹介、チュートリアル、多数の「ハウツー」ガイド、そして <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> を構成するコンポーネントの参照ドキュメントが含まれています。
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>ドキュメントを確認する</b></a>！
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    コミュニティ
                </h2>
                <div class="mb-5">
                    <p>
                        機能、バグ、または提案に関連するディスカッションを、以下のコミュニティフォーラムで開くことができます。
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
                        また、お気に入りのソーシャルメディアで <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> に関連する公式のフォーラムディスカッションを作成することも許可されています。
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    貢献する
                </h2>
                <div class="mb-5">
                    <p>
                        ドキュメントを作成したり、モジュールを作成したり、適切なライブラリを追加したりして、<a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> をさらに良くすることに貢献できます。これらの貢献は、<a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a> 上の <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a> リポジトリに対する <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Issues</b></a> または <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Pull Request</b></a> の形で行われます。
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    サポート
                </h2>
                <div class="mb-5">
                    <p>
                        一人で戦う研究者（シングルファイター）として、私は時折、今まで探求したことのない外の世界を楽しみたいと思っています。少しの休暇をとれば、研究に応用できる別の輝かしいアイデアを思いつくかもしれません。
                    </p>
                    <p>
                        ほとんどの一人の研究者と同様に、私の研究が役立ったと感じ、道徳的または物質的なサポートを提供したい場合は、<a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara 開発ウェブサイト</b></a> から遠慮なくご連絡ください。どのようなサポートでも大変感謝しており、もちろんそれが私のさらなる自信につながります。
                    </p>
                </div>
                <h5 class="text-center">
                    改めて、<a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> をお試しいただきありがとうございます。
                </h5>
                <h5 class="text-center mb-3">
                    私たちは素晴らしいです！
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h2>
            </div>
        </div>
    </div>
</div>
