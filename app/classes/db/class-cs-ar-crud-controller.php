<?php

/**
 * The class that handles the communication between the plugin and the custom tables in the DB.
 * Handles getting, inserting, updating, and deleting data from the cs_ar_config, cs_ar_feed_info, and cs_ar_log tables
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARCrudController
{
    private $table_name = '';

    private $config_table_name = '';
    private $feed_info_table_name = '';
    private $log_table_name = '';
    private $article_info_table_name = '';
    private $article_attachement_info_table_name = '';

    private $db;

    /**
     * Initialize the Controller.
     * Set the db to use.
     * Finish the table names for future use.
     * Sets its child's table name.
     */
    public function __construct($table)
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->config_table_name = $this->db->prefix . csARConfig::CONFIG_TABLE;
        $this->feed_info_table_name = $this->db->prefix . csARConfig::FEED_INFO_TABLE;
        $this->log_table_name = $this->db->prefix . csARConfig::LOG_TABLE;
        $this->article_info_table_name = $this->db->prefix . csARConfig::ARTICLE_INFO_TABLE;
        $this->article_attachement_info_table_name = $this->db->prefix . csARConfig::ARTICLE_ATTACHMENT_INFO_TABLE;
        $this->set_table_name($table);
    }

    /**
     * Getter function for the table name variable
     *
     * @return String the full name of table
     */
    public function get_table_name()
    {
        return $this->table_name;
    }

    /**
     * Setter function for the table name variable
     *
     * @param [String] $table  the root name of the table the controller will use
     * @return void
     */
    public function set_table_name($table)
    {
        $this->table_name = $this->db->prefix . $table;
    }

    /**
     * Security function that checks if the table name being submitted into the query functions is a part of the plugin or not
     *
     * @param [String] $table_name  the name of the table the controller wants to use
     * @return boolean  true if the table name is one of the plugin's tables, false if the table name is not
     */
    public function table_check($table_name)
    {
        return ($table_name === $this->config_table_name ||
            $table_name === $this->feed_info_table_name ||
            $table_name === $this->log_table_name ||
            $table_name === $this->article_info_table_name ||
            $table_name === $this->article_attachement_info_table_name) ? true : false;
    }

    /**
     * Generic insert function to add a row to one of the custom tables in the article retriever plugin
     *
     * @param [String] $table_name  the name of the table the controller wants to use
     * @param [array] $values  A key-value pair array of the data that will make up the record
     * @param [array] $format  A format of the data for the prepared statement
     * @return boolean true if successful, false if error
     */
    public function insert_record_into_table($table_name, $values, $format)
    {
        return $this->table_check($table_name) === true ? $this->db->insert($table_name, $values, $format) : false;
    }

    /**
     * Generic select function to get records from one of the custom tables in the article retriever plugin
     *
     * @param [String] $table_name the name of the table the controller wants to use
     * @param [String] $query  the select statemet for get a record or list of records
     * @param array $values  An array values to use in the query
     * @return array an object or array of objects based on the rows from the table queryed if successful, false if in error
     */
    public function get_records_from_table($table_name, $query, $values = array())
    {
        if (empty($values)) {
            return $this->table_check($table_name) === true ? $this->db->get_results($query) : false;
        } else {
            return $this->table_check($table_name) === true ? $this->db->get_results($this->db->prepare($query, $values)) : false;
        }
    }

    /**
     * Generic update function to update a row with new data in one of the custom tables in the article retriever plugin
     *
     * @param [String] $table_name  the name of the table the controller wants to use
     * @param [array] $values  A key-value pair array of the data that will make up the record
     * @param [array] $where  A key-value pair array of the where caluse data that will target the correct record
     * @param [array] $format  A format of the data for the prepared statement
     * @param [array] $where_format  A format of the where clause data for the prepared statement
     * @return boolean true if successful, false if error
     */
    public function update_record_in_table($table_name, $values, $where, $format, $where_format)
    {
        return $this->table_check($table_name) === true ? $this->db->update($table_name, $values, $where, $format, $where_format) : false;
    }

    /**
     * Generic delete function to remove a row from one of the custom tables in the article retriever plugin
     *
     * @param [String] $table_name  the name of the table the controller wants to use
     * @param [array] $where  A key-value pair array of the where caluse data that will target the correct record
     * @param [array] $where_format  A format of the where clause data for the prepared statement
     * @return boolean true if successful, false if error
     */
    public function delete_record_from_table($table_name, $where, $where_format)
    {
        return $this->table_check($table_name) === true ? $this->db->delete($table_name, $where, $where_format) : false;
    }
}
