<div class="jumbotron jumbotron-fluid bg-transparent">
	<div class="container">
		<div class="text-center text-md-left">
			<h3 class="mb-0<?php echo (!$meta->description ? ' mt-3' : null); ?>">
				<?php echo $meta->title; ?>
			</h3>
			<p class="lead">
				<?php echo truncate($meta->description, 256); ?>
			</p>
		</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-4">
			<h6 class="mb-3">
				<?php echo phrase('local_variable'); ?>
			</h6>
			<div class="row">
				<div class="col-6 col-sm-6 col-md-12">
					<div class="form-group">
						<label class="d-block text-muted mb-0">
							AKSARA
						</label>
						<label class="d-block">
							<?php echo aksara('version'); ?>
						</label>
					</div>
				</div>
				<div class="col-6 col-sm-6 col-md-12">
					<div class="form-group">
						<label class="d-block text-muted mb-0">
							<?php echo phrase('built_version'); ?>
						</label>
						<label class="d-block">
							<?php echo aksara('built_version'); ?>
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="d-block text-muted mb-0">
					<?php echo phrase('last_modified'); ?>
				</label>
				<label class="d-block">
					<?php echo aksara('date_modified'); ?>
				</label>
			</div>
		</div>
		<div class="col-md-4">
			<h6 class="mb-3">
				<?php echo phrase('the_laborant'); ?>
			</h6>
			<div class="form-group">
				<label class="d-block text-muted mb-0">
					<a href="//abydahana.github.io" target="_blank">
						Aby Dahana
						<i class="mdi mdi-open-in-new"></i>
					</a>
				</label>
			</div>
		</div>
		<div class="col-md-4">
			<h6 class="mb-3">
				<?php echo phrase('contributors'); ?>
			</h6>
			<div class="form-group">
				<label class="d-block text-muted mb-0">
					<a href="//ganjar.id" target="_blank">
						Ganjar Nugraha
						<i class="mdi mdi-open-in-new"></i>
					</a>
				</label>
			</div>
		</div>
	</div>
	<br />
	<h6 class="mb-3">
		<?php echo phrase('dependencies'); ?>
	</h6>
	<div class="form-group">
		<a href="//php.net" target="_blank">
			PHP
		</a>
		&middot;
		<a href="//codeigniter.com" target="_blank">
			Codeigniter
		</a>
		&middot;
		<a href="//getcomposer.org" target="_blank">
			Composer
		</a>
		&middot;
		<a href="//bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc" target="_blank">
			HMVC
		</a>
		&middot;
		<a href="//mpdf.github.io" target="_blank">
			mPDF
		</a>
	</div>
	<br />
	<h6 class="mb-3">
		JS/CSS <small class="text-muted">(<?php echo phrase('sorted_ascending'); ?>)</small>
	</h6>
	<div class="form-group">
		<a href="//github.com/dreamerslab/jquery.actual" target="_blank">
			Actual
		</a>
		&middot;
		<a href="//devbridge.com/sourcery/components/jquery-autocomplete/" target="_blank">
			Autocomplete
		</a>
		&middot;
		<a href="//github.com/BobKnothe/autoNumeric" target="_blank">
			autoNumeric
		</a>
		&middot;
		<a href="//getbootstrap.com" target="_blank">
			Bootstrap
		</a>
		&middot;
		<a href="//victor-valencia.github.io/bootstrap-iconpicker/" target="_blank">
			Bootstrap Iconpicker
		</a>
		&middot;
		<a href="//itsjavi.com/bootstrap-colorpicker/" target="_blank">
			Bootstrap Colorpicker
		</a>
		&middot;
		<a href="//bootstrap-datepicker.readthedocs.io/en/latest/" target="_blank">
			Bootstrap Datepicker
		</a>
		&middot;
		<a href="//blueimp.github.io/jQuery-File-Upload/" target="_blank">
			FileUploader
		</a>
		&middot;
		<a href="//highcharts.com" target="_blank">
			HighCharts
		</a>
		&middot;
		<a href="//github.com/ematsakov/highlight" target="_blank">
			Highlight
		</a>
		&middot;
		<a href="//infinite-scroll.com" target="_blank">
			Infinite Scroll
		</a>
		&middot;
		<a href="//jquery.com" target="_blank">
			jQuery
		</a>
		&middot;
		<a href="//jqueryui.com/draggable/" target="_blank">
			jQuery UI Draggable
		</a>
		&middot;
		<a href="//stuk.github.io/jszip/" target="_blank">
			JSZip
		</a>
		&middot;
		<a href="//github.com/tuupola/lazyload" target="_blank">
			LazyLoad
		</a>
		&middot;
		<a href="//materialdesignicons.com" target="_blank">
			Materialdesignicons
		</a>
		&middot;
		<a href="//manos.malihu.gr/jquery-custom-content-scroller/" target="_blank">
			mCustomScrollbar
		</a>
		&middot;
		<a href="//www.mediaelementjs.com/" target="_blank">
			Mediaelementjs
		</a>
		&middot;
		<a href="//openlayers.org" target="_blank">
			OpenLayers
		</a>
		&middot;
		<a href="//github.com/OwlCarousel2" target="_blank">
			Owl Carousel
		</a>
		&middot;
		<a href="//popper.js.org" target="_blank">
			Popper
		</a>
		&middot;
		<a href="//select2.org" target="_blank">
			Select2
		</a>
		&middot;
		<a href="//camohub.github.io/jquery-sortable-lists/" target="_blank">
			Sortable
		</a>
		&middot;
		<a href="//summernote.org" target="_blank">
			Summernote
		</a>
		&middot;
		<a href="javascript:void(0)">
			Typewritter
		</a>
		&middot;
		<a href="//github.com/customd/jquery-visible" target="_blank">
			Visible
		</a>
		&middot;
		<a href="//maze.digital/webticker/" target="_blank">
			Webticker
		</a>
	</div>
	<br />
	<br />
	<div class="row">
		<div class="col-md-6 offset-md-3">
			<p class="lead text-center">
				Thank you for those developers that brings their works for free. Without them, Aksara cannot be implemented.
				<br />
				We are awesome!
				<br />
				<br />
				<i class="mdi mdi-heart text-danger"></i>
				<a href="//abydahana.github.io" target="_blank">
					<b>
						Aby Dahana
					</b>
				</a>
			</p>
		</div>
	</div>
</div>