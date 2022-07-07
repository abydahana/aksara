<?php
	if(!$permission->uploads || !$permission->writable)
	{
		echo '
			<div class="alert alert-danger rounded-0 border-0 mb-0">
				<div class="container">
					<h4>
						Notice!
					</h4>
					' . (!$permission->uploads ? '<p class="mb-0 text-danger"><b>' . FCPATH . UPLOAD_PATH . '/</b> is not writable.</p>' : null) . '
					' . (!$permission->writable ? '<p class="mb-0 text-danger"><b>' . WRITEPATH . '</b> is not writable.</p>' : null) . '
					<br />
					<a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>Click here</b></a> to get advice how to solve this issue.
				</div>
			</div>
		';
	}
?>

<div class="bg-light pt-5 pb-5">
	<div class="container">
		<div class="row">
			<div class="col-lg-8 offset-lg-2">
				<h1 class="text-center">
					<?php echo $meta->title; ?>
				</h1>
				<p class="lead text-center">
					<?php echo truncate($meta->description, 256); ?>
				</p>
			</div>
		</div>
	</div>
</div>
<div class="container pt-5 pb-5">
	<div class="row">
		<div class="col-lg-8 offset-lg-2">
			<h3 class="mb-3 text-center">
				You are using <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
			</h3>
			<?php
				if($error)
				{
					echo '
						<div class="mb-5">
							<p>
								Unfortunatelly, your request to install the sample data cannot be processed due to folder permission issue.
							</p>
							<p>
								You could install the sample data manually by using this method:
							</p>
							<ol>
								<li>
									<a href="' . base_url('install/assets/sample-module.zip') . '" target="_blank" class="text-primary"><b>Click here</b></a> to download the sample module;
								</li>
								<li>
									Extract to <code>' . ROOTPATH . 'modules</code> folder;
								</li>
								<li>
									Reload this page.
								</li>
							</ol>
						</div>
					';
				}
				else
				{
					echo '
						<div class="mb-5">
							<p>
								You are viewing this page because you installing <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> as "<b>DEVELOPER MODE</b>". There\'s no example content that been created. Just like popular PHP framework, you must build your own modules by referencing the function served by <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>. You can still login and add your content to the built-in module (<b>CMS</b> a.k.a <b>Content Management System</b>) such <b>Blogs</b>, <b>Pages</b>, <b>Galleries</b> and many more.
							</p>
							<p>
								This module is located in
								<br />
								<code>' . ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . 'Home</code>.
							</p>
							<p>
								You can <b>override</b> this module into
								<br />
								<code>' . ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . 'Home</code> without removing the original one.
							</p>
							<p>
								<b>How could that be done?</b> Because you are using <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
							</p>
						</div>
					';
				}
			?>
			<hr class="mt-5 mb-5" />
			<h3 class="mb-3 text-center">
				Go Further
			</h3>
			<h4 class="mb-3">
				<i class="mdi mdi-book-open-page-variant"></i>
				&nbsp;
				Documentation
			</h4>
			<div class="mb-5">
				<p>
					The Guidelines contains an introduction, tutorial, a number of "how to" guides, and then reference documentation for the components that make up the <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
					<br />
					<a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>Check the Documentation</b></a>!
				</p>
			</div>
			<h4 class="mb-3">
				<i class="mdi mdi-account-group-outline"></i>
				&nbsp;
				Community
			</h4>
			<div class="mb-5">
				<p>
					You can open discussion related to the features, bugs or suggestions to the following community forum:
				</p>
				<p class="mb-1">
					<a href="https://github.com/abydahana/Aksara/issues" class="text-primary" target="blank">
						https://github.com/abydahana/Aksara/issues<i class="mdi mdi-open-in-new"></i>
					</a>
				</p>
				<p class="mb-1">
					<a href="https://www.facebook.com/groups/Codeigniterdev" class="text-primary" target="blank">
						https://www.facebook.com/groups/Codeigniterdev<i class="mdi mdi-open-in-new"></i>
					</a>
				</p>
				<p class="mb-1">
					<a href="https://www.facebook.com/groups/codeigniter.id" class="text-primary" target="blank">
						https://www.facebook.com/groups/codeigniter.id<i class="mdi mdi-open-in-new"></i>
					</a>
				</p>
				<p>
					<a href="https://www.facebook.com/groups/phpid" class="text-primary" target="blank">
						https://www.facebook.com/groups/phpid<i class="mdi mdi-open-in-new"></i>
					</a>
				</p>
				<p>
					You also permitted to make an official forum discussion related to <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> in your favourite social media.
				</p>
			</div>
			<h4 class="mb-3">
				<i class="mdi mdi-flask-outline"></i>
				&nbsp;
				Contribute
			</h4>
			<div class="mb-5">
				<p>
					You are allowed to contribute by writing documentation, creating modules and adding suitable libraries to make <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> even better. These contributors are made in the form of <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Issues</b></a> or <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Pull Request</b></a> on the <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> repository on <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>.
				</p>
			</div>
			<h4 class="mb-3">
				<i class="mdi mdi-account-heart-outline"></i>
				&nbsp;
				Support
			</h4>
			<div class="mb-5">
				<p>
					As a <b>single fighter</b> researcher, I occasionally want to enjoy an outside world that I've never explored. Maybe with a little vacation, I can come up with another bright idea to apply to my research.
				</p>
				<p>
					Like most single fighter researchers, if you feel helped by the research I did and want to provide moral or material support, don't hesitate to contact me from the <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara development website</b></a>. I would really appreciate whatever your support was, and of course it will give me more confidence.
				</p>
			</div>
			<h5 class="text-center">
				Once again, thank you.
			</h5>
			<h5 class="text-center mb-3">
				We are awesome!
			</h5>
			<h4 class="text-center">
				<a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
			</h4>
		</div>
	</div>
</div>
