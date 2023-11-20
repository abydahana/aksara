<?php
    $pager->setSurroundCount(0);

    $current_page = 1;
    $last_page = parse_str(parse_url($pager->getLast(), PHP_URL_QUERY), $output);
    $last_page = (isset($output['page']) ? $output['page'] : 0);

    if (is_numeric(service('request')->getGet('per_page')) && service('request')->getGet('per_page')) {
        $current_page = service('request')->getGet('per_page');
    }
?>

<ul class="pagination pagination-sm mb-0">
    <li class="page-item<?= ($current_page <= 1 ? ' disabled' : null); ?>">
        <a href="<?= ($current_page > 1 ? current_page(null, ['per_page' => 0]) : 'javascript:void(0)'); ?>" class="page-link --xhr" aria-label="<?= phrase('First'); ?>">
            <?= phrase('First'); ?>
        </a>
    </li>

    <?php if ($current_page > 1): ?>
    <li class="page-item">
        <a href="<?= current_page(null, ['per_page' => ($current_page - 1)]); ?>" class="page-link --xhr" aria-label="<?= phrase('Previous'); ?>">
            &lt;
        </a>
    </li>
    <?php endif; ?>

    <li class="page-item active">
        <a href="javascript:void(0)" class="page-link">
            <?= $current_page; ?>
        </a>
    </li>

    <?php if ($last_page > $current_page): ?>
    <li class="page-item">
        <a href="<?= current_page(null, ['per_page' => ($current_page + 1)]); ?>" class="page-link --xhr" aria-label="<?= phrase('Next'); ?>">
            &gt;
        </a>
    </li>
    <?php endif; ?>

    <li class="page-item<?= ($last_page <= $current_page ? ' disabled' : null); ?>">
        <a href="<?= ($last_page > $current_page ? current_page(null, ['per_page' => $last_page]) : 'javascript:void(0)'); ?>" class="page-link --xhr" aria-label="<?= phrase('Last'); ?>">
            <?= phrase('Last'); ?>
        </a>
    </li>
</ul>
