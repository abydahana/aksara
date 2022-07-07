<?php

if($total > 0 && isset($results->table_data))
{
	$thead											= null;
	$tbody											= null;
	foreach($results->table_data as $key => $val)
	{
		$rows										= null;
		
		foreach($val as $fields => $params)
		{
			if($params->hidden) continue;
			
			if(0 == $key)
			{
				$thead								.= '<th class="bordered">' . $params->label . '</th>';
			}
			
			$rows									.= '<td class="bordered">' . $params->content . '</td>';
		}
		
		$tbody										.= '
			<tr>
				' . $rows . '
			</tr>
		';
	}
	
	echo '
		<html>
			<head>
				<title>' . $meta->title . '</title>
				<link rel="icon" type="image/x-icon" href="' . get_image('settings', get_setting('app_icon'), 'icon') . '" />
				<style type="text/css">
					.print
					{
						display: none
					}
					@media print
					{
						.no-print
						{
							display: none
						}
						.print
						{
							display: block
						}
					}
					@page
					{
						sheet-size: 13.5in 8.5in;
						footer: html_footer
					}
					*
					{
						font-family: Tahoma
					}
					label,
					h4
					{
						display: block
					}
					a,
					a:hover,
					a:focus,
					a:visited,
					a:link
					{
						text-decoration: none;
						color: #000
					}
					hr
					{
						border-top: 1px solid #999999;
						border-bottom: 0;
						margin-bottom: 15px
					}
					.separator
					{
						border-top: 3px solid #000000;
						border-bottom: 1px solid #000000;
						padding: 1px;
						margin-bottom: 30px
					}
					.text-sm
					{
						font-size: 10px
					}
					.text-uppercase
					{
						text-transform: uppercase
					}
					.text-muted
					{
						color: #888888
					}
					.text-sm-start
					{
						text-align: left!important
					}
					.text-center
					{
						text-align: center
					}
					.text-end
					{
						text-align: right
					}
					table
					{
						width: 100%
					}
					th
					{
						text-align:center;
						font-weight: bold
					}
					td
					{
						padding: 5px;
						vertical-align: top
					}
					.table
					{
						border-collapse: collapse
					}
					.table th.bordered,
					.table td.bordered
					{
						border: 1px solid #000
					}
					.table .table th.bordered:first-child,
					.table .table td.bordered:first-child
					{
						border-left: 0
					}
					.table .table th.bordered:last-child,
					.table .table td.bordered:last-child
					{
						border-right: 0
					}
					.col-sm-6
					{
						width: 50%;
						float: left;
						margin: 12px 0;
						
					}
					input
					{
						border: 1px solid #aaa!important;
						width: 60px!important
					}
					.pagination
					{
						margin: 0;
						padding: 0;
						list-style-type: none;
						display: inline;
						float: right;
						line-height: 1.5
					}
					nav > form
					{
						margin: 0;
						display: inline;
						float: right;
						line-height: 1.5;
						margin-right: 15px
					}
					nav > form > .input-group > input,
					nav > form > .input-group > .input-group-append
					{
						display: inline;
						padding: 3px
					}
					.pagination li
					{
						display: inline-block;
						margin: 0
					}
					.pagination li a,
					.pagination li input
					{
						padding: 2px 10px;
						border: 1px solid #aaa
					}
					.btn-sm
					{
						padding: 2px
					}
					.no-padding
					{
						padding: 0;
						border: 0
					}
					.no-margin
					{
						margin: 0
					}
				</style>
			</head>
			<body>
				<div class="text-center">
					<table>
						<thead>
							<tr>
								<th>
									<img src="' . get_image('settings', get_setting('app_icon'), 'icon') . '" alt="..." />
								</th>
								<th align="center">
									<h3 class="no-margin">
										' . get_setting('app_name') . '
									</h3>
									<h2 class="no-margin">
										' . get_setting('office_name') . '
									</h2>
									<p class="text-sm no-margin">
										' . get_setting('office_address') . '
									</p>
									<p class="text-sm no-margin">
										' . phrase('phone') . ':
										' . get_setting('office_phone') . '
										/
										' . phrase('fax') . ':
										' . get_setting('office_fax') . '
										/
										' . get_setting('office_email') . '
									</p>
								</th>
							</tr>
						</thead>
					</table>
					<div class="separator"></div>
				</div>
				<table class="table">
					<thead>
						<tr>
							' . $thead . '
						</tr>
					</thead>
					<tbody>
						' . $tbody . '
					</tbody>
				</table>
				' . ($method == 'print' ? '
				<div class="no-print">
					' . $template->pagination . '
				</div>
				' : null) . '
				' . ($method == 'pdf' ? '
				<htmlpagefooter name="footer" class="print">
					<table>
						<tfoot>
							<tr>
								<td class="text-muted text-sm">
									<i>
										' . phrase('the_document_was_generated_from') . ' ' . get_setting('app_name') . ' ' . phrase('at') . ' ' . date('Y-m-d H:i:s') . '
									</i>
								</td>
								<td class="text-muted text-sm text-end">
									' . phrase('page') . ' {PAGENO} ' . phrase('of') . ' {nb}
								</td>
							</tr>
						</tfoot>
					</table>
				</htmlpagefooter>
				' : null) . '
				<script type="text/javascript">
					window.print()
				</script>
			</body>
		</html>
	';
}
else if($total > 0 && isset($results->form_data))
{
	$fields											= null;
	foreach($results->form_data as $field => $params)
	{
		$fields										.= '
			<tr>
				<td class="text-muted text-uppercase text-end">
					' . $params->label . '
				</td>
				<td width="70%">
					' . $params->content . '
					<hr />
				</td>
			</tr>
		';
	}
	
	echo '
		<html>
			<head>
				<title>' . $meta->title . '</title>
				<link rel="icon" type="image/x-icon" href="' . get_image('settings', get_setting('app_icon'), 'icon') . '" />
				<style type="text/css">
					.print
					{
						display: none
					}
					@media print
					{
						.no-print
						{
							display: none
						}
						.print
						{
							display: block
						}
					}
					@page
					{
						sheet-size: 8.5in 13.5in;
						footer: html_footer
					}
					*
					{
						font-family: Tahoma
					}
					label,
					h4
					{
						display: block
					}
					a,
					a:hover,
					a:focus,
					a:visited,
					a:link
					{
						text-decoration: none;
						color: #000
					}
					hr
					{
						border-top: 1px solid #999999;
						border-bottom: 0;
						margin-bottom: 15px
					}
					.separator
					{
						border-top: 3px solid #000000;
						border-bottom: 1px solid #000000;
						padding: 1px;
						margin-bottom: 50px
					}
					.text-sm
					{
						font-size: 12px
					}
					.text-uppercase
					{
						text-transform: uppercase
					}
					.text-muted
					{
						color: #888888
					}
					.text-end
					{
						text-align: right
					}
					.text-center
					{
						text-align: center
					}
					table
					{
						width: 100%
					}
					th
					{
						text-align:center;
						font-weight: bold
					}
					td
					{
						padding: 5px;
						vertical-align: top
					}
				</style>
			</head>
			<body>
				<div class="text-center">
					<table>
						<thead>
							<tr>
								<th>
									<img src="' . get_image('settings', get_setting('app_icon'), 'icon') . '" alt="..." />
								</th>
								<th align="center">
									<h2>
										' . get_setting('app_name') . '
									</h2>
									<p class="text-sm">
										' . get_setting('office_address') . '
										/
										' . phrase('phone') . ':
										' . get_setting('office_phone') . '
										/
										' . phrase('fax') . ':
										' . get_setting('office_fax') . '
										/
										' . get_setting('office_email') . '
									</p>
								</th>
							</tr>
						</thead>
					</table>
					<div class="separator"></div>
				</div>
				<table>
					<tbody>
						' . $fields . '
					</tbody>
				</table>
				' . ($method == 'pdf' ? '
				<htmlpagefooter name="footer" class="print">
					<table>
						<tfoot>
							<tr>
								<td class="text-muted text-sm">
									<i>
										' . phrase('the_document_was_generated_from') . ' ' . get_setting('app_name') . ' ' . phrase('at') . ' ' . date('Y-m-d H:i:s') . '
									</i>
								</td>
								<td class="text-muted text-sm text-end">
									' . phrase('page') . ' {PAGENO} ' . phrase('of') . ' {nb}
								</td>
							</tr>
						</tfoot>
					</table>
				</htmlpagefooter>
				' : null) . '
				<script type="text/javascript">
					window.print()
				</script>
			</body>
		</html>
	';
}
else
{
	echo phrase('no_result_found');
}
