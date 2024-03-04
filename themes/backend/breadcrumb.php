<nav role="breadcrumb" class="position-fixed w-100 bg-light border-bottom" id="breadcrumb-wrapper">
	<div class="container-fluid">
		<ol class="breadcrumb rounded-0 mb-0">
			<?php
                foreach ($breadcrumb as $key => $val) {
                    echo '
						<li class="breadcrumb-item">
							<a href="' . $val->url . '" class="--xhr">
								' . ($val->icon ? '<i class="' . $val->icon . '"></i>' : null) . '
								' . $val->label . '
							</a>
						</li>
					';
                }
			?>
		</ol>
	</div>
</nav>