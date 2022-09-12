<?php
$extra_toolbar										= null;
$primary											= array();

if(isset($results->extra_action->toolbar))
{
	foreach($results->extra_action->toolbar as $key => $val)
	{
		$extra_toolbar								.= '
			<a href="' . go_to($val->url, $val->parameter) . '" class="btn btn-sm ' . ($val->class ? $val->class : 'btn-dark --xhr') . ' rounded-pill"' . (isset($val->new_tab) && $val->new_tab == true ? ' target="_blank"' : null) . '>
				<i class="' . ($val->icon ? $val->icon : 'mdi mdi-link') . '"></i>
				' . $val->label . '
			</a>
		';
	}
}
?>
<div class="alias-table-toolbar">
	<div class="container-fluid pt-3 pb-3">
		<div class="row">
			<div class="col-md-10 text-center text-md-start">
				<?php if(!isset($results->unset_action) || !in_array('create', $results->unset_action)) { ?>
					<a href="<?php echo go_to('create'); ?>" class="btn btn-primary btn-sm rounded-pill --btn-create <?php echo (isset($modal_html) ? '--modal' : '--open-modal-form'); ?>">
						<i class="mdi mdi-plus"></i>
						<span class="hidden-xs hidden-sm">
							<?php echo phrase('create'); ?>
						</span>
					</a>
				<?php } ?>
				<?php echo (isset($extra_toolbar) ? $extra_toolbar : null); ?>
				<?php if(!isset($results->unset_action) || !in_array('export', $results->unset_action)) { ?>
					<a href="<?php echo go_to('export'); ?>" class="btn btn-success btn-sm rounded-pill --btn-export" target="_blank">
						<i class="mdi mdi-file-excel"></i>
						<span class="hidden-xs hidden-sm">
							<?php echo phrase('export'); ?>
						</span>
					</a>
				<?php } ?>
				<?php if(!isset($results->unset_action) || !in_array('print', $results->unset_action)) { ?>
					<a href="<?php echo go_to('print'); ?>" class="btn btn-warning btn-sm rounded-pill --btn-print" target="_blank">
						<i class="mdi mdi-printer"></i>
						<span class="hidden-xs hidden-sm">
							<?php echo phrase('print'); ?>
						</span>
					</a>
				<?php } ?>
				<?php if(!isset($results->unset_action) || !in_array('pdf', $results->unset_action)) { ?>
					<a href="<?php echo go_to('pdf'); ?>" class="btn btn-info btn-sm rounded-pill --btn-pdf" target="_blank">
						<i class="mdi mdi-file-pdf"></i>
						<span class="hidden-xs hidden-sm">
							<?php echo phrase('pdf'); ?>
						</span>
					</a>
				<?php } ?>
				<?php if(!isset($results->unset_action) || !in_array('delete', $results->unset_action)) { ?>
					<a href="<?php echo go_to('delete'); ?>" class="btn btn-danger btn-sm rounded-pill disabled d-none --open-delete-confirm" data-bs-toggle="tooltip" title="<?php echo phrase('delete_checked'); ?>" data-bulk-delete="true">
						<i class="mdi mdi-trash-can-outline"></i>
					</a>
				<?php } ?>
			</div>
			<div class="col-md-2">
				<a href="javascript:void(0)" class="btn btn-success btn-sm d-block rounded-pill"data-bs-toggle="modal" data-bs-target="#searchModal">
					<i class="mdi mdi-magnify"></i>
					<?php echo phrase('search'); ?>
				</a>
			</div>
		</div>
	</div>
</div>
<div class="alias-grid-container">
	<div class="container-fluid">
		<?php
			/**
			 * Table data
			 */
			if(isset($results->table_data) && $total)
			{
				$grid								= null;
				
				foreach($results->table_data as $key => $val)
				{
					$item_option					= null;
					$extra_option					= array();
					$reading						= true;
					$updating						= true;
					$deleting						= true;
					
					if(isset($results->extra_action->option[$key]))
					{
						$num						= 0;
						foreach($results->extra_action->option[$key] as $_key => $_val)
						{
							if(isset($_val->new_tab->restrict))
							{
								$id					= key((array) $_val->new_tab->restrict);
								if(in_array($val->$id->original, $_val->new_tab->restrict->$id)) continue;
							}
							
							$extra_option[]			= array
							(
								'url'				=> go_to($_val->url, $_val->parameter),
								'class'				=> 'btn ' . str_replace('btn', 'btn-ignore', $_val->class),
								'icon'				=> $_val->icon,
								'label'				=> $_val->label,
								'new_tab'			=> $_val->new_tab
							);
						}
					}
					
					$primary_key					= array();
					$hash							= null;
					$columns						= null;
					$slideshow						= null;
					$slideshow_count				= 0;
					
					foreach($val as $field => $params)
					{
						if(isset($params->token))
						{
							$hash					= $params->token;
						}
						
						if($params->primary)
						{
							$primary_key[$field]	= $params->primary;
							
							if(isset($results->unset_read->$field) && is_array($results->unset_read->$field) && in_array($params->original, $results->unset_read->$field))
							{
								$reading			= false;
							}
							
							if(isset($results->unset_update->$field) && is_array($results->unset_update->$field) && in_array($params->original, $results->unset_update->$field))
							{
								$updating			= false;
							}
							
							if(isset($results->unset_delete->$field) && is_array($results->unset_delete->$field) && in_array($params->original, $results->unset_delete->$field))
							{
								$deleting			= false;
							}
						}
						
						if($params->hidden) continue;
						
						if(isset($results->grid->thumbnail) && $field == $results->grid->thumbnail && array_intersect($params->type, array('image', 'images')))
						{
							$qs						= array();
							
							if($results->grid->parameter)
							{
								foreach($results->grid->parameter as $_key => $_val)
								{
									$qs[$_key]		= (isset($val->$_val->original) ? $val->$_val->original : $_val);
								}
							}
							
							$images					= json_decode($params->original);
							
							if($images)
							{
								$num				= 0;
								
								foreach($images as $src => $alt)
								{
									if(!in_array(strtolower(pathinfo($src, PATHINFO_EXTENSION)), array('jpg', 'jpeg', 'png', 'gif'))) continue;
									
									if($num == 3) break;
									
									$slideshow		.= '
										<div class="carousel-item rounded-4' . (!$num ? ' active' : null) . '">
											<a href="' . (isset($results->grid->url[$key]) ? $results->grid->url[$key] : get_image($results->grid->path, $src, 'thumb')) . '"' . (isset($results->grid->url[$key]) && !$results->grid->new_tab ? ' class="--xhr"' : ' target="_blank"') . '>
												<div class="clip gradient-top rounded-top"></div>
												<img src="' . get_image($results->grid->path, $src, 'thumb') . '" class="d-block rounded w-100" alt="' . $alt . '">
											</a>
											<div class="carousel-caption">
												<p>
													' . $alt . '
												</p>
											</div>
										</div>
									';
									
									$num++;
								}
								
								$slideshow_count	= $num;
							}
							else
							{
								$slideshow			= '
									<div class="carousel-item rounded-4 active">
										<a href="' . (isset($results->grid->url[$key]) ? $results->grid->url[$key] : get_image($results->grid->path, $params->original, 'thumb')) . '"' . (isset($results->grid->url[$key]) && !$results->grid->new_tab ? ' class="--xhr"' : ' target="_blank"') . '>
											<div class="clip gradient-top rounded-top"></div>
											<img src="' . get_image($results->grid->path, $params->original, 'thumb') . '" class="d-block rounded w-100" alt="...">
										</a>
									</div>
								';
							}
						}
						else
						{
							$columns				.= '
								<li class="list-group-item pt-1 pb-1" data-bs-toggle="tooltip" title="' . $params->label . '">
									' . $params->content . '
								</li>
							';
						}
					}
					
					if($reading && !in_array('read', $results->unset_action))
					{
						$extra_option[]				= array
						(
							'url'					=> go_to('read', $results->query_string[$key]),
							'class'					=> 'btn --modal',
							'icon'					=> 'mdi mdi-magnify',
							'label'					=> phrase('view'),
							'new_tab'				=> false
						);
					}
					
					if($updating && !in_array('update', $results->unset_action))
					{
						$extra_option[]				= array
						(
							'url'					=> go_to('update', $results->query_string[$key]),
							'class'					=> 'btn --modal',
							'icon'					=> 'mdi mdi-square-edit-outline',
							'label'					=> phrase('update'),
							'new_tab'				=> false
						);
					}
					
					if($deleting && !in_array('delete', $results->unset_action))
					{
						$extra_option[]				= array
						(
							'url'					=> go_to('delete', $results->query_string[$key]),
							'class'					=> 'btn --open-delete-confirm',
							'icon'					=> 'mdi mdi-trash-can-outline',
							'label'					=> phrase('delete'),
							'new_tab'				=> false
						);
					}
					
					if($extra_option)
					{
						foreach($extra_option as $_key => $_val)
						{
							if($_key == 3) break;
							
							$item_option			.= '
								<a href="' . $_val['url'] . '" class="text-truncate ' . $_val['class'] . '" data-bs-toggle="tooltip" title="' . $_val['label'] . '"' . (isset($_val['new_tab']) && $_val['new_tab'] ? ' target="_blank"' : null) . '>
									<i class="' . $_val['icon'] . '"></i>
								</a>
							';
							
							unset($extra_option[$_key]);
						}
					}
					
					$grid							.= '
						<div class="col-sm-6 col-md-4 col-lg-3">
							<div class="card shadow-sm rounded-4 border-0 overflow-hidden mb-3">
								' . ($slideshow ? '
								<div id="slideshow_' . $key . '" class="carousel slide" data-bs-ride="carousel">
									<div class="carousel-inner">
										' . $slideshow . '
									</div>
									' . ($slideshow_count > 1 ? '
									<a class="carousel-control-prev gradient-right" href="#slideshow_' . $key . '" role="button" data-bs-slide="prev">
										<span class="carousel-control-prev-icon" aria-hidden="true"></span>
									</a>
									<a class="carousel-control-next gradient-left" href="#slideshow_' . $key . '" role="button" data-bs-slide="next">
										<span class="carousel-control-next-icon" aria-hidden="true"></span>
									</a>
									' : null) . '
								</div>
								' : null) . '
								<div class="card-body pe-0 pb-0 ps-0">
									<ul class="list-group list-group-flush">
										' . $columns . '
									</ul>
									<div class="btn-group d-flex bg-white border-top rounded-0">
									
										' . $item_option . '
										
										' . ($extra_option ? '
										<a href="" class="btn --open-item-option" data-url="' . go_to('____', $results->query_string[$key]) . '" data-document-url="' . go_to('____', $results->query_string[$key]) . '" data-read="' . ($reading ? 1 : 0) . '" data-update="' . ($updating ? 1 : 0) . '" data-delete="' . ($deleting ? 1 : 0) . '" data-restrict="' . htmlspecialchars(json_encode($results->unset_action)) . '" data-additional-option="' . htmlspecialchars(json_encode($extra_option)) . '" data-bs-toggle="tooltip" title="' . phrase('more') . '">
											<i class="mdi mdi-dots-horizontal-circle-outline"></i>
										</a>
										' : null) . '
									</div>
								</div>
							</div>
						</div>
					';
				}
				
				echo '
					<div class="row">
						' . $grid . '
					</div>
				';
			}
			else
			{
				echo '
					<div class="text-center pt-5 pb-5">
						<i class="mdi mdi-dropbox mdi-5x text-muted"></i>
						<br />
						<p class="lead text-muted">
							' . phrase('no_matching_record_were_found') . '
						</p>
					</div>
				';
			}
		?>
	</div>
</div>
<div class="alias-pagination">
	<div class="container-fluid pt-2 pb-2">
		<?php echo $template->pagination; ?>
	</div>
</div>

<!-- search modal -->
<div class="modal --prevent-remove" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<form action="<?php echo go_to(null, array('per_page' => null, 'q' => null)); ?>" method="POST" class="modal-content --xhr-form">
			<div class="modal-header">
				<h5 class="modal-title" id="searchModalCenterTitle">
					<i class="mdi mdi-magnify"></i>
					<?php echo phrase('search_data'); ?>
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo phrase('close'); ?>"></button>
			</div>
			<div class="modal-body">
				<?php
					if(service('request')->getGet())
					{
						foreach(service('request')->getGet() as $key => $val)
						{
							$key					= preg_replace('/[^\w-]/', '', $key);
							
							if(!$key || in_array($key, array('aksara', 'q', 'per_page', 'column'))) continue;
							
							echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($val) . '" />';
						}
					}
				?>
				
				<?php echo (isset($results->filter) ? '<div class="mb-3">' . $results->filter . '</div>' : null); ?>
				
				<div class="mb-3">
					<input type="text" name="q" class="form-control" placeholder="<?php echo phrase('keyword_to_search'); ?>" value="<?php echo (service('request')->getGet('q') ? htmlspecialchars(service('request')->getGet('q')) : null); ?>" role="autocomplete" />
				</div>
				<div class="mb-3">
					<select name="column" class="form-control">
						<option value="all"><?php echo phrase('all_columns'); ?></option>
						<?php
							if(isset($results->columns))
							{
								foreach($results->columns as $key => $val)
								{
									echo '<option value="' . $val->field . '"' . ($val->field == service('request')->getGet('column') ? ' selected' : null) . '>' . $val->label . '</option>';
								}
							}
						?>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<div class="d-grid">
					<button type="submit" class="btn btn-primary">
						<i class="mdi mdi-magnify"></i>
						<?php echo phrase('search'); ?>
					</button>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function()
	{
		/**
		 * Add the form format into the local storage
		 */
		<?php if(!isset($modal_html)) { ?>if($.inArray('create', <?php echo json_encode((isset($results->unset_action) ? $results->unset_action : array())); ?>) === -1)
		{
			/* generate the response data */
			$.ajax
			({
				method: 'POST',
				url: '<?php echo go_to('create', $query_string); ?>',
				data:
				{
					prefer: 'modal'
				},
				beforeSend: function()
				{
					sessionStorage.setItem('form', '')
				}
			})
			.done(function(response)
			{
				sessionStorage.setItem('form', JSON.stringify(response))
			})
		}
		
		else if($.inArray('update', <?php echo json_encode((isset($results->unset_action) ? $results->unset_action : array())); ?>) === -1)
		{
			/* generate the response data */
			$.ajax
			({
				method: 'POST',
				url: '<?php echo go_to('update', $query_string); ?>',
				data:
				{
					prefer: 'modal'
				},
				beforeSend: function()
				{
					sessionStorage.setItem('form', '')
				}
			})
			.done(function(response)
			{
				sessionStorage.setItem('form', JSON.stringify(response))
			})
		}<?php } ?>

		/**
		 * Add the view format into the local storage
		 */
		if($.inArray('read', <?php echo json_encode((isset($results->unset_action) ? $results->unset_action : array())); ?>) === -1)
		{
			$.ajax
			({
				method: 'POST',
				url: '<?php echo go_to('read', $query_string); ?>',
				data:
				{
					prefer: 'modal'
				},
				beforeSend: function()
				{
					sessionStorage.setItem('read', '')
				}
			})
			.done(function(response)
			{
				sessionStorage.setItem('read', JSON.stringify(response))
			})
		}
	})
</script>
