<?php

/**
 * The class that handles the retrieval process for contentstream articles.
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARArticleRetrievalController
{
    private $cs_ar_config_table_controller;
    private $cs_ar_feed_info_table_controller;
    private $cs_ar_log_table_controller;
    private $cs_ar_encryptor;
    public $cs_ar_rest_controller;
    public $cs_ar_article_parser;

    /**
     *  Start this class.
     *  Instantate the db table controllers, the encryptor, the rest controller, and the article parser.
     */
    public function __construct()
    {
        $this->cs_ar_config_table_controller = new csARConfigTableController();
        $this->cs_ar_feed_info_table_controller = new csARFeedInfoTableController();
        $this->cs_ar_log_table_controller = new csARLogTableController();
        $this->cs_ar_encryptor = new csAREncryptor();
        $this->cs_ar_rest_controller = new csARRestController();
        $this->cs_ar_article_parser = new csARArticleParser();
    }

    /**
     * Main function of the plugin.
     * Retrives articles based on the information in the config and feed info tables before passing to the article parser for processing.
     *
     * @return boolean Returns true if the process is a success, false if it failed at some point
     */
    public function retrieve_articles()
    {
        $config_id = 1;
        //get the config info
        $config_info = $this->get_config_info($config_id);
        //if config_info is null, stop the retrieval
        if ($config_info === null) {
            return false;
        }

        //get an access token
        $access_token = $this->get_access_token($config_id, $config_info->cs_ar_config_username, $config_info->cs_ar_config_password);
        //if token is null, stop the retrieval
        if ($access_token === null) {
            return false;
        }

        //get the feed ids to use for article retrieval
        $feed_id_array = $this->get_feed_id_array($config_id, $config_info->cs_ar_config_feed_amount, $config_info->cs_ar_config_subscriber_id, $access_token);
        //if list is empty, stop the retrieval
        if (empty($feed_id_array)) {
            return false;
        }

        foreach ($feed_id_array as $feed_id) {
            //get the list of content that is queued up for the feed
            $content_list = $this->get_content_list($config_id, $access_token, $config_info->cs_ar_config_subscriber_id, $feed_id);
            //if list is empty, stop the retrieval
            if ($content_list !== null) {
                foreach ($content_list as $content_info) {
                    //get article data
                    $article_info = $this->get_article_info($config_id, $access_token, $feed_id, $content_info->uid);
                    //make sure article info is not null
                    if ($article_info !== null) {
                        //parse article data into a wordpress post with custom type and images
                        $this->parse_article($article_info);
                        //remove the article from the queue for the feed
                        $this->remove_article_from_queue($config_id, $access_token, $feed_id, $content_info->uid, $content_info->title);
                    }
                }
            }
        }
        //article retrieval sucessfully completed!
        return true;
    }

    /**
     * Log the action the plugin does when it queries the DB during the retrieval process
     *
     * @param [int] $config_id  The id of the config record that is being used in the article retrieval process
     * @param [String] $action_name  The name of the action that just occured
     * @param [String] $status  Whether the action was successful or errored out
     * @param [String] $message  The message that should be stored in the DB saying what the action did
     * @return void
     */
    public function log_db_action($config_id, $action_name, $status, $message)
    {
        $this->cs_ar_log_table_controller->insert_record(array(
            'cs_ar_log_config_id' => $config_id,
            'cs_ar_log_timestamp' => $this->cs_ar_log_table_controller->generate_time_stamp(),
            'cs_ar_log_action' => $action_name,
            'cs_ar_log_status' => $status,
            'cs_ar_log_results' => $message,
        ));
    }

    /**
     * Get the config info from the DB
     *
     * @param [int] $config_id  The id of the config record that is being used in the article retrieval process
     * @return array  the record from the DB contain the configuration data or null if record is not found
     */
    public function get_config_info($config_id)
    {
        //get the config data from DB
        $config_results = $this->cs_ar_config_table_controller->get_one_record_by_id($config_id);
        $config_info = null;
        $status = csARConfig::REQUEST_ERROR_STATUS;
        $message = csARConfig::GET_CONFIG_INFO_ERROR_MESSAGE;
        //check the data, if false or empty return null to stop the retrieval process
        if ($config_results !== false && !empty($config_results)) {
            $config_info = $config_results[0];
            $status = csARConfig::REQUEST_SUCCESS_STATUS;
            $message = csARConfig::GET_CONFIG_INFO_SUCCESS_MESSAGE;
        }
        //log action
        $this->log_db_action($config_id, csARConfig::GET_CONFIG_INFO_REQUEST_NAME, $status, $message);
        //return the config info
        return $config_info;
    }

    /**
     * Creates an access token request and calls the cs server uses the rest controller to get a token that will be used for the rest of the rest calls
     *
     * @param [int] $config_id  The id of the config record that is being used in the article retrieval process
     * @param [String] $username  The encrypted value that represents the username that the user has inputted into the plugin
     * @param [String] $password  The encrypted value that represents the password that the user has inputted into the plugin
     * @return String  the access token that will be used for the rest of the REST calls or null if request is in error
     */
    public function get_access_token($config_id, $username, $password)
    {
        //create access token request
        $access_token_request = new csARAccessTokenRequest($config_id, $this->cs_ar_encryptor->decrypt($username), $this->cs_ar_encryptor->decrypt($password));
        //send access token request as a POST
        $this->cs_ar_rest_controller->send_post_request($access_token_request);
        //return the token if everything is fine, null if in error
        return !$access_token_request->has_error_occurred() ? $access_token_request->get_response()->access_token : null;
    }

    /**
     * Determine whether the article retrieval process should use the wpdb or rest to get feed ids
     *
     * @param [int] $config_id  The id of the config record that is being used in the article retrieval process
     * @param [String] $feed_amount  The configuration field that tells the plugin whether to use the DB or CS server to get feed ids
     * @param [int] $subscriber_id  The id of the subscriber entity in the contentstream app
     * @param [String] $access_token  The token that will allow access to the REST routes coming from contentstream
     * @return array  A list of ids that will be used to retrieve content
     */
    public function get_feed_id_array($config_id, $feed_amount, $subscriber_id, $access_token)
    {
        //build feed id array: if feed amount is not set to ALL query feed info table, else if feed amount is set to ALL send the enabled feeds request as a GET
        return $feed_amount !== 'ALL' ? $this->get_feed_ids_from_db($config_id) : $this->get_feed_ids_from_cs($config_id, $access_token, $subscriber_id);
    }

    /**
     * Generates an array of ids based on the feed info table in the wpdb
     *
     * @param [int] $config_id  The id of the config record that is being used in the article retrieval process
     * @return array  A list of ids that will be used to retrieve content, could be empty
     */
    public function get_feed_ids_from_db($config_id)
    {
        $feed_id_array = array();
        $status = csARConfig::REQUEST_SUCCESS_STATUS;
        $message = csARConfig::GET_FEED_INFO_SUCCESS_MESSAGE;
        //get records from the db
        $feed_results = $this->cs_ar_feed_info_table_controller->get_records_by_config_id($config_id);
        //return empty array if results from db are false or empty
        if (!$feed_results || empty($feed_results)) {
            $feed_results = array();
            $status = csARConfig::REQUEST_ERROR_STATUS;
            $message = csARConfig::GET_FEED_INFO_ERROR_MESSAGE;
        }

        //loop through records retrieved from the feed info table
        foreach ($feed_results as $feed_info) {
            array_push($feed_id_array, $feed_info->cs_ar_feed_info_search_id);
        }
        //log action
        $this->log_db_action($config_id, csARConfig::GET_FEED_INFO_REQUEST_NAME, $status, $message);
        //return completed array of ids
        return $feed_id_array;
    }

    /**
     * Generates an array of ids based on the enabled feed REST route from the contentstream server
     *
     * @param [int] $config_id  The id of the config record that is being used in the article retrieval process
     * @param [String] $access_token  The token that will allow access to the REST routes coming from contentstream
     * @param [int] $subscriber_id  The id of the subscriber entity in the contentstream app
     * @return array  A list of ids that will be used to retrieve content, could be empty
     */
    public function get_feed_ids_from_cs($config_id, $access_token, $subscriber_id)
    {
        $feed_id_array = array();
        //create a new request
        $cs_ar_enabled_feeds_request = new csAREnabledFeedsRequest($config_id, $access_token, $subscriber_id);
        //send request to cs server
        $this->cs_ar_rest_controller->send_get_request($cs_ar_enabled_feeds_request);
        //if the request is in error, return empty array
        if ($cs_ar_enabled_feeds_request->has_error_occurred()) {
            return $feed_id_array;
        }

        //loop through the results of the request
        foreach ($cs_ar_enabled_feeds_request->get_response()->enabled_searches as $feed_info) {
            array_push($feed_id_array, $feed_info->search_id);
        }

        //return completed array of ids
        return $feed_id_array;
    }

    /**
     * Creates a request that will be used by the REST controller to get a list of the content (article ids and basic info about the article) from the contentstream server
     *
     * @param [int] $config_id  The id of the config record that is being used in the article retrieval process
     * @param [String] $access_token  The token that will allow access to the REST routes coming from contentstream
     * @param [int] $subscriber_id  The id of the subscriber entity in the contentstream app
     * @param [int] $feed_id  The id of the feed when the content list is being queued up
     * @return array  A list of the content (article ids and basic info about the article), could be null
     */
    public function get_content_list($config_id, $access_token, $subscriber_id, $feed_id)
    {
        //create a new request
        $cs_ar_content_list_request = new csARContentListRequest($config_id, $access_token, $subscriber_id, $feed_id);
        //send request to cs server
        $this->cs_ar_rest_controller->send_get_request($cs_ar_content_list_request);
        //return the list of content, includes article ids for the next step in the retrieval process
        //if the request is in error, return null
        return !$cs_ar_content_list_request->has_error_occurred() ? $cs_ar_content_list_request->get_response()->content_list : null;
    }

    /**
     * Creates a request that will be used by the REST controller to get the data and images related to a specific article.
     *
     * @param [int] $config_id  The id of the config record that is being used in the article retrieval process
     * @param [String] $access_token  The token that will allow access to the REST routes coming from contentstream
     * @param [int] $feed_id  The id of the feed when the content list is being queued up
     * @param [int] $uid  The id of the article that is being retrieved
     * @return array  An object array of the article data and images from contentstream, could be null if request is in error
     */
    public function get_article_info($config_id, $access_token, $feed_id, $uid)
    {
        //create a new request
        $cs_ar_get_article_request = new csARGetArticleRequest($config_id, $access_token, $feed_id, $uid);
        //send request to cs server
        $this->cs_ar_rest_controller->send_get_request($cs_ar_get_article_request);
        //return article data
        //if the request is in error, return null
        return !$cs_ar_get_article_request->has_error_occurred() ? $cs_ar_get_article_request->get_response() : null;
    }

    /**
     * Pass the article data into the parser so it can become a wordpress post with the content type cs_article
     *
     * @param [object] $article_info  An object array of the article data and images from contentstream
     * @return void
     */
    public function parse_article($article_info)
    {
        $this->cs_ar_article_parser->parse($article_info);
    }

    /**
     * Creates a request that will be used by the REST controller to remove an article from the queue once the article has been added to wordpress.
     *
     * @param [int] $config_id  The id of the config record that is being used in the article retrieval process
     * @param [String] $access_token  The token that will allow access to the REST routes coming from contentstream
     * @param [int] $feed_id  The id of the feed when the content list is being queued up
     * @param [int] $uid  The id of the article that is being retrieved
     * @param [String] $title  The title of the article that is being retrieved
     * @return void
     */
    public function remove_article_from_queue($config_id, $access_token, $feed_id, $uid, $title)
    {
        //create a new request and send request to cs server as a DELETE to remove the article from search queue
        $this->cs_ar_rest_controller->send_delete_request(new csARRemoveFromQueueRequest($config_id, $access_token, $feed_id, $uid, $title));
    }
}
