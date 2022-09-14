<?php
$extra_toolbar										= null;
$primary											= array();

if(isset($results->extra_action->toolbar))
{
	foreach($results->extra_action->toolbar as $key => $val)
	{
		$extra_toolbar								.= '
			<a href="' . go_to($val->url, $val->parameter) . '" class="btn btn-sm ' . ($val->class ? $val->class : 'btn-dark --xhr') . '"' . (isset($val->new_tab) && $val->new_tab == true ? ' target="_blank"' : null) . '>
				<i class="' . ($val->icon ? $val->icon : 'mdi mdi-link') . '"></i>
				' . $val->label . '
			</a>
		';
	}
}
?>
<div class="container-fluid">
	<div class="pt-1 pb-1 alias-table-toolbar border-bottom">
		<div class="row">
			<div class="col">
				<div class="btn-group btn-group-sm">
					<?php if(!isset($results->unset_action) || !in_array('create', $results->unset_action)) { ?>
						<a href="<?php echo go_to('create'); ?>" class="btn btn-primary --btn-create <?php echo (isset($modal_html) ? '--modal' : '--open-modal-form'); ?>">
							<i class="mdi mdi-plus"></i>
							<span class="hidden-xs hidden-sm">
								<?php echo phrase('create'); ?>
							</span>
						</a>
					<?php } ?>
					<?php echo (isset($extra_toolbar) ? $extra_toolbar : null); ?>
					<?php if(!isset($results->unset_action) || !in_array('export', $results->unset_action)) { ?>
						<a href="<?php echo go_to('export'); ?>" class="btn btn-success --btn-export" target="_blank">
							<i class="mdi mdi-file-excel"></i>
							<span class="hidden-xs hidden-sm">
								<?php echo phrase('export'); ?>
							</span>
						</a>
					<?php } ?>
					<?php if(!isset($results->unset_action) || !in_array('print', $results->unset_action)) { ?>
						<a href="<?php echo go_to('print'); ?>" class="btn btn-warning --btn-print" target="_blank">
							<i class="mdi mdi-printer"></i>
							<span class="hidden-xs hidden-sm">
								<?php echo phrase('print'); ?>
							</span>
						</a>
					<?php } ?>
					<?php if(!isset($results->unset_action) || !in_array('pdf', $results->unset_action)) { ?>
						<a href="<?php echo go_to('pdf'); ?>" class="btn btn-info --btn-pdf" target="_blank">
							<i class="mdi mdi-file-pdf"></i>
							<span class="hidden-xs hidden-sm">
								<?php echo phrase('pdf'); ?>
							</span>
						</a>
					<?php } ?>
					<?php if(!isset($results->unset_action) || !in_array('delete', $results->unset_action)) { ?>
						<a href="<?php echo go_to('delete'); ?>" class="btn btn-danger disabled d-none --open-delete-confirm" data-bs-toggle="tooltip" title="<?php echo phrase('delete_checked'); ?>" data-bulk-delete="true">
							<i class="mdi mdi-trash-can-outline"></i>
						</a>
					<?php } ?>
				</div>
			</div>
			<div class="col<?php echo (!isset($results->filter) || !$results->filter ? '-4' : null); ?>">
				<form action="<?php echo go_to(null, array('per_page' => null, 'q' => null)); ?>" method="POST" class="--xhr-form">
					<?php
						if(service('request')->getGet())
						{
							foreach(service('request')->getGet() as $key => $val)
							{
								$key				= preg_replace('/[^\w-]/', '', $key);
								
								if(!$key || in_array($key, array('aksara', 'q', 'per_page', 'column'))) continue;
								
								echo '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($val) . '" />';
							}
						}
					?>
					<div class="input-group input-group-sm">
						
						<?php echo (isset($results->filter) ? $results->filter : null); ?>
						<input type="text" name="q" class="form-control" placeholder="<?php echo phrase('keyword_to_search'); ?>" value="<?php echo (service('request')->getGet('q') ? htmlspecialchars(service('request')->getGet('q')) : null); ?>" role="autocomplete" />
						<select name="column" class="form-control form-control-sm">
							<option value="all"><?php echo phrase('all_columns'); ?></option>
							<?php
								if(isset($results->columns))
								{
									foreach($results->columns as $key => $val)
									{
										echo '
											<option value="' . $val->field . '"' . ($val->field == service('request')->getGet('column') ? ' selected' : null) . '>
												' . $val->label . '
											</option>
										';
									}
								}
							?>
						</select>
						<button type="submit" class="btn btn-primary">
							<i class="mdi mdi-magnify"></i>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="table-responsive alias-table-index">
		<table class="table table-sm table-hover mb-0">
			<thead>
				<tr>
					<?php
						$colspan					= (!isset($results->unset_action) || !in_array('delete', $results->unset_action) ? 2 : 1);
						if(isset($results->extra_action->option) || !isset($results->unset_action) || !in_array('read', $results->unset_action) || !in_array('update', $results->unset_action) || !in_array('delete', $results->unset_action) || !in_array('print', $results->unset_action) || !in_array('pdf', $results->unset_action))
						{
							echo (!isset($results->unset_action) || !in_array('delete', $results->unset_action) ? '
								<th width="1" class="border-top-0">
									<input type="checkbox" role="checker" data-parent="table" class="form-check-input bulk-delete" />
								</th>
							' : '') . '
								<th width="1" class="border-top-0">
									' . phrase('options') . '
								</th>
							';
						}
						
						if(isset($results->columns))
						{
							foreach($results->columns as $key => $val)
							{
								echo '
									<th class="border-top-0' . ('right' == $val->align ? ' text-end' : null) . (strpos($val->label, ' ') === false ? ' text-nowrap' : null) . '">
										<a href="' . go_to(null, array('order' => $val->field, 'sort' => get_userdata('sortOrder'))) . '" class="--xhr' . ($val->field == service('request')->getGet('order') ? ' text-primary' : ' text-default') . '">
											<i class="mdi mdi-sort-' . ($val->field == service('request')->getGet('order') && 'asc' == service('request')->getGet('sort') ? 'ascending' : 'descending') . ' float-end pt-1' . ($val->field == service('request')->getGet('order') ? ' text-primary' : ' text-muted') . '"></i>
											' . $val->label . '
										</a>
									</th>
								';
								$colspan++;
							}
						}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
					if(isset($results->table_data) && $total)
					{
						$reference						= array();
						
						foreach($results->table_data as $key => $val)
						{
							$extra_option				= null;
							$extra_dropdown				= null;
							$reading					= true;
							$updating					= true;
							$deleting					= true;
							
							if(isset($results->extra_action->option[$key]))
							{
								foreach($results->extra_action->option[$key] as $_key => $_val)
								{
									$class				= null;
									$label				= null;
									$icon				= null;
									
									if(isset($_val->new_tab) && is_object($_val->new_tab))
									{
										if(isset($_val->new_tab->restrict))
										{
											$id			= key((array) $_val->new_tab->restrict);
											
											if(in_array($val->$id->original, $_val->new_tab->restrict->$id)) continue;
										}
										else
										{
											$original	= $_val->new_tab->key;
											
											if(isset($_val->new_tab->key) && isset($_val->new_tab->value) && isset($val->$original->original) && $val->$original->original == $_val->new_tab->value)
											{
												$class	= (isset($_val->new_tab->class) ? $_val->new_tab->class : null);
												$label	= (isset($_val->new_tab->label) ? $_val->new_tab->label : null);
												$icon	= (isset($_val->new_tab->icon) ? $_val->new_tab->icon : null);
											}
										}
									}
									
									$extra_option		.= '
										<a href="' . go_to($_val->url, $_val->parameter) . '" class="btn btn-xs ' . ($class ? $class : ($_val->class ? $_val->class : 'btn-secondary --xhr')) . '" data-bs-toggle="tooltip" title="' . ($label ? $label : $_val->label) . '"' . (isset($_val->new_tab) && $_val->new_tab == true ? ' target="_blank"' : null) . '>
											<i class="' . ($icon ? $icon : ($_val->icon ? $_val->icon : 'mdi mdi-link')) . '"></i>
										</a>
									';
								}
							}
							
							if(isset($results->extra_action->dropdown[$key]))
							{
								foreach($results->extra_action->dropdown[$key] as $_key => $_val)
								{
									$extra_dropdown		.= '
										<a href="' . go_to($_val->url, $_val->parameter) . '" class="list-group-item pt-1 pe-0 pb-1 ps-0 ' . ($_val->class ? str_replace('btn', 'unused-btn', $_val->class) : '--xhr') . '"' . (isset($_val->new_tab) && is_bool($_val->new_tab) ? ' target="_blank"' : null) . '>
											<i class="' . ($_val->icon ? $_val->icon : 'mdi mdi-link') . '" style="width:22px"></i>
											' . $_val->label . '
										</a>
									';
								}
							}
							
							$columns					= null;
							
							foreach($val as $field => $params)
							{
								if(isset($params->primary) && $params->primary)
								{
									$primary[$field]	= $params->primary;
									
									if(isset($results->unset_read->$field) && is_array($results->unset_read->$field) && in_array($params->original, $results->unset_read->$field))
									{
										$reading		= false;
									}
									
									if(isset($results->unset_update->$field) && is_array($results->unset_update->$field) && in_array($params->original, $results->unset_update->$field))
									{
										$updating		= false;
									}
									
									if(isset($results->unset_delete->$field) && is_array($results->unset_delete->$field) && in_array($params->original, $results->unset_delete->$field))
									{
										$deleting		= false;
									}
								}
								
								if(isset($params->hidden) && $params->hidden) continue;
								
								$columns				.= '
									<td id="__c_' . $field . '">
										' . (isset($params->content) ? $params->content : null) . '
									</td>
								';
							}
							
							$options					= (!isset($results->unset_action) || !in_array('delete', $results->unset_action) ? '
								<td>
									' . ($deleting ? '<input type="checkbox" name="bulk_delete[]" class="form-check-input checker-children" value="' . htmlspecialchars(json_encode($results->query_string[$key])) . '" />' : '') . '
								</td>
								' : '') . '
								<td>
									<div class="btn-group">
										' . ($reading && (!isset($results->unset_action) || !in_array('read', $results->unset_action)) ? '
											<a href="' . go_to('read', $results->query_string[$key]) . '" class="btn btn-primary btn-xs --open-modal-read" data-bs-toggle="tooltip" title="' . phrase('read') . '">
												<i class="mdi mdi-magnify"></i>
											</a>
										' : null) . '
										' . ($updating && (!isset($results->unset_action) || !in_array('update', $results->unset_action)) ? '
											<a href="' . go_to('update', $results->query_string[$key]) . '" class="btn btn-info btn-xs ' . (isset($modal_html) ? '--modal' : '--open-modal-form') . '" data-bs-toggle="tooltip" title="' . phrase('update') . '">
												<i class="mdi mdi-square-edit-outline"></i>
											</a>
										' : null) . '
										' . $extra_option . '
										' . ($extra_dropdown || ($reading && !in_array('print', $results->unset_action)) || ($reading && !in_array('pdf', $results->unset_action)) ? '
											<button type="button" class="btn btn-xs btn-secondary toggle-tooltip" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="' . htmlspecialchars
											('
												<div class="list-group list-group-flush">
													' . $extra_dropdown . '
													' . ($reading && !in_array('print', $results->unset_action) ? '<a href="' . go_to('print', $results->query_string[$key]) . '" class="list-group-item pt-1 pe-0 pb-1 ps-0" target="_blank"><i class="mdi mdi-printer" style="width:22px"></i>' . phrase('print') . '</a>' : null) . '
													' . ($reading && !in_array('pdf', $results->unset_action) ? '<a href="' . go_to('pdf', $results->query_string[$key]) . '" class="list-group-item pt-1 pe-0 pb-1 ps-0"  target="_blank"><i class="mdi mdi-file-pdf text-danger" style="width:22px"></i>' . phrase('pdf') . '</a>' : null) . '
												</div>
											') . '" data-bs-container="body" data-bs-html="true">
												<i class="mdi mdi-chevron-down"></i>
											</button>
										' : null) . '
										' . ($deleting && (!isset($results->unset_action) || !in_array('delete', $results->unset_action)) ? '
											<a href="' . go_to('delete', $results->query_string[$key]) . '" class="btn btn-danger btn-xs --open-delete-confirm" data-bs-toggle="tooltip" title="' . phrase('delete') . '">
												<i class="mdi mdi-trash-can-outline"></i>
											</a>
										' : null) . '
									</div>
								</td>
							';
							
							foreach($results->item_reference as $_key => $_val)
							{
								if(isset($val->$_val->content) && (!isset($reference[$_val]) || !in_array($val->$_val->content, $reference[$_val])))
								{
									echo '
										<tr>
											' . (isset($results->extra_action->option) || !isset($results->unset_action) || !in_array('read', $results->unset_action) || !in_array('update', $results->unset_action) || !in_array('delete', $results->unset_action) || !in_array('print', $results->unset_action) || !in_array('pdf', $results->unset_action) ? '<td>&nbsp;</td>' : null) . '
											<td colspan="' . ($colspan - (isset($results->extra_action->option) || !isset($results->unset_action) || !in_array('read', $results->unset_action) || !in_array('update', $results->unset_action) || !in_array('delete', $results->unset_action) || !in_array('print', $results->unset_action) || !in_array('pdf', $results->unset_action) ? 1 : 0)) . '">
												<div class="fw-bold text-primary">
													' . $val->$_val->original . '
												</div>
											</td>
										</tr>
									';
									
									$reference[$_val][]	= $val->$_val->content;
								}
							}
							
							echo '
								<tr id="item__' . (isset($results->query_string[$key]->aksara) ? $results->query_string[$key]->aksara : null) . '">
									' . (isset($results->extra_action->option) || !isset($results->unset_action) || !in_array('read', $results->unset_action) || !in_array('update', $results->unset_action) || !in_array('delete', $results->unset_action) || !in_array('print', $results->unset_action) || !in_array('pdf', $results->unset_action) ? $options : null) . '
									' . $columns . '
								</tr>
							';
						}
					}
					else
					{
						echo '
							<tr class="no-hover">
								<td colspan="' . $colspan . '">
									<div class="text-center pt-5 pb-5">
										<i class="mdi mdi-text mdi-5x text-muted"></i>
										<br />
										<p class="lead text-muted">
											' . phrase('no_matching_record_were_found') . '
										</p>
									</div>
								</td>
							</tr>
						';
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="alias-pagination border-top pt-2 pb-2">
		<?php echo $template->pagination; ?>
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
