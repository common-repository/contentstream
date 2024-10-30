<?php

/**
 * The class that handles the uninstallation of the plugin, should only be called by the uninstall file
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARPluginUninstaller
{
    public $cs_ar_cron_scheduler = '';
    public $cs_ar_db_table_controller = '';
    public $cs_ar_post_type_controller = '';

    /**
     *  Initialize the class
     */
    public function __construct()
    {
        $this->cs_ar_cron_scheduler = new csARArticleRetrievalCron();
        $this->cs_ar_db_table_controller = new csARDBTableController();
        $this->cs_ar_post_type_controller = new csARPostTypeController();
    }

    /**
     * The main uninstalling function of the plugin
     *
     * @return void
     */
    public function uninstall_plugin()
    {
        $blog_ids = (!$this->check_for_multisite()) ? array(1) : $this->get_blog_ids();
        foreach ($blog_ids as $blog_id) {
            $this->remove_plugin_data_from_site($blog_id);
        }
        $this->switch_to_blog_by_id(get_current_blog_id());
    }

    /**
     * Removes the custom tables, cron jobs, and content from an individual site
     *
     * @param [String] $blog_id  The id of the site that the plugin should go through
     * @return void
     */
    public function remove_plugin_data_from_site($blog_id)
    {
        $this->switch_to_blog_by_id($blog_id);
        $config = $this->get_config_record();
        $this->unschedule_cron_job();
        $prefix = ((int) $blog_id === 1) ? 'wp_' : 'wp_' . $blog_id . '_';
        $delete_content = ((int) $config->cs_ar_config_delete_content_on_uninstall === 1) ? true : false;
        if ($delete_content) {
            $this->delete_content_from_wordpress();
        }
        $this->delete_custom_tables_from_DB($prefix, $delete_content);
    }

    /**
     * Checks to see if wordpress is set up for multisite
     *
     * @return boolean  function will return true if wordpress is set up with multiple sites, false if wordpress is not set up like that
     */
    public function check_for_multisite()
    {
        return is_multisite();
    }

    /**
     * @codeCoverageIgnore
     * Switch between sites in a multisite setup of wordpress
     *
     * @param [String] $blog_id  The id of the site that the plugin should go through
     * @return void
     */
    public function switch_to_blog_by_id($blog_id)
    {
        if ($this->check_for_multisite()) {
            switch_to_blog($blog_id);
        }

    }

    /**
     * @codeCoverageIgnore
     * Gets a list of blog ids when in a mutlisite setup of wordpress
     *
     * @return array  the list of site ids
     */
    public function get_blog_ids()
    {
        global $wpdb;
        return $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    }

    /**
     * Get the config record from custom table
     *
     * @return array  the config record
     */
    public function get_config_record()
    {
        $cs_ar_config_table_controller = new csARConfigTableController();
        $config_record = $cs_ar_config_table_controller->get_one_record_by_id(1);
        return ($config_record !== false && !empty($config_record)) ? $config_record[0] : (object) array('cs_ar_config_delete_content_on_uninstall' => 0);
    }

    /**
     * Unschedule the plugin's cron job
     *
     * @return void
     */
    public function unschedule_cron_job()
    {
        $this->cs_ar_cron_scheduler->unschedule();
    }

    /**
     * Delete the custom tables from wordpress DB
     *
     * @param [String] $prefix  The beginning of tables name that identify them as part of a certain site in a multisite setup
     * @param [String] $delete_content  Whether or not the custom article info tables should be deleted
     * @return void
     */
    public function delete_custom_tables_from_DB($prefix, $delete_content)
    {
        $this->cs_ar_db_table_controller->drop_all_tables($prefix, $delete_content);
    }

    /**
     * Delete articles and their related data, author and images, from wordpress
     *
     * @return void
     */
    public function delete_content_from_wordpress()
    {
        $this->cs_ar_post_type_controller->delete_cs_articles();
    }
}
