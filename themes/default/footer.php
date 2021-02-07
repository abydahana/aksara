<div class="bg-white pt-5">
	<div class="container">
		<div class="row">
			<div class="col-lg-2 text-sm-center">
				<p>
					<a href="<?php echo base_url(); ?>">
						<img src="<?php echo get_image('settings', get_setting('app_icon'), 'thumb'); ?>" class="img-fluid grayscale mt-2 --xhr" />
					</a>
				</p>
			</div>
			<div class="col-6 col-sm-4 col-lg-2">
				<div class="mb-5">
					<ul class="list-unstyled">
						<li class="pt-1 pb-1 mb-3">
							<h6>
								<?php echo phrase('featured'); ?>
							</h6>
						</li>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('blogs'); ?>" class="--xhr">
								<?php echo phrase('news'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('galleries'); ?>" class="--xhr">
								<?php echo phrase('galleries'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('peoples'); ?>" class="--xhr">
								<?php echo phrase('peoples'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('announcements'); ?>" class="--xhr">
								<?php echo phrase('announcements'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('testimonials'); ?>" class="--xhr">
								<?php echo phrase('testimonials'); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-lg-2">
				<div class="mb-5">
					<ul class="list-unstyled">
						<li class="pt-1 pb-1 mb-3">
							<h6>
								<?php echo phrase('knowledge_center'); ?>
							</h6>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/documentation" target="_blank">
								<?php echo phrase('documentation'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/features" target="_blank">
								<?php echo phrase('features'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/faqs" target="_blank">
								<?php echo phrase('faqs'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/terms-and-conditions" target="_blank">
								<?php echo phrase('terms_and_conditions'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/privacy-policy" target="_blank">
								<?php echo phrase('privacy_policy'); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-lg-2">
				<div class="mb-5">
					<ul class="list-unstyled">
						<li class="pt-1 pb-1 mb-3">
							<h6>
								<?php echo phrase('links'); ?>
							</h6>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/about/company" target="_blank">
								About Aksara
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/about/goals" target="_blank">
								Aksara Goals
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/about/awards" target="_blank">
								Aksara Awards
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/about/sponsors" target="_blank">
								Become a Sponsor
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/about/donation" target="_blank">
								Donation Program
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-12 col-sm-12 col-lg-4">
				<div class="mb-5">
					<ul class="list-unstyled">
						<li class="pt-1 pb-1 mb-3">
							<a href="<?php echo base_url('pages/contact'); ?>" class="--xhr">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-office-building text-primary"></i>
									</div>
									<div class="col-10 col-sm-11">
										<h6>
											<?php echo (get_setting('office_name') ? get_setting('office_name') : 'Non Profit'); ?>
										</h6>
									</div>
								</div>
							</a>
						</li>
						<?php if(get_setting('office_address')) { ?>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('pages/contact'); ?>" class="--xhr">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-map-marker text-danger"></i>
									</div>
									<div class="col-10 col-sm-11">
										<?php echo nl2br(get_setting('office_address')); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
						<?php if(get_setting('office_email')) { ?>
						<li class="pt-1 pb-1">
							<a href="mailto:<?php echo get_setting('office_email'); ?>">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-at text-danger"></i>
									</div>
									<div class="col-10 col-sm-11">
										<?php echo get_setting('office_email'); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
						<?php if(get_setting('office_phone')) { ?>
						<li class="pt-1 pb-1">
							<a href="tel:<?php echo get_setting('office_phone'); ?>">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-phone text-success"></i>
									</div>
									<div class="col-10 col-sm-11">
										<?php echo get_setting('office_phone'); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
						<?php if(get_setting('office_fax')) { ?>
						<li class="pt-1 pb-1">
							<a href="fax:<?php echo get_setting('office_fax'); ?>">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-fax text-warning"></i>
									</div>
									<div class="col-10 col-sm-11">
										<?php echo get_setting('office_fax'); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
						<?php if(get_setting('whatsapp_number')) { ?>
						<li class="pt-1 pb-1">
							<a href="https://api.whatsapp.com/send?phone=<?php echo str_replace(array('+', '-', ' '), array(null, null, null), get_setting('whatsapp_number')); ?>&text=<?php echo phrase('hello') . '%20' . get_setting('app_name'); ?>...">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1 text-success">
										<i class="mdi mdi-whatsapp"></i>
									</div>
									<div class="col-10 col-sm-11">
										<?php echo get_setting('whatsapp_number'); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
						<?php if(get_setting('twitter_username')) { ?>
						<li class="pt-1 pb-1">
							<a href="https://www.twitter.com/<?php echo get_setting('twitter_username'); ?>">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-twitter text-info"></i>
									</div>
									<div class="col-10 col-sm-11">
										@<?php echo get_setting('twitter_username'); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
						<?php if(get_setting('instagram_username')) { ?>
						<li class="pt-1 pb-1">
							<a href="https://www.instagram.com/<?php echo get_setting('instagram_username'); ?>">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-instagram text-danger"></i>
									</div>
									<div class="col-10 col-sm-11">
										@<?php echo get_setting('instagram_username'); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
		<div class="text-center">
			<small class="font-weight-bold">
				<?php echo phrase('copyright'); ?> &#169;<?php echo date('Y'); ?> - <?php echo get_setting('office_name'); ?>
			</small>
			<small>
				(<a href="<?php echo base_url('pages/about'); ?>" class="font-weight-bold --xhr">Aksara <?php echo aksara('built_version'); ?></a>)
			</small>
		</div>
		<div class="row">
			<div class="col-md-8 offset-md-2">
				<div class="text-center text-muted text-sm font-weight-light mb-0">
					<?php echo get_setting('app_description'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
