<footer id="footer-wrapper" class="pt-5">
	<div class="container">
		<div class="row">
			<div class="col-lg-2 text-sm-center">
				<div class="row">
					<div class="col-4 offset-4 col-sm-4 offset-sm-4 col-md-4 offset-md-4 col-lg-8 offset-lg-0">
						<p>
							<a href="<?php echo base_url(); ?>">
								<img src="<?php echo get_image('settings', get_setting('app_icon'), 'thumb'); ?>" class="img-fluid grayscale mt-2 --xhr" />
							</a>
						</p>
					</div>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-lg-2">
				<div class="mb-5">
					<ul class="list-unstyled">
						<li class="pt-1 pb-1 mb-3">
							<h6 class="fw-bold">
								<?php echo phrase('Featured'); ?>
							</h6>
						</li>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('blogs'); ?>" class="text-dark --xhr">
								<?php echo phrase('News'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('galleries'); ?>" class="text-dark --xhr">
								<?php echo phrase('Galleries'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('videos'); ?>" class="text-dark --xhr">
								<?php echo phrase('Videos'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('peoples'); ?>" class="text-dark --xhr">
								<?php echo phrase('Peoples'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('announcements'); ?>" class="text-dark --xhr">
								<?php echo phrase('Announcements'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('testimonials'); ?>" class="text-dark --xhr">
								<?php echo phrase('Testimonials'); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-lg-2">
				<div class="mb-5">
					<ul class="list-unstyled">
						<li class="pt-1 pb-1 mb-3">
							<h6 class="fw-bold">
								<?php echo phrase('Knowledge Center'); ?>
							</h6>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/documentation" target="_blank" class="text-dark">
								<?php echo phrase('Documentation'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/features" target="_blank" class="text-dark">
								<?php echo phrase('Features'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/faqs" target="_blank" class="text-dark">
								<?php echo phrase('FAQs'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/terms-and-conditions" target="_blank" class="text-dark">
								<?php echo phrase('Terms and Conditions'); ?>
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/privacy-policy" target="_blank" class="text-dark">
								<?php echo phrase('Privacy Policy'); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-6 col-sm-4 col-lg-2">
				<div class="mb-5">
					<ul class="list-unstyled">
						<li class="pt-1 pb-1 mb-3">
							<h6 class="fw-bold">
								<?php echo phrase('Links'); ?>
							</h6>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/about/company" target="_blank" class="text-dark">
								About Aksara
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/about/goals" target="_blank" class="text-dark">
								Aksara Goals
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/about/awards" target="_blank" class="text-dark">
								Aksara Awards
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/about/sponsors" target="_blank" class="text-dark">
								Become a Sponsor
							</a>
						</li>
						<li class="pt-1 pb-1">
							<a href="//www.aksaracms.com/pages/about/donation" target="_blank" class="text-dark">
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
							<a href="<?php echo base_url('pages/contact'); ?>" class="text-dark --xhr">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-home-city-outline text-primary"></i>
									</div>
									<div class="col-10 col-sm-11">
										<h6 class="fw-bold">
											<?php echo(get_setting('office_name') ? get_setting('office_name') : 'Non Profit'); ?>
										</h6>
									</div>
								</div>
							</a>
						</li>
						<?php if (get_setting('office_address'))
						{ ?>
						<li class="pt-1 pb-1">
							<a href="<?php echo base_url('pages/contact'); ?>" class="--xhr">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-google-maps text-danger"></i>
									</div>
									<div class="col-10 col-sm-11 text-dark">
										<?php echo nl2br(get_setting('office_address')); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
						<?php if (get_setting('office_email'))
						{ ?>
						<li class="pt-1 pb-1">
							<a href="mailto:<?php echo get_setting('office_email'); ?>">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-at text-danger"></i>
									</div>
									<div class="col-10 col-sm-11 text-dark">
										<?php echo get_setting('office_email'); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
						<?php if (get_setting('office_phone'))
						{ ?>
						<li class="pt-1 pb-1">
							<a href="tel:<?php echo get_setting('office_phone'); ?>">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-phone text-success"></i>
									</div>
									<div class="col-10 col-sm-11 text-dark">
										<?php echo get_setting('office_phone'); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
						<?php if (get_setting('office_fax'))
						{ ?>
						<li class="pt-1 pb-1">
							<a href="fax:<?php echo get_setting('office_fax'); ?>">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-fax text-warning"></i>
									</div>
									<div class="col-10 col-sm-11 text-dark">
										<?php echo get_setting('office_fax'); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
						<?php if (get_setting('whatsapp_number'))
						{ ?>
						<li class="pt-1 pb-1">
							<a href="https://api.whatsapp.com/send?phone=<?php echo str_replace(['+', '-', ' '], [null, null, null], get_setting('whatsapp_number')); ?>&text=<?php echo phrase('Hello') . '%20' . get_setting('app_name'); ?>...">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1 text-success">
										<i class="mdi mdi-whatsapp"></i>
									</div>
									<div class="col-10 col-sm-11 text-dark">
										<?php echo get_setting('whatsapp_number'); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
						<?php if (get_setting('twitter_username'))
						{ ?>
						<li class="pt-1 pb-1">
							<a href="https://www.twitter.com/<?php echo get_setting('twitter_username'); ?>">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-twitter text-info"></i>
									</div>
									<div class="col-10 col-sm-11 text-dark">
										@<?php echo get_setting('twitter_username'); ?>
									</div>
								</div>
							</a>
						</li>
						<?php } ?>
						<?php if (get_setting('instagram_username'))
						{ ?>
						<li class="pt-1 pb-1">
							<a href="https://www.instagram.com/<?php echo get_setting('instagram_username'); ?>">
								<div class="row no-gutters">
									<div class="col-2 col-sm-1">
										<i class="mdi mdi-instagram text-danger"></i>
									</div>
									<div class="col-10 col-sm-11 text-dark">
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
			<small class="fw-bold">
				<?php echo phrase('Copyright'); ?> &#169;<?php echo date('Y'); ?> - <?php echo get_setting('office_name'); ?>
			</small>
			<small>
				(<a href="<?php echo base_url('pages/about'); ?>" class="fw-bold --xhr">Aksara <?php echo aksara('build_version'); ?></a>)
			</small>
		</div>
		<div class="row">
			<div class="col-md-8 offset-md-2">
				<div class="text-center">
					<small class="text-muted">
						<?php echo get_setting('app_description'); ?>
					</small>
				</div>
			</div>
		</div>
	</div>
</footer>
