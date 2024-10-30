<?php

/**
 * The class that handles the communication between the plugin and the custom table cs_ar_feed_info in the DB.
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARFeedInfoTableController extends csARCrudController
{

    /**
     * Initialize the Controller.
     * Finish the table name for future use.
     */
    public function __construct()
    {
        parent::__construct(csARConfig::FEED_INFO_TABLE);
    }
    /**
     * Insert a row into the feed info table in the article retriever plugin
     *
     * @param [array] $values  the data the user has entered for their feed info record
     * @return boolean true if successful, false if error
     */
    public function insert_record($values)
    {
        return parent::insert_record_into_table(parent::get_table_name(), $values, array('%d', '%d'));
    }

    /**
     * Get one record from the feed info table based on the id provided
     *
     * @param [String] $id  The id of the feed info record requested
     * @return array  an object array of the record and its data
     */
    public function get_one_record_by_id($id)
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name WHERE cs_ar_feed_info_id = %d", array($id));
    }

    /**
     * Get multiple records from the feed info table by config id provided
     *
     * @param [String] $id  The config id of the feed info record or records requested
     * @return array  an object array of the records and their data
     */
    public function get_records_by_config_id($id)
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name WHERE cs_ar_feed_info_config_id = %d", array($id));
    }

    /**
     * Get all records from the feed info table
     *
     * @return array  an object array of the records and their data
     */
    public function get_all_records()
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name ", array());
    }

    /**
     * Update a row with new data in the feed info table in the article retriever plugin
     *
     * @param [int] $id  The id of the feed info record requested
     * @param [array] $values  the data the user has entered for their feed info record
     * @return boolean true if successful, false if error
     */
    public function update_record($id, $values)
    {
        return parent::update_record_in_table(parent::get_table_name(), $values, array('cs_ar_feed_info_id' => $id), array('%d', '%d', '%d'), array('%d'));
    }

    /**
     * Delete a row from the feed info table in the article retriever plugin
     *
     * @param [int] $id  The id of the feed info record that should be deleted
     * @return boolean true if successful, false if error
     */
    public function delete_record($id)
    {
        return parent::delete_record_from_table(parent::get_table_name(), array('cs_ar_feed_info_id' => $id), array('%d'));
    }
}
