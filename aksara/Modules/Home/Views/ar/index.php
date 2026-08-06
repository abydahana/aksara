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
                تنبيه!
            </h4>
            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH; ?></b> غير قابل للكتابة.
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH; ?></b> غير قابل للكتابة.
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>انقر هنا</b></a> للحصول على نصيحة حول كيفية حل هذه المشكلة.
        </div>
    </div>
<?php endif; ?>

<div class="py-3 py-md-5 bg-light" dir="rtl" style="text-align: right;">
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
                    أنت تستخدم <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>أكسارا</b></a>!
                </h3>
            </div>
        </div>
    </div>
</div>
<div class="py-5" dir="rtl" style="text-align: right;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="mb-5">
                    <p>
                        أنت تشاهد هذه الصفحة لأنك قمت بتثبيت <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>أكسارا</b></a> في "<b>وضع المطور</b>". لا يوجد محتوى تجريبي تم إنشاؤه. تمامًا مثل إطارات عمل PHP الشهيرة، يجب عليك بناء وحداتك الخاصة من خلال الإشارة إلى الوظيفة التي تقدمها <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>أكسارا</b></a>. لا يزال بإمكانك تسجيل الدخول وإضافة المحتوى الخاص بك إلى الوحدة المدمجة (<b>نظام إدارة المحتوى CMS</b>) مثل <b>المدونات</b>، و<b>الصفحات</b>، و<b>المعارض</b> وغيرها الكثير.
                    </p>
                    <p>
                        تقع هذه الوحدة في
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR; ?>Home</code>.
                    </p>
                    <p>
                        يمكنك <b>تجاوز</b> هذه الوحدة في
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR; ?>Home</code> دون إزالة النسخة الأصلية.
                    </p>
                    <p>
                        <b>كيف يمكن القيام بذلك؟</b> لأنك تستخدم <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>أكسارا</b></a>!
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    اذهب أبعد من ذلك
                </h3>
                <h4 class="mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    التوثيق
                </h4>
                <div class="mb-5">
                    <p>
                        تحتوي الإرشادات على مقدمة، وبرامج تعليمية، وعدد من أدلة "كيفية"، ثم توثيق مرجعي للمكونات التي تشكل <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>أكسارا</b></a>.
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>تحقق من التوثيق</b></a>!
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    المجتمع
                </h4>
                <div class="mb-5">
                    <p>
                        يمكنك فتح نقاش يتعلق بالميزات أو الأخطاء أو الاقتراحات في منتدى المجتمع التالي:
                    </p>
                    <p class="mb-1">
                        <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank" dir="ltr">
                            https://github.com/abydahana/aksara/issues<i class="mdi mdi-open-in-new"></i>
                        </a>
                    </p>
                    <p class="mb-1">
                        <a href="https://github.com/abydahana/aksara/discussions" class="text-primary" target="blank" dir="ltr">
                            https://github.com/abydahana/aksara/discussions<i class="mdi mdi-open-in-new"></i>
                        </a>
                    </p>
                    <p>
                        يُسمح لك أيضًا بإنشاء نقاش رسمي في المنتدى يتعلق بـ <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>أكسارا</b></a> في وسائل التواصل الاجتماعي المفضلة لديك.
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    المساهمة
                </h4>
                <div class="mb-5">
                    <p>
                        يُسمح لك بالمساهمة عن طريق كتابة التوثيق، وإنشاء الوحدات وإضافة المكتبات المناسبة لجعل <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>أكسارا</b></a> أفضل. تتم هذه المساهمات في شكل <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>مشكلات (Issues)</b></a> أو <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>طلبات سحب (Pull Request)</b></a> على مستودع <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>أكسارا</b></a> على <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>.
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    الدعم
                </h4>
                <div class="mb-5">
                    <p>
                        بصفتي باحثًا منفردًا، أرغب أحيانًا في الاستمتاع بعالم خارجي لم أستكشفه من قبل. ربما مع قليل من الإجازة، يمكنني الخروج بفكرة مشرقة أخرى لتطبيقها على بحثي.
                    </p>
                    <p>
                        مثل معظم الباحثين المنفردين، إذا شعرت أن البحث الذي قمت به ساعدك وترغب في تقديم الدعم المعنوي أو المادي، فلا تتردد في الاتصال بي من <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>موقع تطوير أكسارا</b></a>. سأكون ممتنًا جدًا لأي دعم تقدمه، وبالطبع سيمنحني المزيد من الثقة.
                    </p>
                </div>
                <h5 class="text-center">
                    مرة أخرى، شكرًا لتجربتك <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>أكسارا</b></a>.
                </h5>
                <h5 class="text-center mb-3">
                    نحن رائعون!
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank" dir="ltr"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h4>
            </div>
        </div>
    </div>
</div>
