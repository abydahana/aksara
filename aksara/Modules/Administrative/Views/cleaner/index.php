<?php
	$hour											= floor(service('request')->config->sessionExpiration / 3600);
?>
<div class="container-fluid pt-3 pb-3">
	<div class="alert alert-warning">
		<p>
			<b>
				<?php echo phrase('you_are_about_to_cleaning_up_unused_garbage_session'); ?>
			</b>
			<br />
			<?php echo phrase('all_inactive_session_within'); ?> <b><?php echo $hour . ' ' . ($hour > 1 ? phrase('hours') : phrase('hour')); ?></b> <?php echo phrase('will_be_removed'); ?>
			<?php echo phrase('this_action_cannot_be_undone'); ?>
		</p>
		<hr />
		<a href="<?php echo go_to('clean'); ?>" class="btn btn-warning btn-sm --xhr show-progress">
			<i class="mdi mdi-check"></i>
			<?php echo phrase('click_to_continue'); ?>
		</a>
	</div>
</div>
