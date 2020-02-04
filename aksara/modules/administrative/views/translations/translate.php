<div class="container-fluid pt-3">
	<div class="row">
		<div class="col-sm-2">
			<label class="text-muted d-block">
				<?php echo phrase('language'); ?>
			</label>
		</div>
		<div class="col">
			<label>
				<?php echo (isset($results->form_data->language->original) ? $results->form_data->language->original : null); ?>
			</label>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-2">
			<label class="text-muted d-block">
				<?php echo phrase('alias'); ?>
			</label>
		</div>
		<div class="col">
			<label>
				<?php echo (isset($results->form_data->description->original) ? $results->form_data->description->original : null); ?>
			</label>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-2">
			<label class="text-muted d-block">
				<?php echo phrase('code'); ?>
			</label>
		</div>
		<div class="col">
			<label>
				<?php echo (isset($results->form_data->code->original) ? $results->form_data->code->original : null); ?>
			</label>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-2">
			<label class="text-muted d-block">
				<?php echo phrase('locale'); ?>
			</label>
		</div>
		<div class="col">
			<label>
				<?php echo (isset($results->form_data->locale->original) ? $results->form_data->locale->original : null); ?>
			</label>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-2">
			<label class="text-muted d-block">
				<?php echo phrase('total_phrase'); ?>
			</label>
		</div>
		<div class="col">
			<label>
				<?php echo (isset($pagination->total_rows) ? number_format($pagination->total_rows) : 0); ?>
			</label>
		</div>
	</div>
	<hr />
	<form action="<?php echo current_page(); ?>" method="POST" class="--validate-form" enctype="multipart/form-data">
		<div class="row">
			<?php
				if(isset($phrases))
				{
					foreach($phrases as $key => $val)
					{
						echo '
							<div class="col-sm-6 col-md-3">
								<div class="form-group">
									<input type="text" name="phrase[' . $key . ']" class="form-control" value="' . $val . '" placeholder="' . $key . '" data-toggle="tooltip" title="' . $key . '" autocomplete="off" />
								</div>
							</div>
						';
					}
				}
			?>
		</div>
		<div class="opt-btn">
			<a href="<?php echo current_page('../', array('id' => null, 'code' => null, 'per_page' => null)); ?>" class="btn btn-light --xhr">
				<i class="mdi mdi-arrow-left"></i>
				<?php echo phrase('back'); ?>
			</a>
			<input type="hidden" name="token" value="<?php echo $token; ?>" />
			<button type="submit" class="btn btn-primary float-right">
				<i class="mdi mdi-check"></i>
				<?php echo phrase('translate'); ?>
			</button>
		</div>
	</form>
	<hr />
	<?php echo $this->template->pagination($pagination); ?>
	<div class="opt-btn-overlap-fix"></div><!-- fix the overlap -->
</div>