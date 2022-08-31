<?php
	$col											= null;
	$column_1										= null;
	$column_2										= null;
	$column_3										= null;
	$column_4										= null;
	$merged											= array();
	$map											= null;
	
	foreach($results->form_data as $field => $params)
	{
		if(in_array('coordinate', $params->type) || in_array('point', $params->type) || in_array('polygon', $params->type) || in_array('linestring', $params->type))
		{
			$map									= '
				<div class="mb-3" style="margin-right: -15px; margin-left: -15px">
					' . $params->content . '
				</div>
			';
			continue;
		}
		if(isset($results->merged_field->$field) && !isset($results->merged_content->$field))
		{
			$col									= null;
			foreach($results->merged_field->$field as $key => $val)
			{
				if(in_array($val, $merged) || !isset($results->form_data->$val)) continue;
				
				$col								.= '
					<div class="' . (isset($results->field_size->$val) ? $results->field_size->$val : 'col') . '">
						<div class="mb-3">
							<label class="text-muted d-block mb-0" for="' . $val . '_label">
								' . $results->form_data->$val->label . '
							</label>
							<p id="' . $val . '_label" class="text-break-word">
								' . $results->form_data->$val->content . '
							</p>
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
				<div class="mb-3">
					<label class="text-muted d-block mb-0" for="' . $field . '_label">
						' . $params->label . '
					</label>
					<p class="text-break-word">
						' . $params->content . '
					</p>
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
	
	<?php echo ('modal' == service('request')->getPost('prefer') ? $meta->description : null); ?>
	
	<?php echo $map; ?>
	
	<div class="row">
		<div class="column col-md-<?php echo ('modal' == service('request')->getPost('prefer') ? 12 : (1 == $results->column_total ? 6 : (2 == $results->column_total ? 10 : 10))); ?>">
			<?php
				if(4 == $results->column_total)
				{
					echo '
						<div class="row">
							<div class="column ' . (isset($results->column_size[0]) ? $results->column_size[0] : ($col ? 'col-md-3' : 'col')) . '">
								' . $column_1 . '
							</div>
							<div class="column ' . (isset($results->column_size[1]) ? $results->column_size[1] : ($col ? 'col-md-3' : 'col')) . '">
								' . $column_2 . '
							</div>
							<div class="column ' . (isset($results->column_size[2]) ? $results->column_size[2] : ($col ? 'col-md-3' : 'col')) . '">
								' . $column_3 . '
							</div>
							<div class="column ' . (isset($results->column_size[3]) ? $results->column_size[3] : ($col ? 'col-md-3' : 'col')) . '">
								' . $column_4 . '
							</div>
						</div>
					';
				}
				else if(3 == $results->column_total)
				{
					echo '
						<div class="row">
							<div class="column ' . (isset($results->column_size[0]) ? $results->column_size[0] : ($col ? 'col-md-4' : 'col')) . '">
								' . $column_1 . '
							</div>
							<div class="column ' . (isset($results->column_size[1]) ? $results->column_size[1] : ($col ? 'col-md-4' : 'col')) . '">
								' . $column_2 . '
							</div>
							<div class="column ' . (isset($results->column_size[2]) ? $results->column_size[2] : ($col ? 'col-md-4' : 'col')) . '">
								' . $column_3 . '
							</div>
						</div>
					';
				}
				else if(2 == $results->column_total)
				{
					echo '
						<div class="row">
							<div class="column ' . (isset($results->column_size[0]) ? $results->column_size[0] : ($col ? 'col-md-6' : 'col')) . '">
								' . $column_1 . '
							</div>
							<div class="column ' . (isset($results->column_size[1]) ? $results->column_size[1] : ($col ? 'col-md-6' : 'col')) . '">
								' . $column_2 . '
							</div>
						</div>
					';
				}
				else
				{
					echo $column_1 . $column_2 . $column_3 . $column_4;
				}
			?>
		</div>
	</div>
	<?php echo ('modal' == service('request')->getPost('prefer') ? '<hr class="row" />' : '<div class="opt-btn-overlap-fix"></div><!-- fix the overlap -->'); ?>
	<div class="row<?php echo ('modal' != service('request')->getPost('prefer') ? ' opt-btn' : null); ?>">
		<div class="col-md-<?php echo ('modal' == service('request')->getPost('prefer') ? '12 text-end' : (1 == $results->column_total ? 6 : (2 == $results->column_total ? 10 : 10))); ?>">
		
			<?php if('modal' == service('request')->getPost('prefer')) { ?>
			<button type="button" class="btn btn-link" data-bs-dismiss="modal">
				<?php echo phrase('close'); ?>
				<em class="text-sm">(esc)</em>
			</button>
			<?php } else { ?>
				<a href="<?php echo go_to(null, $results->query_string); ?>" class="btn btn-link --xhr">
					<i class="mdi mdi-arrow-left"></i>
					<?php echo phrase('back'); ?>
				</a>
			<?php } ?>
			
			<a href="<?php echo current_page('../update'); ?>" class="btn btn-primary float-end <?php echo (service('request')->getUserAgent()->isMobile() ? '--xhr' : '--open-modal-form'); ?>">
				<i class="mdi mdi-square-edit-outline"></i>
				<?php echo phrase('update'); ?>
			</a>
		</div>
	</div>
</div>
