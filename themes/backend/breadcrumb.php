<nav role="breadcrumb" class="position-fixed w-100 bg-light border-bottom" id="breadcrumb-wrapper">
    <div class="container-fluid">
        <ol class="breadcrumb rounded-0 mb-0">
            <?php foreach ($breadcrumb as $key => $val): ?>
                <li class="breadcrumb-item">
                    <?= ($val->url ? '<a href="' . $val->url . '" class="--xhr">' : '<b class="text-muted">'); ?>
                        <?= ($val->icon ? '<i class="' . $val->icon . '"></i>' : null); ?>
                        <?= $val->label; ?>
                    <?= ($val->url ? '</a>' : '</b>'); ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</nav>
