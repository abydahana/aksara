<?php
$link_left											= null;
$link_right											= null;

if(isset($suggestions) && $suggestions)
{
	$num											= 1;
	
	foreach($suggestions as $key => $val)
	{
		if($num % 2 == 0)
		{
			$link_right								.= '
				<li>
					<a href="' . base_url('pages/' . $val->page_slug) . '" class="--xhr">
						' . $val->page_title . '
					</a>
				</li>
			';
		}
		else
		{
			$link_left								.= '
				<li>
					<a href="' . base_url('pages/' . $val->page_slug) . '" class="--xhr">
						' . $val->page_title . '
					</a>
				</li>
			';
		}
		
		$num++;
	}
}

echo '
	<div class="container pt-5 pb-5">
		<div class="text-center pt-5 pb-5">
			<h1 class="text-muted">
				404
			</h1>
			<i class="mdi mdi-dropbox mdi-5x text-muted"></i>
		</div>
		<div class="row mb-5">
			<div class="col-md-6 offset-md-3">
				<h2 class="text-center">
					' . phrase('page_not_found') . '
				</h2>
				<p class="lead text-center mb-5">
					' . phrase('the_page_you_requested_does_not_exist') . '
				</p>
				<div class="text-center mt-5">
					<a href="' . base_url() . '" class="btn btn-outline-primary rounded-pill --xhr">
						<i class="mdi mdi-arrow-left"></i>
						' . phrase('back_to_home') . '
					</a>
				</div>
			</div>
		</div>
		<div class="row mb-2">
			<div class="col-md-10 offset-md-1">
				<h5>
					<i class="mdi mdi-lightbulb-on-outline"></i>
					' . phrase('our_suggestions') . '
					<blink>_</blink>
				</h5>
			</div>
		</div>
		<div class="row">
			<div class="col-md-5 offset-md-1">
				<ul>
					' . $link_left . '
				</ul>
			</div>
			<div class="col-md-5">
				<ul>
					' . $link_right . '
				</ul>
			</div>
		</div>
	</div>
';
