<?php

/**
 * @var object $meta
 * @var array $results
 */
?>

<div class="container-fluid pt-3 pb-3">
	<?php
        $leftItem = null;
        $rightItem = null;
        foreach ($results as $key => $val) {
            /* check if item is applicable for current user group id */
            if (isset($val->group_id) && is_array($val->group_id) && sizeof($val->group_id) > 0 && ! in_array(get_userdata('group_id'), $val->group_id)) {
            continue;
            }

            /* check if item is applying some parameter (extra input) */
            $parameter = null;
            if (isset($val->parameter)) {
                foreach ($val->parameter as $_key => $_val) {
                    $parameter .= $_val;
                }
            }

            $item = '
				<div class="card border border-hover rounded-4 mb-3 ' . (isset($val->color) ? $val->color : 'bg-body-secondary') . '">
					<a href="#collapse_' . $key . '" class="card-header border-0" data-bs-toggle="collapse">
						<div class="row align-items-center">
							<div class="col-3 col-sm-2">
								<i class="mdi ' . (isset($val->icon) ? $val->icon : $meta->icon) . ' mdi-3x"></i>
							</div>
							<div class="col col-sm-10">
								<h5 class="mb-0 text-truncate">
									' . $val->title . '
								</h5>
								<p class="mb-0 text-truncate">
									' . $val->description . '
								</p>
							</div>
						</div>
					</a>
					<div id="collapse_' . $key . '" class="collapse' . (! $key ? ' show' : null) . '" data-bs-parent="#accordion">
						<div class="card-body rounded-4 bg-body-tertiary p-3">
							<form action="' . go_to($val->controller) . '" method="GET" target="_blank" class="no-ajax">
								<!--
								<div class="form-group mb-3 alert alert-info">
									' . $val->description . '
								</div>
								-->

								' . $parameter . '

								<div class="row">
									<div class="col-6">
										<div class="d-grid">
											<button type="submit" name="method" class="btn btn-primary btn-sm rounded-pill" value="preview">
												<i class="mdi mdi-magnify"></i>
												' . phrase('Preview') . '
											</button>
										</div>
									</div>
									<div class="col-6">
										<div class="d-grid">
											<button type="submit" name="method" class="btn btn-primary btn-sm rounded-pill" value="embed">
												<i class="mdi mdi-cloud-download"></i>
												' . phrase('Download') . '
											</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			';

            if ('right' == $val->placement) {
                $rightItem .= $item;
            } else {
                $leftItem .= $item;
            }
        } ?>

	<div class="row" id="accordion">
		<div class="col-md-6">
			<?= $leftItem; ?>
		</div>
		<div class="col-md-6">
			<?= $rightItem; ?>
		</div>
	</div>
</div>
