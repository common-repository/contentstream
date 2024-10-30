<?php

/**
 * The class that handles all the rest calls to the contentstream serve.
 * This class will be dealing with contentstream's new RESTful API using cURL.
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARRestController
{
    public $curl;

    /**
     *  Start this class
     *
     */
    public function __construct()
    {
        //nothing yet...
    }

    /**
     * Creates a new cURL handler
     *
     * @return void
     */
    public function init_curl()
    {
        $this->curl = curl_init();
    }

    /**
     * Shuts down the current cURL handler
     * Note: cannot be directly tested but used in all tests for the controller
     *
     * @return void
     */
    public function close_curl()
    {
        curl_close($this->curl);
    }

    /**
     * Sets a field on the current cURL handler to a given value.
     *
     * @param [String] $name  The name of the field that is being set
     * @param [mixed] $value  The value that is being set to the field, could be a string, boolean value, or array
     * @return void
     */
    public function set_curl_option($name, $value)
    {
        curl_setopt($this->curl, $name, $value);
    }

    /**
     * @codeCoverageIgnore
     * Executes the cURL call to the external server and retrieves the data
     * Note: cannot be tested because curl_exec will call out to the URL specified in the handler
     *
     * @return array  The response of the call from cURL
     */
    public function execute_curl_call()
    {
        return curl_exec($this->curl);
    }

    /**
     * Checks to make sure the request that is being sent to the contentstream server through cURL is one of the plugin's predefined request object
     *
     * @param [object] $request  A classed object that contains the necessary fields and URL for cURL to use to send the REST cal
     * @return boolean  Returns true if the request object is one of the plugin's, false if not
     */
    public function check_request_class($request)
    {
        $class_name = get_class($request);
        return ($class_name === csARConfig::ACCESS_TOKEN_REQUEST_CLASS_NAME ||
            $class_name === csARConfig::ENABLED_FEEDS_REQUEST_CLASS_NAME ||
            $class_name === csARConfig::CONTENT_LIST_REQUEST_CLASS_NAME ||
            $class_name === csARConfig::GET_ARTICLE_REQUEST_CLASS_NAME ||
            $class_name === csARConfig::REMOVE_FROM_QUEUE_REQUEST_CLASS_NAME) ? true : false;
    }

    /**
     * Send a rest request through the cURL client as a POST
     *
     * @param [object] $request  A classed object that contains the necessary fields and URL for cURL to use to send the REST call
     * @return void
     */
    public function send_post_request($request)
    {
        if ($this->check_request_class($request)) {
            $this->send_rest_request($request, 'POST');
        }

    }

    /**
     * Send a rest request through the cURL client as a GET
     *
     * @param [object] $request  A classed object that contains the necessary fields and URL for cURL to use to send the REST call
     * @return void
     */
    public function send_get_request($request)
    {
        if ($this->check_request_class($request)) {
            $this->send_rest_request($request);
        }

    }

    /**
     * Send a rest request through the cURL client as a DELETE
     *
     * @param [object] $request  A classed object that contains the necessary fields and URL for cURL to use to send the REST call
     * @return void
     */
    public function send_delete_request($request)
    {
        if ($this->check_request_class($request)) {
            $this->send_rest_request($request, 'DELETE');
        }

    }

    /**
     * Generic function that sends rest requests from cURL, method defaults to GET
     *
     * @param [object] $request  A classed object that contains the necessary fields and URL for cURL to use to send the REST call
     * @param String $method  The HTTP method that cURL should use when sending the request, defaults to GET if no method is specified
     * @return void
     */
    public function send_rest_request($request, $method = 'GET')
    {
        //init curl
        $this->init_curl();
        //set URL
        $this->set_curl_option(CURLOPT_URL, csArConfig::REST_API_URL . $request->get_request_url());
        //tell curl that there will be a return response
        $this->set_curl_option(CURLOPT_RETURNTRANSFER, true);
        //check method being used and add attributes accordingly
        if ($method === 'POST') {
            $this->set_curl_option(CURLOPT_POST, true);
            $this->set_curl_option(CURLOPT_POSTFIELDS, $request->get_request_data());
        } else if ($method === 'DELETE') {
            $this->set_curl_option(CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        //add access token if the request has one
        if ($request->has_token()) {
            $this->set_curl_option(CURLOPT_HTTPHEADER, array(
                'Content-Type: ' . csARConfig::REQUEST_CONTENT_TYPE,
                'Authorization: ' . csArConfig::ACCESS_TOKEN_BEARER . $request->get_access_token(),
            ));
        }
        //execute the request
        $curl_response = $this->execute_curl_call();
        //attach response to request
        $request->set_response($curl_response);
        //close curl
        $this->close_curl();
    }
}
