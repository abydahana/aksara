<div class="container-fluid">
	<div class="row">
		<div class="col-md-3">
			<div class="sticky-top">
				<div class="pretty-scrollbar">
					<a href="<?php echo base_url('apis/documentation'); ?>" class="--xhr<?php echo (!$active ? ' text-primary fw-bold' : null); ?>">
						<?php echo phrase('get_started'); ?>
					</a>
					<br />
					
					<?php
						if($modules)
						{
							foreach($modules as $key => $val)
							{
								echo '
									<a href="' . current_page(null, array('slug' => $val, 'group' => null)) . '" class="--xhr' . ($val == $active ? ' text-primary fw-bold' : null) . '">
										' . str_replace('/', ' &gt; ', $val) . '
									</a>
									<br />
								';
							}
						}
					?>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<?php
				$selected							= service('request')->getGet('group');
				
				if($active)
				{
					$group_collector				= array();
					$access_token					= false;
					
					if($permission->groups)
					{
						$groups						= null;
						$privileges					= array();
						
						foreach($permission->groups as $key => $val)
						{
							$group_collector[]		= $val->group_id;
							$method					= null;
							$extract_privileges		= json_decode($val->group_privileges);
							
							if(isset($extract_privileges->$active))
							{
								foreach($extract_privileges->$active as $_key => $_val)
								{
									$method				.= '<a href="#--method-' . $_val . '"><span class="badge bg-success"><i class="mdi mdi-link"></i> ' . phrase($_val) . '</span></a>&nbsp;';
								}
							}
							
							if($val->group_id)
							{
								$access_token		= true;
							}
							
							$groups					.= '<option value="' . $val->group_id . '"' . ($val->group_id == $selected ? ' selected' : null) . '>' . $val->group_name . '</option>';
							
							$privileges[$selected]	= $method;
						}
						
						echo '
							<div class="row">
								<div class="col-md-6">
									<h4 class="mb-3 --title">
										' . $active . '
									</h4>
								</div>
							</div>
							<div class="table-responsive">
								<table class="table table-bordered table-sm">
									<thead>
										<tr>
											<th>
												' . phrase('group_name') . '
											</th>
											<th>
												' . phrase('privileges') . '
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td width="250">
												<form action="' . current_page(null) . '" method="POST" class="--xhr-form">
													<select name="group" class="form-control form-control-sm">
														' . $groups . '
													</select>
												</form>
											</td>
											<td>
												' . (isset($privileges[$selected]) ? $privileges[$selected] : null) . '
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						';
					}
					
					echo '
						<h5 class="mt-3">
							' . phrase('request_method') . '
						</h5>
					';
					
					if($permission->privileges)
					{
						$method						= array();
						
						foreach($permission->privileges as $key => $val)
						{
							$method[]				= $val;
							
							echo '
								<div class="mb-3" id="--method-' . $val . '">
									<h5 class="mb-1">
										<span class="badge bg-primary bg-md">
											' . (in_array($val, array('create', 'update')) ? 'POST' : (in_array($val, array('delete')) ? 'DELETE' : 'GET')) . '
										</span>
									</h5>
									<div class="rounded pt-2 pe-3 pb-2 ps-3 bg-dark">
										<code class="text-light">' . base_url(('index' !== $val ? $active . '/' . $val : $active)) . '</code>
									</div>
								</div>
								<h5 class="mt-3">
									' . phrase('header') . '
								</h5>
								<div class="table-responsive">
									<table class="table table-bordered table-sm">
										<thead>
											<tr>
												<th>
													' . phrase('field') . '
												</th>
												<th>
													' . phrase('type') . '
												</th>
												<th>
													' . phrase('description') . '
												</th>
												<th width="100" class="text-center">
													' . phrase('required') . '
												</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													<span style="font-family:Consolas">
														X-API-KEY
													</span>
												</td>
												<td>
													String
												</td>
												<td>
													' . phrase('valid_api_key_added_in_api_service') . '
												</td>
												<td class="text-center">
													<span class="badge bg-danger">
														' . phrase('required') . '
													</span>
												</td>
											</tr>
											' . ($access_token ? '
											<tr>
												<td>
													<span style="font-family:Consolas">
														X-ACCESS-TOKEN
													</span>
												</td>
												<td>
													String
												</td>
												<td>
													' . phrase('the_token_that_given_from_authentication_response') . '
												</td>
												<td class="text-center">
													<span class="badge bg-danger">
														' . phrase('required') . '
													</span>
												</td>
											</tr>
											' : null) . '
										</tbody>
									</table>
								</div>
								<div class="text-center --spinner">
									<div class="spinner-border" role="status"></div>
								</div>
								<div class="--query-' . $val . ' d-none">
									<h5 class="mt-3">
										' . phrase('query_string') . '
									</h5>
									<div class="table-responsive">
										<table class="table table-bordered table-sm">
											<thead>
												<tr>
													<th>
														' . phrase('field') . '
													</th>
													<th>
														' . phrase('type') . '
													</th>
													<th>
														' . phrase('description') . '
													</th>
													<th width="100" class="text-center">
														' . phrase('required') . '
													</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
								<div class="--parameter-' . $val . ' d-none">
									<h5 class="mt-3">
										' . phrase('parameter') . '
									</h5>
									<div class="table-responsive">
										<table class="table table-bordered table-sm">
											<thead>
												<tr>
													<th>
														' . phrase('field') . '
													</th>
													<th>
														' . phrase('type') . '
													</th>
													<th>
														' . phrase('description') . '
													</th>
													<th width="100" class="text-center">
														' . phrase('required') . '
													</th>
												</tr>
											</thead>
											<tbody>
											</tbody>
										</table>
									</div>
								</div>
								<div class="--response-success-' . $val . ' d-none">
									<h5 class="mt-3">
										' . phrase('success_response') . '
									</h5>
									<pre class="language-javascript rounded mt-0"><code>{}</code></pre>
								</div>
								<div class="--response-error-' . $val . ' d-none">
									<h5 class="mt-3">
										' . phrase('error_response') . '
									</h5>
									<pre class="language-javascript rounded mt-0"><code>{}</code></pre>
								</div>
								<br />
								<br />
							';
						}
					}
				}
				else
				{
					echo '
						<h4 class="mb-3">
							Pengenalan Fitur
						</h4>
						<p>
							<a href="//www.aksaracms.com" target="_blank"><b class="text-primary">Aksara</b></a> telah dilengkapi dengan kemampuan untuk membuat output <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> sekaligus tanpa Anda perlu menambahkan backend atau modul khusus untuk pengelolaan <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a>. Konsep pada implementasi <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> selaras dengan fitur-fitur pada backoffice <a href="//www.aksaracms.com" target="_blank"><b class="text-primary">Aksara</b></a> yang sedang Anda buka saat ini.
						</p>
						<p>
							Anda tidak perlu lagi memikirkan hal-hal rumit yang membebani pekerjaan Anda. Seluruh permintaan <a href="//en.wikipedia.org/wiki/API" target="_blank"><b>API</b></a> akan melalui proses otorisasi dan pengecekan hak akses, termasuk pada validasi yang telah Anda tentukan pada tiap modul yang telah atau akan Anda bangun.
						</p>
						<p>
							Semudah itukah? Ya, karena ini <a href="//www.aksaracms.com" target="_blank"><b class="text-primary">Aksara</b></a>!
						</p>
						<hr />
						<h4 class="mb-3">
							Mulai Dari Mana?
						</h4>
						<p>
							Untuk dapat menggunakan fitur permintaan API, Anda perlu menambahkan kunci API terlebih dahulu dari menu <a href="' . go_to('../services') . '" class="--xhr"><b>Pengelola Layanan API</b></a>. Tentukan metode permintaan yang dapat digunakan, kisaran IP yang diizinkan dan juga tanggal kadaluwarsa untuk kunci API yang dibuat.
						</p>
						<p>
							Sematkan kunci API yang telah dibuat yang dikhususkan untuk klien tertentu pada property HEADER saat melakukan permintaan.
						</p>
						<div class="table-responsive">
							<table class="table table-bordered table-sm">
								<thead>
									<tr>
										<th>
											' . phrase('field') . '
										</th>
										<th>
											' . phrase('type') . '
										</th>
										<th>
											' . phrase('description') . '
										</th>
										<th width="100" class="text-center">
											' . phrase('required') . '
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<span style="font-family:Consolas">
												X-API-KEY
											</span>
										</td>
										<td>
											String
										</td>
										<td>
											' . phrase('valid_api_key_added_in_api_service') . '
										</td>
										<td class="text-center">
											<span class="badge bg-danger">
												' . phrase('required') . '
											</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<p>
							Untuk modul yang memerlukan hak akses sebagaimana telah dikhususkan untuk grup pengguna tertentu, tambahkan parameter <code>X-ACCESS-TOKEN</code> yang didapatkan dari respon permintaan otentikasi pada permintaan HEADER.
						</p>
						<div class="table-responsive">
							<table class="table table-bordered table-sm">
								<thead>
									<tr>
										<th>
											' . phrase('field') . '
										</th>
										<th>
											' . phrase('type') . '
										</th>
										<th>
											' . phrase('description') . '
										</th>
										<th width="100" class="text-center">
											' . phrase('required') . '
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<span style="font-family:Consolas">
												X-ACCESS-TOKEN
											</span>
										</td>
										<td>
											String
										</td>
										<td>
											' . phrase('the_token_that_given_from_authentication_response') . '
										</td>
										<td class="text-center">
											<span class="badge bg-danger">
												' . phrase('required') . '
											</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<p>
							Proses otentikasi dilakukan pada dengan melakukan POST ke <code>' . base_url('auth') . '</code> serta menyematkan <code>X-API-KEY</code> pada HEADER, berikut data formulir (form-data) dengan parameter <code>username</code> dan <code>password</code> sesuai kredensial pengguna.
						</p>
						<hr />
						<h4>
							Mengambil Hasil Query
						</h4>
						<p>
							Saat Anda meminta data, ada helper query string (yang disebutkan di bawah "<code>query_string</code>") yang akan membantu Anda mengambil data secara lebih spesifik. Selain itu juga tersedia helper query string untuk mengambil hasil yang menggunakan kriteria tertentu.
						</p>
						<div class="table-responsive">
							<table class="table table-bordered table-sm">
								<thead>
									<tr>
										<th>
											' . phrase('key') . '
										</th>
										<th>
											' . phrase('type') . '
										</th>
										<th>
											' . phrase('description') . '
										</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<span style="font-family:Consolas">
												limit
											</span>
										</td>
										<td>
											Angka
										</td>
										<td>
											Menerapkan limit atau batas pengambilan data
										</td>
									</tr>
									<tr>
										<td>
											<span style="font-family:Consolas">
												offset
											</span>
										</td>
										<td>
											Angka
										</td>
										<td>
											Menerapkan offset pada saat mengambil data
										</td>
									</tr>
									<tr>
										<td>
											<span style="font-family:Consolas">
												order
											</span>
										</td>
										<td>
											String
										</td>
										<td>
											Bidang atau kolom yang akan diurutkan
										</td>
									</tr>
									<tr>
										<td>
											<span style="font-family:Consolas">
												sort
											</span>
										</td>
										<td>
											String <code>ASC|DESC</code>
										</td>
										<td>
											Jenis pengurutan (sortir)
										</td>
									</tr>
									<tr>
										<td>
											<span style="font-family:Consolas">
												q
											</span>
										</td>
										<td>
											String
										</td>
										<td>
											Kata kunci pencarian
										</td>
									</tr>
									<tr>
										<td>
											<span style="font-family:Consolas">
												column
											</span>
										</td>
										<td>
											String
										</td>
										<td>
											Bidang spesifik yang akan dilakukan pencarian
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					';
				}
			?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		if(UA !== 'mobile')
		{
			$('.pretty-scrollbar').mCustomScrollbar
			({
				autoHideScrollbar: true,
				axis: 'y',
				scrollInertia: 170,
				mouseWheelPixels: 170,
				setHeight: $(window).outerHeight(true) - (($('.navbar').length ? $('.navbar').outerHeight(true) : 0) + ($('.alias-table-header').length ? $('.alias-table-header').outerHeight(true) : 0)),
				advanced:
				{
					updateOnContentResize: true
				},
				autoHideScrollbar: false
			})
		}
		
		$.ajax
		({
			url: '<?php echo current_page(); ?>',
			context: this,
			method: 'POST',
			data:
			{
				mode: 'fetch',
				group: '<?php echo ($selected ? $selected : (isset($group_collector[0]) ? $group_collector[0] : 0)); ?>',
				method: JSON.parse('<?php echo json_encode($method); ?>')
			},
			beforeSend: function()
			{
			}
		})
		.done(function(response, status, error)
		{
			$('.--spinner').remove();
			
			if(response.results)
			{
				$.each(response.results, function(key, val)
				{
					if(typeof val.query_string !== 'undefined')
					{
						$.each(val.query_string, function(_key, _val)
						{
							if($('.--query-' + key).hasClass('d-none'))
							{
								$('.--query-' + key).removeClass('d-none')
							}
							
							$('<tr><td><span style="font-family:Consolas">' + _key + '</span></td><td>int</td><td>-</td><td class="text-center"><span class="badge bg-danger"><?php echo phrase('required'); ?></span></td></tr>').appendTo('.--query-' + key + ' tbody')
						})
					}
					
					if(typeof val.parameter !== 'undefined')
					{
						$.each(val.parameter, function(_key, _val)
						{
							if($('.--parameter-' + key).hasClass('d-none'))
							{
								$('.--parameter-' + key).removeClass('d-none')
							}
							
							$('<tr><td><span style="font-family:Consolas">' + _key + '</span></td><td>' + JSON.stringify(_val.type) + '</td><td>' + _val.label + '</td><td class="text-center">' + (_val.required ? '<span class="badge bg-danger"><?php echo phrase('required'); ?></span>' : '') + '</td></tr>').appendTo('.--parameter-' + key + ' tbody')
						})
					}
					
					if(typeof val.response.success !== 'undefined')
					{
						if($('.--response-success-' + key).hasClass('d-none'))
						{
							$('.--response-success-' + key).removeClass('d-none')
						}
						
						$('.--response-success-' + key + ' pre code').text(JSON.stringify(val.response.success, null, 4))
					}
					
					if(typeof val.response.error !== 'undefined')
					{
						if($('.--response-error-' + key).hasClass('d-none'))
						{
							$('.--response-error-' + key).removeClass('d-none')
						}
						
						$('.--response-error-' + key + ' pre code').text(JSON.stringify(val.response.error, null, 4))
					}
				})
			}
		})
	})
</script>
