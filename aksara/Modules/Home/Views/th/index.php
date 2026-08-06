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
                ประกาศ!
            </h4>
            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH; ?></b> ไม่สามารถเขียนได้
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH; ?></b> ไม่สามารถเขียนได้
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>คลิกที่นี่</b></a> เพื่อรับคำแนะนำวิธีแก้ปัญหานี้
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
                    คุณกำลังใช้ <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
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
                        คุณกำลังดูหน้านี้เนื่องจากคุณติดตั้ง <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> ในโหมด "<b>DEVELOPER MODE</b>" ยังไม่มีการสร้างเนื้อหาตัวอย่าง เช่นเดียวกับเฟรมเวิร์ก PHP ยอดนิยมอื่น ๆ คุณต้องสร้างโมดูลของคุณเองโดยอ้างอิงถึงฟังก์ชันที่ให้บริการโดย <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> คุณยังคงสามารถเข้าสู่ระบบและเพิ่มเนื้อหาของคุณลงในโมดูลที่ติดตั้งมาให้ (<b>CMS</b> หรือ <b>ระบบจัดการเนื้อหา</b>) เช่น <b>บล็อก</b>, <b>หน้าเว็บ</b>, <b>แกลเลอรี</b> และอื่น ๆ อีกมากมาย
                    </p>
                    <p>
                        โมดูลนี้ตั้งอยู่ใน
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR; ?>Home</code>
                    </p>
                    <p>
                        คุณสามารถ <b>เขียนทับ (override)</b> โมดูลนี้ไปที่
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR; ?>Home</code> ได้โดยไม่ต้องลบไฟล์ต้นฉบับ
                    </p>
                    <p>
                        <b>เป็นไปได้อย่างไร?</b> เพราะคุณกำลังใช้ <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    ไปให้ไกลกว่านั้น
                </h3>
                <h4 class="mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    เอกสารกำกับ
                </h4>
                <div class="mb-5">
                    <p>
                        แนวทางปฏิบัติประกอบด้วยบทนำ บทช่วยสอน คู่มือ "วิธีใช้งาน" และเอกสารอ้างอิงสำหรับส่วนประกอบต่างๆ ที่สร้าง <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>ตรวจสอบเอกสารกำกับ</b></a>!
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    ชุมชน
                </h4>
                <div class="mb-5">
                    <p>
                        คุณสามารถเปิดการสนทนาที่เกี่ยวข้องกับคุณสมบัติ ข้อบกพร่อง หรือข้อเสนอแนะในฟอรัมชุมชนต่อไปนี้:
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
                        นอกจากนี้ คุณยังได้รับอนุญาตให้สร้างฟอรัมสนทนาอย่างเป็นทางการที่เกี่ยวข้องกับ <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> บนโซเชียลมีเดียที่คุณชื่นชอบ
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    มีส่วนร่วม
                </h4>
                <div class="mb-5">
                    <p>
                        คุณได้รับอนุญาตให้มีส่วนร่วมโดยการเขียนเอกสารประกอบ สร้างโมดูล และเพิ่มไลบรารีที่เหมาะสมเพื่อทำให้ <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> ดียิ่งขึ้นไปอีก การมีส่วนร่วมเหล่านี้ทำในรูปแบบของ <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>ปัญหา (Issues)</b></a> หรือ <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>คำขอดึง (Pull Request)</b></a> บนพื้นที่เก็บข้อมูล <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a> บน <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>
                    </p>
                </div>
                <h4 class="mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    การสนับสนุน
                </h4>
                <div class="mb-5">
                    <p>
                        ในฐานะนักวิจัยอิสระ บางครั้งฉันต้องการเพลิดเพลินกับโลกภายนอกที่ไม่เคยสำรวจ บางทีอาจจะไปพักผ่อนสักหน่อยเพื่อนำไอเดียใหม่ ๆ มาใช้กับงานวิจัยของฉัน
                    </p>
                    <p>
                        เช่นเดียวกับนักวิจัยอิสระส่วนใหญ่ หากคุณรู้สึกว่าได้รับความช่วยเหลือจากงานวิจัยที่ฉันทำ และต้องการให้การสนับสนุนทางศีลธรรมหรือทางวัตถุ อย่าลังเลที่จะติดต่อฉันได้จาก <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>เว็บไซต์นักพัฒนา Aksara</b></a> ฉันยินดีรับการสนับสนุนทุกรูปแบบ ซึ่งแน่นอนว่ามันจะช่วยเพิ่มความมั่นใจให้ฉันมากยิ่งขึ้น
                    </p>
                </div>
                <h5 class="text-center">
                    ขอขอบคุณอีกครั้งที่ทดลองใช้ <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>
                </h5>
                <h5 class="text-center mb-3">
                    พวกเราสุดยอด!
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h4>
            </div>
        </div>
    </div>
</div>
