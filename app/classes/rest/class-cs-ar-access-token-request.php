<?php

/**
 * The class that handles the request data and response for the access token REST route for contentstream.
 * This class is extended from the base class csARRestRequest that handles the access token and config id fields as well as error handling and action logging.
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARAccessTokenRequest extends csARRestRequest
{
    private $username;

    private $password;

    /**
     * Start this class.
     * Calls its parent's construct function passing the config id and leaves the access token empty.
     * Sets the username and password fields.
     *
     * @param [String] $config_id  The id of the config record that is being used in the article retrieval process
     * @param [String] $username  The decrypted value that represents the username that the user has inputted into the plugin
     * @param [String] $password  The decrypted value that represents the password that the user has inputted into the plugin
     */
    public function __construct($config_id, $username, $password)
    {
        parent::__construct($config_id);
        $this->set_username($username);
        $this->set_password($password);
    }

    /**
     * The getter function of the private var username
     *
     * @return String  The username
     */
    public function get_username()
    {
        return $this->username;
    }

    /**
     * The setter function of the private var username
     *
     * @param [String] $username  The value the username should be set to
     * @return void
     */
    public function set_username($username)
    {
        $this->username = $username;
    }

    /**
     * The getter function of the private var password
     *
     * @return String  The password
     */
    public function get_password()
    {
        return $this->password;
    }

    /**
     * The setter function of the private var password
     *
     * @param [String] $password  The value the password should be set to
     * @return void
     */
    public function set_password($password)
    {
        $this->password = $password;
    }

    /**
     * Finishes up the URL for the REST call and returns it
     *
     * @return String  The completed URL string for the accessToken REST call
     */
    public function get_request_url()
    {
        return csARConfig::ACCESS_TOKEN_REQUEST_URL;
    }

    /**
     * Builds and return an object array for the accessToken request call
     *
     * @return array An object array that contains the private fields username and password for the accessToken request call
     */
    public function get_request_data()
    {
        return array(
            'username' => $this->get_username(),
            'password' => $this->get_password(),
        );
    }

    /**
     * Sets the response from the REST call and logs the action that was taken
     *
     * @param [array] $response  The json string that was returned from the cs server
     * @return void
     */
    public function set_response($response)
    {
        parent::set_response($response);
        parent::log_request_action(csARConfig::ACCESS_TOKEN_REQUEST_NAME, csARConfig::ACCESS_TOKEN_REQUEST_SUCCESS_MESSAGE);
    }
}
