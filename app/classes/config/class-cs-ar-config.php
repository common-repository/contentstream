<?php
/**
 * The class that handles all the constants in the plugin
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARConfig
{
    //The beginning part of the rest api url - found in the file: class-cs-ar-rest-controller.php
    const REST_API_URL = 'https://stream.cfetechnology.com/rest/',
    //ContentStream Article Post Type - found in the file: class-cs-ar-post-type.php
    CS_ARTICLE_POST_TYPE = 'cs_article',
    CS_SETTINGS_SLUG = 'cs_ar_settings',
    CS_LISTING_SLUG = 'cs_article_listing',
    //menu icon for the plugin - found in the file: class-cs-ar-post-type.php
    MENU_ICON_PATH = 'app/images/cs-ar-menu-icon.png',
    TIMEZONE = 'America/Chicago',
    //Example of date formatting for the log table: July 10, 2018, 9:20 AM CDT - found in the file: class-cs-ar-log-table-view.php
    TIMESTAMP_DISPLAY_FORMAT = 'F j, Y, g:i A T',
    //The names of the custom tables the plugin uses - found in the files location the db folder
    CONFIG_TABLE = 'cs_ar_config',
    FEED_INFO_TABLE = 'cs_ar_feed_info',
    LOG_TABLE = 'cs_ar_log',
    ARTICLE_INFO_TABLE = 'cs_ar_article_info',
    ARTICLE_ATTACHMENT_INFO_TABLE = 'cs_ar_article_attachment_info',
    //The placeholder value for the image path in CS articles used by the article parser to find the proper spot to insert the wordpress URLs of images  - found in the file: class-cs-ar-article-parser.php
    //ARTICLE_IMAGE_PATH_PLACEHOLDER = 'syndicationAssets/',
    //The encryption method used by the plugin - found in the file: class-cs-ar-encryptor.php
    ENCRYPT_MEHTOD = 'AES-128-CBC',
    //The beginning of the access token for the REST requests - found in the file: class-cs-ar-rest-controller.php
    ACCESS_TOKEN_BEARER = 'Bearer ',
    REQUEST_CONTENT_TYPE = 'application/json',
    //The regex used to find the placeholders in both the URLs and success messages for the REST requests - found in the request files location the rest folder
    REQUEST_PLACEHOLDER_REGEX = '/{.*?}/',
    //the text for the success status for the request action logging - found in the file: class-cs-ar-rest-request.php
    REQUEST_SUCCESS_STATUS = 'SUCCESS',
    //the text for the error status for the request action logging - found in the file: class-cs-ar-rest-request.php
    REQUEST_ERROR_STATUS = 'ERROR',
    //The name and messages for getting config info - found in file: class-cs-ar-article-retrieval-controller.php
    GET_CONFIG_INFO_REQUEST_NAME = 'getConfigInfo',
    GET_CONFIG_INFO_DISPLAY_NAME = 'Getting config info from database',
    GET_CONFIG_INFO_SUCCESS_MESSAGE = 'Found config info',
    GET_CONFIG_INFO_ERROR_MESSAGE = 'Unable to find config info',
    //The name, class name, url and success message for the access token rest call - found in the file: class-cs-ar-access-token-request.php
    ACCESS_TOKEN_REQUEST_NAME = 'getAccessToken',
    ACCESS_TOKEN_DISPLAY_NAME = 'Generating access token',
    ACCESS_TOKEN_REQUEST_CLASS_NAME = 'csARAccessTokenRequest',
    ACCESS_TOKEN_REQUEST_URL = 'user/accessToken',
    ACCESS_TOKEN_REQUEST_SUCCESS_MESSAGE = 'Access token generated',
    //The name and messages for getting feed info from DB - found in file: class-cs-ar-article-retrieval-controller.php
    GET_FEED_INFO_REQUEST_NAME = 'getSearchInfoFromDB',
    GET_FEED_INFO_DISPLAY_NAME = 'Getting search info from database',
    GET_FEED_INFO_SUCCESS_MESSAGE = 'Found search info to use for article retrieval',
    GET_FEED_INFO_ERROR_MESSAGE = 'Unable to find search info',
    //The name, class name, url and success message for the enabled feeds rest call - found in the file: class-cs-ar-enabled-feeds-request.php
    ENABLED_FEEDS_REQUEST_NAME = 'getSearchInfoFromContentStream',
    ENABLED_FEEDS_DISPLAY_NAME = 'Getting search info from ContentStream',
    ENABLED_FEEDS_REQUEST_CLASS_NAME = 'csAREnabledFeedsRequest',
    ENABLED_FEEDS_REQUEST_URL = 'enabledSearches/subscriber/{subscriber_id}',
    ENABLED_FEEDS_REQUEST_SUCCESS_MESSAGE = 'Found {searchCount} enabled search(es) to use for article retrieval',
    //The name, class name, url and success message for the content list rest call - found in the file: class-cs-ar-content-list-request.php
    CONTENT_LIST_REQUEST_NAME = 'getContentList',
    CONTENT_LIST_DISPLAY_NAME = 'Getting article list from ContentStream',
    CONTENT_LIST_REQUEST_CLASS_NAME = 'csARContentListRequest',
    CONTENT_LIST_REQUEST_URL = 'contentList/subscriber/{subscriber_id}/search/{search_id}',
    CONTENT_LIST_REQUEST_SUCCESS_MESSAGE = 'Found {articleCount} article(s) for the search with id {feedId}',
    //The name, class name, url and success message for the get article rest call - found in the file: class-cs-ar-get-article-request.php
    GET_ARTICLE_REQUEST_NAME = 'getArticle',
    GET_ARTICLE_DISPLAY_NAME = 'Getting article from ContentStream',
    GET_ARTICLE_REQUEST_CLASS_NAME = 'csARGetArticleRequest',
    GET_ARTICLE_REQUEST_URL = 'search/{search_id}/article/{uid}',
    GET_ARTICLE_REQUEST_SUCCESS_MESSAGE = 'Retrieved content and image(s) for the article: {articleTitle}',
    //The name, class name, url and success message for the remove from queue rest call - found in the file: class-cs-ar-remove-from-queue-request.php
    REMOVE_FROM_QUEUE_REQUEST_NAME = 'removeArticleFromQueue',
    REMOVE_FROM_QUEUE_DISPLAY_NAME = 'Removing article from retrieval queue',
    REMOVE_FROM_QUEUE_REQUEST_CLASS_NAME = 'csARRemoveFromQueueRequest',
    REMOVE_FROM_QUEUE_REQUEST_URL = 'search/{search_id}/article/{uid}',
    REMOVE_FROM_QUEUE_REQUEST_SUCCESS_MESSAGE = 'The article, {articleTitle}, was removed from the search queue',
    //Settings form success and error messages - found in the file: class-cs-ar-settings-form.php
    SETTINGS_FORM_SUCCESS_MESSAGE = 'ContentStream settings have been saved!',
    SETTINGS_FORM_ERROR_MESSAGE = 'ContentStream settings could not be saved!',
    //Article Retrieval success and error messages - found in the file: class-cs-ar-settings-form.php
    ARTICLE_RETRIEVAL_SUCCESS_MESSAGE = 'All available articles retrieved from ContentStream!',
    ARTICLE_RETRIEVAL_ERROR_MESSAGE = 'Something went wrong with the retrieval process. Please check your log for more details.',
    //Settings form field error messages - found in the file: class-cs-ar-settings-form.php
    USERNAME_BLANK_ERROR_MESSAGE = 'Username is required.',
    USERNAME_EMAIL_ERROR_MESSAGE = 'Username must be a valid email address.',
    PASSWORD_BLANK_ERROR_MESSAGE = 'Password is required.',
    SUBSCRIBER_ID_BLANK_ERROR_MESSAGE = 'Subscriber ID is required.',
    SUBSCRIBER_ID_NUMBER_ERROR_MESSAGE = 'Subscriber ID must be a number.',
    FREQUENCY_BLANK_ERROR_MESSAGE = 'A selection is required.',
    AMOUNT_BLANK_ERROR_MESSAGE = 'A selection is required.',
    SEARCH_ID_BLANK_ERROR_MESSAGE = 'Search ID is required.',
    SEARCH_ID_NUMBER_ERROR_MESSAGE = 'Search ID must be a number.';
}
