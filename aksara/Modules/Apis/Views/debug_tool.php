<div class="container-fluid p-3">
	<form action="<?php echo current_page(); ?>" method="POST" class="--api-debug">
		<div class="row --apply-increase-one border-bottom">
			<div class="col-md-10">
				<div class="input-group mb-3">
					<select name="method" class="form-control" style="max-width:100px">
						<option value="GET">
							GET
						</option>
						<option value="POST">
							POST
						</option>
						<option value="DELETE">
							DELETE
						</option>
					</select>
					<input type="text" name="url" class="form-control" placeholder="<?php echo phrase('enter_service_url'); ?>" />
				</div>
			</div>
			<div class="col-md-2">
				<div class="d-grid mb-3">
					<button type="submit" class="btn btn-primary">
						<i class="mdi mdi-send"></i>
						<?php echo phrase('send'); ?>
					</button>
				</div>
			</div>
		</div>
		<div style="margin-right:-1rem; margin-left:-1rem">
			<nav class="--apply-increase-two" style="margin-top:-1px">
				<div class="nav nav-tabs" id="nav-tab" role="tablist">
					<a class="nav-item nav-link rounded-0" data-bs-toggle="tab" href="#params-headers" role="tab" style="border-left:0">
						<?php echo phrase('request_headers'); ?>
					</a>
					<a class="nav-item nav-link rounded-0" data-bs-toggle="tab" href="#params-body" role="tab">
						<?php echo phrase('request_body'); ?>
					</a>
					<a class="nav-item nav-link rounded-0 active response-result" data-bs-toggle="tab" href="#results-pretty" role="tab">
						<?php echo phrase('response'); ?>
					</a>
				</div>
			</nav>
			<div class="tab-content" id="nav-tabContent1" style="margin-top:-1px">
				<div class="tab-pane border-bottom p-3" id="params-headers" role="tabpanel">
					<div class="row">
						<div class="col-6 col-md-4 text-muted">
							<div class="mb-3">
								<input type="text" name="header_key[]" class="form-control form-control-sm param-header-key" placeholder="Key" />
							</div>
						</div>
						<div class="col-6 col-md-6 ps-0 text-muted">
							<div class="mb-3">
								<div class="input-group">
									<input type="text" name="header_value[]" class="form-control form-control-sm param-header-value" placeholder="Value" />
									<button type="button" class="btn btn-secondary btn-sm" onclick="jExec($(this).closest('.row').remove())">
										<i class="mdi mdi-window-close"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
					<button type="button" class="btn btn-secondary btn-sm --add-parameter" data-parameter="header">
						<i class="mdi mdi-plus"></i>
						<?php echo phrase('add_parameter'); ?>
					</button>
				</div>
				<div class="tab-pane border-bottom p-3" id="params-body" role="tabpanel">
					<div class="row">
						<div class="col-6 col-md-4 text-muted">
							<div class="mb-3">
								<input type="text" name="body_key[]" class="form-control form-control-sm param-body-key" placeholder="Key" />
							</div>
						</div>
						<div class="col-6 col-md-6 ps-0 text-muted">
							<div class="mb-3">
								<div class="input-group">
									<input type="text" name="body_value[]" class="form-control form-control-sm param-body-value" placeholder="Value" />
									<button type="button" class="btn btn-secondary btn-sm" onclick="jExec($(this).closest('.row').remove())">
										<i class="mdi mdi-window-close"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
					<button type="button" class="btn btn-secondary btn-sm --add-parameter" data-parameter="body">
						<i class="mdi mdi-plus"></i>
						<?php echo phrase('add_parameter'); ?>
					</button>
				</div>
				<div class="tab-pane border-bottom p-3 p-3 show active" id="results-pretty" role="tabpanel">
					<pre class="rounded mt-0 mb-0 language-javascript"><code>{}</code></pre>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		$('.--add-parameter').on('click', function(e)
		{
			var initial					= $(this).attr('data-parameter');
			e.preventDefault(),
			$(
				'<div class="row">' +
					'<div class="text-muted col-6 col-md-4">' +
						'<div class="mb-3">' +
							'<input type="text" name="' + initial + '_key[]" class="form-control form-control-sm param-' + initial + '-key" placeholder="<?php echo phrase('key'); ?>" />' +
						'</div>' +
					'</div>' +
					'<div class="text-muted col-6 col-md-6 ps-0">' +
						'<div class="mb-3">' +
							'<div class="input-group">' +
								'<input type="text" name="' + initial + '_value[]" class="form-control form-control-sm param-' + initial + '-value" placeholder="<?php echo phrase('value'); ?>" />' +
								'<button type="button" class="btn btn-secondary btn-sm" onclick="jExec($(this).closest(\'.row\').remove())">' +
									'<i class="mdi mdi-window-close"></i>' +
								'</button>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>'
			)
			.insertBefore($(this))
		}),
		
		$('.--api-debug').on('submit', function(e)
		{
			e.preventDefault(),
			$('.mdi.mdi-send').removeClass('mdi-send').addClass('mdi-loading mdi-spin'),
			$('.response-result').trigger('click');
			
			if(!$(this).find('input[name=url]').val())
			{
				$('.mdi.mdi-loading.mdi-spin').removeClass('mdi-loading mdi-spin').addClass('mdi-send'),
				
				$('pre code').text(JSON.stringify({error: "<?php echo phrase('no_service_url_given'); ?>"}, null, 4)),
				
				Prism.highlightAll();
				
				return;
			}
			
			var header							= {},
				body							= {},
				method							= $(this).find('select[name=method]').val(),
				parameter						= new FormData(this);
			
			$('.param-header-key').each(function(num, value)
			{
				var key							= $(this).val(),
					val							= $('.param-header-value:eq(' + num + ')').val();
				if(val)
				{
					header[key]					= val;
				}
			}),
			
			$('.param-body-key').each(function(num, value)
			{
				var key							= $(this).val(),
					val							= $('.param-body-value:eq(' + num + ')').val();
				if(val)
				{
					body[key]					= val;
				}
			}),
			
			$.ajax
			({
				url: $(this).find('input[name=url]').val(),
				method: method,
				data: body,
				headers: header,
				beforeSend: function()
				{
					$('pre code').text('<?php echo phrase('requesting'); ?>...'),
					$('.result-html').html('')
				}
			})
			.always(function(response, status, error)
			{
				if(typeof response !== 'object')
				{
					response					= {
						error: '<?php echo phrase('the_response_is_not_a_valid_object'); ?>'
					};
				}
				
				$('.mdi.mdi-loading.mdi-spin').removeClass('mdi-loading mdi-spin').addClass('mdi-send'),
				$('pre code').text(JSON.stringify((typeof response.responseJSON !== 'undefined' ? response.responseJSON : response), null, 4)),
				Prism.highlightAll();
				
				if(UA !== 'mobile')
				{
					$('.pane-wrapper').mCustomScrollbar
					({
						autoHideScrollbar: true,
						axis: 'y',
						scrollInertia: 170,
						mouseWheelPixels: 170,
						setHeight: $(window).outerHeight(true) - (($('.navbar').length ? $('.navbar').outerHeight(true) : 0) + ($('.alias-table-header').length ? $('.alias-table-header').outerHeight(true) : 0) + ($('.--apply-increase-one').length ? $('.--apply-increase-one').outerHeight(true) : 0) + ($('.--apply-increase-two').length ? $('.--apply-increase-two').outerHeight(true) : 0) + 65),
						advanced:
						{
							updateOnContentResize: true
						},
						autoHideScrollbar: false
					})
				}
			})
		})
	})
</script>
