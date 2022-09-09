<?php
	if(!get_userdata('username') || !get_userdata('password'))
	{
		echo '
			<div class="alert alert-danger border-0 rounded-0 mb-0">
				<h5>
					' . phrase('notice') . '
				</h5>
				' . (!get_userdata('username') ? '<p class="mb-0">' . phrase('please_set_your_username_as_an_alternative_to_the_email_when_sign_in') . '</p>' : null) . '
				' . (!get_userdata('password') ? '<p class="mb-0">' . phrase('please_set_your_password_to_keep_your_account_safe') . '</p>' : null) . '
				<br />
				<a href="' . base_url('administrative/account') . '" class="fw-bold --xhr">' . phrase('update_your_profile_info') . '</a>
			</div>
		';
	}
?>

<div class="container-fluid pt-3 pb-3">
	<h5>
		<?php echo phrase('welcome_back'); ?>, <?php echo get_userdata('first_name') . ' ' . get_userdata('last_name'); ?>!
	</h5>
</div>
