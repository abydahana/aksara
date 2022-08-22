<?php
	$extra_submit									= null;
	
	if($results->extra_action->submit)
	{
		foreach($results->extra_action->submit as $key => $val)
		{
			$extra_submit							.= '
				<a href="' . go_to($val->url, $val->parameter) . '" class="' . ($val->class ? $val->class : 'btn-default --xhr') . ' float-end ml-1"' . ($val->new_tab ? ' target="_blank"' : null) . '>
					<i class="' . ($val->icon ? $val->icon : 'mdi mdi-link') . '"></i>
					' . $val->label . '
				</a>
			';
		}
	}
	
	$col											= null;
	$column_1										= null;
	$column_2										= null;
	$column_3										= null;
	$column_4										= null;
	$merged											= array();
	$map											= null;
	$hidden											= null;
	
	foreach($results->form_data as $field => $params)
	{
		if(in_array('hidden', $params->type))
		{
			$hidden									.= '<input type="hidden" name="' . $field . '" value="' . $params->original . '" />';
			
			continue;
		}
		
		if(in_array('coordinate', $params->type) || in_array('point', $params->type) || in_array('polygon', $params->type) || in_array('linestring', $params->type))
		{
			$map									= '
				<div class="mb-3" style="margin-right: -15px; margin-left: -15px">
					' . $params->content . '
				</div>
			';
			
			continue;
		}
		
		if(isset($results->merged_field->$field))
		{
			$col									= null;
			
			foreach($results->merged_field->$field as $key => $val)
			{
				if(in_array($val, $merged) || !isset($results->form_data->$val)) continue;
				
				$col								.= '
					<div class="' . (isset($results->field_size->$val) ? $results->field_size->$val : 'col') . '">
						<div class="form-group mb-3">
							' . ($results->form_data->$val->label ? '
							<label class="text-muted d-block mb-0" for="' . $val . '_input">
								' . $results->form_data->$val->label . '
								' . ($results->form_data->$val->tooltip ? '<i class="mdi mdi-information-outline text-primary" data-toggle="tooltip" title="' . $results->form_data->$val->tooltip . '"></i>' : null) . '
								' . ($results->form_data->$val->required ? '<b class="text-danger"> *</b>' : null) . '
							</label>
							' : null) . '
							' . ($results->form_data->$val->prepend || $results->form_data->$val->append ? '<div class="input-group">' : '') . '
								' . ($results->form_data->$val->prepend ? '
									<span class="input-group-text">
										' . $results->form_data->$val->prepend . '
									</span>
								' : '') . '
								
								' . $results->form_data->$val->content . '
								
								' . ($results->form_data->$val->append ? '
									<span class="input-group-text">
										' . $results->form_data->$val->append . '
									</span>
								' : '') . '
							' . ($results->form_data->$val->prepend || $results->form_data->$val->append ? '</div>' : '') . '
						</div>
					</div>
				';
				
				$merged[]							= $val;
			}
			
			if(4 == $params->position)
			{
				if(isset($results->set_heading->$field))
				{
					$column_4						.= '<h5>' . $results->set_heading->$field . '</h5>';
				}
				
				$column_4							.= '<div class="row">' . $col . '</div>';
			}
			else if(3 == $params->position)
			{
				if(isset($results->set_heading->$field))
				{
					$column_3						.= '<h5>' . $results->set_heading->$field . '</h5>';
				}
				
				$column_3							.= '<div class="row">' . $col . '</div>';
			}
			else if(2 == $params->position)
			{
				if(isset($results->set_heading->$field))
				{
					$column_2						.= '<h5>' . $results->set_heading->$field . '</h5>';
				}
				
				$column_2							.= '<div class="row">' . $col . '</div>';
			}
			else
			{
				if(isset($results->set_heading->$field))
				{
					$column_1						.= '<h5>' . $results->set_heading->$field . '</h5>';
				}
				
				$column_1							.= '<div class="row">' . $col . '</div>';
			}
		}
		else
		{
			if(in_array($field, $merged)) continue;
			
			$output									= '
				<div class="form-group mb-3">
					' . ($params->label ? '
					<label class="text-muted d-block mb-0" for="' . $field . '_input">
						' . $params->label . '
						' . ($params->tooltip ? '<i class="mdi mdi-information-outline text-primary" data-bs-toggle="tooltip" title="' . $params->tooltip . '"></i>' : null) . '
						' . ($params->required ? '<b class="text-danger"> *</b>' : null) . '
					</label>
					' : null) . '
					' . ($params->prepend || $params->append ? '<div class="input-group">' : '') . '
						' . ($params->prepend ? '
							<span class="input-group-text">
								' . $params->prepend . '
							</span>
						' : '') . '
						
						' . $params->content . '
						
						' . ($params->append ? '
							<span class="input-group-text">
								' . $params->append . '
							</span>
						' : '') . '
					' . ($params->prepend || $params->append ? '</div>' : '') . '
				</div>
			';
			
			if(4 == $params->position)
			{
				if(isset($results->set_heading->$field))
				{
					$column_4						.= '<h5>' . $results->set_heading->$field . '</h5>';
				}
				
				$column_4							.= $output;
			}
			else if(3 == $params->position)
			{
				if(isset($results->set_heading->$field))
				{
					$column_3						.= '<h5>' . $results->set_heading->$field . '</h5>';
				}
				
				$column_3							.= $output;
			}
			else if(2 == $params->position)
			{
				if(isset($results->set_heading->$field))
				{
					$column_2						.= '<h5>' . $results->set_heading->$field . '</h5>';
				}
				
				$column_2							.= $output;
			}
			else
			{
				if(isset($results->set_heading->$field))
				{
					$column_1						.= '<h5>' . $results->set_heading->$field . '</h5>';
				}
				
				$column_1							.= $output;
			}
		}
		
	}
?>
<div class="container-fluid <?php echo ($map || ('modal' == service('request')->getPost('prefer') && $meta->description) ? 'pb-3' : 'pt-3 pb-3'); ?>">
	<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
		
		<?php echo ('modal' == service('request')->getPost('prefer') ? $meta->description : null); ?>
		
		<?php echo $map; ?>
		
		<div class="row">
			<div class="col-md-<?php echo ('modal' == service('request')->getPost('prefer') ? 12 : (1 == $results->column_total ? 6 : ($results->column_total > 2 ? 12 : 10))); ?>">
				<?php
					if(4 == $results->column_total)
					{
						echo '
							<div class="row">
								<div class="' . (isset($results->column_size[0]) ? $results->column_size[0] : ($col ? 'col-md-3' : 'col')) . '">
									' . $column_1 . '
								</div>
								<div class="' . (isset($results->column_size[1]) ? $results->column_size[1] : ($col ? 'col-md-3' : 'col')) . '">
									' . $column_2 . '
								</div>
								<div class="' . (isset($results->column_size[2]) ? $results->column_size[2] : ($col ? 'col-md-3' : 'col')) . '">
									' . $column_3 . '
								</div>
								<div class="' . (isset($results->column_size[3]) ? $results->column_size[3] : ($col ? 'col-md-3' : 'col')) . '">
									' . $column_4 . '
								</div>
							</div>
						';
					}
					else if(3 == $results->column_total)
					{
						echo '
							<div class="row">
								<div class="' . (isset($results->column_size[0]) ? $results->column_size[0] : ($col ? 'col-md-4' : 'col')) . '">
									' . $column_1 . '
								</div>
								<div class="' . (isset($results->column_size[1]) ? $results->column_size[1] : ($col ? 'col-md-4' : 'col')) . '">
									' . $column_2 . '
								</div>
								<div class="' . (isset($results->column_size[2]) ? $results->column_size[2] : ($col ? 'col-md-4' : 'col')) . '">
									' . $column_3 . '
								</div>
							</div>
						';
					}
					else if(2 == $results->column_total)
					{
						echo '
							<div class="row">
								<div class="' . (isset($results->column_size[0]) ? $results->column_size[0] : ($col ? 'col-md-6' : 'col')) . '">
									' . $column_1 . '
								</div>
								<div class="' . (isset($results->column_size[1]) ? $results->column_size[1] : ($col ? 'col-md-6' : 'col')) . '">
									' . $column_2 . '
								</div>
							</div>
						';
					}
					else
					{
						echo $column_1 . $column_2 . $column_3 . $column_4;
					}
					
					echo $hidden;
				?>
				
				<div class="--validation-callback mb-0"></div>
			</div>
		</div>
		<?php echo ('modal' == service('request')->getPost('prefer') ? '<hr class="row" />' : '<div class="opt-btn-overlap-fix"></div><!-- fix the overlap -->'); ?>
		<div class="row<?php echo ('modal' != service('request')->getPost('prefer') ? ' opt-btn' : null); ?>">
			<div class="col-md-<?php echo ('modal' == service('request')->getPost('prefer') ? '12 text-end' : (1 == $results->column_total ? 6 : ($results->column_total > 2 ? 12 : 10))); ?>">
				
				<?php if('modal' == service('request')->getPost('prefer')) { ?>
				<button type="button" class="btn btn-light" data-bs-dismiss="modal">
					<?php echo phrase('close'); ?>
					<em class="text-sm">(esc)</em>
				</button>
				<?php } else { ?>
				<a href="<?php echo current_page('../', $results->query_string); ?>" class="btn btn-link --xhr">
					<i class="mdi mdi-arrow-left"></i>
					<?php echo phrase('back'); ?>
				</a>
				<?php } ?>
				
				<?php echo $extra_submit; ?>
				
				<button type="submit" class="btn btn-primary float-end">
					<i class="mdi mdi-check"></i>
					<?php echo phrase('submit'); ?>
					<em class="text-sm">(ctrl+s)</em>
				</button>
			</div>
		</div>
	</form>
</div>
