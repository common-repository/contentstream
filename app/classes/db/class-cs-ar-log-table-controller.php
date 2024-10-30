<?php

/**
 * The class that handles the communication between the plugin and the custom table cs_ar_log in the DB.
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARLogTableController extends csARCrudController
{

    /**
     * Initialize the controller.
     * Finish the table name for future use.
     */
    public function __construct()
    {
        parent::__construct(csARConfig::LOG_TABLE);
    }
    /**
     * Insert a row into the log table in the article retriever plugin
     *
     * @param [array] $values  the data the user has entered for their log record
     * @return boolean true if successful, false if error
     */
    public function insert_record($values)
    {
        return parent::insert_record_into_table(parent::get_table_name(), $values, array('%d', '%s', '%s', '%s', '%s'));
    }

    /**
     * Get one record from the log table based on the id provided
     *
     * @param [String] $id  The id of the log record requested
     * @return array  an object array of the record and its data
     */
    public function get_one_record_by_id($id)
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name WHERE cs_ar_log_id = %d", array($id));
    }

    /**
     * Get multiple records from the log table by config id provided
     *
     * @param [String] $id  The config id of the log record or records requested
     * @return array  an object array of the records and their data
     */
    public function get_records_by_config_id($id)
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name WHERE cs_ar_log_config_id = %d", array($id));
    }

    /**
     * Get the total number of records for a particular config id
     *
     * @param [String] $id  The config id of the log record or records requested
     * @return integer  the total number of records found
     */
    public function get_number_of_records_by_config_id($id)
    {
        $table_name = parent::get_table_name();
        $result = parent::get_records_from_table($table_name, "SELECT DISTINCT count(cs_ar_log_id) AS total FROM $table_name WHERE cs_ar_log_config_id = %d", array($id));
        return $result !== false && !empty($result) ? $result[0]->total : 0;
    }

    /**
     * Get multiple records from the log table by config id provided where an offset and limit can be set to change what results are returned
     *
     * @param [String] $id  The config id of the log record or records requested
     * @param integer $offset  Tells the query at what result it should begin at, default is 0
     * @param integer $limit  Tells the query how many results it should return, default is 20
     * @return array  an object array of the records and their data
     */
    public function get_records_by_config_id_with_offset_and_limit($id, $offset = 0, $limit = 20)
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name WHERE cs_ar_log_config_id = %d ORDER BY cs_ar_log_id DESC LIMIT %d, %d", array($id, $offset, $limit));
    }

    /**
     * Get all records from the log table
     *
     * @return array  an object array of the records and their data
     */
    public function get_all_records()
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name ", array());
    }

    /**
     * Update a row with new data in the log table in the article retriever plugin
     *
     * @param [int] $id  The id of the log record requested
     * @param [array] $values  the data the user has entered for their log record
     * @return boolean true if successful, false if error
     */
    public function update_record($id, $values)
    {
        return parent::update_record_in_table(parent::get_table_name(), $values, array('cs_ar_log_id' => $id), array('%d', '%d', '%s', '%s', '%s', '%s'), array('%d'));
    }

    /**
     * Delete a row from the log table in the article retriever plugin
     *
     * @param [int] $id  The id of the log record that should be deleted
     * @return boolean true if successful, false if error
     */
    public function delete_record($id)
    {
        return parent::delete_record_from_table(parent::get_table_name(), array('cs_ar_log_id' => $id), array('%d'));
    }

    /**
     * Generates a properly formatted timestamp for the log. Should be used when creating the values array for inserting/updating log records.
     *
     * @return String  A stringify date that is standard tothe timestamp field in the log table
     */
    public function generate_time_stamp()
    {
        return date("Y-m-d H:i:s", time());
    }
}
