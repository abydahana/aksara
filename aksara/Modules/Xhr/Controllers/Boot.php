<?php

namespace Aksara\Modules\Xhr\Controllers;

/**
 * XHR > Boot
 * Some basic generator
 *
 * @author			Aby Dahana <abydahana@gmail.com>
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.0.0
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Boot extends \Aksara\Laboratory\Core
{
	public function index()
	{
		foreach($this->_phrases() as $key => $val)
		{
			phrase($val);
		}
		
		return throw_exception(301, phrase('the_aksara_service_has_been_successfully_started'), base_url(), true);
	}
	
	private function _phrases()
	{
		return array_map('trim', explode(' ', 'profile no_response_could_be_loaded make_sure_to_check_the_following_mistake module_structure incorrect_view_path database_table_existence something_caused_by_typo error your_browser_might_experiences_problem_related_to_file_reading_permissions_on_your_device your_content_goes_here choose file files to_upload files_were_chosen file_was_chosen close download remove drag_file_to_upload pasting_file are_you_sure_want_to_remove_the_selected_file maximum_file_limit_is allowed_file_type is_too_large maximum_file_size_is file_is_too_large maximum_overall_file_size_is file_named has_been_chosen folder_upload_is_not_allowed nothing_found search loading consider_to_check_your_internet_connection print we_are_working_on_it_to_solve_the_problem_immediatelly collapse expand move_up move_down question detailed_answer_for_above_question add_step background thumbnail title description target_url button_label options read update more_options pdf delete no_matching_record_were_found create export delete_checked keyword_to_search all_columns go back submit report_to_admin showing_data delete_data data_will_be_deleted are_you_sure_want_to_delete_this_data cancel untitled authentication please_enter_your_account_information_to_sign_in enter_your_username_or_email enter_password forgot_password sign_in do_not_have_an_account sign_in_with_google sign_in_with_facebook register_an_account language dashboard notifications account sign_out sunday monday tuesday wednesday thursday friday saturday january february march april may june july august september october november'));
	}
}
