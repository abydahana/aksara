<div class="container-fluid">
	<div class="row">
		<div class="col-md-3">
			<div class="sticky-top">
				<div class="pretty-scrollbar">
					<a href="<?php echo base_url('apis/documentation'); ?>" class="--xhr<?php echo (!$active ? ' text-primary fw-bold' : null); ?>">
						<?php echo phrase('get_started'); ?>
					</a>
					<br />
					
					<?php
						if($modules)
						{
							foreach($modules as $key => $val)
							{
								echo '
									<a href="' . current_page(null, array('slug' => $val, 'group' => null)) . '" class="--xhr' . ($val == $active ? ' text-primary fw-bold' : null) . '">
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
				$selected							= service('request')->getGet('group');
				
				if($active)
				{
					$group_collector				= array();
					$access_token					= false;
					
					if($permission->groups)
					{
						$groups						= null;
						$privileges					= array();
						
						foreach($permission->groups as $key => $val)
						{
							$group_collector[]		= $val->group_id;
							$method					= null;
							$extract_privileges		= json_decode($val->group_privileges);
							
							if(isset($extract_privileges->$active))
							{
								foreach($extract_privileges->$active as $_key => $_val)
								{
									$method				.= '<a href="#--method-' . $_val . '"><span class="badge bg-success"><i class="mdi mdi-link"></i> ' . phrase($_val) . '</span></a>&nbsp;';
								}
							}
							
							if($val->group_id)
							{
								$access_token		= true;
							}
							
							$groups					.= '<option value="' . $val->group_id . '"' . ($val->group_id == $selected ? ' selected' : null) . '>' . $val->group_name . '</option>';
							
							$privileges[$selected]	= $method;
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
												' . phrase('group_name') . '
											</th>
											<th>
												' . phrase('privileges') . '
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
						<h5 class="mt-3">
							' . phrase('request_method') . '
						</h5>
					';
					
					if($permission->privileges)
					{
						$method						= array();
						
						foreach($permission->privileges as $key => $val)
						{
							$method[]				= $val;
							
							echo '
								<div class="mb-3" id="--method-' . $val . '">
									<h5 class="mb-1">
										<span class="badge bg-primary bg-md">
											' . (in_array($val, array('create', 'update')) ? 'POST' : (in_array($val, array('delete')) ? 'DELETE' : 'GET')) . '
										</span>
									</h5>
									<div class="rounded pt-2 pe-3 pb-2 ps-3 bg-dark">
										<code class="text-light">' . base_url(('index' !== $val ? $active . '/' . $val : $active)) . '</code>
									</div>
								</div>
								<h5 class="mt-3">
									' . phrase('header') . '
								</h5>
								<div class="table-responsive">
									<table class="table table-bordered table-sm">
										<thead>
											<tr>
												<th>
													' . phrase('field') . '
												</th>
												<th>
													' . phrase('type') . '
												</th>
												<th>
													' . phrase('description') . '
												</th>
												<th width="100" class="text-center">
													' . phrase('required') . '
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
													' . phrase('valid_api_key_added_in_api_service') . '
												</td>
												<td class="text-center">
													<span class="badge bg-danger">
														' . phrase('required') . '
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
													' . phrase('the_token_that_given_from_authentication_response') . '
												</td>
												<td class="text-center">
													<span class="badge bg-danger">
														' . phrase('required') . '
													</span>
												</td>
											</tr>
											' : null) . '
										</tbody>
									</table>
								</div>
								<div class="text-center --spinner">
									<div class="spinner-border" role="status"></div>
								</div>
								<div class="--query-' . $val . ' d-none">
									<h5 class="mt-3">
										' . phrase('query_string') . '
									</h5>
									<div class="table-responsive">
										<table class="table table-bordered table-sm">
											<thead>
												<tr>
													<th>
														' . phrase('field') . '
													</th>
													<th>
														' . phrase('type') . '
													</th>
													<th>
														' . phrase('description') . '
													</th>
													<th width="100" class="text-center">
														' . phrase('required') . '
													</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
								<div class="--parameter-' . $val . ' d-none">
									<h5 class="mt-3">
										' . phrase('parameter') . '
									</h5>
									<div class="table-responsive">
										<table class="table table-bordered table-sm">
											<thead>
												<tr>
													<th>
														' . phrase('field') . '
													</th>
													<th>
														' . phrase('type') . '
													</th>
													<th>
														' . phrase('description') . '
													</th>
													<th width="100" class="text-center">
														' . phrase('required') . '
													</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
								<div class="--response-success-' . $val . ' d-none">
									<h5 class="mt-3">
										' . phrase('success_response') . '
									</h5>
									<pre class="language-javascript rounded mt-0"><code>{}</code></pre>
								</div>
								<div class="--response-error-' . $val . ' d-none">
									<h5 class="mt-3">
										' . phrase('error_response') . '
									</h5>
									<pre class="language-javascript rounded mt-0"><code>{}</code></pre>
								</div>
								<br />
								<br />
							';
						}
					}
				}
				else
				{
					echo '
						<h4 class="mb-3">
							Introduction
						</h4>
						<p>
							<a href="//www.aksaracms.com" target="_blank"><b class="text-primary">Aksara</b></a> built with capability to deliver the <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> output without building the another controller to produce the <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> request and response. The concept and workflow of the <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> implementation\'s just same as you accessing the application built with <a href="//www.aksaracms.com" target="_blank"><b class="text-primary">Aksara</b></a>, which you are open now.
						</p>
						<p>
							You will no longer need to think about complicated things that burden your work. All the <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> request will be deliver through the authentication (handshake), permission checks, including the validation that you\'ve defined for each existing or future modules.
						</p>
						<p>
							It\'s that easy? Yes, because it\'s <a href="//www.aksaracms.com" target="_blank"><b class="text-primary">Aksara</b></a>!
						</p>
						<hr />
						<h4 class="mb-3">
							Getting Started
						</h4>
						<p>
							To be able to use the API request feature, you need to first add an API key from the <a href="' . go_to('../services') . '" class="--xhr"><b>API Services Management</b></a> menu. Specify the allowed request method, the allowed IP range and also the expiration date of the generated API key.
						</p>
						<p>
							Use the generated API key to a specific client in the HEADER property when making a request.
						</p>
						<div class="table-responsive">
							<table class="table table-bordered table-sm">
								<thead>
									<tr>
										<th>
											' . phrase('field') . '
										</th>
										<th>
											' . phrase('type') . '
										</th>
										<th>
											' . phrase('description') . '
										</th>
										<th width="100" class="text-center">
											' . phrase('required') . '
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
											' . phrase('valid_api_key_added_in_api_service') . '
										</td>
										<td class="text-center">
											<span class="badge bg-danger">
												' . phrase('required') . '
											</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<p>
							For modules that require permission as specified for specific user groups, add the <code>X-ACCESS-TOKEN</code> parameter to the client HEADER and set the value with the token obtained from the authentication request.
						</p>
						<div class="table-responsive">
							<table class="table table-bordered table-sm">
								<thead>
									<tr>
										<th>
											' . phrase('field') . '
										</th>
										<th>
											' . phrase('type') . '
										</th>
										<th>
											' . phrase('description') . '
										</th>
										<th width="100" class="text-center">
											' . phrase('required') . '
										</th>
									</tr>
								</thead>
								<tbody>
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
											' . phrase('the_token_that_given_from_authentication_response') . '
										</td>
										<td class="text-center">
											<span class="badge bg-danger">
												' . phrase('required') . '
											</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<p>
							The authentication process can be POST to <code>' . base_url('auth') . '</code> by adding the <code>X-API-KEY</code> on the client HEADER, including the form data (form-data) to send the <code>username</code> and <code>password</code> of the user.
						</p>
						<hr />
						<h4>
							Retrieving the Query Builder
						</h4>
						<p>
							When you\'re requesting the data, there are query string helper (which mentioned under the "<code>query_string</code>") that will help you to retrieving the data to be matched with the query string keywords. Besides that, it\'s also available query string helper to retrieving the specified results.
						</p>
						<div class="table-responsive">
							<table class="table table-bordered table-sm">
								<thead>
									<tr>
										<th>
											' . phrase('key') . '
										</th>
										<th>
											' . phrase('type') . '
										</th>
										<th>
											' . phrase('description') . '
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<span style="font-family:Consolas">
												limit
											</span>
										</td>
										<td>
											Number
										</td>
										<td>
											Applying the result limit
										</td>
									</tr>
									<tr>
										<td>
											<span style="font-family:Consolas">
												offset
											</span>
										</td>
										<td>
											Number
										</td>
										<td>
											Applying the result offset
										</td>
									</tr>
									<tr>
										<td>
											<span style="font-family:Consolas">
												order
											</span>
										</td>
										<td>
											String
										</td>
										<td>
											Field name to be ordered
										</td>
									</tr>
									<tr>
										<td>
											<span style="font-family:Consolas">
												sort
											</span>
										</td>
										<td>
											String <code>ASC|DESC</code>
										</td>
										<td>
											Sort order
										</td>
									</tr>
									<tr>
										<td>
											<span style="font-family:Consolas">
												q
											</span>
										</td>
										<td>
											String
										</td>
										<td>
											The keyword to applying search
										</td>
									</tr>
									<tr>
										<td>
											<span style="font-family:Consolas">
												column
											</span>
										</td>
										<td>
											String
										</td>
										<td>
											Specified field to apply the specific search
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					';
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
							
							$('<tr><td><span style="font-family:Consolas">' + _key + '</span></td><td>int</td><td>-</td><td class="text-center"><span class="badge bg-danger"><?php echo phrase('required'); ?></span></td></tr>').appendTo('.--query-' + key + ' tbody')
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
							
							$('<tr><td><span style="font-family:Consolas">' + _key + '</span></td><td>' + JSON.stringify(_val.type) + '</td><td>' + _val.label + '</td><td class="text-center">' + (_val.required ? '<span class="badge bg-danger"><?php echo phrase('required'); ?></span>' : '') + '</td></tr>').appendTo('.--parameter-' + key + ' tbody')
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
