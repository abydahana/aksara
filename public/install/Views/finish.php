<h4>
	<?php echo phrase('congratulations'); ?>
</h4>
<p>
	<?php echo phrase('aksara_has_been_successfully_installed_on_your_system'); ?>
</p>
<hr class="row" />
<p class="mb-0">
	<?php echo phrase('you_can_login_as_superuser_using_following_credential'); ?>
</p>
<div class="row">
	<div class="col-4 font-weight-bold">
		<?php echo phrase('username'); ?>
	</div>
	<div class="col-8">
		<?php echo session()->get('username'); ?>
	</div>
</div>
<div class="row form-group">
	<div class="col-4 font-weight-bold">
		<?php echo phrase('password'); ?>
	</div>
	<div class="col-8">
		<?php echo session()->get('password'); ?>
	</div>
</div>
<hr />
<div class="row">
	<div class="col-md-5">
		<img src="assets/like-a-boss.png" class="img-fluid" alt="Like a boss..." />
	</div>
	<div class="col-md-7">
		<p>
			<?php echo phrase('follow_our_updates_to_get_our_other_works_if_you_find_this_useful'); ?>
		</p>
		<p>
			<?php echo phrase('just_to_remind_you'); ?>
			<?php echo phrase('we_also_collect_donations_from_people_like_you_to_support_our_research'); ?>
		</p>
		<p>
			<?php echo phrase('regardless_of_the_amount_will_be_very_useful'); ?>
		</p>
		<p>
			<?php echo phrase('cheers'); ?>,
			<br />
			<a href="//abydahana.github.io" class="text-primary text-decoration-none" target="_blank">
				<b>Aby Dahana</b>
			</a>
		</p>
	</div>
</div>
<hr class="row" />
<div class="row">
	<div class="col-sm-6">
		&nbsp;
	</div>
	<div class="col-sm-6">
		<a href="../<?php echo ((isset($_SERVER['HTTP_MOD_REWRITE']) && strtolower($_SERVER['HTTP_MOD_REWRITE']) == 'on') || (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) ? null : 'index.php/'); ?>xhr/boot" class="btn btn-warning btn-block font-weight-bold">
			<i class="mdi mdi-rocket"></i>
			<?php echo phrase('launch_your_site'); ?>
		</a>
	</div>
</div>