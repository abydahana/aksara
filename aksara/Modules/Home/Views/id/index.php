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
                Pemberitahuan!
            </h4>
            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH; ?></b> tidak dapat ditulis.
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH; ?></b> tidak dapat ditulis.
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>Klik di sini</b></a> untuk mendapatkan panduan cara menyelesaikan masalah ini.
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
                    Anda menggunakan <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
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
                        Anda melihat halaman ini karena Anda menginstal <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> dalam "<b>MODE PENGEMBANG</b>". Belum ada konten contoh yang dibuat. Seperti kerangka kerja PHP populer lainnya, Anda harus membangun modul Anda sendiri dengan merujuk pada fungsi yang disajikan oleh <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>. Anda tetap dapat masuk dan menambahkan konten Anda ke modul bawaan (<b>CMS</b> atau <b>Sistem Manajemen Konten</b>) seperti <b>Blog</b>, <b>Halaman</b>, <b>Galeri</b>, dan banyak lagi.
                    </p>
                    <p>
                        Modul ini terletak di
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR; ?>Home</code>.
                    </p>
                    <p>
                        Anda dapat <b>menimpa</b> modul ini ke
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR; ?>Home</code> tanpa menghapus versi aslinya.
                    </p>
                    <p>
                        <b>Bagaimana hal itu bisa dilakukan?</b> Karena Anda menggunakan <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    Lebih Lanjut
                </h3>
                <h4 class="mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    Dokumentasi
                </h4>
                <div class="mb-5">
                    <p>
                        Panduan ini berisi pengantar, tutorial, sejumlah panduan praktis, dan dokumentasi referensi untuk komponen-komponen yang menyusun <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>Lihat Dokumentasi</b></a>!
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    Komunitas
                </h4>
                <div class="mb-5">
                    <p>
                        Anda dapat membuka diskusi terkait fitur, bug, atau saran di forum komunitas berikut:
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
                        Anda juga diizinkan untuk membuat diskusi forum resmi terkait <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> di media sosial favorit Anda.
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    Kontribusi
                </h4>
                <div class="mb-5">
                    <p>
                        Anda diizinkan untuk berkontribusi dengan menulis dokumentasi, membuat modul, dan menambahkan pustaka yang sesuai untuk membuat <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> menjadi lebih baik. Kontribusi ini dilakukan dalam bentuk <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Issues</b></a> atau <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Pull Request</b></a> di repositori <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a> di <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>.
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    Dukungan
                </h4>
                <div class="mb-5">
                    <p>
                        Sebagai seorang peneliti tunggal (single fighter), saya sesekali ingin menikmati dunia luar yang belum pernah saya jelajahi. Mungkin dengan sedikit liburan, saya bisa mendapatkan ide cemerlang lainnya untuk diterapkan pada penelitian saya.
                    </p>
                    <p>
                        Seperti kebanyakan peneliti tunggal lainnya, jika Anda merasa terbantu dengan penelitian yang saya lakukan dan ingin memberikan dukungan moral atau materiil, jangan ragu untuk menghubungi saya melalui <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>situs web pengembangan Aksara</b></a>. Saya akan sangat menghargai apa pun dukungan Anda, dan tentu saja itu akan memberi saya lebih banyak kepercayaan diri.
                    </p>
                </div>
                <h5 class="text-center">
                    Sekali lagi, terima kasih telah mencoba <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                </h5>
                <h5 class="text-center mb-3">
                    Kita luar biasa!
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h4>
            </div>
        </div>
    </div>
</div>
