<?php

/**
 * Generic class for the RESTful requests the article retriver will be sending to the contentstream server.
 * Also handles the errors coming from the REST request as well as logging those actions in the DB.
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARRestRequest
{
    private $config_id;

    private $access_token;

    private $error_occurred;

    private $error_description;

    protected $response;

    public $cs_ar_log_table_controller;

    /**
     * Start this class.
     * Sets the config_id field and the access_token field if the access_token is provided
     * Creates a new instance of the log table controller for action logging
     *
     * @param [String] $config_id  The id of the config record that is being used in the article retrieval process
     * @param string $access_token  The token that will allow access to the REST routes coming from contentstream
     */
    public function __construct($config_id, $access_token = '')
    {
        $this->set_config_id($config_id);
        $this->set_access_token($access_token);
        $this->cs_ar_log_table_controller = new csARLogTableController();
    }

    /**
     * The getter function of the private var config_id
     *
     * @return String  The config_id
     */
    public function get_config_id()
    {
        return $this->config_id;
    }

    /**
     * The setter function of the private var config_id
     *
     * @param [String] $config_id  The value the config_id should be set to
     * @return void
     */
    public function set_config_id($config_id)
    {
        $this->config_id = $config_id;
    }

    /**
     * The getter function of the private var access_token
     *
     * @return String  The access_token
     */
    public function get_access_token()
    {
        return $this->access_token;
    }

    /**
     * The setter function of the private var access_token
     *
     * @param [String] $access_token  The value the access_token should be set to
     * @return void
     */
    public function set_access_token($access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * Checks to see if the access_token has been set
     *
     * @return boolean  Returns true if the access_token is set, false if not
     */
    public function has_token()
    {
        return $this->get_access_token() !== '' ? true : false;
    }

    /**
     * Checks to see if the request is in error
     *
     * @return boolean  Returns true if error_occurred is set to 1, false if set to 0
     */
    public function has_error_occurred()
    {
        return $this->error_occurred === 1 ? true : false;
    }

    /**
     * The setter function of the private var error_occurred
     *
     * @param [int] $error_occurred  The value the error_occurred should be set to
     * @return void
     */
    public function set_error_occurred($error_occurred)
    {
        $this->error_occurred = $error_occurred;
    }

    /**
     * The getter function of the private var error_description
     *
     * @return String  The error_description
     */
    public function get_error_description()
    {
        return $this->error_description;
    }

    /**
     * The setter function of the private var error_description
     *
     * @param [String] $error_description  The value the error_description should be set to
     * @return void
     */
    public function set_error_description($error_description)
    {
        $this->error_description = $error_description;
    }

    /**
     * The getter function of the private var response
     *
     * @return array  The response from the REST call
     */
    public function get_response()
    {
        return $this->response;
    }

    /**
     * Base getter function for the response from the REST call. Also sets the error fields for handling.
     *
     * @param [String] $response  The response from the REST call, should be a json string for the function to decode
     * @return void
     */
    public function set_response($response)
    {
        $this->response = json_decode($response);
        $this->set_error_occurred($this->get_response()->error_occurred);
        $this->set_error_description($this->get_response()->error_description);
    }

    /**
     * Logs the action that was taken by the request
     *
     * @param [String] $action_name  The name of the action that just occured
     * @param [String] $action_success_message  The message that should be stored in the DB if the action was successful
     * @return void
     */
    public function log_request_action($action_name, $action_success_message)
    {
        $this->cs_ar_log_table_controller->insert_record(array(
            'cs_ar_log_config_id' => $this->get_config_id(),
            'cs_ar_log_timestamp' => $this->cs_ar_log_table_controller->generate_time_stamp(),
            'cs_ar_log_action' => $action_name,
            'cs_ar_log_status' => !$this->has_error_occurred() ? csArConfig::REQUEST_SUCCESS_STATUS : csArConfig::REQUEST_ERROR_STATUS,
            'cs_ar_log_results' => !$this->has_error_occurred() ? $action_success_message : $this->get_error_description(),
        ));
    }

    /**
     * Builds a string replacing the placeholders with values
     *
     * @param [String] $base_message  The initial message with placeholder text
     * @param [array] $replacement_array  The list of what the placeholders should be replaced with
     * @return String  A string that has had its placeholders replacement with actual values, returns empty string if error has occurred
     */
    public function build_string_with_placeholders($base_message, $replacement_array)
    {
        if ($this->has_error_occurred()) {
            return '';
        }

        $success_message = $base_message;
        $placeholder_array = $this->build_placeholder_array($base_message);
        for ($index = 0; $index < count($placeholder_array); $index++) {
            $success_message = str_replace($placeholder_array[$index], $replacement_array[$index], $success_message);
        }
        return $success_message;
    }

    /**
     * Builds an array of placeholders for the success message
     *
     * @param String $base_message  The message with the placeholders
     * @return array  A list of placeholders, could be empty
     */
    public function build_placeholder_array($base_message)
    {
        $matches = array();
        preg_match_all(csARConfig::REQUEST_PLACEHOLDER_REGEX, $base_message, $matches);
        return !empty($matches) ? $matches[0] : $matches;
    }
}
