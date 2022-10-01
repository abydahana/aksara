$(document).ready(function()
{
	/**
	 * Trigger submit on enter, except holding the shift key
	 */
	$('body').off('eypress keydown', 'textarea[name=comments]'),
	$('body').on('keypress keydown', 'textarea[name=comments]', function(e)
	{
		if(e.keyCode == 13 && !e.shiftKey &&  !$(this).closest('form').find('button[type=submit]').length)
		{
			e.preventDefault(),
			
			$(this).closest('form').trigger('submit'),
			$(this).closest('form').trigger('reset'),
			$(this).closest('form').find('.btn-danger').trigger('click'),
			$(this).closest('form').find('.fileupload').addClass('d-none')
		}
	}),
	
	/**
	 * Append reply form
	 */
	$('body').off('click', '.--reply'),
	$('body').on('click', '.--reply', function(e)
	{
		e.preventDefault(),
		
		$(this).closest('#comment-container').find('form').remove(),
		$(
			'<form action="' + $(this).attr('href') + '" method="POST" class="--validate-form mt-3">' +
				'<div class="row">' +
					'<div class="col-10 offset-2 col-lg-11 offset-lg-1 text-sm">' +
						'Replying to <b>' + $(this).attr('data-mention') + '</b>' +
					'</div>' +
				'</div>' +
				'<div class="form-group mb-3">' +
					'<div class="row g-0 align-items-center">' +
						'<div class="col-2 col-lg-1 pt-1 pe-3">' +
							'<img src="' + $(this).attr('data-profile-photo') + '" class="img-fluid rounded-circle" />' +
						'</div>' +
						'<div class="col-10 col-lg-11">' +
							'<div class="position-relative">' +
								'<textarea name="comments" class="form-control" placeholder="' + (typeof phrase.type_a_reply !== 'undefined' ? phrase.type_a_reply : 'Type a reply') + '" rows="1"></textarea>' +
								'<div class="btn-group position-absolute bottom-0 end-0">' +
									'<button type="button" class="btn btn-link" data-bs-toggle="tooltip" title="' + (typeof phrase.attach_photo !== 'undefined' ? phrase.attach_photo : 'Attach photo') + '" onclick="jExec($(this).closest(\'form\').find(\'.fileupload\').removeClass(\'d-none\').find(\'input[type=file]\').trigger(\'click\'))">' +
										'<i class="mdi mdi-camera"></i>' +
									'</button>' +
								'</div>' +
							'</div>' +
							'<div data-provides="fileupload" class="fileupload fileupload-new d-none">' +
								'<span class="btn btn-file" style="width:80px">' +
									'<input type="file" name="attachment" accept=".jpg,.png,.gif" role="image-upload" id="attachment_input" />' +
									'<div class="fileupload-new text-center">' +
										'<img class="img-fluid upload_preview" src="' + config.base_url + 'uploads/widget/icons/placeholder.png' + '" alt="..." />' +
									'</div>' +
									'<button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0" onclick="jExec($(this).closest(\'.btn-file\').find(\'input[type=file]\').val(\'\'), $(this).closest(\'.btn-file\').find(\'img\').attr(\'src\', config.base_url + \'uploads/widget/icons/placeholder.png\'), $(this).closest(\'.fileupload\').addClass(\'d-none\'))">' +
										'<i class="mdi mdi-window-close"></i>' +
									'</button>' +
								'</span>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>' +
				'<div class="row g-0 align-items-center">' +
					'<div class="col-10 offset-2 col-lg-11 offset-lg-1">' +
						'<div class="--validation-callback"></div>' +
					'</div>' +
				'</div>' +
			'</form>'
		)
		.appendTo(($(this).closest('#comment-reply').length ? $(this).closest('#comment-reply') : $(this).closest('.comment-item').find('#comment-reply')));
		
		if($(this).closest('#comment-reply').length)
		{
			$(this).closest('#comment-reply').find('form').find('textarea').trigger('focus')
		}
		else
		{
			$(this).closest('.comment-item').find('#comment-reply').find('form').find('textarea').trigger('focus')
		}
		
		$('[data-bs-toggle=tooltip]').tooltip()
	})
});
