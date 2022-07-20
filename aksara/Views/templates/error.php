<div class="container-fluid">
	<div class="row bg-dark full-height">
		<div class="col-12">
			<div class="pt-3 pb-3 text-success font-monospace">
				<p class="mb-0">
					[info@localhost ~]# aksara trace -exception
				</p>
				<p class="mb-0 text-danger">
					<?php echo phrase('no_response_could_be_loaded'); ?>
					<br />
					<?php echo phrase('make_sure_to_check_the_following_mistake'); ?>:
					<ol class="text-danger">
						<li>
							<?php echo phrase('module_structure'); ?>,
						</li>
						<li>
							<?php echo phrase('incorrect_view_path'); ?>,
						</li>
						<li>
							<?php echo phrase('database_table_existence'); ?>,
						</li>
						<li>
							<?php echo phrase('something_caused_by_typo'); ?>.
						</li>
					</ol>
				</p>
				<p class="mb-0">
					[info@localhost ~]# <blink>_</blink>
				</p>
			</div>
		</div>
	</div>
</div>
