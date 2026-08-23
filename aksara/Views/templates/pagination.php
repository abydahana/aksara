<?php

/**
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(0);

$currentPage = 1;
parse_str(parse_url($pager->getLast(), PHP_URL_QUERY), $output);
$lastPage = $output['page'] ?? 0;

if (is_numeric(service('request')->getGet('page')) && service('request')->getGet('page')) {
    $currentPage = service('request')->getGet('page');
}
?>

<ul class="pagination pagination-sm mb-0">
    <li class="page-item<?= $currentPage <= 1 ? ' disabled' : null ?>">
        <a href="<?= $currentPage > 1 ? current_page(null, ['page' => null]) : current_page() ?>" class="page-link text-nowrap --xhr" id="first" aria-label="<?= phrase('First') ?>">
            <?= phrase('First') ?>
        </a>
    </li>

    <?php if ($currentPage > 1): ?>
        <li class="page-item">
            <a href="<?= current_page(null, [
              'page' => $currentPage - 1,
            ]) ?>" class="page-link text-nowrap --xhr" id="prev" aria-label="<?= phrase('Previous') ?>">
                <?= service('request')->getHeaderLine('X-API-KEY') ? phrase('Prev') : '&lt;' ?>
            </a>
        </li>
    <?php endif; ?>

    <li class="page-item active">
        <a href="<?= current_page() ?>" class="page-link text-nowrap" id="current">
            <?= $currentPage ?>
        </a>
    </li>

    <?php if ($lastPage > $currentPage): ?>
        <li class="page-item">
            <a href="<?= current_page(null, [
              'page' => $currentPage + 1,
            ]) ?>" class="page-link text-nowrap --xhr" id="next" aria-label="<?= phrase('Next') ?>">
                <?= service('request')->getHeaderLine('X-API-KEY') ? phrase('Next') : '&gt;' ?>
            </a>
        </li>
    <?php endif; ?>

    <li class="page-item<?= $lastPage <= $currentPage ? ' disabled' : null ?>">
        <a href="<?= $lastPage > $currentPage ? current_page(null, ['page' => $lastPage]) : current_page() ?>" class="page-link text-nowrap --xhr" id="last" aria-label="<?= phrase('Last') ?>">
            <?= phrase('Last') ?>
        </a>
    </li>
</ul>
