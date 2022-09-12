<div class="container-fluid pt-3 pb-3 bg-light full-height">
	<?php
		$prefix_action								= array();
		
		if(!isset($results->unset_action) || !in_array('create', $results->unset_action))
		{
			$prefix_action							= array
			(
				array
				(
					'url'							=> go_to('create'),
					'label'							=> phrase('create'),
					'class'							=> '',
					'icon'							=> 'mdi mdi-plus',
					'parameter'						=> array
					(
					),
					'new_tab'						=> false,
					'locally'						=> true
				)
			);
		}
		
		if($prefix_action && isset($results->extra_action))
		{
			$results->extra_action->toolbar			= json_decode
			(
				json_encode
				(
					array_merge
					(
						$prefix_action,
						
						(array) $results->extra_action->toolbar,
						
						array
						(
							array
							(
								'url'				=> go_to(null, array('per_page' => null)),
								'label'				=> phrase('search'),
								'class'				=> '',
								'icon'				=> 'mdi mdi-magnify',
								'parameter'			=> array
								(
								),
								'new_tab'			=> false,
								'locally'			=> true,
								'modal'				=> '#searchModal'
							),
							array
							(
								'url'				=> current_page(),
								'label'				=> phrase('refresh'),
								'class'				=> '',
								'icon'				=> 'mdi mdi-refresh',
								'parameter'			=> array
								(
								),
								'new_tab'			=> false,
								'locally'			=> true
							)
						)
					)
				)
			);
		}
		
		$extra_bottom_toolbar						= null;
		$extra_toolbar								= null;
		
		if(isset($results->extra_action->toolbar))
		{
			foreach($results->extra_action->toolbar as $key => $val)
			{
				if($key <= 4)
				{
					$extra_bottom_toolbar			.= '
						<a href="' . (isset($val->locally) ? $val->url : go_to($val->url)) . '" class="btn text-dark text-truncate ' . ($val->class ? str_replace('btn', 'btn-ignore', $val->class) : 'btn-default' . (!isset($val->modal) ? ' --xhr' : null)) . '"' . ($val->new_tab ? ' target="_blank"' : (isset($val->modal) ? ' data-bs-toggle="modal" data-bs-target="' . $val->modal . '"' : null)) . '>
							<i class="' . ($val->icon ? $val->icon : 'mdi mdi-link') . '"></i>
							' . $val->label . '
						</a>
					';
				}
				else
				{
					$extra_toolbar					.= '
						<a href="' . go_to($val->url) . '" class="list-group-item list-group-item-action text-truncate ' . ($val->class ? str_replace('btn', 'btn-ignore', $val->class) : 'btn-default' . (!isset($val->modal) ? ' --xhr' : null)) . '"' . ($val->new_tab ? ' target="_blank"' : (isset($val->modal) ? ' data-bs-toggle="modal" data-bs-target="' . $val->modal . '"' : null)) . '>
							<i class="' . ($val->icon ? $val->icon : 'mdi mdi-link') . '"></i>
							' . $val->label . '
						</a>
					';
				}
			}
		}
		
		/**
		 * Table data
		 */
		if(isset($results->table_data) && $total)
		{
			$grid									= null;
			
			foreach($results->table_data as $key => $val)
			{
				$item_option						= null;
				$extra_option						= array();
				$reading							= true;
				$updating							= true;
				$deleting							= true;
				
				if(isset($results->extra_action->option[$key]))
				{
					$num							= 0;
					
					foreach($results->extra_action->option[$key] as $_key => $_val)
					{
						if(isset($_val->new_tab->restrict))
						{
							$id						= key((array) $_val->new_tab->restrict);
							
							if(in_array($val->$id->original, $_val->new_tab->restrict->$id)) continue;
						}
						
						$extra_option[]				= array
						(
							'url'					=> go_to($_val->url, $_val->parameter),
							'class'					=> 'btn ' . str_replace('btn', 'btn-ignore', $_val->class),
							'icon'					=> $_val->icon,
							'label'					=> $_val->label,
							'new_tab'				=> $_val->new_tab
						);
					}
				}
				
				$primary_key						= array();
				$hash								= null;
				$columns							= null;
				$slideshow							= null;
				$slideshow_count					= 0;
				$column_number						= 0;
				$has_cover							= false;
				
				foreach($val as $field => $params)
				{
					if(isset($params->token))
					{
						$hash						= $params->token;
					}
					
					if($params->primary)
					{
						$primary_key[$field]		= $params->primary;
						
						if(isset($results->unset_read->$field) && is_array($results->unset_read->$field) && in_array($params->original, $results->unset_read->$field))
						{
							$reading				= false;
						}
						
						if(isset($results->unset_update->$field) && is_array($results->unset_update->$field) && in_array($params->original, $results->unset_update->$field))
						{
							$updating				= false;
						}
						
						if(isset($results->unset_delete->$field) && is_array($results->unset_delete->$field) && in_array($params->original, $results->unset_delete->$field))
						{
							$deleting				= false;
						}
					}
					
					if($params->hidden) continue;
					
					if(isset($results->grid->thumbnail) && $field == $results->grid->thumbnail && array_intersect($params->type, array('image', 'images')))
					{
						$qs							= array();
						
						if($results->grid->parameter)
						{
							foreach($results->grid->parameter as $_key => $_val)
							{
								$qs[$_key]			= (isset($val->$_val->original) ? $val->$_val->original : $_val);
							}
						}
						
						$images						= json_decode($params->original);
						
						if($images)
						{
							$num					= 0;
							
							foreach($images as $src => $alt)
							{
								if(!in_array(strtolower(pathinfo($src, PATHINFO_EXTENSION)), array('jpg', 'jpeg', 'png', 'gif'))) continue;
								
								if($num == 3) break;
								
								$slideshow			.= '
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
							
							$slideshow_count		= $num;
						}
						else
						{
							$slideshow				= '
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
						$columns					.= '
							<li class="list-group-item pt-1 pb-1">
								<label class="d-block text-sm text-muted">
									' . $params->label . '
								</label>
								' . $params->content . '
							</li>
						';
					}
				}
				
				if($reading && !in_array('read', $results->unset_action))
				{
					$extra_option[]					= array
					(
						'url'						=> go_to('read', $results->query_string[$key]),
						'class'						=> 'btn --xhr',
						'icon'						=> 'mdi mdi-magnify',
						'label'						=> phrase('view'),
						'new_tab'					=> false
					);
				}
				
				if($updating && !in_array('update', $results->unset_action))
				{
					$extra_option[]					= array
					(
						'url'						=> go_to('update', $results->query_string[$key]),
						'class'						=> 'btn --xhr',
						'icon'						=> 'mdi mdi-square-edit-outline',
						'label'						=> phrase('update'),
						'new_tab'					=> false
					);
				}
				
				if($deleting && !in_array('delete', $results->unset_action))
				{
					$extra_option[]					= array
					(
						'url'						=> go_to('delete', $results->query_string[$key]),
						'class'						=> 'btn --open-delete-confirm',
						'icon'						=> 'mdi mdi-trash-can-outline',
						'label'						=> phrase('delete'),
						'new_tab'					=> false
					);
				}
				
				if($extra_option)
				{
					foreach($extra_option as $_key => $_val)
					{
						if($_key == 3) break;
						
						$item_option				.= '
							<a href="' . $_val['url'] . '" class="text-truncate pt-1 pb-1 ' . $_val['class'] . '"' . (isset($_val['new_tab']) && $_val['new_tab'] ? ' target="_blank"' : null) . '>
								<i class="' . $_val['icon'] . '"></i>
								<span class="d-block text-sm">
									' . $_val['label'] . '
								</span>
							</a>
						';
						
						unset($extra_option[$_key]);
					}
				}
				
				$grid								.= '
					<div class="col-sm-6 col-md-4 col-lg-3">
						<div class="card shadow-sm rounded-4 overflow-hidden border-0 mb-3">
							' . ($slideshow ? '
							<div id="slideshow_' . $key . '" class="carousel slide" data-ride="carousel">
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
							<div class="card-body pe-0 pb-0 ps-0' . ($has_cover ? ' pt-0' : null) . '">
								<ul class="list-group list-group-flush">
									' . $columns . '
								</ul>
								<div class="btn-group d-flex bg-white border-top rounded-0">
								
									' . $item_option . '
									
									' . ($extra_option ? '
									<a href="javascript:void(0)" class="btn --open-item-option" data-additional-option="' . htmlspecialchars(json_encode($extra_option)) . '" data-bs-toggle="tooltip" title="' . phrase('more') . '">
										<i class="mdi mdi-dots-horizontal-circle-outline"></i>
										<span class="d-block text-sm">
											' . phrase('more') . '
										</span>
									</a>
									' : null) . '
								</div>
							</div>
						</div>
					</div>
				';
			}
			
			echo '
				<div class="alias-table-index">
					<div class="row">
						' . $grid . '
					</div>
				</div>
			';
		}
		else
		{
			echo '
				<div class="alias-table-index">
					<div class="text-center full-height d-flex align-items-center justify-content-center">
						<div>
							<i class="mdi mdi-dropbox mdi-5x text-muted"></i>
							<br />
							<p class="lead text-muted">
								' . phrase('no_matching_record_were_found') . '
							</p>
						</div>
					</div>
				</div>
			';
		}
		
		/**
		 * Pagination
		 */
		echo ($pagination->total_rows > 0 ? '<div class="alias-pagination"><div class="pt-3">' . $template->pagination . '</div></div>' : null);
	?>
	
	<div class="opt-btn-overlap-fix"></div><!-- fix the overlap -->
	
	<!-- bottom toolbar -->
	<div class="btn-group btn-group-sm rounded-0 opt-btn">
	
		<?php echo $extra_bottom_toolbar; ?>
		
		<?php if($extra_toolbar) { ?>
			<a href="javascript:void(0)" class="btn text-dark text-truncate" data-bs-toggle="modal" data-bs-target="#toolbarModal">
				<i class="mdi mdi-dots-horizontal-circle-outline"></i>
				<?php echo phrase('more'); ?>
			</a>
		<?php } ?>
	</div>
</div>

<?php if($extra_toolbar) { ?>
<!-- extra toolbar -->
<div class="modal --prevent-remove" id="toolbarModal" tabindex="-1" role="dialog" aria-labelledby="toolbarModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="toolbarModalCenterTitle">
					<i class="mdi mdi-dots-horizontal-circle-outline"></i>
					<?php echo phrase('more'); ?>
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo phrase('close'); ?>"></button>
			</div>
			<div class="modal-body">
				<div class="list-group list-group-flush">
					<?php echo $extra_toolbar; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<!-- search modal -->
<div class="modal --prevent-remove" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<form action="<?php echo go_to(null, array('per_page' => null, 'q' => null)); ?>" method="FORM" class="modal-content --xhr-form">
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
				
				<div class="form-group mb-3">
					<input type="text" name="q" class="form-control form-control-sm" placeholder="<?php echo phrase('keyword_to_search'); ?>" value="<?php echo (service('request')->getGet('q') ? htmlspecialchars(service('request')->getGet('q')) : null); ?>" role="autocomplete" />
				</div>
				<div class="form-group mb-3">
					<select name="column" class="form-control form-control-sm">
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
