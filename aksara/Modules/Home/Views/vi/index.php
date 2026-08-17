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
                Thông báo!
            </h2>
            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= FCPATH . UPLOAD_PATH ?></b> không thể ghi.
                </p>
            <?php endif; ?>

            <?php if (! $permission->uploads): ?>
                <p class="mb-0 text-danger">
                    <b><?= WRITEPATH ?></b> không thể ghi.
                </p>
            <?php endif; ?>

            <br />
            <a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>Nhấn vào đây</b></a> để nhận lời khuyên về cách giải quyết vấn đề này.
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
                    Bạn đang sử dụng <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
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
                        Bạn đang xem trang này vì bạn đã cài đặt <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> ở "<b>CHẾ ĐỘ NHÀ PHÁT TRIỂN</b>". Chưa có nội dung mẫu nào được tạo. Giống như các framework PHP phổ biến, bạn phải xây dựng các mô-đun của riêng mình bằng cách tham khảo các hàm được cung cấp bởi <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>. Bạn vẫn có thể đăng nhập và thêm nội dung của mình vào mô-đun tích hợp (<b>CMS</b> hay còn gọi là <b>Hệ thống quản trị nội dung</b>) như <b>Blog</b>, <b>Trang</b>, <b>Thư viện ảnh</b> và nhiều thứ khác.
                    </p>
                    <p>
                        Mô-đun này được đặt trong
                        <br />
                        <code><?= ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR ?>Home</code>.
                    </p>
                    <p>
                        Bạn có thể <b>ghi đè</b> mô-đun này vào
                        <br />
                        <code><?= ROOTPATH . 'modules' . DIRECTORY_SEPARATOR ?>Home</code> mà không cần xóa phiên bản gốc.
                    </p>
                    <p>
                        <b>Làm thế nào có thể làm được điều đó?</b> Bởi vì bạn đang sử dụng <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
                    </p>
                </div>
                <hr class="mt-5 mb-5" />
                <h3 class="mb-3 text-center">
                    Tiến Xa Hơn
                </h2>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-book-open-page-variant"></i>
                    &nbsp;
                    Tài liệu
                </h2>
                <div class="mb-5">
                    <p>
                        Tài liệu hướng dẫn bao gồm phần giới thiệu, hướng dẫn, một số bài hướng dẫn "cách làm" và sau đó là tài liệu tham khảo cho các thành phần tạo nên <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                        <br />
                        <a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>Kiểm tra Tài liệu</b></a>!
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-group-outline"></i>
                    &nbsp;
                    Cộng đồng
                </h2>
                <div class="mb-5">
                    <p>
                        Bạn có thể mở cuộc thảo luận liên quan đến các tính năng, lỗi hoặc đề xuất trên diễn đàn cộng đồng sau:
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
                        Bạn cũng được phép tạo cuộc thảo luận trên diễn đàn chính thức liên quan đến <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> trên mạng xã hội yêu thích của bạn.
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-flask-outline"></i>
                    &nbsp;
                    Đóng góp
                </h2>
                <div class="mb-5">
                    <p>
                        Bạn được phép đóng góp bằng cách viết tài liệu, tạo các mô-đun và thêm các thư viện phù hợp để làm cho <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> trở nên tốt hơn nữa. Các đóng góp này được thực hiện dưới dạng <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Vấn đề (Issues)</b></a> hoặc <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Yêu cầu kéo (Pull Request)</b></a> trên kho lưu trữ <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>Aksara</b></a> trên <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>.
                    </p>
                </div>
                <h2 class="h4 mb-3">
                    <i class="mdi mdi-account-heart-outline"></i>
                    &nbsp;
                    Hỗ trợ
                </h2>
                <div class="mb-5">
                    <p>
                        Với tư cách là một nhà nghiên cứu độc lập (single fighter), đôi khi tôi muốn tận hưởng một thế giới bên ngoài mà tôi chưa từng khám phá. Có lẽ với một chút kỳ nghỉ, tôi có thể nghĩ ra một ý tưởng tuyệt vời khác để áp dụng cho nghiên cứu của mình.
                    </p>
                    <p>
                        Giống như hầu hết các nhà nghiên cứu độc lập khác, nếu bạn cảm thấy được giúp đỡ bởi những nghiên cứu mà tôi đã làm và muốn cung cấp hỗ trợ tinh thần hoặc vật chất, đừng ngần ngại liên hệ với tôi qua <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>trang web phát triển Aksara</b></a>. Tôi thực sự đánh giá cao bất cứ sự hỗ trợ nào của bạn, và tất nhiên nó sẽ mang lại cho tôi nhiều sự tự tin hơn.
                    </p>
                </div>
                <h5 class="text-center">
                    Một lần nữa, cảm ơn bạn đã thử sử dụng <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
                </h5>
                <h5 class="text-center mb-3">
                    Chúng ta thật tuyệt vời!
                </h5>
                <h4 class="text-center">
                    <a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
                </h2>
            </div>
        </div>
    </div>
</div>
