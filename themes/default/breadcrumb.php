<nav role="breadcrumb" aria-label="breadcrumb" id="breadcrumb-wrapper">
    <ol class="breadcrumb rounded-0 mb-0 pt-1 pb-1">
        <?php foreach ($breadcrumb as $key => $val): ?>
            <li class="breadcrumb-item">
                <a href="<?= $val->url; ?>" class="--xhr">
                    <?= ($val->icon ? '<i class="' . $val->icon . '"></i>' : null); ?>
                    <?= $val->label; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>