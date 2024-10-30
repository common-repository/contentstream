<?php

/**
 * The class that handles the settings form for the ContentStream Article Retriever
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARSettingsForm
{
    public $cs_ar_article_retrieval_controller;
    public $cs_ar_config_table_controller;
    public $cs_ar_feed_info_table_controller;
    private $encryptor;
    public $need_redirect;

    /**
     *  Initialize the class and the table controllers and encryptor
     */
    public function __construct()
    {
        $this->cs_ar_article_retrieval_controller = new csARArticleRetrievalController();
        $this->cs_ar_config_table_controller = new csARConfigTableController();
        $this->cs_ar_feed_info_table_controller = new csARFeedInfoTableController();
        $this->encryptor = new csAREncryptor();
        $this->need_redirect = true;

    }

    /**
     * Main display function for the settings form
     *
     * @return boolean  This return value is for testing
     */
    public function display_form()
    {
        $current_url = $_SERVER['REQUEST_URI'];
        $config_id = 1;
        //Validate user input
        $errors = $this->validate_settings_form();
        //perform action that the user selected. Can be save data, retrieve now, or nothing
        $action_message = $this->execute_form_action($config_id, $errors);
        $settings_data = $this->get_data_for_form($config_id);

        if ($this->need_redirect &&
            ($settings_data['cs_ar_config_use_custom_post_type'] === '0' && $current_url !== '/wp-admin/admin.php?page=' . csARConfig::CS_SETTINGS_SLUG) ||
            ($settings_data['cs_ar_config_use_custom_post_type'] === '1' && $current_url !== '/wp-admin/edit.php?post_type=' . csARConfig::CS_ARTICLE_POST_TYPE . '&page=' . csARConfig::CS_SETTINGS_SLUG)) {
            $this->need_redirect = false;
            return $this->redirect_to_new_page(($current_url === '/wp-admin/admin.php?page=' . csARConfig::CS_SETTINGS_SLUG) ? '/wp-admin/edit.php?post_type=' . csARConfig::CS_ARTICLE_POST_TYPE . '&page=' . csARConfig::CS_SETTINGS_SLUG : '/wp-admin/admin.php?page=' . csARConfig::CS_SETTINGS_SLUG);
        } else {
            $this->need_redirect = true;
        }

        $this->display_settings_form($settings_data, $errors, $action_message);

        return true;
    }

    /**
     * @codeCoverageIgnore
     * Redirect function for when an user switch plugin from using custom post type to regular 'post' post type
     *
     * @param [String] $url
     * @return void
     */
    public function redirect_to_new_page($url)
    {
        return wp_redirect($url);
    }

    /**
     * @codeCoverageIgnore
     * Displays the setting form
     * Note: Cannot be tested because this function will echo out all the html from file to the console
     *
     * @param [array] $settings_data  An array that conains the data that should populate the form fields
     * @param [array] $errors  An array that contains all of the errors the user made during the previous submit, could be empty
     * @param [array] $action_message  An array that contains the status message for the user and whether that message is an error
     * @return void
     */
    public function display_settings_form($settings_data, $errors, $action_message)
    {
        include CS1_AR_PATH . 'app/templates/cs-ar-settings-form.php';
    }

    /**
     * @codeCoverageIgnore
     * Displays the form action success/error message using admin notices
     * Note: Cannot be tested because this function will echo out all the html from file to the console
     *
     * @param [String] $message  the message that will displayed to the user
     * @param [boolean] $is_error  Tells the notice whether or not it should be display the success version or the error version
     * @return void
     */
    public function display_status_message($message, $is_error)
    {
        include CS1_AR_PATH . 'app/templates/cs-ar-admin-notice.php';
    }

    /**
     * Sanitizes the user's inputs to the form
     *
     * @return void
     */
    public function sanitize_form_inputs()
    {
        $_POST['cs_ar_config_username'] = sanitize_email($_POST['cs_ar_config_username']);
        $_POST['cs_ar_config_password'] = sanitize_text_field($_POST['cs_ar_config_password']);
        $_POST['cs_ar_config_feed_amount'] = sanitize_text_field($_POST['cs_ar_config_feed_amount']);
        $_POST['cs_ar_config_retrieval_frequency'] = sanitize_text_field($_POST['cs_ar_config_retrieval_frequency']);
        $_POST['cs_ar_config_subscriber_id'] = sanitize_text_field($_POST['cs_ar_config_subscriber_id']);
        $_POST['cs_ar_feed_info_search_id'] = sanitize_text_field($_POST['cs_ar_feed_info_search_id']);
        $_POST['cs_ar_config_use_custom_template'] = sanitize_text_field($_POST['cs_ar_config_use_custom_template']);
        $_POST['cs_ar_config_delete_content_on_uninstall'] = sanitize_text_field($_POST['cs_ar_config_delete_content_on_uninstall']);
        $_POST['cs_ar_config_use_on_home_page'] = sanitize_text_field($_POST['cs_ar_config_use_on_home_page']);
        $_POST['cs_ar_config_use_in_search'] = sanitize_text_field($_POST['cs_ar_config_use_in_search']);
        $_POST['cs_ar_config_has_archive_page'] = sanitize_text_field($_POST['cs_ar_config_has_archive_page']);
        $_POST['cs_ar_config_use_custom_post_type'] = sanitize_text_field($_POST['cs_ar_config_use_custom_post_type']);
    }

    /**
     * Validates the user's inputs to the form
     *
     * @return array  An array that contains all of the errors the user made during the previous submit, could be empty
     */
    public function validate_settings_form()
    {
        $errors = new WP_Error();
        //blank check for username
        $this->blank_check_for_text_field('cs_ar_config_username', $errors, csARConfig::USERNAME_BLANK_ERROR_MESSAGE);
        //email check for username
        $this->email_check_for_text_field('cs_ar_config_username', $errors, csARConfig::USERNAME_EMAIL_ERROR_MESSAGE);
        //blank check for password
        $this->blank_check_for_text_field('cs_ar_config_password', $errors, csARConfig::PASSWORD_BLANK_ERROR_MESSAGE);
        //blank check for subscriber id
        $this->blank_check_for_text_field('cs_ar_config_subscriber_id', $errors, csARConfig::SUBSCRIBER_ID_BLANK_ERROR_MESSAGE);
        //number check for subscriber id
        $this->numeric_check_for_text_field('cs_ar_config_subscriber_id', $errors, csARConfig::SUBSCRIBER_ID_NUMBER_ERROR_MESSAGE);
        //blank check for frequency
        $this->blank_check_for_select_field('cs_ar_config_retrieval_frequency', $errors, csARConfig::FREQUENCY_BLANK_ERROR_MESSAGE);
        //blank check for feed amount
        $this->blank_check_for_select_field('cs_ar_config_feed_amount', $errors, csARConfig::AMOUNT_BLANK_ERROR_MESSAGE);
        //blank check for feed id if feed amount is set to single
        $this->blank_check_for_two_text_fields('cs_ar_feed_info_search_id', 'cs_ar_config_feed_amount', $errors, csARConfig::SEARCH_ID_BLANK_ERROR_MESSAGE);
        //number check for feed id if feed amount is set to single
        //$this->numeric_check_for_two_text_fields('cs_ar_feed_info_search_id', 'cs_ar_config_feed_amount', $errors, csARConfig::SEARCH_ID_NUMBER_ERROR_MESSAGE);
        $this->numeric_check_for_text_field('cs_ar_feed_info_search_id', $errors, csARConfig::SEARCH_ID_NUMBER_ERROR_MESSAGE);
        //check for custom template
        $this->check_for_single_checkbox('cs_ar_config_use_custom_template');
        //check for delete content on uninstall
        $this->check_for_single_checkbox('cs_ar_config_delete_content_on_uninstall');
        //check for use_on_home_page
        $this->check_for_single_checkbox('cs_ar_config_use_on_home_page');
        //check for has_archive_page
        $this->check_for_single_checkbox('cs_ar_config_has_archive_page');
        //check for use_in_search
        $this->check_for_single_checkbox('cs_ar_config_use_in_search');
        //check for use_in_search
        $this->check_for_single_checkbox('cs_ar_config_use_custom_post_type');
        //return errors if there are any and if not return empty array
        return $errors->errors;
    }

    /**
     * Checks the post value for the given field to make sure it is not blank
     *
     * @param [String] $field_name  The name of the form field to check
     * @param [object] $errors  The wordpress error object that stores all the errors found on the form
     * @param [String] $error_message  The message that should be shown to the user if the function finds an error
     * @return void
     */
    public function blank_check_for_text_field($field_name, $errors, $error_message)
    {
        if (isset($_POST[$field_name]) && sanitize_text_field($_POST[$field_name]) === '') {
            $errors->add($field_name . '_error', $error_message);
        }
    }

    /**
     * Checks the post value for the given field to make sure it is not blank
     *
     * @param [String] $field_name  The name of the form field to check
     * @param [object] $errors  The wordpress error object that stores all the errors found on the form
     * @param [String] $error_message  The message that should be shown to the user if the function finds an error
     * @return void
     */
    public function blank_check_for_select_field($field_name, $errors, $error_message)
    {
        if (isset($_POST[$field_name]) && sanitize_text_field($_POST[$field_name]) === '-') {
            $errors->add($field_name . '_error', $error_message);
        }
    }

    /**
     * Checks the post value for the given field to make sure it is a valid email address
     *
     * @param [String] $field_name  The name of the form field to check
     * @param [object] $errors  The wordpress error object that stores all the errors found on the form
     * @param [String] $error_message  The message that should be shown to the user if the function finds an error
     * @return void
     */
    public function email_check_for_text_field($field_name, $errors, $error_message)
    {
        if (isset($_POST[$field_name]) && $_POST[$field_name] !== '' && !is_email($_POST[$field_name])) {
            $errors->add($field_name . '_error', $error_message);
        }
    }

    /**
     * Checks the post value for the given field to make sure it is a valid number
     *
     * @param [String] $field_name  The name of the form field to check
     * @param [object] $errors  The wordpress error object that stores all the errors found on the form
     * @param [String] $error_message  The message that should be shown to the user if the function finds an error
     * @return void
     */
    public function numeric_check_for_text_field($field_name, $errors, $error_message)
    {
        if (isset($_POST[$field_name]) && sanitize_text_field($_POST[$field_name]) !== '' && !is_numeric(sanitize_text_field($_POST[$field_name]))) {
            $errors->add($field_name . '_error', $error_message);
        }
    }

    /**
     * Checks the post value for the given field to make sure it is not blank if another field is a certain value
     *
     * @param [String] $field_name  The name of the form field to check
     * @param [String] $field_name_2  The name of the secondary form field
     * @param [object] $errors  The wordpress error object that stores all the errors found on the form
     * @param [String] $error_message  The message that should be shown to the user if the function finds an error
     * @return void
     */
    public function blank_check_for_two_text_fields($field_name, $field_name_2, $errors, $error_message)
    {
        if ((isset($_POST[$field_name]) && isset($_POST[$field_name_2])) && (sanitize_text_field($_POST[$field_name_2]) === 'SINGLE' && sanitize_text_field($_POST[$field_name]) === '')) {
            $errors->add($field_name . '_error', $error_message);
        }
    }

    /**
     * Checks the post value for the given field to make sure it is a valid number if another field is a certain value
     *
     * @param [String] $field_name  The name of the form field to check
     * @param [String] $field_name_2  The name of the secondary form field
     * @param [object] $errors  The wordpress error object that stores all the errors found on the form
     * @param [String] $error_message  The message that should be shown to the user if the function finds an error
     * @return void
     */
    public function numeric_check_for_two_text_fields($field_name, $field_name_2, $errors, $error_message)
    {
        if ((isset($_POST[$field_name]) && isset($_POST[$field_name_2])) && (sanitize_text_field($_POST[$field_name_2]) === 'SINGLE' && sanitize_text_field($_POST[$field_name]) !== '' && !is_numeric(sanitize_text_field($_POST[$field_name])))) {
            $errors->add($field_name . '_error', $error_message);
        }
    }

    /**
     * Checks the given checkbox field and if unchecked will set value to 0 for saving purposes
     *
     * @param [String] $field_name  The name of the form field to check
     * @return void
     */
    public function check_for_single_checkbox($field_name)
    {
        if ((isset($_POST[$field_name]) && (int) sanitize_text_field($_POST[$field_name]) !== 1) || (!isset($_POST[$field_name]))) {
            $_POST[$field_name] = 0;
        }
    }

    /**
     * Executes an action of the settings form based on user input, won't do anything if the user doesn't have permission to 'manage_options' in Wordpress
     *
     * @param [String] $config_id  The ID of the config record if there is one
     * @param [array] $errors  An array that contains all of the errors the user made during the previous submit, could be empty
     * @return array  An array that contains the status message for the user and whether that message is an error
     */
    public function execute_form_action($config_id, $errors)
    {
        $form_action_result = array(
            'is_error' => false,
            'message' => '',
        );

        $cs_ar_settings_save = isset($_POST['cs_ar_settings_save']);
        $cs_ar_article_retrieve = isset($_POST['cs_ar_article_retrieve']);

        if ($cs_ar_settings_save || $cs_ar_article_retrieve) {
            if ($this->check_if_user_can_execute()) {
                $form_action_result = $this->save_settings($config_id, $errors);

                if (!$form_action_result['is_error'] && $cs_ar_article_retrieve) {
                    $form_action_result = $this->retrieve_articles_now();
                }
            } else {
                $form_action_result['is_error'] = true;
                $form_action_result['message'] = "You don't have permission to use this form!";
            }
        }

        $cs_ar_settings_save = '';
        $cs_ar_article_retrieve = '';

        return $form_action_result;
    }

    /**
     * Saves the form based on user's inputes
     *
     * @param [String] $config_id  The ID of the config record if there is one
     * @param [array] $errors  An array that contains all of the errors the user made during the previous submit, could be empty
     * @return array  An array that contains the status message for the user and whether that message is an error
     */
    public function save_settings($config_id, $errors)
    {
        $response = array(
            'is_error' => false,
            'message' => '',
        );

        if (empty($errors)) {
            $cs_ar_activator = new csARPluginActivator();
            $old_data = $this->get_data_for_form($config_id);
            $old_frequency = $old_data['cs_ar_config_retrieval_frequency'];
            $old_should_use_custom_post_type = $old_data['cs_ar_config_use_custom_post_type'];
            //Sanitizes user input for saving
            $this->sanitize_form_inputs();
            $is_config_info_saved = $this->save_config_settings($config_id);
            $is_feed_info_saved = $_POST['cs_ar_feed_info_search_id'] !== '' ? $this->save_feed_info_settings($config_id) : 1;
            if ($is_config_info_saved === 1 && $is_feed_info_saved === 1) {
                if ($old_frequency !== $_POST['cs_ar_config_retrieval_frequency']) {
                    global $cs_ar_cron_scheduler;
                    $cs_ar_cron_scheduler->schedule($_POST['cs_ar_config_retrieval_frequency']);
                }

                if ($old_should_use_custom_post_type !== $_POST['cs_ar_config_use_custom_post_type']) {
                    if ($_POST['cs_ar_config_use_custom_post_type'] === '1') {
                        $cs_ar_activator->activate_cs_articles();
                    } else {
                        $cs_ar_activator->deactivate_cs_articles();
                    }
                }
                $_POST = "";
                $response['message'] = csARConfig::SETTINGS_FORM_SUCCESS_MESSAGE;
            } else {
                $response['is_error'] = true;
                $response['message'] = csARConfig::SETTINGS_FORM_ERROR_MESSAGE;
            }
        }

        return $response;
    }

    /**
     * Saves feed info to DB based on the user's input
     *
     * @param [String] $config_id  The ID of the config record that will be related to the feed info record
     * @return boolean  If return true if the insert/update is successful, and false if it is not
     */
    public function save_feed_info_settings($config_id)
    {
        //get DB record
        $feed_info = $this->cs_ar_feed_info_table_controller->get_records_by_config_id($config_id);
        //if empty, insert the record
        if (empty($feed_info)) {
            return $this->cs_ar_feed_info_table_controller->insert_record(
                array(
                    'cs_ar_feed_info_config_id' => $config_id,
                    'cs_ar_feed_info_search_id' => sanitize_text_field($_POST['cs_ar_feed_info_search_id']),
                )
            );
        }

        //if not empty, check search id, if the DB value and the form value are the same return 1, if not update DB
        return $feed_info[0]->cs_ar_feed_info_search_id === $_POST['cs_ar_feed_info_search_id'] ? 1 : $this->cs_ar_feed_info_table_controller->update_record($feed_info[0]->cs_ar_feed_info_id,
            array(
                'cs_ar_feed_info_id' => $feed_info[0]->cs_ar_feed_info_id,
                'cs_ar_feed_info_config_id' => $config_id,
                'cs_ar_feed_info_search_id' => sanitize_text_field($_POST['cs_ar_feed_info_search_id']),
            )
        );
    }

    /**
     * Saves config info to DB based on the user's input
     *
     * @param [String] $config_id  The ID of the config record if there is one
     * @return boolean  If return true if the insert/update is successful, and false if it is not
     */
    public function save_config_settings($config_id)
    {
        //get DB record
        $config_info = $this->get_data_for_form($config_id);
        //if username is blank, insert the record
        if ($config_info['cs_ar_config_username'] === '' || $config_info['cs_ar_config_username'] === false) {
            return $this->cs_ar_config_table_controller->insert_record(
                array(
                    'cs_ar_config_username' => $this->encryptor->encrypt(sanitize_email($_POST['cs_ar_config_username'])),
                    'cs_ar_config_password' => $this->encryptor->encrypt(sanitize_text_field($_POST['cs_ar_config_password'])),
                    'cs_ar_config_feed_amount' => sanitize_text_field($_POST['cs_ar_config_feed_amount']),
                    'cs_ar_config_retrieval_frequency' => sanitize_text_field($_POST['cs_ar_config_retrieval_frequency']),
                    'cs_ar_config_subscriber_id' => sanitize_text_field($_POST['cs_ar_config_subscriber_id']),
                    'cs_ar_config_use_custom_template' => sanitize_text_field($_POST['cs_ar_config_use_custom_template']),
                    'cs_ar_config_delete_content_on_uninstall' => sanitize_text_field($_POST['cs_ar_config_delete_content_on_uninstall']),
                    'cs_ar_config_use_on_home_page' => sanitize_text_field($_POST['cs_ar_config_use_on_home_page']),
                    'cs_ar_config_use_in_search' => sanitize_text_field($_POST['cs_ar_config_use_in_search']),
                    'cs_ar_config_has_archive_page' => sanitize_text_field($_POST['cs_ar_config_has_archive_page']),
                    'cs_ar_config_use_custom_post_type' => sanitize_text_field($_POST['cs_ar_config_use_custom_post_type']),
                )
            );
        }

        //if username is not blank, check the DB values and the form values, if values are the same return 1, if not update DB
        return ($config_info['cs_ar_config_username'] === $_POST['cs_ar_config_username'] &&
            $config_info['cs_ar_config_password'] === $_POST['cs_ar_config_password'] &&
            $config_info['cs_ar_config_feed_amount'] === $_POST['cs_ar_config_feed_amount'] &&
            $config_info['cs_ar_config_retrieval_frequency'] === $_POST['cs_ar_config_retrieval_frequency'] &&
            $config_info['cs_ar_config_subscriber_id'] === $_POST['cs_ar_config_subscriber_id'] &&
            $config_info['cs_ar_config_use_custom_template'] === $_POST['cs_ar_config_use_custom_template'] &&
            $config_info['cs_ar_config_delete_content_on_uninstall'] === $_POST['cs_ar_config_delete_content_on_uninstall'] &&
            $config_info['cs_ar_config_use_on_home_page'] === $_POST['cs_ar_config_use_on_home_page'] &&
            $config_info['cs_ar_config_use_in_search'] === $_POST['cs_ar_config_use_in_search'] &&
            $config_info['cs_ar_config_has_archive_page'] === $_POST['cs_ar_config_has_archive_page'] &&
            $config_info['cs_ar_config_use_custom_post_type'] === $_POST['cs_ar_config_use_custom_post_type']) ? 1 : $this->cs_ar_config_table_controller->update_record($config_id,
            array(
                'cs_ar_config_id' => $config_id,
                'cs_ar_config_username' => $this->encryptor->encrypt(sanitize_email($_POST['cs_ar_config_username'])),
                'cs_ar_config_password' => $this->encryptor->encrypt(sanitize_text_field($_POST['cs_ar_config_password'])),
                'cs_ar_config_feed_amount' => sanitize_text_field($_POST['cs_ar_config_feed_amount']),
                'cs_ar_config_retrieval_frequency' => sanitize_text_field($_POST['cs_ar_config_retrieval_frequency']),
                'cs_ar_config_subscriber_id' => sanitize_text_field($_POST['cs_ar_config_subscriber_id']),
                'cs_ar_config_use_custom_template' => sanitize_text_field($_POST['cs_ar_config_use_custom_template']),
                'cs_ar_config_delete_content_on_uninstall' => sanitize_text_field($_POST['cs_ar_config_delete_content_on_uninstall']),
                'cs_ar_config_use_on_home_page' => sanitize_text_field($_POST['cs_ar_config_use_on_home_page']),
                'cs_ar_config_use_in_search' => sanitize_text_field($_POST['cs_ar_config_use_in_search']),
                'cs_ar_config_has_archive_page' => sanitize_text_field($_POST['cs_ar_config_has_archive_page']),
                'cs_ar_config_use_custom_post_type' => sanitize_text_field($_POST['cs_ar_config_use_custom_post_type']),
            )
        );
    }

    /**
     * Calls the retrieve_articles function
     *
     * @return array  An array that contains the status message for the user and whether that message is an error
     */
    public function retrieve_articles_now()
    {
        $response = array(
            'is_error' => false,
            'message' => '',
        );

        $_POST = "";
        $retrieval_success = $this->cs_ar_article_retrieval_controller->retrieve_articles();
        if ($retrieval_success) {
            $response['message'] = csARConfig::ARTICLE_RETRIEVAL_SUCCESS_MESSAGE;
        } else {
            $response['is_error'] = true;
            $response['message'] = csARConfig::ARTICLE_RETRIEVAL_ERROR_MESSAGE;
        }

        return $response;
    }

    /**
     * @codeCoverageIgnore
     * Checks to see if the user has the permission to manage options, meaning play with the Settings tab in Wordpress, and return a boolean value based on results
     *
     * @return boolean  Whether or not the user can manage options in Wordpress
     */
    public function check_if_user_can_execute()
    {
        return current_user_can('manage_options');
    }

    /**
     * Gets the data from the DB for the settings form
     *
     * @param [int] $config_id  The ID of the config record if there is one
     * @return array  An array that conains the data that should populate the form fields
     */
    public function get_data_for_form($config_id)
    {
        return $this->configure_data_for_form($this->get_config_info_for_form($config_id), $this->get_search_info_for_form($config_id));
    }

    /**
     * Gets the config info record from DB based on config id
     *
     * @param [int] $config_id  The ID of the config record if there is one
     * @return object  The record from the DB, could contain blank fields
     */
    public function get_config_info_for_form($config_id)
    {
        $config_results = $this->cs_ar_config_table_controller->get_one_record_by_id($config_id);
        return ($config_results !== false && !empty($config_results)) ? $config_results[0] : (object) array(
            'cs_ar_config_username' => '',
            'cs_ar_config_password' => '',
            'cs_ar_config_feed_amount' => '',
            'cs_ar_config_retrieval_frequency' => '',
            'cs_ar_config_subscriber_id' => '',
            'cs_ar_config_use_custom_template' => '',
            'cs_ar_config_delete_content_on_uninstall' => '',
            'cs_ar_config_use_on_home_page' => '',
            'cs_ar_config_use_in_search' => '',
            'cs_ar_config_has_archive_page' => '',
            'cs_ar_config_use_custom_post_type' => '',
        );
    }

    /**
     * Gets the feed info record from DB based on config id
     *
     * @param [int] $config_id  The ID of the config record if there is one
     * @return object  The record from the DB, could contain blank fields
     */
    public function get_search_info_for_form($config_id)
    {
        $search_results = $this->cs_ar_feed_info_table_controller->get_records_by_config_id($config_id);
        return ($search_results !== false && !empty($search_results)) ? $search_results[0] : (object) array('cs_ar_feed_info_search_id' => '');
    }

    /**
     * Configures the record objects from the DB into a key-value array for the settings form to use
     *
     * @param [object] $config_data  The record from the config table in the DB, could contain blank fields
     * @param [object] $search_info_data  The record from the feed info table in the DB, could contain blank fields
     * @return array  An array that conains the data that should populate the form fields
     */
    public function configure_data_for_form($config_data, $search_info_data)
    {
        return array(
            'cs_ar_config_username' => $this->encryptor->decrypt($config_data->cs_ar_config_username),
            'cs_ar_config_password' => $this->encryptor->decrypt($config_data->cs_ar_config_password),
            'cs_ar_config_feed_amount' => $config_data->cs_ar_config_feed_amount,
            'cs_ar_config_retrieval_frequency' => $config_data->cs_ar_config_retrieval_frequency,
            'cs_ar_config_subscriber_id' => $config_data->cs_ar_config_subscriber_id,
            'cs_ar_feed_info_search_id' => $search_info_data->cs_ar_feed_info_search_id,
            'cs_ar_config_use_custom_template' => $config_data->cs_ar_config_use_custom_template,
            'cs_ar_config_delete_content_on_uninstall' => $config_data->cs_ar_config_delete_content_on_uninstall,
            'cs_ar_config_use_on_home_page' => $config_data->cs_ar_config_use_on_home_page,
            'cs_ar_config_use_in_search' => $config_data->cs_ar_config_use_in_search,
            'cs_ar_config_has_archive_page' => $config_data->cs_ar_config_has_archive_page,
            'cs_ar_config_use_custom_post_type' => $config_data->cs_ar_config_use_custom_post_type,
        );
    }
}
