<div class="bg-light text-secondary pt-5 pb-5">
	<div class="container">
		<div class="text-center">
			<h3 class="mb-0<?php echo (!$meta->description ? ' mt-3' : null); ?>">
				<?php echo $meta->title; ?>
			</h3>
			<p class="lead">
				<?php echo truncate($meta->description, 256); ?>
			</p>
		</div>
	</div>
</div>
<div class="container pt-5 pb-5">
	<div class="row">
		<div class="col-md-10 offset-md-1">
			<?php
				if($results)
				{
					foreach($results as $key => $val)
					{
						$album_cover				= null;
						$image_thumb				= null;
						$images						= json_decode($val->gallery_images, true);
						
						if(!empty($images))
						{
							$num					= 1;
							foreach($images as $src => $alt)
							{
								if($num >= 4) break;
								
								if(1 == $num)
								{
									$album_cover	= $src;
								}
								else if($num > 1)
								{
									$image_thumb	.= '<a href="' . go_to(array($val->gallery_slug, $src)) . '" class="--xhr"><img src="' . get_image('galleries', $src, 'thumb') . '" class="w-100" /></a>';
								}
								
								$num++;
							}
						}
						
						echo '
							<div class="rounded-4 overflow-hidden shadow-sm mb-5">
								<div class="row g-0">
									<div class="col-' . (count($images) <= 2 ? 'md-' : null) . (count($images) == 2 ? 6 : (count($images) == 1 ? 12 : 9)) . ' text-center d-flex align-items-center" style="background:url(' . get_image('galleries', $album_cover) . ') center center no-repeat; background-size:cover; min-height:320px">
										<div class="p-3 w-100" style="background:rgba(0, 0, 0, .5)">
											<h4 class="text-light">
												<span class="badge bg-primary float-end">
													' . count($images) . '
												</span>
												' . $val->gallery_title . '
											</h4>
											<p class="text-light">
												' . truncate($val->gallery_description, 160) . '
											</p>
											<p class="text-light">
												' . (count($images) > 4 ? '<a href="' . go_to($val->gallery_slug) . '" class="btn btn-outline-light rounded-pill --xhr"><i class="mdi mdi-folder-multiple-image"></i> ' . phrase('show_all') . '</a>' : '<a href="' . go_to(array($val->gallery_slug, $album_cover)) . '" class="btn btn-outline-light rounded-pill --xhr"><i class="mdi mdi-magnify-plus"></i> ' . phrase('show') . '</a>') . '
											</p>
										</div>
									</div>
									' . (count($images) > 1 ? '
									<div class="col-' . (count($images) <= 2 ? 'md-' : null) . (count($images) > 2 ? 3 : 6) . ' bg-dark d-flex align-items-center">
										<div class="w-100">
											' . $image_thumb . '
										</div>
									</div>
									' : '') . '
								</div>
							</div>
						';
					}
					
					if($total > $limit)
					{
						echo '
							<div class="pt-3 pb-3">
								' . $template->pagination . '
							</div>
						';
					}
				}
				else
				{
					echo '
						<div class="text-muted">
							<i class="mdi mdi-information-outline"></i>
							' . phrase('no_album_is_available') . '
						</div>
					';
				}
			?>
		</div>
	</div>
</div>
