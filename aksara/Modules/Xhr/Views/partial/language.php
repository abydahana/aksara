<div class="container-fluid pt-3 pb-3">
	<?php
		if($languages)
		{
			foreach($languages as $key => $val)
			{
				if($key)
				{
					echo '<hr />';
				}
				
				if($val->code == get_userdata('language'))
				{
					echo '
						<b class="d-block">
							' . $val->language . '
						</b>
					';
				}
				else
				{
					echo '
						<a href="' . base_url('xhr/language/' . $val->code) . '" class="d-block --xhr">
							' . $val->language . '
						</a>
					';
				}
			}
		}
	?>
</div>