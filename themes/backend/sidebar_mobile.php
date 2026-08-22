<?php

/**
 * @var array $menus
 */
$bottomSheetMenus = generate_menu($menus, 'navbar-nav flex-column gap-1', 'nav-item', 'nav-link d-flex gap-2 align-items-center --xhr', 'dropdown-toggle', 'data-bs-toggle="dropdown"', 'dropdown', 'dropdown-menu');
?>

<!-- Bottom Sheet offcanvas for Mobile Navigation -->
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasBottomSheet" aria-labelledby="offcanvasBottomSheetLabel">
    <div class="offcanvas-header flex-column align-items-center pb-0 position-relative">
        <div class="drag-handle mb-1"></div>
        <h5 class="offcanvas-title w-100 text-center" id="offcanvasBottomSheetLabel">
            <?= phrase('Main Navigation') ?>
        </h5>
    </div>
    <div class="offcanvas-body">
        <div class="p-3 user-bg-masking mb-3 rounded-3">
            <div class="d-flex align-items-center">
                <div class="flex-grow-0">
                    <a href="<?= base_url('user') ?>">
                        <img src="<?= get_image('users', get_userdata('photo'), 'thumb') ?>" class="img-fluid rounded-4" width="60" height="60" alt="<?= get_userdata('first_name') . ' ' . get_userdata('last_name') ?>" loading="lazy" decoding="async" />
                    </a>
                </div>
                <div class="flex-grow-1 ps-3">
                    <a href="<?= base_url('user') ?>">
                        <strong class="fs-5 mb-0 text-break-word mb-0">
                            <?= get_userdata('first_name') . ' ' . get_userdata('last_name') ?>
                        </strong>
                    </a>
                    <p class="text-sm mb-2">
                        <i class="mdi mdi-circle text-success"></i>
                        <?= phrase('Online') . (get_userdata('year') ? ' <span class="badge bg-warning text-dark me-1">' . get_userdata('year') . '</span>' : '') ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('xhr/partial/account') ?>" class="btn btn-outline-primary btn-xs rounded-pill --modal --force-xs">
                            <i class="mdi mdi-cogs"></i> <?= phrase('Account') ?>
                        </a>
                        <a href="<?= base_url('xhr/partial/language') ?>" class="btn btn-sm rounded-pill --modal --force-xs">
                            <i class="mdi mdi-translate"></i> <?= phrase('Language') ?>
                            <i class="mdi mdi-chevron-down"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?= $bottomSheetMenus ?>
    </div>
</div>
