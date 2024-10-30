<?php

/**
 * The class that handles the cron functionality for contentstream plugin.
 * Note: If cron jobs aren't working, try removing 'define(DISABLE_WP_CRON,true);' from your wp-config.php.
 * If cron jobs are still not working, try adding 'define('ALTERNATE_WP_CRON', true);' to your wp-config.php
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARArticleRetrievalCron
{

    /**
     *  Start this class.
     */
    public function __construct()
    {
        //nothing yet...
    }

    /**
     * Adds a filter to wordpress to add the custom intervals needed by the contentstream article retriever and adds an action for contentstream's cron job
     *
     * @return void
     */
    public function init()
    {
        global $wpdb;

        add_filter('cron_schedules', array($this, 'add_cs_ar_cron_intervals'));

        $blog_ids = (!is_multisite()) ? array(1) : $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        foreach ($blog_ids as $blog_id) {
            $prefix = ((int) $blog_id === 1) ? 'wp_' : 'wp_' . $blog_id . '_';
            add_action($prefix . 'cs_ar_cron_job', array($this, 'execute_cs_ar_cron_job'));
        }

    }

    /**
     * Adds the custom interval to wordpress's schedules object
     *
     * @param [object] $schedules  Wordpress's schedule object that controls the intervals taht cron jobs use
     * @return void
     */
    public function add_cs_ar_cron_intervals($schedules)
    {
        $schedules['weekly'] = array(
            'interval' => 604800, //in seconds
            'display' => esc_html__('Once Weekly'),
        );
        $schedules['monthly'] = array(
            'interval' => 2419200, //in seconds
            'display' => esc_html__('Once Monthly'),
        );
        return $schedules;
    }

    /**
     * schedule a job in Wordpress's cron
     *
     * @param [String] $interval  The interval the job should execute at
     * @return void
     */
    public function schedule($interval)
    {
        global $wpdb;

        $this->unschedule();

        if ($interval !== 'never' && $interval !== '') {
            wp_schedule_event(time(), $interval, $wpdb->prefix . 'cs_ar_cron_job');
        }
    }

    /**
     * unschedule a job from Wordpress's cron
     *
     * @return void
     */
    public function unschedule()
    {
        global $wpdb;
        if (wp_next_scheduled($wpdb->prefix . 'cs_ar_cron_job') !== false) {
            wp_clear_scheduled_hook($wpdb->prefix . 'cs_ar_cron_job');
        }
    }

    /**
     * The executable function for the contentstream article retriever's custom cron job
     *
     * @return boolean
     */
    public function execute_cs_ar_cron_job()
    {
        //$cs_ar_config_table_controller = new csARConfigTableController();
        $cs_ar_article_retrieval_controller = new csARArticleRetrievalController();
        return $cs_ar_article_retrieval_controller->retrieve_articles();
    }
}
