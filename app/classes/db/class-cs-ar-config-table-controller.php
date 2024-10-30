<?php

/**
 * The class that handles the communication between the plugin and the custom table cs_ar_config in the DB.
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARConfigTableController extends csARCrudController
{

    /**
     * Initialize the handler.
     * Finish the table name for future use.
     */
    public function __construct()
    {
        parent::__construct(csARConfig::CONFIG_TABLE);
    }

    /**
     * Insert a row into the config table in the article retriever plugin
     *
     * @param [array] $values  the data the user has entered for their config record
     * @return boolean true if successful, false if error
     */
    public function insert_record($values)
    {
        return parent::insert_record_into_table(parent::get_table_name(), $values, array('%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d'));
    }

    /**
     * Get one record from the config table based on the id provided
     *
     * @param [String] $id  The id of the config record requested
     * @return array  an object array of the record and its data
     */
    public function get_one_record_by_id($id)
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name WHERE cs_ar_config_id = %d", array($id));
    }

    /**
     * Get all records from the config table
     *
     * @return array  an object array of the records and their data
     */
    public function get_all_records()
    {
        $table_name = parent::get_table_name();
        return parent::get_records_from_table($table_name, "SELECT DISTINCT * FROM $table_name ");
    }

    /**
     * Checks to see if cs_article should be created
     *
     * @return boolean  Should return true if the config record's cs_ar_config_use_custom_post_type field is set to 1, else return false
     */
    public function should_cs_article_exist()
    {
        $config_info = $this->get_one_record_by_id(1);
        if ($config_info !== false && !empty($config_info)) {
            return $config_info[0]->cs_ar_config_use_custom_post_type === '1' ? true : false;
        }

        return '';
    }

    /**
     * Checks to see if the post with the type cs_article should use the plugin's custom template
     *
     * @return boolean  Should return true if the config record's cs_ar_config_use_custom_template field is set to 1, else return false
     */
    public function should_cs_article_use_custom_template()
    {
        $config_info = $this->get_one_record_by_id(1);
        if ($config_info !== false && !empty($config_info)) {
            return $config_info[0]->cs_ar_config_use_custom_template === '1' ? true : false;
        }

        return false;
    }

    /**
     * Checks to see if contentstream article should appear on the home page
     *
     * @return boolean  Should return true if the config record's cs_ar_config_use_on_home_page field is set to 1, else return false
     */
    public function should_cs_article_show_on_home_page()
    {
        $config_info = $this->get_one_record_by_id(1);
        if ($config_info !== false && !empty($config_info)) {
            return $config_info[0]->cs_ar_config_use_on_home_page === '1' ? true : false;
        }

        return false;
    }

    /**
     * Checks to see if contentstream article should appear in the search
     *
     * @return boolean  Should return true if the config record's cs_ar_config_use_in_search field is set to 1, else return false
     */
    public function should_cs_article_show_in_search()
    {
        $config_info = $this->get_one_record_by_id(1);
        if ($config_info !== false && !empty($config_info)) {
            return $config_info[0]->cs_ar_config_use_in_search === '1' ? false : true;
        }

        return true;
    }

    /**
     * Checks to see if contentstream article should have an archive page
     *
     * @return boolean  Should return true if the config record's cs_ar_config_has_archive_page field is set to 1, else return false
     */
    public function should_cs_article_have_archive_page()
    {
        $config_info = $this->get_one_record_by_id(1);
        if ($config_info !== false && !empty($config_info)) {
            return $config_info[0]->cs_ar_config_has_archive_page === '1' ? true : false;
        }

        return false;
    }

    /**
     * Update a row with new data in the config table in the article retriever plugin
     *
     * @param [int] $id  The id of the config record that should be updated
     * @param [array] $values  the data the user has entered for their config record
     * @return boolean true if successful, false if error
     */
    public function update_record($id, $values)
    {
        return parent::update_record_in_table(parent::get_table_name(), $values, array('cs_ar_config_id' => $id), array('%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d'), array('%d'));
    }

    /**
     * Delete a row from the config table in the article retriever plugin
     *
     * @param [int] $id  The id of the config record that should be deleted
     * @return boolean true if successful, false if error
     */
    public function delete_record($id)
    {
        return parent::delete_record_from_table(parent::get_table_name(), array('cs_ar_config_id' => $id), array('%d'));
    }
}
