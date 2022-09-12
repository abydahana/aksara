<div class="container-fluid">
	<div class="row">
		<div class="col-md-3 col-lg-2 order-2 order-md-1">
			<div class="sticky-top">
				<div class="pretty-scrollbar">
					<?php
						$errors						= null;
						
						if($logs)
						{
							foreach($logs as $key => $val)
							{
								$errors				.= '
									<li class="list-group-item">
										<a href="' . current_page('remove', array('log' => $val)) . '" class="float-end text-danger --xhr" data-bs-toggle="tooltip" title="' . phrase('remove') . '">
											<i class="mdi mdi-window-close"></i>
										</a>
										<a href="' . current_page(null, array('report' => $val)) . '" class="--xhr' . ($val == service('request')->getGet('report') ? ' fw-bold' : null) . '">' . $val . '</a>
									</li>';
							}
							
							echo '
								<div class="d-grid mt-3 mb-3">
									<a href="' . current_page('clear') . '" class="btn btn-danger btn-sm --xhr">
										<i class="mdi mdi-delete-empty"></i>
										' . phrase('clear_logs') . '
									</a>
								</div>
								<ul class="list-group list-group-flush">
									' . $errors . '
								</ul>
							';
						}
						else
						{
							echo '<div class="pt-3 pb-3">' . phrase('no_error_log') . '</div>';
						}
					?>
				</div>
			</div>
		</div>
		<div class="col-md-9 col-lg-10 order-1 order-md-2 stretch-height">
			<div class="sticky-top">
				<?php
					if($report)
					{
						$num						= 0;
						
						foreach($report as $key => $val)
						{
							if(!$val || !trim($val)) continue;
							
							$error					= explode("\n", trim($val));
							$title					= null;
							$message				= null;
							$traces					= null;
							
							if($error)
							{
								foreach($error as $_key => $_val)
								{
									if(!$_key)
									{
										$title		= $_val;
									}
									else if($_key == 1)
									{
										$message	= $_val;
									}
									else
									{
										$traces		.= '<li>' . substr($_val, 2) . '</li>';
									}
								}
							}
							
							echo '
								<div class="pt-3 pb-3' . ($num ? 'mt-3' : null) . '">
									<b class="text-danger">
										' . $title . '
									</b>
									<p class="mb-0">
										' . $message . '
									</p>
									<ol>
										' . $traces . '
									</ol>
								</div>
							';
							
							$num++;
						}
					}
					else
					{
						echo '<div class="pt-3 pb-3">' . ($errors ? phrase('click_on_the_log_file_to_show_the_error_details') : phrase('yay_your_application_is_working_fine')) . '</div>';
					}
				?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		if(UA !== 'mobile')
		{
			$('.stretch-height').css
			({
				minHeight: $(window).outerHeight(true) - (($('.navbar').length ? $('.navbar').outerHeight(true) : 0) + ($('.alias-table-header').length ? $('.alias-table-header').outerHeight(true) : 0)),
				borderLeft: '1px solid rgba(0,0,0,.2)'
			}),
			
			$('.pretty-scrollbar').mCustomScrollbar
			({
				autoHideScrollbar: true,
				axis: 'y',
				scrollInertia: 170,
				mouseWheelPixels: 170,
				setHeight:  $(window).outerHeight(true) - (($('.navbar').length ? $('.navbar').outerHeight(true) : 0) + ($('.alias-table-header').length ? $('.alias-table-header').outerHeight(true) : 0)),
				advanced:
				{
					updateOnContentResize: true
				},
				autoHideScrollbar: false
			})
		}
	})
</script>