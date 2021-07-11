<style type="text/css">
	#breadcrumb-wrapper
	{
		z-index: 1024!important
	}
	
	.pretty-scrollbar
	{
		height: 100px;
		overflow-y: hidden
	}
</style>

<div class="container-fluid pt-3 pb-3">
	<div class="row">
		<div class="col-md-3">
			<div class="sticky-top" style="top:88px">
				<div class="pretty-scrollbar">
					<?php
						if($modules)
						{
							foreach($modules as $key => $val)
							{
								echo '
									<a href="' . current_page(null, array('slug' => $val, 'group' => null)) . '" class="--xhr' . ($val == $active ? ' text-primary font-weight-bold' : null) . '">
										' . str_replace('/', ' &gt; ', $val) . '
									</a>
									<br />
								';
							}
						}
					?>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<?php
				$group_collector					= array();
				$selected							= service('request')->getGet('group');
				$access_token						= false;
				
				if($permission->groups)
				{
					$groups							= null;
					$privileges						= array();
					
					foreach($permission->groups as $key => $val)
					{
						$group_collector[]			= $val->group_id;
						$method						= null;
						$extract_privileges			= json_decode($val->group_privileges);
						
						if(isset($extract_privileges->$active))
						{
							foreach($extract_privileges->$active as $_key => $_val)
							{
								$method				.= '<a href="#--method-' . $_val . '"><span class="badge badge-success"><i class="mdi mdi-check"></i> ' . phrase($_val) . '</span></a>&nbsp;';
							}
						}
						
						if($val->group_id)
						{
							$access_token			= true;
						}
						
						$groups						.= '<option value="' . $val->group_id . '"' . ($val->group_id == $selected ? ' selected' : null) . '>' . $val->group_name . '</option>';
						
						$privileges[$selected]		= $method;
					}
					
					echo '
						<div class="row">
							<div class="col-md-6">
								<h4 class="mb-3 --title">
									' . $active . '
								</h4>
							</div>
						</div>
						<div class="table-responsive">
							<table class="table table-bordered table-sm">
								<thead>
									<tr>
										<th>
											Group Name
										</th>
										<th>
											Privileges
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td width="250">
											<form action="' . current_page(null) . '" method="POST" class="--xhr-form">
												<select name="group" class="form-control form-control-sm">
													' . $groups . '
												</select>
											</form>
										</td>
										<td>
											' . (isset($privileges[$selected]) ? $privileges[$selected] : null) . '
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					';
				}
				
				echo '
					<h4 class="mt-3 mb-3">
						Request Method
					</h4>
				';
				
				if($permission->privileges)
				{
					$method							= array();
					
					foreach($permission->privileges as $key => $val)
					{
						$method[]					= $val;
						
						echo '
							<div class="form-group" id="--method-' . $val . '">
								<h5 class="mb-1">
									<span class="badge badge-primary badge-md">
										' . (in_array($val, array('create', 'update')) ? 'POST' : (in_array($val, array('delete')) ? 'DELETE' : 'GET')) . '
									</span>
								</h5>
								<div class="rounded pt-2 pr-3 pb-2 pl-3 bg-dark">
									<code class="text-light">' . base_url(('index' !== $val ? $active . '/' . $val : $active)) . '</code>
								</div>
							</div>
							<h5 class="mt-3 mb-3">
								Header
							</h5>
							<div class="table-responsive">
								<table class="table table-bordered table-sm">
									<thead>
										<tr>
											<th>
												Field
											</th>
											<th>
												Type
											</th>
											<th>
												Description
											</th>
											<th width="100" class="text-center">
												Required
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												<span style="font-family:Consolas">
													X-API-KEY
												</span>
											</td>
											<td>
												String
											</td>
											<td>
												The valid API key that been added in API Services.
											</td>
											<td class="text-center">
												<span class="badge badge-danger">
													Required
												</span>
											</td>
										</tr>
										' . ($access_token ? '
										<tr>
											<td>
												<span style="font-family:Consolas">
													X-ACCESS-TOKEN
												</span>
											</td>
											<td>
												String
											</td>
											<td>
												The token that given from authentication callback (handshake).
											</td>
											<td class="text-center">
												<span class="badge badge-danger">
													Required
												</span>
											</td>
										</tr>
										' : null) . '
									</tbody>
								</table>
							</div>
							<div class="text-center --spinner">
								<div class="spinner-border" role="status">
									<span class="sr-only">' . phrase('loading') . '</span>
								</div>
							</div>
							<div class="--query-' . $val . ' d-none">
								<h5 class="mt-3 mb-3">
									Query String
								</h5>
								<div class="table-responsive">
									<table class="table table-bordered table-sm">
										<thead>
											<tr>
												<th>
													Field
												</th>
												<th>
													Type
												</th>
												<th>
													Description
												</th>
												<th width="100" class="text-center">
													Required
												</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
							<div class="--parameter-' . $val . ' d-none">
								<h5 class="mt-3 mb-3">
									Parameter
								</h5>
								<div class="table-responsive">
									<table class="table table-bordered table-sm">
										<thead>
											<tr>
												<th>
													Field
												</th>
												<th>
													Type
												</th>
												<th>
													Description
												</th>
												<th width="100" class="text-center">
													Required
												</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
							<div class="--response-success-' . $val . ' d-none">
								<h5 class="mt-3 mb-3">
									Success Response
								</h5>
								<pre class="rounded-0 border-top border-bottom mt-0 mb-0 language-json"><code>{}</code></pre>
							</div>
							<div class="--response-error-' . $val . ' d-none">
								<h5 class="mt-3 mb-3">
									Error Response
								</h5>
								<pre class="rounded-0 border-top border-bottom mt-0 mb-0 language-json"><code>{}</code></pre>
							</div>
							<br />
							<br />
						';
					}
				}
			?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		if(UA !== 'mobile')
		{
			$('.pretty-scrollbar').mCustomScrollbar
			({
				autoHideScrollbar: true,
				axis: 'y',
				scrollInertia: 170,
				mouseWheelPixels: 170,
				setHeight: $(window).outerHeight(true) - (($('.navbar').length ? $('.navbar').outerHeight(true) : 0) + ($('.alias-table-header').length ? $('.alias-table-header').outerHeight(true) : 0)),
				advanced:
				{
					updateOnContentResize: true
				},
				autoHideScrollbar: false
			})
		}
		
		$.ajax
		({
			url: '<?php echo current_page(); ?>',
			context: this,
			method: 'POST',
			data:
			{
				mode: 'fetch',
				group: '<?php echo ($selected ? $selected : (isset($group_collector[0]) ? $group_collector[0] : 0)); ?>',
				method: JSON.parse('<?php echo json_encode($method); ?>')
			},
			beforeSend: function()
			{
			}
		})
		.done(function(response, status, error)
		{
			console.log(response),
			$('.--spinner').remove();
			
			if(response.results)
			{
				$.each(response.results, function(key, val)
				{
					if(typeof val.query_string !== 'undefined')
					{
						$.each(val.query_string, function(_key, _val)
						{
							if($('.--query-' + key).hasClass('d-none'))
							{
								$('.--query-' + key).removeClass('d-none')
							}
							
							$('<tr><td><span style="font-family:Consolas">' + _val + '</span></td><td>int</td><td>-</td><td class="text-center"><span class="badge badge-danger">Required</span></td></tr>').appendTo('.--query-' + key + ' tbody')
						})
					}
					
					if(typeof val.parameter !== 'undefined')
					{
						$.each(val.parameter, function(_key, _val)
						{
							if($('.--parameter-' + key).hasClass('d-none'))
							{
								$('.--parameter-' + key).removeClass('d-none')
							}
							
							$('<tr><td><span style="font-family:Consolas">' + _key + '</span></td><td>' + _val.type + '</td><td>' + _val.label + '</td><td class="text-center">' + (_val.required ? '<span class="badge badge-danger">Required</span>' : '') + '</td></tr>').appendTo('.--parameter-' + key + ' tbody')
						})
					}
					
					if(typeof val.response.success !== 'undefined')
					{
						if($('.--response-success-' + key).hasClass('d-none'))
						{
							$('.--response-success-' + key).removeClass('d-none')
						}
						
						$('.--response-success-' + key + ' pre code').text(JSON.stringify(val.response.success, null, 4))
					}
					
					if(typeof val.response.error !== 'undefined')
					{
						if($('.--response-error-' + key).hasClass('d-none'))
						{
							$('.--response-error-' + key).removeClass('d-none')
						}
						
						$('.--response-error-' + key + ' pre code').text(JSON.stringify(val.response.error, null, 4))
					}
				})
			}
		})
	})
</script>
