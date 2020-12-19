<?php
	if(file_exists('..' . DIRECTORY_SEPARATOR . 'config.php'))
	{
		header('Location: ../');
		exit;
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Aksara Installer</title>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="msapplication-navbutton-color" content="#007bff" />
		<meta name="theme-color" content="#007bff" />
		<meta name="apple-mobile-web-app-status-bar-style" content="#007bff" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="viewport" content="user-scalable=no, width=device-width, height=device-height, initial-scale=1, maximum-scale=1" />
		<link rel="stylesheet" href="../public/assets/bootstrap/bootstrap.min.css" type="text/css" />
	</head>
	<style type="text/css">
		html,
		body
		{
			height: 100%;
			min-height: 100%
		}
	</style>
	<body class="bg-light">
		<div class="container-fluid h-100">
			<div class="row h-100 align-items-center">
				<div class="col-md-8 offset-md-2 pt-3 pb-3">
					<div class="card shadow">
						<div class="card-body pt-0 pr-3 pb-0 pl-3">
							<div class="row">
								<div class="col-md-4 bg-light border-right pt-3 d-none d-md-block">
									<div class="sticky-top" style="top:15px">
										<a href="//www.aksaracms.com" class="text-primary text-decoration-none" target="_blank">
											<h4 class="font-weight-bold mb-3">
												Aksara <small class="text-sm font-weight-light">Installer</small>
											</h4>
										</a>
										<hr class="row" />
										<p class="step requirement">
											<b>
												Checking Requirements
											</b>
										</p>
										<hr />
										<p class="step database">
											<b>
												Database Configuration
											</b>
										</p>
										<hr />
										<p class="step security">
											<b>
												Security Configuration
											</b>
										</p>
										<hr />
										<p class="step system">
											<b>
												System Configuration
											</b>
										</p>
										<hr />
										<p class="step final">
											<b>
												Finalizing
											</b>
										</p>
									</div>
								</div>
								<div class="col-md-8 pt-3 pb-3">
									<div class="sticky-top step-content" style="top:15px">
										<form action="requirement.php" method="POST" class="--validate-form">
											<h4>
												Hello there...
											</h4>
											<p>
												Thank you for choosing <a href="//www.aksaracms.com" class="text-primary text-decoration-none" target="_blank"><b>Aksara</b></a>!
											</p>
											<hr class="row" />
											<p>
												Before we start the installation, please take a moment to read this few notes. You could check the "<b>Agreement</b>" box and skip reading as usual but i still believe there's a "<b>Nerd</b>" that would read my notes sentence by sentences.
											</p>
											<ol>
												<li>
													<p>
														<a href="//www.aksaracms.com" class="text-primary text-decoration-none" target="_blank"><b>Aksara</b></a> is just a tool to build the ecosystems according to your needs. But something you built with <a href="//www.aksaracms.com" class="text-primary text-decoration-none" target="_blank"><b>Aksara</b></a> should be subjected to it;
													</p>
												</li>
												<li>
													<p>
														You're allowed to re-distribute the ecosystems you built with <a href="//www.aksaracms.com" class="text-primary text-decoration-none" target="_blank"><b>Aksara</b></a> without any concern of my permission, however admitting it was built by you as a whole is a shame;
													</p>
												</li>
												<li>
													<p>
														Never disappoint the creative people who share their work for free, or you'll find them selling their future idea at prices you can't reach.
													</p>
												</li>
											</ol>
											<p>
												Three notes should be enough. I look forward to your support!
											</p>
											<p class="mb-0">
												The fool,
											</p>
											<p class="mb-0">
												<a href="//abydahana.github.io" class="text-primary text-decoration-none" target="_blank">
													<b>
														Aby Dahana
													</b>
												</a>
											</p>
											<hr class="row" />
											<div class="row">
												<div class="col-md-6">
													<label>
														<input type="checkbox" name="agree" value="1" />
														Pretend to agree
													</label>
												</div>
												<div class="col-md-6 text-right">
													<button type="submit" class="btn btn-primary btn-block" disabled>
														Start Installation
													</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript" src="../public/assets/jquery/jquery.min.js"></script>
		<script type="text/javascript" src="../public/assets/bootstrap/bootstrap.min.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function()
			{
				$('body').on('click change', 'input[name=agree]', function(e)
				{
					if($(this).is(':checked'))
					{
						$(this).closest('form').find('button[type=submit]').prop('disabled', false)
					}
					else
					{
						$(this).closest('form').find('button[type=submit]').prop('disabled', true)
					}
				}),
				
				$('body').on('click change', 'input[name=request_config]', function(e)
				{
					if($(this).is(':checked'))
					{
						$('.using_ftp').slideUp()
					}
					else
					{
						$('.using_ftp').slideDown()
					}
				}),
				
				$('body').on('click touch', '.--xhr', function(e)
				{
					e.preventDefault(),
					$.ajax
					({
						url: $(this).attr('href'),
						context: this,
						beforeSend: function()
						{
							$('.failure').remove()
						}
					})
					.done(function(response)
					{
						if(response.status == 301)
						{
							window.location.href	= response.url;
							
							return;
						}
						else if(response.status !== 200)
						{
							$('<div class="alert alert-warning failure"><b>Whoops!</b> ' + response.message + '</div>').prependTo('.--validate-form'),
							$('html, body').animate
							({
								scrollTop: $('.failure').offset().top - 60
							}, 500)
						}
						
						$('.step' + response.active).addClass('text-warning'),
						$(response.passed).removeClass('text-warning').addClass('text-success'),
						$('.step-content').html(response.html)
					})
					.fail(function(response, status, error)
					{
						$(this).find('button[type=submit]').prop('disabled', false),
						$('<div class="alert alert-danger failure"><b>Whoops!</b> ' + error + '</div>').prependTo('.--validate-form'),
						$('html, body').animate
						({
							scrollTop: $('.failure').offset().top - 60
						}, 500)
					})
				}),
				
				$('body').on('submit', '.--validate-form', function(e)
				{
					e.preventDefault(),
					$.ajax
					({
						url: $(this).attr('action'),
						method: $(this).attr('method'),
						data: new FormData(this),
						contentType: false,
						processData: false,
						context: this,
						beforeSend: function()
						{
							$('.failure').remove(),
							$(this).find('button[type=submit]').prop('disabled', true)
						}
					})
					.done(function(response)
					{
						if(response.status !== 200)
						{
							$('<div class="alert alert-warning failure"><b>Whoops!</b> ' + response.message + '</div>').prependTo('.--validate-form'),
							$('html, body').animate
							({
								scrollTop: $('.failure').offset().top - 60
							}, 500)
						}
						
						$(this).find('button[type=submit]').prop('disabled', false),
						$('.step' + response.active).addClass('text-warning'),
						$(response.passed).removeClass('text-warning').addClass('text-success'),
						$('.step-content').html(response.html)
					})
					.fail(function(response, status, error)
					{
						$(this).find('button[type=submit]').prop('disabled', false),
						$('<div class="alert alert-danger failure"><b>Whoops!</b> ' + error + '</div>').prependTo('.--validate-form'),
						$('html, body').animate
						({
							scrollTop: $('.failure').offset().top - 60
						}, 500)
					})
				})
			})
		</script>
	</body>
</html>