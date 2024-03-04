<aside role="sidebar" class="sidebar-menu" id="sidebar-wrapper">
	<div class="p-3 user-bg-masking hide-on-collapse mb-4">
		<div class="row g-0 align-items-center">
			<div class="col-3 col-sm-2 col-lg-3">
				<a href="<?= base_url('user'); ?>">
					<img src="<?= get_image('users', get_userdata('photo'), 'icon'); ?>" class="img-fluid rounded-4" />
				</a>
			</div>
			<div class="col-9 col-sm-10 col-lg-9 ps-2">
				<a href="<?= base_url('user'); ?>">
					<h6 class="mb-0 text-break-word mb-0">
						<?= get_userdata('first_name') . ' ' . get_userdata('last_name'); ?>
					</h6>
				</a>
				<p class="text-sm mb-lg-0">
					<i class="mdi mdi-circle text-success"></i>
					
					<?= phrase('Online') . (get_userdata('year') ? '<span class="badge badge-warning d-md-none d-lg-none d-xl-none">' . get_userdata('year') . '</span>' : ''); ?>
				</p>
				<p class="d-lg-none d-xl-none mb-0">
					<a href="<?= base_url('xhr/partial/account'); ?>" class="btn btn-outline-primary btn-xs --modal">
						<i class="mdi mdi-cogs"></i>
						<?= phrase('Account'); ?>
					</a>
					<a href="<?= base_url('xhr/partial/language'); ?>" class="btn btn-xs float-end --modal">
						<i class="mdi mdi-translate"></i>
						<?= phrase('Language'); ?>
						<i class="mdi mdi-chevron-down"></i>
					</a>
				</p>
			</div>
		</div>
	</div>
	
	<?= generate_menu($menus, 'nav flex-column', 'nav-item', 'nav-link --xhr', 'dropdown-toggle', 'data-toggle="expand-collapse"', '', 'list-unstyled flex-column collapse'); ?>
	
</aside>
