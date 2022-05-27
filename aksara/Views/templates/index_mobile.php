<div class="container-fluid pt-3 pb-3 bg-light">
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
						<a href="' . (isset($val->locally) ? $val->url : go_to($val->url)) . '" class="btn text-dark text-truncate ' . ($val->class ? str_replace('btn', 'btn-ignore', $val->class) : 'btn-default' . (!isset($val->modal) ? ' --xhr' : null)) . '"' . ($val->new_tab ? ' target="_blank"' : (isset($val->modal) ? ' data-toggle="modal" data-target="' . $val->modal . '"' : null)) . '>
							<i class="' . ($val->icon ? $val->icon : 'mdi mdi-link') . '"></i>
							' . $val->label . '
						</a>
					';
				}
				else
				{
					$extra_toolbar					.= '
						<a href="' . go_to($val->url) . '" class="list-group-item list-group-item-action text-truncate ' . ($val->class ? str_replace('btn', 'btn-ignore', $val->class) : 'btn-default' . (!isset($val->modal) ? ' --xhr' : null)) . '"' . ($val->new_tab ? ' target="_blank"' : (isset($val->modal) ? ' data-toggle="modal" data-target="' . $val->modal . '"' : null)) . '>
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
						$images						= json_decode($params->original);
						
						if($images)
						{
							$num					= 0;
							
							foreach($images as $src => $alt)
							{
								if($num == 3) break;
								
								$slideshow			.= '
									<div class="carousel-item rounded' . (!$num ? ' active' : null) . '">
										<a href="' . get_image($results->grid->path, $src, 'thumb') . '" target="_blank">
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
						}
						
						$slideshow_count			= $num;
					}
					else
					{
						if(!$column_number && isset($params->type) && in_array('image', $params->type))
						{
							preg_match('/<img src="(.*?)"/', $params->content, $background);
							
							$background				= (isset($background[1]) ? str_replace('/icons/', '/thumbs/', $background[1]) : null);
							
							if($background)
							{
								$has_cover			= true;
							}
							
							$columns				.= '
								<li class="list-group-item pt-1 pb-1" data-toggle="tooltip" title="' . $params->label . '" style="border-bottom:0;' . ($background ? 'background:url(' . $background . ') center center no-repeat; background-size:cover; height:256px' : null) . '">
									' . (!$background ? $params->content : null) . '
								</li>
							';
						}
						else
						{
							$columns				.= '
								<li class="list-group-item pt-1 pb-1" data-toggle="tooltip" title="' . $params->label . '">
									<label class="text-muted d-block mb-0">
										' . $params->label . '
									</label>
									' . $params->content . '
								</li>
							';
						}
					}
					
					$column_number++;
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
						<div class="card shadow-sm rounded-more border-0 mb-3">
							' . ($slideshow ? '
							<div id="slideshow_' . $key . '" class="carousel slide" data-ride="carousel">
								<div class="carousel-inner">
									' . $slideshow . '
								</div>
								' . ($slideshow_count > 1 ? '
								<a class="carousel-control-prev gradient-right" href="#slideshow_' . $key . '" role="button" data-slide="prev">
									<span class="carousel-control-prev-icon" aria-hidden="true"></span>
									<span class="sr-only">
										' . phrase('previous') . '
									</span>
								</a>
								<a class="carousel-control-next gradient-left" href="#slideshow_' . $key . '" role="button" data-slide="next">
									<span class="carousel-control-next-icon" aria-hidden="true"></span>
									<span class="sr-only">
										' . phrase('next') . '
									</span>
								</a>
								' : null) . '
							</div>
							' : null) . '
							<div class="card-body pr-0 pb-0 pl-0' . ($has_cover ? ' pt-0' : null) . '">
								<ul class="list-group list-group-flush">
									' . $columns . '
								</ul>
								<div class="btn-group d-flex bg-white border-top">
								
									' . $item_option . '
									
									' . ($extra_option ? '
									<a href="javascript:void(0)" class="btn --open-item-option pt-1 pb-1" data-additional-option="' . htmlspecialchars(json_encode($extra_option)) . '" data-toggle="tooltip" title="' . phrase('more') . '">
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
	<div class="btn-group btn-group-sm opt-btn">
	
		<?php echo $extra_bottom_toolbar; ?>
		
		<?php if($extra_toolbar) { ?>
			<a href="javascript:void(0)" class="btn text-dark text-truncate" data-toggle="modal" data-target="#toolbarModal">
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
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo phrase('close'); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
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
		<form action="<?php echo go_to(null, array('per_page' => null)); ?>" method="FORM" class="modal-content --xhr-form">
			<div class="modal-header">
				<h5 class="modal-title" id="searchModalCenterTitle">
					<i class="mdi mdi-magnify"></i>
					<?php echo phrase('search_data'); ?>
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo phrase('close'); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?php
					if(service('request')->getGet())
					{
						foreach(service('request')->getGet() as $key => $val)
						{
							if(in_array($key, array('aksara', 'q', 'per_page', 'column'))) continue;
							
							echo '<input type="hidden" name="' . $key . '" value="' . $val . '" />';
						}
					}
				?>
				
				<?php echo (isset($results->filter) ? '<div class="form-group">' . $results->filter . '</div>' : null); ?>
				
				<div class="form-group">
					<input type="text" name="q" class="form-control" placeholder="<?php echo phrase('keyword_to_search'); ?>" value="<?php echo htmlspecialchars(service('request')->getGet('q')); ?>" role="autocomplete" />
				</div>
				<div class="form-group">
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
				<button type="submit" class="btn btn-primary btn-block">
					<i class="mdi mdi-magnify"></i>
					<?php echo phrase('search'); ?>
				</button>
			</div>
		</form>
	</div>
</div>
