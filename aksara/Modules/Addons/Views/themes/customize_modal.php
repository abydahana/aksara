<?php
	if(!$writable)
	{
		echo '
			<div class="alert alert-danger rounded-0 border-0 mb-0">
				<div class="container">
					<h5>
						' . phrase('notice') . '
					</h5>
					<p class="mb-0 text-danger">
						<b>' . ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $detail->folder . DIRECTORY_SEPARATOR . 'package.json</b> ' . phrase('is_not_writable') . '
					</p>
				</div>
			</div>
		';
	}
?>

<div class="container pt-3 pb-3">
	<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form">
		<div class="mb-3">
			<div class="card rounded">
				<div class="card-body p-3 background-color border-bottom" style="background:<?php echo (isset($detail->colorscheme->page->background) ? $detail->colorscheme->page->background : '#ffffff'); ?>">
					<div class="row">
						<div class="col-md-6 pt-1">
							<b class="text-color" style="color:<?php echo (isset($detail->colorscheme->page->text) ? $detail->colorscheme->page->text : '#333333'); ?>">
								<?php echo phrase('page_color_scheme'); ?>
							</b>
						</div>
						<div class="col-md-3">
							<div class="input-group input-group-sm" role="colorpicker">
								<input type="text" name="colorscheme[page][background]" class="form-control background-color-picker" placeholder="<?php echo phrase('background_color'); ?>" value="<?php echo (isset($detail->colorscheme->page->background) ? $detail->colorscheme->page->background : null); ?>" />
								<span class="input-group-text" data-bs-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
									&nbsp;&nbsp;&nbsp;
								</span>
							</div>
						</div>
						<div class="col-md-3">
							<div class="input-group input-group-sm" role="colorpicker">
								<input type="text" name="colorscheme[page][text]" class="form-control text-color-picker" placeholder="<?php echo phrase('text_color'); ?>" value="<?php echo (isset($detail->colorscheme->page->text) ? $detail->colorscheme->page->text : null); ?>" />
								<span class="input-group-text" data-bs-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
									&nbsp;&nbsp;&nbsp;
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body p-3 background-color" style="background:<?php echo (isset($detail->colorscheme->sidebar->background) ? $detail->colorscheme->sidebar->background : '#ffffff'); ?>">
					<div class="row">
						<div class="col-md-6 pt-1">
							<b class="text-color" style="color:<?php echo (isset($detail->colorscheme->sidebar->text) ? $detail->colorscheme->sidebar->text : '#333333'); ?>">
								<?php echo phrase('sidebar_color_scheme'); ?>
							</b>
						</div>
						<div class="col-md-3">
							<div class="input-group input-group-sm" role="colorpicker">
								<input type="text" name="colorscheme[sidebar][background]" class="form-control background-color-picker" placeholder="<?php echo phrase('background_color'); ?>" value="<?php echo (isset($detail->colorscheme->sidebar->background) ? $detail->colorscheme->sidebar->background : null); ?>" />
								<span class="input-group-text" data-bs-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
									&nbsp;&nbsp;&nbsp;
								</span>
							</div>
						</div>
						<div class="col-md-3">
							<div class="input-group input-group-sm" role="colorpicker">
								<input type="text" name="colorscheme[sidebar][text]" class="form-control text-color-picker" placeholder="<?php echo phrase('text_color'); ?>" value="<?php echo (isset($detail->colorscheme->sidebar->text) ? $detail->colorscheme->sidebar->text : null); ?>" />
								<span class="input-group-text" data-bs-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
									&nbsp;&nbsp;&nbsp;
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body p-3 background-color border-bottom" style="background:<?php echo (isset($detail->colorscheme->header->background) ? $detail->colorscheme->header->background : '#ffffff'); ?>">
					<div class="row">
						<div class="col-md-6 pt-1">
							<b class="text-color" style="color:<?php echo (isset($detail->colorscheme->header->text) ? $detail->colorscheme->header->text : '#333333'); ?>">
								<?php echo phrase('header_color_scheme'); ?>
							</b>
						</div>
						<div class="col-md-3">
							<div class="input-group input-group-sm" role="colorpicker">
								<input type="text" name="colorscheme[header][background]" class="form-control background-color-picker" placeholder="<?php echo phrase('background_color'); ?>" value="<?php echo (isset($detail->colorscheme->header->background) ? $detail->colorscheme->header->background : null); ?>" />
								<span class="input-group-text" data-bs-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
									&nbsp;&nbsp;&nbsp;
								</span>
							</div>
						</div>
						<div class="col-md-3">
							<div class="input-group input-group-sm" role="colorpicker">
								<input type="text" name="colorscheme[header][text]" class="form-control text-color-picker" placeholder="<?php echo phrase('text_color'); ?>" value="<?php echo (isset($detail->colorscheme->header->text) ? $detail->colorscheme->header->text : null); ?>" />
								<span class="input-group-text" data-bs-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
									&nbsp;&nbsp;&nbsp;
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body p-3 background-color" style="background:<?php echo (isset($detail->colorscheme->footer->background) ? $detail->colorscheme->footer->background : '#ffffff'); ?>">
					<div class="row">
						<div class="col-md-6 pt-1">
							<b class="text-color" style="color:<?php echo (isset($detail->colorscheme->footer->text) ? $detail->colorscheme->footer->text : '#333333'); ?>">
								<?php echo phrase('footer_color_scheme'); ?>
							</b>
						</div>
						<div class="col-md-3">
							<div class="input-group input-group-sm" role="colorpicker">
								<input type="text" name="colorscheme[footer][background]" class="form-control background-color-picker" placeholder="<?php echo phrase('background_color'); ?>" value="<?php echo (isset($detail->colorscheme->footer->background) ? $detail->colorscheme->footer->background : null); ?>" />
								<span class="input-group-text" data-bs-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
									&nbsp;&nbsp;&nbsp;
								</span>
							</div>
						</div>
						<div class="col-md-3">
							<div class="input-group input-group-sm" role="colorpicker">
								<input type="text" name="colorscheme[footer][text]" class="form-control text-color-picker" placeholder="<?php echo phrase('text_color'); ?>" value="<?php echo (isset($detail->colorscheme->footer->text) ? $detail->colorscheme->footer->text : null); ?>" />
								<span class="input-group-text" data-bs-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
									&nbsp;&nbsp;&nbsp;
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="--validation-callback mb-0"></div>
		<hr class="row" />
		<div class="row">
			<div class="col-md-12 text-end">
				<button type="button" class="btn btn-light" data-bs-dismiss="modal">
					<?php echo phrase('close'); ?>
					<em class="text-sm">(esc)</em>
				</button>
				<button type="submit" class="btn btn-primary float-end">
					<i class="mdi mdi-check"></i>
					<?php echo phrase('update'); ?>
					<em class="text-sm">(ctrl+s)</em>
				</button>
			</div>
		</div>
	</form>
</div>
