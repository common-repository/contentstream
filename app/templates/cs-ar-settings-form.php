<div class="wrap cs_ar_float_left">
    <h1 class="wp-heading-inline cs_ar_form_header">ContentStream Settings</h1>
    <!-- display message to user if there is one -->
    <?php echo $action_message['message'] !== '' ? $this->display_status_message($action_message['message'], $action_message['is_error']) : ''; ?>
    <!-- setting form -->
    <form class="cs_ar_settings_form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
        <div class="cs_ar_sub_form cs_ar_float_left">
            <h3 class="wp-heading-inline cs_ar_form_header">Plugin Settings</h3>

            <!-- use_custom_post_type, defaults to yes, there is no validation for this input -->
            <div class="cs_ar_input_div">
                <?php $should_use_custom_post_type = (int) $settings_data['cs_ar_config_use_custom_post_type'];?>
                <label class="cs_ar_form_label cs_ar_retrieval_details_label" for="cs_ar_config_use_custom_post_type">Should the plugin use the custom post type 'ContentStream Article' when downloading your articles?</label>
                <div class="cs_ar_float_left cs_ar_yes_no_container">
                    <label class="cs_ar_float_left cs_ar_yes_no_label" for="cs_ar_config_use_custom_post_type_yes">Yes</label>
                    <input class="cs_ar_float_left" type="radio" id="cs_ar_config_use_custom_post_type_yes" name="cs_ar_config_use_custom_post_type"
                        value="1" <?php if ($should_use_custom_post_type === 1 || $should_use_custom_post_type === '') {
    echo esc_attr('checked');
}
?>>
                    <label class="cs_ar_float_left cs_ar_yes_no_label" for="cs_ar_config_use_custom_post_type_no">No</label>
                    <input class="cs_ar_float_left" type="radio" id="cs_ar_config_use_custom_post_typel_no" name="cs_ar_config_use_custom_post_type"
                        value="0" <?php if ($should_use_custom_post_type === 0) {
    echo esc_attr('checked');
}
?>>
                </div>
            </div>

            <!-- delete content upon uninstall, defaults to no, there is no validation for this input -->
            <div class="cs_ar_input_div">
                <?php $should_delete_content = (int) $settings_data['cs_ar_config_delete_content_on_uninstall'];?>
                <label class="cs_ar_form_label cs_ar_retrieval_details_label" for="cs_ar_config_delete_content_on_uninstall">Should the plugin delete the content it has downloaded upon uninstalling?</label>
                <div class="cs_ar_float_left cs_ar_yes_no_container">
                    <label class="cs_ar_float_left cs_ar_yes_no_label" for="cs_ar_config_delete_content_on_uninstall_yes">Yes</label>
                    <input class="cs_ar_float_left" type="radio" id="cs_ar_config_delete_content_on_uninstall_yes" name="cs_ar_config_delete_content_on_uninstall"
                        value="1" <?php if ($should_delete_content === 1) {
    echo esc_attr('checked');
}
?>>
                    <label class="cs_ar_float_left cs_ar_yes_no_label" for="cs_ar_config_delete_content_on_uninstall_no">No</label>
                    <input class="cs_ar_float_left" type="radio" id="cs_ar_config_delete_content_on_uninstall_no" name="cs_ar_config_delete_content_on_uninstall"
                        value="0" <?php if ($should_delete_content === 0 || $should_delete_content === '') {
    echo esc_attr('checked');
}
?>>
                </div>
            </div>
        </div>

        <div class="cs_ar_sub_form cs_ar_float_left">
            <h3 class="wp-heading-inline cs_ar_form_header">Retrieval Settings</h3>

            <!-- username input, should not be blank and should be a valid email -->
            <div class="cs_ar_input_div">
                <label class="cs_ar_form_label cs_ar_retrieval_details_label" for="cs_ar_config_username">Username
                    <strong class="cs_ar_red_text">*</strong>
                </label>
                <input class="cs_ar_float_left" type="text" id="cs_ar_config_username" name="cs_ar_config_username" autocomplete="off" value="<?php echo (isset($_POST["
                    cs_ar_config_username "]) && $_POST["cs_ar_config_username "] !== '') ? esc_attr($_POST["cs_ar_config_username
                    "]) : esc_attr($settings_data['cs_ar_config_username']); ?>">
                <p class="cs_ar_red_text cs_ar_error">
                    <?php echo isset($errors['cs_ar_config_username_error']) ? esc_attr($errors['cs_ar_config_username_error'][0]) : esc_attr(''); ?>
                </p>
            </div>
            <!-- password input, should not be blank -->
            <div class="cs_ar_input_div">
                <label class="cs_ar_form_label cs_ar_retrieval_details_label" for="cs_ar_config_password">Password
                    <strong class="cs_ar_red_text">*</strong>
                </label>
                <input class="cs_ar_float_left" type="password" id="cs_ar_config_password" name="cs_ar_config_password" autocomplete="off"
                    value="<?php echo (isset($_POST[" cs_ar_config_password "]) && $_POST["cs_ar_config_password
                    "] !== '') ? esc_attr($_POST["cs_ar_config_password "]) : esc_attr($settings_data['cs_ar_config_password']); ?>">
                <p class="cs_ar_red_text cs_ar_error">
                    <?php echo isset($errors['cs_ar_config_password_error']) ? esc_attr($errors['cs_ar_config_password_error'][0]) : esc_attr(''); ?>
                </p>
            </div>
            <!-- subscriber id input, should not be blank and should be a number -->
            <div class="cs_ar_input_div">
                <label class="cs_ar_form_label cs_ar_retrieval_details_label" for="cs_ar_config_subscriber_id">Subscriber ID
                    <strong class="cs_ar_red_text">*</strong>
                </label>
                <input class="cs_ar_float_left" type="text" id="cs_ar_config_subscriber_id" name="cs_ar_config_subscriber_id" value="<?php echo isset($_POST["
                    cs_ar_config_subscriber_id "]) ? esc_attr($_POST["cs_ar_config_subscriber_id
                    "]) : esc_attr($settings_data['cs_ar_config_subscriber_id']); ?>">
                <p class="cs_ar_red_text cs_ar_error">
                    <?php echo isset($errors['cs_ar_config_subscriber_id_error']) ? esc_attr($errors['cs_ar_config_subscriber_id_error'][0]) : esc_attr(''); ?>
                </p>
            </div>
            <!-- retrieval frequency input, a selection should be made -->
            <div class="cs_ar_input_div">
                <?php $freq = isset($_POST["cs_ar_config_retrieval_frequency"]) ? sanitize_text_field($_POST["cs_ar_config_retrieval_frequency"]) : $settings_data['cs_ar_config_retrieval_frequency'];?>
                <label class="cs_ar_form_label cs_ar_retrieval_details_label" for="cs_ar_config_retrieval_frequency">How often should the automated retrieval run?
                    <strong class="cs_ar_red_text">*</strong>
                </label>
                <select class="cs_ar_retrieval_details_select cs_ar_float_left" id="cs_ar_config_retrieval_frequency" name="cs_ar_config_retrieval_frequency">
                    <option <?php if ($freq === '-' || $freq === '') {
    echo esc_attr('selected');
}
?> value="-">Please select a timeframe</option>
                    <option <?php if ($freq === 'daily') {
    echo esc_attr('selected');
}
?> value="daily">Once a day</option>
                    <option <?php if ($freq === 'weekly') {
    echo esc_attr('selected');
}
?> value="weekly">Once a week</option>
                    <option <?php if ($freq === 'monthly') {
    echo esc_attr('selected');
}
?> value="monthly">Once a month</option>
                    <option <?php if ($freq === 'never') {
    echo esc_attr('selected');
}
?> value="never">Never</option>
                </select>
                <p class="cs_ar_red_text cs_ar_error">
                    <?php echo isset($errors['cs_ar_config_retrieval_frequency_error']) ? esc_attr($errors['cs_ar_config_retrieval_frequency_error'][0]) : esc_attr(''); ?>
                </p>
            </div>
            <!-- feed amount input, a selection should be made -->
            <div class="cs_ar_input_div">
                <?php $amount = isset($_POST["cs_ar_config_feed_amount"]) ? sanitize_text_field($_POST["cs_ar_config_feed_amount"]) : $settings_data['cs_ar_config_feed_amount'];?>
                <label class="cs_ar_form_label cs_ar_retrieval_details_label" for="cs_ar_config_feed_amount">How many searches should the automated retrieval use?
                    <strong class="cs_ar_red_text">*</strong>
                </label>
                <select class="cs_ar_retrieval_details_select cs_ar_float_left" id="cs_ar_config_feed_amount" name="cs_ar_config_feed_amount">
                    <option <?php if ($amount === '-' || $amount === '') {
    echo esc_attr('selected');
}
?> value="-">Please select an amount</option>
                    <option <?php if ($amount === 'ALL') {
    echo esc_attr('selected');
}
?> value="ALL">All of my searches</option>
                    <option <?php if ($amount === 'SINGLE') {
    echo esc_attr('selected');
}
?> value="SINGLE">A single search</option>
                </select>
                <p class="cs_ar_red_text cs_ar_error">
                    <?php echo isset($errors['cs_ar_config_feed_amount_error']) ? esc_attr($errors['cs_ar_config_feed_amount_error'][0]) : esc_attr(''); ?>
                </p>
            </div>
            <!-- search id input, if feed amount equals SINGLE, this input should not be blank and should be a number -->
            <div class="cs_ar_input_div">
                <label class="cs_ar_form_label cs_ar_retrieval_details_label" for="cs_ar_feed_info_search_id">Search Number (Required if you selected 'A single search')</label>
                <input class="cs_ar_float_left" type="text" id="cs_ar_feed_info_search_id" name="cs_ar_feed_info_search_id" value="<?php echo isset($_POST["cs_ar_feed_info_search_id"]) ? esc_attr($_POST["cs_ar_feed_info_search_id"]) : esc_attr($settings_data['cs_ar_feed_info_search_id']); ?>">
                <p class="cs_ar_red_text cs_ar_error">
                    <?php echo isset($errors['cs_ar_feed_info_search_id_error']) ? esc_attr($errors['cs_ar_feed_info_search_id_error'][0]) : esc_attr(''); ?>
                </p>
            </div>
        </div>

        <div id="cs_ar_appearance_settings" class="cs_ar_sub_form cs_ar_float_left <?php if ((int) $settings_data['cs_ar_config_use_custom_post_type'] === 0) {
    echo esc_attr('cs_ar_no_show');
}
?>">
            <h3 class="wp-heading-inline cs_ar_form_header">Appearance Settings</h3>

            <!-- custom template inputs, defaults to no, there is no validation for this input -->
            <div class="cs_ar_input_div">
                <?php $use_custom_template = (int) $settings_data['cs_ar_config_use_custom_template'];?>
                <label class="cs_ar_form_label cs_ar_retrieval_details_label" for="cs_ar_config_use_custom_template">Should ContentStream articles use the plugin's custom template when displaying to users?</label>
                <div class="cs_ar_float_left cs_ar_yes_no_container">
                    <label class="cs_ar_float_left cs_ar_yes_no_label" for="cs_ar_config_use_custom_template_yes">Yes</label>
                    <input class="cs_ar_float_left" type="radio" id="cs_ar_config_use_custom_template_yes" name="cs_ar_config_use_custom_template"
                        value="1" <?php if ($use_custom_template === 1) {
    echo esc_attr('checked');
}
?>>
                    <label class="cs_ar_float_left cs_ar_yes_no_label" for="cs_ar_config_use_custom_template_no">No</label>
                    <input class="cs_ar_float_left" type="radio" id="cs_ar_config_use_custom_template_no" name="cs_ar_config_use_custom_template"
                        value="0" <?php if ($use_custom_template === 0 || $use_custom_template === '') {
    echo esc_attr('checked');
}
?>>
                </div>
            </div>

            <!-- use_on_home_page, defaults to no, there is no validation for this input -->
            <div class="cs_ar_input_div">
                <?php $should_use_on_home_page = (int) $settings_data['cs_ar_config_use_on_home_page'];?>
                <label class="cs_ar_form_label cs_ar_retrieval_details_label" for="cs_ar_config_use_on_home_page">Should ContentStream articles appear on your site's home page?</label>
                <div class="cs_ar_float_left cs_ar_yes_no_container">
                    <label class="cs_ar_float_left cs_ar_yes_no_label" for="cs_ar_config_use_on_home_page_yes">Yes</label>
                    <input class="cs_ar_float_left" type="radio" id="cs_ar_config_use_on_home_page_yes" name="cs_ar_config_use_on_home_page"
                        value="1" <?php if ($should_use_on_home_page === 1) {
    echo esc_attr('checked');
}
?>>
                    <label class="cs_ar_float_left cs_ar_yes_no_label" for="cs_ar_config_use_on_home_page_no">No</label>
                    <input class="cs_ar_float_left" type="radio" id="cs_ar_config_use_on_home_page_no" name="cs_ar_config_use_on_home_page"
                        value="0" <?php if ($should_use_on_home_page === 0 || $should_use_on_home_page === '') {
    echo esc_attr('checked');
}
?>>
                </div>
            </div>

            <!-- use_in_search, defaults to no, there is no validation for this input -->
            <div class="cs_ar_input_div">
                <?php $should_use_in_search = (int) $settings_data['cs_ar_config_use_in_search'];?>
                <label class="cs_ar_form_label cs_ar_retrieval_details_label" for="cs_ar_config_use_in_search">Should ContentStream articles appear in your site's search results?</label>
                <div class="cs_ar_float_left cs_ar_yes_no_container">
                    <label class="cs_ar_float_left cs_ar_yes_no_label" for="cs_ar_config_use_in_search_yes">Yes</label>
                    <input class="cs_ar_float_left" type="radio" id="cs_ar_config_use_in_search_yes" name="cs_ar_config_use_in_search"
                        value="1" <?php if ($should_use_in_search === 1) {
    echo esc_attr('checked');
}
?>>
                    <label class="cs_ar_float_left cs_ar_yes_no_label" for="cs_ar_config_use_in_search_no">No</label>
                    <input class="cs_ar_float_left" type="radio" id="cs_ar_config_use_in_search_no" name="cs_ar_config_use_in_search"
                        value="0" <?php if ($should_use_in_search === 0 || $should_use_in_search === '') {
    echo esc_attr('checked');
}
?>>
                </div>
            </div>

            <!-- has_archive_page, defaults to no, there is no validation for this input -->
            <div class="cs_ar_input_div">
                <?php $should_have_archive = (int) $settings_data['cs_ar_config_has_archive_page'];?>
                <label class="cs_ar_form_label cs_ar_retrieval_details_label" for="cs_ar_config_has_archive_page">Should ContentStream articles have their own listing page on your site?</label>
                <div class="cs_ar_float_left cs_ar_yes_no_container">
                    <label class="cs_ar_float_left cs_ar_yes_no_label" for="cs_ar_config_has_archive_page_yes">Yes</label>
                    <input class="cs_ar_float_left" type="radio" id="cs_ar_config_has_archive_page_yes" name="cs_ar_config_has_archive_page"
                        value="1" <?php if ($should_have_archive === 1) {
    echo esc_attr('checked');
}
?>>
                    <label class="cs_ar_float_left cs_ar_yes_no_label" for="cs_ar_config_has_archive_page_no">No</label>
                    <input class="cs_ar_float_left" type="radio" id="cs_ar_config_has_archive_page_no" name="cs_ar_config_has_archive_page"
                        value="0" <?php if ($should_have_archive === 0 || $should_have_archive === '') {
    echo esc_attr('checked');
}
?>>
                </div>
            </div>
        </div>

        <!-- form buttons -->
        <div class="cs_ar_float_right cs_ar_button_section">
            <!-- save the form -->
            <input class="button-primary gfbutton" type="submit" name="cs_ar_settings_save" value="Save Settings" />
            <!-- manually retrieve articles -->
            <input class="button action" type="submit" name="cs_ar_article_retrieve" value="Retrieve Articles Now" />
        </div>
    </form>
</div>
