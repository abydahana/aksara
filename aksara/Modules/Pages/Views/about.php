<div class="bg-light pt-5 pb-5">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-3 col-sm-2 col-md-1">
				<i class="<?php echo $meta->icon; ?> mdi-4x"></i>
			</div>
			<div class="col-9 col-sm-10 col-md-11">
				<h3 class="mb-0<?php echo (!$meta->description ? ' mt-3' : null); ?>">
					<?php echo $meta->title; ?>
				</h3>
				<p class="lead mb-0">
					<?php echo truncate($meta->description, 256); ?>
				</p>
			</div>
		</div>
	</div>
</div>

<div class="container pt-5 pb-5">
	<div class="row">
		<div class="col-md-4">
			<h6 class="mb-3">
				<?php echo phrase('local_variable'); ?>
			</h6>
			<div class="row">
				<div class="col-6 col-sm-6 col-md-12">
					<div class="mb-3">
						<label class="d-block text-muted mb-0">
							AKSARA
						</label>
						<label class="d-block">
							<?php echo aksara('version'); ?>
						</label>
					</div>
				</div>
				<div class="col-6 col-sm-6 col-md-12">
					<div class="mb-3">
						<label class="d-block text-muted mb-0">
							<?php echo phrase('build_version'); ?>
						</label>
						<label class="d-block">
							<?php echo aksara('build_version'); ?>
						</label>
					</div>
				</div>
			</div>
			<div class="mb-3">
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
			<div class="mb-3">
				<label class="d-block mb-0">
					<a href="//abydahana.github.io" target="_blank">
						<b>
							Aby Dahana
							<i class="mdi mdi-launch"></i>
						</b>
					</a>
				</label>
			</div>
		</div>
		<div class="col-md-4">
			<h6 class="mb-3">
				<?php echo phrase('contributors'); ?>
			</h6>
			<div class="mb-3">
				<label class="d-block mb-0">
					<a href="//ganjar.id" target="_blank">
						<b>
							Ganjar Nugraha
							<i class="mdi mdi-launch"></i>
						</b>
					</a>
				</label>
			</div>
		</div>
	</div>
	<br />
	<h6 class="mb-3">
		<?php echo phrase('dependencies'); ?>
	</h6>
	<div class="mb-3">
		<a href="//codeigniter.com" target="_blank">
			CodeIgniter
		</a>
		&middot;
		<a href="//mpdf.github.io" target="_blank">
			mPDF
		</a>
		&middot;
		<a href="//github.com/facebook/graph-sdk" target="_blank">
			Facebook SDK
		</a>
		&middot;
		<a href="//github.com/google/apiclient" target="_blank">
			Google SDK
		</a>
		&middot;
		<a href="//github.com/chillerlan/php-qrcode" target="_blank">
			PHP QR-Code
		</a>
		&middot;
		<a href="//github.com/picqer/php-barcode-generator" target="_blank">
			PHP Barcode
		</a>
		&middot;
		<a href="//github.com/halaxa/json-machine" target="_blank">
			JSON Machine
		</a>
		&middot;
		<a href="//github.com/phpoffice/phpspreadsheet" target="_blank">
			PHPSpreadSheet
		</a>
	</div>
	<br />
	<h6 class="mb-3">
		JS/CSS <small class="text-muted">(<?php echo phrase('sorted_ascending'); ?>)</small>
	</h6>
	<div class="mb-3">
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
		<a href="//bootstrap-datepicker.readthedocs.io/en/latest/" target="_blank">
			Bootstrap Datepicker
		</a>
		&middot;
		<a href="//highcharts.com" target="_blank">
			HighCharts
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
		<a href="//github.com/MrRio/jsPDF" target="_blank">
			jsPDF
		</a>
		&middot;
		<a href="//stuk.github.io/jszip/" target="_blank">
			JSZip
		</a>
		&middot;
		<a href="//materialdesignicons.com" target="_blank">
			Material Design Icons
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
		<a href="//github.com/moment/moment/" target="_blank">
			MomentJS
		</a>
		&middot;
		<a href="//openlayers.org" target="_blank">
			OpenLayers
		</a>
		&middot;
		<a href="//prismjs.com" target="_blank">
			Prism
		</a>
		&middot;
		<a href="//github.com/julien-maurel/jQuery-Scanner-Detection" target="_blank">
			Scanner
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
		<a href="//swiperjs.com" target="_blank">
			SwiperJS
		</a>
		&middot;
		<a href="//github.com/customd/jquery-visible" target="_blank">
			Visible
		</a>
	</div>
	<br />
	<br />
	<div class="row">
		<div class="col-md-6 offset-md-3">
			<p class="lead text-center">
				Thank you for those developers that brings their works for free. Without them, <a href="//aksaracms.com" class="text-primary" target="_blank"><b>Aksara</b></a> cannot be implemented.
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
