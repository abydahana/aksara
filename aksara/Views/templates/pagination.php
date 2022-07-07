<?php
	/**
	 * Pagination template
	 * This template is overriding the core template that too overkill
	 * and un-configurable
	 *
	 * @author			Aby Dahana
	 * @profile			abydahana.github.io
	 * @since			version 4.0.0
	 */
	$pager->setSurroundCount(0);
	$current_page									= (is_numeric(service('request')->getGet('per_page')) && service('request')->getGet('per_page') ? service('request')->getGet('per_page') : 1);
	$last_page										= parse_str(parse_url($pager->getLast(), PHP_URL_QUERY), $output);
	$last_page										= (isset($output['page']) ? $output['page'] : 0);
?>
<ul class="pagination pagination-sm mb-0">
	<?php
		echo '
			<li class="page-item' . ($current_page <= 1 ? ' disabled' : null) . '">
				<a href="' . ($current_page > 1 ? current_page(null, array('per_page' => 0)) : 'javascript:void(0)') . '" class="page-link --xhr" aria-label="' . phrase('first') . '">
					' . phrase('first') . '
				</a>
			</li>
			<li class="page-item' . ($current_page <= 1 ? ' disabled' : null) . '">
				<a href="' . ($current_page > 1 ? current_page(null, array('per_page' => ($current_page - 1))) : 'javascript:void(0)') . '" class="page-link --xhr" aria-label="' . phrase('previous') . '">
					&lt;
				</a>
			</li>
			<li class="page-item active">
				<a href="javascript:void(0)" class="page-link">
					' . $current_page . '
				</a>
			</li>
			<li class="page-item' . ($last_page <= $current_page ? ' disabled' : null) . '">
				<a href="' . ($last_page > $current_page ? current_page(null, array('per_page' => ($current_page + 1))) : 'javascript:void(0)') . '" class="page-link --xhr" aria-label="' . phrase('next') . '">
					&gt;
				</a>
			</li>
			<li class="page-item' . ($last_page <= $current_page ? ' disabled' : null) . '">
				<a href="' . ($last_page > $current_page ? current_page(null, array('per_page' => $last_page)) : 'javascript:void(0)') . '" class="page-link --xhr" aria-label="' . phrase('last') . '">
					' . phrase('last') . '
				</a>
			</li>
		';
	?>
</ul>
