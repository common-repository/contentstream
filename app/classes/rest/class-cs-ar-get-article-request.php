<?php

/**
 * The class that handles the request data and response for the get article REST route for contentstream.
 * This class is extended from the base class csARRestRequest that handles the access token and config id fields as well as error handling and action logging.
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARGetArticleRequest extends csARRestRequest
{
    private $feed_id;

    private $uid;

    /**
     * Start this class.
     * Calls its parent's construct function passing the config id and access token.
     * Sets the feed id and uid.
     *
     * @param [String] $config_id  The id of the config record that is being used in the article retrieval process
     * @param [String] $access_token  The token that will allow access to the REST routes coming from contentstream
     * @param [String] $feed_id  The id of the feed when the content list is being queued up
     * @param [String] $uid  The id of the article that is being retrieved
     */
    public function __construct($config_id, $access_token, $feed_id, $uid)
    {
        parent::__construct($config_id, $access_token);
        $this->set_feed_id($feed_id);
        $this->set_uid($uid);
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
     * @param [String] $feed_id  The value the feed id should be set to
     * @return void
     */
    public function set_feed_id($feed_id)
    {
        $this->feed_id = $feed_id;
    }

    /**
     * The getter function of the private var uid
     *
     * @return String the uid (i.e. the id of the article)
     */
    public function get_uid()
    {
        return $this->uid;
    }

    /**
     * The setter function of the private var uid
     *
     * @param [String] $uid  The value the uid should be set to
     * @return void
     */
    public function set_uid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * Finishes up the URL for the REST call and returns it
     *
     * @return String  The completed URL string for the getArticle REST call
     */
    public function get_request_url()
    {
        return parent::build_string_with_placeholders(
            csARConfig::GET_ARTICLE_REQUEST_URL,
            array($this->get_feed_id(), $this->get_uid())
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
            csARConfig::GET_ARTICLE_REQUEST_NAME,
            parent::build_string_with_placeholders(
                csARConfig::GET_ARTICLE_REQUEST_SUCCESS_MESSAGE,
                array($this->get_response()->title)
            )
        );
    }
}
