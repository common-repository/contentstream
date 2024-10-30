<?php

/**
 * The class that handles the communication between the plugin and the custom table cs_ar_article_attachment_info in the DB.
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARArticleAttachmentInfoTableController extends csARCrudController
{

    /**
     * Initialize the handler.
     * Finish the table name for future use.
     */
    public function __construct()
    {
        parent::__construct(csARConfig::ARTICLE_ATTACHMENT_INFO_TABLE);
    }

    /**
     * Insert a row into the article attachment info table in the article retriever plugin
     *
     * @param [array] $values  the data the user has entered for their article info record
     * @return boolean true if successful, false if error
     */
    public function insert_record($values)
    {
        return parent::insert_record_into_table(parent::get_table_name(), $values, array('%d', '%d', '%s', '%d'));
    }

    /**
     * Get one record from the article attachment info table based on the id provided
     *
     * @param [String] $id  The id of the article info record requested
     * @return array  an object array of the record and its data
     */
    public function get_one_record_by_id($id)
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name WHERE cs_ar_article_attachment_info_id = %d", array($id));
    }

    /**
     * Get one record from the article attachment info table based on the post id and attachment id provided
     *
     * @param [String] $id  The id of the article info record requested
     * @return array  an object array of the record and its data
     */
    public function get_one_records_by_post_id_and_attachement_id($id, $attachement_id)
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name WHERE cs_ar_article_attachment_info_post_id = %d AND cs_ar_article_attachment_info_attachment_id = %d", array($id, $attachement_id));
    }

    /**
     * Get all records from the article attachment info table
     *
     * @return array  an object array of the records and their data
     */
    public function get_all_records()
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name ");
    }

    /**
     * Get all record from the article attachment info table based on the post id provided
     *
     * @param [String] $id  The id of the article info record requested
     * @return array  an object array of the record and its data
     */
    public function get_all_records_by_post_id($id)
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name WHERE cs_ar_article_attachment_info_post_id = %d", array($id));
    }

    /**
     * Get all record from the article attachment info table based on the post id and attachment type provided
     *
     * @param [String] $id  The id of the article info record requested
     * @return array  an object array of the record and its data
     */
    public function get_all_records_by_post_id_and_attachement_type($id, $attachement_type)
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name WHERE cs_ar_article_attachment_info_post_id = %d AND cs_ar_article_attachment_info_attachment_type = %s", array($id, $attachement_type));
    }

    /**
     * Get all records from the article attachment info table by their deleted status
     *
     * @param [int] $is_deleted  Tells the query get articles that are deleted or not. 1 means deleted, 0 means not deleted
     * @return array  an object array of the records and their data
     */
    public function get_all_records_by_is_deleted($is_deleted)
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name WHERE cs_ar_article_attachment_info_is_deleted = %d", array($is_deleted));
    }

    /**
     * Update a row with new data in the article attachment info table in the article retriever plugin
     *
     * @param [int] $id  The id of the article info record that should be updated
     * @param [array] $values  the data the user has entered for their article info record
     * @return boolean true if successful, false if error
     */
    public function update_record($id, $values)
    {
        return parent::update_record_in_table(parent::get_table_name(), $values, array('cs_ar_article_attachment_info_id' => $id), array('%d', '%d', '%d', '%s', '%d'), array('%d'));
    }

    /**
     * Delete a row from the article attachment info table in the article retriever plugin
     *
     * @param [int] $id  The id of the article info record that should be deleted
     * @return boolean true if successful, false if error
     */
    public function delete_record($id)
    {
        return parent::delete_record_from_table(parent::get_table_name(), array('cs_ar_article_attachment_info_id' => $id), array('%d'));
    }
}
