<?php
/*
Plugin Name: ContentStream
Plugin URI: https://www.cfemedia.com/technology/contentstream
Description: This plugin lets you quickly and easily retrieve articles from ContentStream for use in your CMS.
Author: CFE Media and Technology
Version: 1.0.0
Author URI: https://www.cfemedia.com/
License: LGPLv3
License URI: http://www.gnu.org/licenses/lgpl-3.0-standalone.html
 */

defined('ABSPATH') or exit;

//define some pathing constants that could not go into the config file
if (!defined('CS1_AR_PATH')) {
    define('CS1_AR_PATH', trailingslashit(plugin_dir_path(__FILE__)));
}

if (!defined('CS1_AR_URL')) {
    define('CS1_AR_URL', trailingslashit(plugin_dir_url(__FILE__)));
}

if (!defined('CS1_AR_BASENAME')) {
    define('CS1_AR_BASENAME', plugin_basename(__FILE__));
}

if (!defined('CS1_AR_VERSION')) {
    define('CS1_AR_VERSION', '1.0.0');
}

//config file
include_once CS1_AR_PATH . 'app/classes/config/class-cs-ar-config.php';
// the custom db tables class
include_once CS1_AR_PATH . 'app/classes/db/class-cs-ar-db-tables.php';
//the custom table crud controllers
include_once CS1_AR_PATH . 'app/classes/db/class-cs-ar-crud-controller.php';
include_once CS1_AR_PATH . 'app/classes/db/class-cs-ar-config-table-controller.php';
include_once CS1_AR_PATH . 'app/classes/db/class-cs-ar-feed-info-table-controller.php';
include_once CS1_AR_PATH . 'app/classes/db/class-cs-ar-log-table-controller.php';
include_once CS1_AR_PATH . 'app/classes/db/class-cs-ar-article-info-table-controller.php';
include_once CS1_AR_PATH . 'app/classes/db/class-cs-ar-article-attachment-info-table-controller.php';
// the custom post type class
include_once CS1_AR_PATH . 'app/classes/posttype/class-cs-ar-post-type.php';
//the cron scheduler
include_once CS1_AR_PATH . 'app/classes/retrieval/class-cs-ar-article-retrieval-cron.php';
// the encryptor
include_once CS1_AR_PATH . 'app/classes/helpers/class-cs-ar-encryptor.php';
// the rest client
include_once CS1_AR_PATH . 'app/classes/rest/class-cs-ar-rest-controller.php';
//the rest requests
include_once CS1_AR_PATH . 'app/classes/rest/class-cs-ar-rest-request.php';
include_once CS1_AR_PATH . 'app/classes/rest/class-cs-ar-access-token-request.php';
include_once CS1_AR_PATH . 'app/classes/rest/class-cs-ar-enabled-feeds-request.php';
include_once CS1_AR_PATH . 'app/classes/rest/class-cs-ar-content-list-request.php';
include_once CS1_AR_PATH . 'app/classes/rest/class-cs-ar-get-article-request.php';
include_once CS1_AR_PATH . 'app/classes/rest/class-cs-ar-remove-from-queue-request.php';
//the cs article parser
include_once CS1_AR_PATH . 'app/classes/retrieval/class-cs-ar-article-parser.php';
//the cs article retriever
include_once CS1_AR_PATH . 'app/classes/retrieval/class-cs-ar-article-retrieval-controller.php';
//settings form and log table that admin displays
include_once CS1_AR_PATH . 'app/classes/admin/class-cs-ar-settings-form.php';
include_once CS1_AR_PATH . 'app/classes/admin/class-cs-ar-log-table-view.php';
include_once CS1_AR_PATH . 'app/classes/admin/class-cs-ar-article-listing-view.php';
// the main admin interface class
include_once CS1_AR_PATH . 'app/classes/admin/class-cs-ar-admin.php';
//plugin activator/deactivator
include_once CS1_AR_PATH . 'app/classes/config/class-cs-ar-plugin-activator.php';

// initialize the pieces of the plugin as global vars
global $cs_ar_db_table_controller;
global $cs_ar_post_type_controller;
global $cs_ar_cron_scheduler;
global $cs_ar_admin;
//create the controllers
$cs_ar_db_table_controller = new csARDBTableController();
$cs_ar_post_type_controller = new csARPostTypeController();
$cs_ar_cron_scheduler = new csARArticleRetrievalCron();
$cs_ar_admin = new csARAdmin();
//init the plugin
$cs_ar_db_table_controller->init();
$cs_ar_post_type_controller->init();
$cs_ar_cron_scheduler->init();
$cs_ar_admin->init();

/**
 * Activate the plugin
 *
 * @return void
 */
function cs_ar_activate_plugin()
{
    if (!current_user_can('activate_plugins')) {
        return;
    }

    $cs_ar_config_table_controller = new csARConfigTableController();
    $cs_ar_activator = new csARPluginActivator();

    if ($cs_ar_config_table_controller->should_cs_article_exist()) {
        $cs_ar_activator->activate_cs_articles();
    }

    $cs_ar_activator->activate_cron_job();
}
//register the activation function
register_activation_hook(__FILE__, 'cs_ar_activate_plugin');
/**
 * Deactivate the plugin
 *
 * @return void
 */
function cs_ar_deactivate_plugin()
{
    if (!current_user_can('activate_plugins')) {
        return;
    }

    $cs_ar_activator = new csARPluginActivator();
    $cs_ar_activator->deactivate_cs_articles();
    $cs_ar_activator->deactivate_cron_job();
}
//register the deactivation function
register_deactivation_hook(__FILE__, 'cs_ar_deactivate_plugin');
