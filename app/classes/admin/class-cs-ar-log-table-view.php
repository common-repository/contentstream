<?php

/**
 * The class that handles the log table for the ContentStream Article Retriever
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARLogTableView
{
    public $cs_ar_log_table_controller;
    private $limit = 20;

    /**
     *  Initialize the class and the log table controller
     */
    public function __construct()
    {
        $this->cs_ar_log_table_controller = new csARLogTableController();
    }

    /**
     * Main display function for the table view
     *
     * @return boolean  This return value is for testing
     */
    public function display_table_view()
    {
        $this->display_log_table($this->get_log_table_data($this->get_page_number()));
        return true;
    }

    /**
     * @codeCoverageIgnore
     * Display the pagination for the log table
     * Note: Cannot be tested because this function will echo out all the html from file to the console
     *
     * @param [array] $data  An array that contains the page number, total results, total amount of pages
     * @param boolean $add_input  Tells the pagination whether or not to display an input to change the page
     * @return void
     */
    public function display_pagination($data, $add_input = false)
    {
        include CS1_AR_PATH . 'app/templates/cs-ar-pagination.php';
    }

    /**
     * @codeCoverageIgnore
     * Display the log table
     * Note: Cannot be tested because this function will echo out all the html from file to the console
     *
     * @param [array] $data  An array that contains the page number, total results, total amount of pages, and the log records
     * @return void
     */
    public function display_log_table($data)
    {
        include CS1_AR_PATH . 'app/templates/cs-ar-log-table-view.php';
    }

    /**
     * Get the paginated data for the table
     *
     * @param [integer] $page_num  What page the table is on
     * @return array  An array that contains the page number, total results, total amount of pages, and the log records
     */
    public function get_log_table_data($page_num)
    {
        $offset = ($page_num - 1) * $this->limit;
        $results = $this->cs_ar_log_table_controller->get_records_by_config_id_with_offset_and_limit(1, $offset, $this->limit);
        $total = $this->cs_ar_log_table_controller->get_number_of_records_by_config_id(1);
        return array(
            'page_num' => $page_num,
            'total' => $total,
            'num_of_pages' => (int) ceil($total / $this->limit),
            'results' => ($results !== false && !empty($results)) ? $results : array(),
        );
    }

    /**
     * Get the current page the table is on
     *
     * @return integer  The page the table is currently on, or 1 if pagenum is not set
     */
    public function get_page_number()
    {
        return (isset($_GET['pagenum']) && is_numeric(sanitize_text_field($_GET['pagenum']))) ? absint(sanitize_text_field($_GET['pagenum'])) : 1;
    }

    /**
     * Format the timestamp of an action into something more presentable to the user
     *
     * @param [String] $timestamp  The date when the action took place
     * @return String  The formatted date
     */
    public function format_timestamp($timestamp)
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $timestamp);
        $date->setTimezone(new DateTimeZone(csARConfig::TIMEZONE));
        return esc_attr($date->format(csARConfig::TIMESTAMP_DISPLAY_FORMAT));
    }

    public function get_display_name_for_action($action)
    {
        switch ($action) {
            case csARConfig::GET_CONFIG_INFO_REQUEST_NAME:return esc_attr(csARConfig::GET_CONFIG_INFO_DISPLAY_NAME);
            case csARConfig::ACCESS_TOKEN_REQUEST_NAME:return esc_attr(csARConfig::ACCESS_TOKEN_DISPLAY_NAME);
            case csARConfig::GET_FEED_INFO_REQUEST_NAME:return esc_attr(csARConfig::GET_FEED_INFO_DISPLAY_NAME);
            case csARConfig::ENABLED_FEEDS_REQUEST_NAME:return esc_attr(csARConfig::ENABLED_FEEDS_DISPLAY_NAME);
            case csARConfig::CONTENT_LIST_REQUEST_NAME:return esc_attr(csARConfig::CONTENT_LIST_DISPLAY_NAME);
            case csARConfig::GET_ARTICLE_REQUEST_NAME:return esc_attr(csARConfig::GET_ARTICLE_DISPLAY_NAME);
            case csARConfig::REMOVE_FROM_QUEUE_REQUEST_NAME:return esc_attr(csARConfig::REMOVE_FROM_QUEUE_DISPLAY_NAME);
            default:return esc_attr($action);
        }
    }
}
