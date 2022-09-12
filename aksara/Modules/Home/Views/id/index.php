<?php
	if(!$permission->uploads || !$permission->writable)
	{
		echo '
			<div class="alert alert-danger rounded-0 border-0 mb-0">
				<div class="container">
					<h4>
						Peringatan!
					</h4>
					' . (!$permission->uploads ? '<p class="mb-0 text-danger"><b>' . FCPATH . UPLOAD_PATH . '/</b> tidak dapat ditulisi.</p>' : null) . '
					' . (!$permission->writable ? '<p class="mb-0 text-danger"><b>' . WRITEPATH . '</b> tidak dapat ditulisi.</p>' : null) . '
					<br />
					<a href="//github.com/abydahana/Aksara/issues/2" target="_blank"><b>Klik di sini</b></a> untuk membaca dokumentasi untuk memperbaiki masalah ini.
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
				Anda menggunakan <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
			</h3>
			<?php
				if($error)
				{
					echo '
						<div class="mb-5">
							<p>
								Sayangnya permintaan Anda untuk menginstal sampel data tidak dapat diproses karena ada masalah pada izin penulisan folder.
							</p>
							<p>
								Anda tetap dapat menginstal sampel data secara manual menggunakan cara berikut:
							</p>
							<ol>
								<li>
									<a href="' . base_url('install/assets/sample-module.zip') . '" target="_blank" class="text-primary"><b>Klik di sini</b></a> untuk mendapatkan paket sampel data;
								</li>
								<li>
									Ekstrak ke dalam folder <code>' . ROOTPATH . 'modules</code>;
								</li>
								<li>
									Muat ulang halaman ini.
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
								Anda mendapatkan halaman ini karena memilih instalasi <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> pada "<b>MODE PENGEMBANG</b>". Untuk itu kami tidak menyertakan contoh konten pada instalasi saat ini. Seperti framework PHP populer lainnya, Anda mungkin perlu membuat modul Anda sendiri dengan menggunakan referensi metode yang ada dalam <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>. Anda tetap dapat masuk dan menambahkan konten pada modul bawaan (<b>CMS</b> a.k.a <b>Content Management System</b>) seperti <b>Blog</b>, <b>Halaman</b>, <b>Galeri</b> dan lebih banyak lagi.
							</p>
							<p>
								Modul halaman ini terdapat pada folder
								<br />
								<code>' . ROOTPATH . 'aksara' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . 'Home</code>.
							</p>
							<p>
								Anda dapat <b>menimpa</b> modul ini pada folder
								<br />
								<code>' . ROOTPATH . 'modules' . DIRECTORY_SEPARATOR . 'Home</code> tanpa menghapus modul yang asli.
							</p>
							<p>
								<b>Bagaimana hal itu bisa dilakukan?</b> Karena Anda menggunakan <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>!
							</p>
						</div>
					';
				}
			?>
			<hr class="mt-5 mb-5" />
			<h3 class="mb-3 text-center">
				Lebih Jauh Lagi
			</h3>
			<h4 class="mb-3">
				<i class="mdi mdi-book-open-page-variant"></i>
				&nbsp;
				Dokumentasi
			</h4>
			<div class="mb-5">
				<p>
					Panduan berisi pendahuluan, tutorial, sejumlah panduan "cara", dan kemudian dokumentasi referensi untuk komponen untuk membangun ekosistem dengan <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a>.
					<br />
					<a href="//www.aksaracms.com/pages/documentation" class="text-primary" target="_blank"><b>Periksa Dokumentasi</b></a>!
				</p>
			</div>
			<h4 class="mb-3">
				<i class="mdi mdi-account-group-outline"></i>
				&nbsp;
				Komunitas
			</h4>
			<div class="mb-5">
				<p>
					Anda bisa membuka diskusi terkait fitur, bug atau saran ke forum komunitas berikut:
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
					Anda juga dipersilakan untuk membuat forum diskusi resmi yang digunakan untuk membahas <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> di media sosial favorit Anda.
				</p>
			</div>
			<h4 class="mb-3">
				<i class="mdi mdi-flask-outline"></i>
				&nbsp;
				Kontribusi
			</h4>
			<div class="mb-5">
				<p>
					Anda dipersilakan untuk berkontribusi dengan menulis dokumentasi, membuat modul dan menambahkan library yang sesuai untuk membuat <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> menjadi lebih baik. Halaman kontributor ini dibuat dalam bentuk <a href="https://github.com/abydahana/aksara/issues" class="text-primary" target="blank"><b>Issues</b></a> atau <a href="https://github.com/abydahana/aksara/pulls" class="text-primary" target="blank"><b>Pull Request</b></a> di repositori <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Aksara</b></a> di <a href="https://github.com/abydahana/aksara" class="text-primary" target="blank"><b>GitHub</b></a>.
				</p>
			</div>
			<h4 class="mb-3">
				<i class="mdi mdi-account-heart-outline"></i>
				&nbsp;
				Dukungan
			</h4>
			<div class="mb-5">
				<p>
					Sebagai seorang <b>peneliti tunggal</b>, saya sesekali ingin menikmati dunia luar yang belum pernah saya jelajahi. Mungkin dengan sedikit liburan, saya bisa mendapatkan ide cemerlang lain untuk diterapkan pada penelitian saya lainnya.
				</p>
				<p>
					Seperti kebanyakan peneliti lainnya, jika merasa terbantu dengan penelitian yang saya lakukan dan ingin memberikan dukungan moril maupun materil, jangan sungkan untuk menghubungi saya dari <a href="//www.aksaracms.com" class="text-primary" target="blank"><b>Website Pengembangan Aksara</b></a>. Saya akan sangat menghargai apapun dukungan Anda, dan tentu saja itu akan membuat saya lebih percaya diri.
				</p>
			</div>
			<h5 class="text-center fw-light">
				Sekali lagi, terima kasih.
			</h5>
			<h5 class="text-center fw-light mb-3">
				Kita semua luar biasa!
			</h5>
			<h4 class="text-center">
				<a href="//abydahana.github.io" target="_blank"><b><i class="mdi mdi-heart text-danger"></i> Aby Dahana</b></a>
			</h4>
		</div>
	</div>
</div>
