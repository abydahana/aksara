<?php
	if(!$writable)
	{
		echo '
			<div class="alert alert-danger rounded-0 border-0 mb-0">
				<div class="container">
					<h4>
						' . phrase('notice') . '
					</h4>
					<p class="mb-0 text-danger">
						<b>' . ROOTPATH . 'themes' . DIRECTORY_SEPARATOR . $detail->folder . DIRECTORY_SEPARATOR . 'package.json</b> ' . phrase('is_not_writable') . '
					</p>
				</div>
			</div>
		';
	}
?>

<div class="container-fluid pt-3 pb-3">
	<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form">
		<div class="row">
			<div class="col-md-8">
				<div class="form-group">
					<div class="card rounded-0">
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
										<div class="input-group-append" data-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
											<span class="input-group-text">
												&nbsp;&nbsp;&nbsp;
											</span>
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="input-group input-group-sm" role="colorpicker">
										<input type="text" name="colorscheme[page][text]" class="form-control text-color-picker" placeholder="<?php echo phrase('text_color'); ?>" value="<?php echo (isset($detail->colorscheme->page->text) ? $detail->colorscheme->page->text : null); ?>" />
										<div class="input-group-append" data-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
											<span class="input-group-text">
												&nbsp;&nbsp;&nbsp;
											</span>
										</div>
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
										<div class="input-group-append" data-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
											<span class="input-group-text">
												&nbsp;&nbsp;&nbsp;
											</span>
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="input-group input-group-sm" role="colorpicker">
										<input type="text" name="colorscheme[sidebar][text]" class="form-control text-color-picker" placeholder="<?php echo phrase('text_color'); ?>" value="<?php echo (isset($detail->colorscheme->sidebar->text) ? $detail->colorscheme->sidebar->text : null); ?>" />
										<div class="input-group-append" data-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
											<span class="input-group-text">
												&nbsp;&nbsp;&nbsp;
											</span>
										</div>
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
										<div class="input-group-append" data-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
											<span class="input-group-text">
												&nbsp;&nbsp;&nbsp;
											</span>
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="input-group input-group-sm" role="colorpicker">
										<input type="text" name="colorscheme[header][text]" class="form-control text-color-picker" placeholder="<?php echo phrase('text_color'); ?>" value="<?php echo (isset($detail->colorscheme->header->text) ? $detail->colorscheme->header->text : null); ?>" />
										<div class="input-group-append" data-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
											<span class="input-group-text">
												&nbsp;&nbsp;&nbsp;
											</span>
										</div>
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
										<div class="input-group-append" data-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
											<span class="input-group-text">
												&nbsp;&nbsp;&nbsp;
											</span>
										</div>
									</div>
								</div>
								<div class="col-md-3">
									<div class="input-group input-group-sm" role="colorpicker">
										<input type="text" name="colorscheme[footer][text]" class="form-control text-color-picker" placeholder="<?php echo phrase('text_color'); ?>" value="<?php echo (isset($detail->colorscheme->footer->text) ? $detail->colorscheme->footer->text : null); ?>" />
										<div class="input-group-append" data-toggle="tooltip" title="<?php echo phrase('pick_a_color'); ?>">
											<span class="input-group-text">
												&nbsp;&nbsp;&nbsp;
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="--validation-callback mb-0"></div>
			</div>
		</div>
		<div class="opt-btn-overlap-fix"></div><!-- fix the overlap -->
		<div class="row opt-btn">
			<div class="col-md-8">
				<a href="<?php echo current_page('../', array('theme' => null)); ?>" class="btn btn-link --xhr">
					<i class="mdi mdi-arrow-left"></i>
					<?php echo phrase('back'); ?>
				</a>
				<button type="submit" class="btn btn-primary float-right">
					<i class="mdi mdi-check"></i>
					<?php echo phrase('update'); ?>
					<em class="text-sm">(ctrl+s)</em>
				</button>
			</div>
		</div>
	</form>
</div>
