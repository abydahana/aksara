<nav role="breadcrumb" aria-label="breadcrumb" id="breadcrumb-wrapper">
    <ol class="breadcrumb rounded-0 mb-0 pt-1 pb-1">
        <?php foreach ($breadcrumb as $key => $val): ?>
            <li class="breadcrumb-item">
                <?= ($val->url ? '<a href="' . $val->url . '" class="--xhr">' : '<b class="text-muted">'); ?>
                    <?= ($val->icon ? '<i class="' . $val->icon . '"></i>' : null); ?>
                    <?= $val->label; ?>
                <?= ($val->url ? '</a>' : '</b>'); ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
