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
		<div class="card mb-3">
			<div class="card-body p-2 rounded" style="background:<?php echo (isset($detail->colorscheme->page->background) ? $detail->colorscheme->page->background : '#ffffff'); ?>; color:<?php echo (isset($detail->colorscheme->page->text) ? $detail->colorscheme->page->text : '#333333'); ?>">
				<div class="row align-items-center">
					<div class="col-12 col-md-6 col-lg-8">
						<b>
							<?php echo phrase('page_color_scheme'); ?>
						</b>
					</div>
					<div class="col-6 col-md-3 col-lg-2">
						<div class="input-group input-group-sm">
							<span class="input-group-text">
								<?php echo phrase('background'); ?>
							</span>
							<input type="color" name="colorscheme[page][background]" class="form-control form-control-color background-color" value="<?php echo (isset($detail->colorscheme->page->background) ? $detail->colorscheme->page->background : '#ffffff'); ?>" />
						</div>
					</div>
					<div class="col-6 col-md-3 col-lg-2">
						<div class="input-group input-group-sm">
							<span class="input-group-text">
								<?php echo phrase('foreground'); ?>
							</span>
							<input type="color" name="colorscheme[page][text]" class="form-control form-control-color foreground-color" value="<?php echo (isset($detail->colorscheme->page->text) ? $detail->colorscheme->page->text : '#333333'); ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card mb-3">
			<div class="card-body p-2 rounded" style="background:<?php echo (isset($detail->colorscheme->header->background) ? $detail->colorscheme->header->background : '#333333'); ?>; color:<?php echo (isset($detail->colorscheme->header->text) ? $detail->colorscheme->header->text : '#fafafa'); ?>">
				<div class="row align-items-center">
					<div class="col-12 col-md-6 col-lg-8">
						<b>
							<?php echo phrase('header_color_scheme'); ?>
						</b>
					</div>
					<div class="col-6 col-md-3 col-lg-2">
						<div class="input-group input-group-sm">
							<span class="input-group-text">
								<?php echo phrase('background'); ?>
							</span>
							<input type="color" name="colorscheme[header][background]" class="form-control form-control-color background-color" value="<?php echo (isset($detail->colorscheme->header->background) ? $detail->colorscheme->header->background : '#333333'); ?>" />
						</div>
					</div>
					<div class="col-6 col-md-3 col-lg-2">
						<div class="input-group input-group-sm">
							<span class="input-group-text">
								<?php echo phrase('foreground'); ?>
							</span>
							<input type="color" name="colorscheme[header][text]" class="form-control form-control-color foreground-color" value="<?php echo (isset($detail->colorscheme->header->text) ? $detail->colorscheme->header->text : '#fafafa'); ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card mb-3">
			<div class="card-body p-2 rounded" style="background:<?php echo (isset($detail->colorscheme->sidebar->background) ? $detail->colorscheme->sidebar->background : '#ffffff'); ?>; color:<?php echo (isset($detail->colorscheme->sidebar->text) ? $detail->colorscheme->sidebar->text : '#333333'); ?>">
				<div class="row align-items-center">
					<div class="col-12 col-md-6 col-lg-8">
						<b>
							<?php echo phrase('sidebar_color_scheme'); ?>
						</b>
					</div>
					<div class="col-6 col-md-3 col-lg-2">
						<div class="input-group input-group-sm">
							<span class="input-group-text">
								<?php echo phrase('background'); ?>
							</span>
							<input type="color" name="colorscheme[sidebar][background]" class="form-control form-control-color background-color" value="<?php echo (isset($detail->colorscheme->sidebar->background) ? $detail->colorscheme->sidebar->background : '#fafafa'); ?>" />
						</div>
					</div>
					<div class="col-6 col-md-3 col-lg-2">
						<div class="input-group input-group-sm">
							<span class="input-group-text">
								<?php echo phrase('foreground'); ?>
							</span>
							<input type="color" name="colorscheme[sidebar][text]" class="form-control form-control-color foreground-color" value="<?php echo (isset($detail->colorscheme->sidebar->text) ? $detail->colorscheme->sidebar->text : '#333333'); ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card mb-3">
			<div class="card-body p-2 rounded" style="background:<?php echo (isset($detail->colorscheme->footer->background) ? $detail->colorscheme->footer->background : '#ffffff'); ?>; color:<?php echo (isset($detail->colorscheme->footer->text) ? $detail->colorscheme->footer->text : '#333333'); ?>">
				<div class="row align-items-center">
					<div class="col-12 col-md-6 col-lg-8">
						<b>
							<?php echo phrase('footer_color_scheme'); ?>
						</b>
					</div>
					<div class="col-6 col-md-3 col-lg-2">
						<div class="input-group input-group-sm">
							<span class="input-group-text">
								<?php echo phrase('background'); ?>
							</span>
							<input type="color" name="colorscheme[footer][background]" class="form-control form-control-color background-color" value="<?php echo (isset($detail->colorscheme->footer->background) ? $detail->colorscheme->footer->background : '#ffffff'); ?>" />
						</div>
					</div>
					<div class="col-6 col-md-3 col-lg-2">
						<div class="input-group input-group-sm">
							<span class="input-group-text">
								<?php echo phrase('foreground'); ?>
							</span>
							<input type="color" name="colorscheme[footer][text]" class="form-control form-control-color foreground-color" value="<?php echo (isset($detail->colorscheme->footer->text) ? $detail->colorscheme->footer->text : '#333333'); ?>" />
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
