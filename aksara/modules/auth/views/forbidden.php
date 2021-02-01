<div class="box no-border animated fadeIn">
	<div class="box-header with-border">
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse">
				<i class="fa fa-minus"></i>
			</button>
			<button type="button" class="btn btn-box-tool" data-widget="maximize">
				<i class="fa fa-expand"></i>
			</button>
			<button type="button" class="btn btn-box-tool"<?php echo ($modal ? ' data-dismiss="modal"' : ' data-widget="remove"'); ?>>
				<i class="fa fa-times"></i>
			</button>
		</div>
		<h3 class="box-title">
			<i class="fa fa-ban"></i>
			<?php echo phrase('access_denied'); ?>
		</h3>
	</div>
	<div class="box-body text-center animated zoomIn">
		<div class="row">
			<div class="col-md-6 offset-md-<?php echo (get_userdata('is_logged') ? '2' : '3'); ?>">
				<br />
				<br />
				<div class="alert alert-warning">
					<i class="fa fa-exclamation-triangle fa-5x"></i>
					<p>
						<?php echo phrase('you_do_not_have_any_suficient_privileges_to_access_or_modify_this_page') ?>
					</p>
					<br />
					<a href="<?php echo base_url(); ?>" class="btn btn-outline-warning --xhr">
						<i class="fa fa-chevron-left"></i>
						<?php echo phrase('back_to_dashboard'); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
