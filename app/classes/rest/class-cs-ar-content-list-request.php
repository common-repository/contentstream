<?php

/**
 * The class that handles the request data and response for the content list REST route for contentstream.
 * This class is extended from the base class csARRestRequest that handles the access token and config id fields as well as error handling and action logging.
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARContentListRequest extends csARRestRequest
{
    private $subscriber_id;

    private $feed_id;

    /**
     * Start this class.
     * Calls its parent's construct function passing the config id and access token.
     * Sets the subscriber and feed ids.
     *
     * @param [String] $config_id  The id of the config record that is being used in the article retrieval process
     * @param [String] $access_token  The token that will allow access to the REST routes coming from contentstream
     * @param [String] $subscriber_id  The id of the subscriber entity in the contentstream app
     * @param [String] $feed_id  The id of the feed when the content list is being queued up
     */
    public function __construct($config_id, $access_token, $subscriber_id, $feed_id)
    {
        parent::__construct($config_id, $access_token);
        $this->set_subscriber_id($subscriber_id);
        $this->set_feed_id($feed_id);
    }

    /**
     * The getter function of the private var subscriber_id
     *
     * @return String  The subscriber id
     */
    public function get_subscriber_id()
    {
        return $this->subscriber_id;
    }

    /**
     * The setter function of the private var subscriber_id
     *
     * @param [String] $subscriber_id  The value the subscriber_id should be set to
     * @return void
     */
    public function set_subscriber_id($subscriber_id)
    {
        $this->subscriber_id = $subscriber_id;
    }

    /**
     * The getter function of the private var feed_id
     *
     * @return String  The feed id
     */
    public function get_feed_id()
    {
        return $this->feed_id;
    }

    /**
     * The setter function of the private var feed_id
     *
     * @param [String] $feed_id  The value the feed_id should be set to
     * @return void
     */
    public function set_feed_id($feed_id)
    {
        $this->feed_id = $feed_id;
    }

    /**
     * Finishes up the URL for the REST call and returns it
     *
     * @return String  The completed URL string for the getContentList REST call
     */
    public function get_request_url()
    {
        return parent::build_string_with_placeholders(
            csARConfig::CONTENT_LIST_REQUEST_URL,
            array($this->get_subscriber_id(), $this->get_feed_id())
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
        parent::log_request_action(
            csARConfig::CONTENT_LIST_REQUEST_NAME,
            parent::build_string_with_placeholders(
                csARConfig::CONTENT_LIST_REQUEST_SUCCESS_MESSAGE,
                array(count($this->get_response()->content_list), $this->get_feed_id())
            )
        );
    }
}
