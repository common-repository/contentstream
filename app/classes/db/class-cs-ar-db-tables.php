<?php

/**
 * The class that handles the creation and deletion of the custom tables for the ContentStream Article Retriever
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */

class csARDBTableController
{
    public $db;

    /**
     *  Start this class
     *
     * @global object $cs_article_retriever_db_table_controller
     */
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * Initializes the controller and creates the tables
     *
     * @return void
     */
    public function init()
    {
        $this->create_all_tables();
    }

    /**
     * Creates all custom tables that the plugin needs to function properly
     *
     * @return void
     */
    public function create_all_tables()
    {
        $this->create_config_table();
        $this->create_feed_info_table();
        $this->create_log_table();
        $this->create_article_info_table();
        $this->create_article_attachment_info_table();
    }

    /**
     * Drops all custom tables
     *
     * @param string $prefix  The prefix the DB uses to id its tables, needed to remove tables from multisite setups
     * @param boolean $delete_articles  Tells the plugin whether or not to delte the article info table upon uninstall
     * @return void
     */
    public function drop_all_tables($prefix = '', $delete_articles = true)
    {
        $this->drop_log_table($prefix);
        $this->drop_feed_info_table($prefix);
        $this->drop_config_table($prefix);
        if ($delete_articles) {
            $this->drop_article_info_table($prefix);
            $this->drop_article_attachment_info_table($prefix);
        }
    }

    /**
     * Create the config table for the article retriever.
     * Tables contains the following fields: id, username, password, feed_amount, retrieval_frequency
     *
     * @return void
     */
    public function create_config_table()
    {
        $table = csARConfig::CONFIG_TABLE;

        $fields = " cs_ar_config_id INT AUTO_INCREMENT,
            cs_ar_config_username BLOB,
            cs_ar_config_password BLOB,
            cs_ar_config_feed_amount TEXT,
            cs_ar_config_retrieval_frequency TEXT,
            cs_ar_config_subscriber_id INT,
            cs_ar_config_use_custom_template INT,
            cs_ar_config_delete_content_on_uninstall INT,
            cs_ar_config_use_on_home_page INT,
            cs_ar_config_use_in_search INT,
            cs_ar_config_has_archive_page INT,
            cs_ar_config_use_custom_post_type INT,
            CONSTRAINT PRIMARY KEY (cs_ar_config_id),
            INDEX subscriber_id (cs_ar_config_subscriber_id) ";

        $this->create_table($table, $fields);
    }

    /**
     * Drops the config table for the article retriever.
     *
     * @param [String] $prefix  The prefix the DB uses to id its tables, needed to remove tables from multisite setups
     * @return boolean returns true if successful, false if not
     */
    public function drop_config_table($prefix)
    {
        return $this->drop_table($prefix, csARConfig::CONFIG_TABLE);
    }

    /**
     * Create the feed info table for the article retriever.
     * Tables contains the following fields: id, config_id, feed_id
     *
     * @return void
     */
    public function create_feed_info_table()
    {
        $table = csARConfig::FEED_INFO_TABLE;

        $fields = " cs_ar_feed_info_id INT AUTO_INCREMENT,
        cs_ar_feed_info_config_id INT,
        cs_ar_feed_info_search_id INT,
        CONSTRAINT PRIMARY KEY (cs_ar_feed_info_id),
        INDEX config_id (cs_ar_feed_info_config_id),
        INDEX search_id (cs_ar_feed_info_search_id) ";

        $this->create_table($table, $fields);
    }

    /**
     * Drops the feed info table for the article retriever.
     *
     * @param [String] $prefix  The prefix the DB uses to id its tables, needed to remove tables from multisite setups
     * @return boolean returns true if successful, false if notoid
     */
    public function drop_feed_info_table($prefix)
    {
        return $this->drop_table($prefix, csARConfig::FEED_INFO_TABLE);
    }

    /**
     * Create the log table for the article retriever.
     * Tables contains the following fields: id, config_id, timestamp, action, status, results
     *
     * @return void
     */
    public function create_log_table()
    {
        $table = csARConfig::LOG_TABLE;

        $fields = " cs_ar_log_id INT AUTO_INCREMENT,
        cs_ar_log_config_id INT,
        cs_ar_log_timestamp DATETIME,
        cs_ar_log_action TEXT,
        cs_ar_log_status TEXT,
        cs_ar_log_results TEXT,
        CONSTRAINT PRIMARY KEY (cs_ar_log_id),
        INDEX config_id (cs_ar_log_config_id),
        INDEX timestamp (cs_ar_log_timestamp) ";

        $this->create_table($table, $fields);
    }

    /**
     * Drops the feed info table for the article retriever.
     *
     * @param [String] $prefix  The prefix the DB uses to id its tables, needed to remove tables from multisite setups
     * @return boolean returns true if successful, false if not
     */
    public function drop_log_table($prefix)
    {
        return $this->drop_table($prefix, csARConfig::LOG_TABLE);
    }

    /**
     * Create the article_info table for the article retriever.
     * Tables contains the following fields: id, post_id, uid, download_number, initial_timestamp, and update_timestamp
     *
     * @return void
     */
    public function create_article_info_table()
    {
        $table = csARConfig::ARTICLE_INFO_TABLE;

        $fields = " cs_ar_article_info_id INT AUTO_INCREMENT,
            cs_ar_article_info_config_id INT,
            cs_ar_article_info_post_id INT,
            cs_ar_article_info_uid INT,
            cs_ar_article_info_download_number INT,
            cs_ar_article_info_is_deleted INT,
            cs_ar_article_info_initial_timestamp DATETIME,
            cs_ar_article_info_update_timestamp DATETIME,
            CONSTRAINT PRIMARY KEY (cs_ar_article_info_id),
            INDEX config_id (cs_ar_article_info_config_id),
            INDEX post_id (cs_ar_article_info_post_id),
            INDEX uid (cs_ar_article_info_uid) ";

        $this->create_table($table, $fields);
    }

    /**
     * Drops the article_info table for the article retriever.
     *
     * @param [String] $prefix  The prefix the DB uses to id its tables, needed to remove tables from multisite setups
     * @return boolean returns true if successful, false if not
     */
    public function drop_article_info_table($prefix)
    {
        return $this->drop_table($prefix, csARConfig::ARTICLE_INFO_TABLE);
    }

    /**
     * Create the article_info table for the article retriever.
     * Tables contains the following fields: id, post_id, uid, download_number, initial_timestamp, and update_timestamp
     *
     * @return void
     */
    public function create_article_attachment_info_table()
    {
        $table = csARConfig::ARTICLE_ATTACHMENT_INFO_TABLE;

        $fields = " cs_ar_article_attachment_info_id INT AUTO_INCREMENT,
            cs_ar_article_attachment_info_post_id INT,
            cs_ar_article_attachment_info_attachment_id INT,
            cs_ar_article_attachment_info_attachment_type TEXT,
            cs_ar_article_attachment_info_is_deleted INT,
            CONSTRAINT PRIMARY KEY (cs_ar_article_attachment_info_id),
            INDEX post_id (cs_ar_article_attachment_info_post_id),
            INDEX attachment_id (cs_ar_article_attachment_info_attachment_id) ";

        $this->create_table($table, $fields);
    }

    /**
     * Drops the article_info table for the article retriever.
     *
     * @param [String] $prefix  The prefix the DB uses to id its tables, needed to remove tables from multisite setups
     * @return boolean returns true if successful, false if not
     */
    public function drop_article_attachment_info_table($prefix)
    {
        return $this->drop_table($prefix, csARConfig::ARTICLE_ATTACHMENT_INFO_TABLE);
    }

    /**
     * Generic create function for db tables
     * Note: Cannot be tested directly because of how Wordpress's unit test config sets up DB tables as temporary tables
     *
     * @param [String] $table  the name of tha table that should be created without the prefix
     * @param [String] $fields  the fields that make up the table and how they should behave
     * @return void
     */
    public function create_table($table, $fields)
    {
        $table_name = $this->db->prefix . $table;
        $charset_collate = $this->db->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name ( $fields ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Generic drop function for db tables
     * Note: Cannot be tested directly because of how Wordpress's unit test config sets up DB tables as temporary tables
     *
     * @param [String] $prefix  The prefix the DB uses to id its tables, needed to remove tables from multisite setups
     * @param [String] $table  The name of the table that should be deleted
     * @return boolean returns true if successful, false if not
     */
    public function drop_table($prefix, $table)
    {
        if ($prefix === '') {
            $prefix = $this->db->prefix;
        }

        $table_name = $prefix . $table;
        $sql = "DROP TABLE IF EXISTS $table_name";
        return $this->db->query($sql);
    }
}
