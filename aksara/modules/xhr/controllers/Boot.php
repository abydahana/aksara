<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * XHR > Boot
 * Some basic generator
 *
 * @version			2.1.1
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 */
class Boot extends Aksara
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		foreach($this->_phrases() as $key => $val)
		{
			phrase($val);
		}
		
		return throw_exception(301, phrase('you_have_successfully_run_the_service'), base_url(), true);
	}
	
	private function _phrases()
	{
		return array_map('trim', explode(' ', 'from options read update more_options print pdf delete no_matching_record_were_found create export print pdf delete_checked keyword_to_search all_columns go back submit add_data close error loading report_to_admin update showing_data close delete_data data_will_be_deleted are_you_sure_want_to_delete_this_data cancel no_view_could_be_loaded make_sure_to_check_the_following_mistake module_structure incorect_view_path database_table_existence something_caused_by_typo title_was_not_set authentication use_your_account_information_to_start_session enter_your_username_or_email enter_password choose_year remember_session sign_in do_not_have_an_account sign_with_google sign_with_facebook register_an_account language dashboard notifications guidelines account sign_out type_column_name insert_after move_up move_down remove tick_to_show the_layer_property_could_not_be_empty this_browser_might_experience_problems_related_to_file_reading_permissions_on_your_device your_content_goes_here choose file files to_upload files_were file_was chosen download drag_file_to_upload pasting_file are_you_sure_want_to_remove_the_selected_file maximum_file_limit allowed_file_type is_too_large max_file_size file_too_large max_overall_file_size file_name has_been_chosen folder_upload_is_not_allowed nothing_found search consider_to_check_your_internet_connection we_are_working_on_it_to_solve_the_problem collapse expand move_up move_down question detailed_answer_for_above_question add_step slider_background slider_thumbnail slider_title slider_description target_url button_label default_marker this_can_be_drag_on_edit_mode detail up_to position at search_place measurement_result latitude longitude no_layer_available use_control_and_drag_combination_to_count_the_layer_data click_on_the_layer_to_get_information use_control_and_drag_combination_to_extract_information area distance location acres miles kilometers hectares yards feet meters choose_spatial_data_file browse clear_overlay page_size choose_page_size slow fast resolution choose_page_size choose_resolution download layer_overlap legend no_additional_information tick_the_layer_name_to_show_on_map below_is_a_list_of_layer_legend choose_the_type_of_tool_and_draw_on_map_to_measure choose_the_tile_type_below_to_replace_map_style upload_your_own_shape_file_to_examine_inside_map export_current_map_preview_include_it_is_features_as_image_or_document remove_feature track_me use_two_fingers_to_move_the_map field_that_can_be_access_and_modify the_selected_url_or_module_is_not_support_crud please_choose_the_url_or_module click_to_get_detail problem_detected because_you_did_not_check_any_filter_and_entered_keyword_is_too_short_the_result_are_limited_to_keep_performace_stable please_wait_while_the_response_being_rendered_to_the_map'));
	}
}
