<?php

/**
 * The class that handles the article list for the ContentStream Article Retriever
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARArticleListingView
{
    private $limit = 20;
    private $old_keyword = '';

    /**
     *  Initialize the class and the article list controller
     */
    public function __construct()
    {
        //nothing yet...
    }

    /**
     * @codeCoverageIgnore
     * Display the pagination for the article list
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
     * Display the article list
     * Note: Cannot be tested because this function will echo out all the html from file to the console
     *
     * @param [array] $data  An array that contains the page number, total results, total amount of pages, and the log records
     * @return void
     */
    public function display_article_list($data, $posts)
    {
        include CS1_AR_PATH . 'app/templates/cs-ar-article-listing-view.php';
    }

    /**
     * Query the DB for posts and build the necessary objects to display the article listing view
     *
     * @return void
     */
    public function display_list_view()
    {
        $post_ids = $this->get_article_ids_from_db();
        $search_criteria = $this->get_search_criteria();
        $page_number = $this->get_page_number($search_criteria);
        $post_status = $this->get_post_status($search_criteria);

        $posts = $this->get_posts($post_ids, $search_criteria, $page_number, $post_status);

        $this->display_article_list($this->build_data_for_pagination($post_ids, $search_criteria, $page_number, $post_status), $posts);

        $this->old_keyword = $search_criteria !== '' ? $search_criteria : '';

        return true;
    }

    /**
     * Create an object for the pagination view of the list
     *
     * @param [array] $post_ids  Ther list of post ids
     * @param [String] $search_criteria  The value from the keyword search input
     * @param [int] $page_number  The value from the pagination
     * @param [String] $post_status  The value from the selected status link
     * @return array  The pagination object
     */
    public function build_data_for_pagination($post_ids, $search_criteria, $page_number, $post_status)
    {
        $total_posts = $this->get_posts($post_ids, $search_criteria, $page_number, $post_status, true);
        return array(
            'total' => count($total_posts),
            'num_of_pages' => (int) ceil(count($total_posts) / $this->limit),
            'page_num' => $page_number,
            'results' => $total_posts,
            'true_total' => count($this->get_posts($post_ids, $search_criteria, $page_number, '', true, true)),
            'published_total' => count($this->get_posts($post_ids, $search_criteria, $page_number, 'publish', true, true)),
            'trashed_total' => count($this->get_posts($post_ids, $search_criteria, $page_number, 'trash', true, true)),
            'date_dropdown' => $this->format_timestamp_for_dropdown($this->get_posts($post_ids, $search_criteria, $page_number, $post_status, true, true)),
        );
    }

    /**
     * Build the argument list and query the DB for posts
     *
     * @param [array] $post_ids  Ther list of post ids
     * @param [String] $search_criteria  The value from the keyword search input
     * @param [int] $page_number  The value from the pagination
     * @param [String] $post_status  The value from the selected status link
     * @param boolean $get_all  Boolean value that tell the query whether or not to get all posts or only 20
     * @param boolean $for_counts  Boolean value that tells the query whether this search will be used for display or counts
     * @return array  The list of posts
     */
    public function get_posts($post_ids, $search_criteria, $page_number, $post_status, $get_all = false, $for_counts = false)
    {
        $args = array('post__in' => $post_ids, 'posts_per_page' => $get_all ? -1 : 20);

        $order = $this->get_order($search_criteria);
        $order_by = $this->get_order_by($search_criteria);
        $cat = $this->get_cat($search_criteria);
        $m = $this->get_m($search_criteria);

        if ($m !== '' && !$for_counts) {
            $args['m'] = $m;
        }

        if ($cat !== '' && !$for_counts) {
            $args['cat'] = $cat;
        }

        if ($post_status !== '') {
            $args['post_status'] = $post_status;
        }

        if ($order !== '' && !$for_counts) {
            $args['orderby'] = $order_by;
            $args['order'] = $order;
        }

        if ($search_criteria !== '' && !$for_counts) {
            $args['s'] = $search_criteria;
        }

        if ($page_number > 1 && !$get_all) {
            $args['offset'] = 20 * ($page_number - 1);
        }

        return $this->query_the_db_for_posts($args);
    }

    /**
     * @codeCoverageIgnore
     * Query the DB for posts based on values from the listing view
     *
     * @param [array] $args  The list of argument the query should use to get the posts
     * @return array  The list of posts
     */
    public function query_the_db_for_posts($args)
    {
        return query_posts($args);
    }

    /**
     * @codeCoverageIgnore
     * Get the list of ids for the contentstream articles found in the DB
     *
     * @return array The list of post ids that the plugin knows
     */
    public function get_article_ids_from_db()
    {
        return (new csARArticleInfoTableController())->get_all_post_ids();
    }

    /**
     * Get the current value of the m input, used as part of the article query's date range
     *
     * @return String  The value of the date dropdown, could be empty
     */
    public function get_m($search_criteria)
    {
        return (isset($_GET['m']) && sanitize_text_field($_GET['m']) !== '' && sanitize_text_field($_GET['m']) !== '0' && is_numeric(sanitize_text_field($_GET['m'])) && $search_criteria === $this->old_keyword) ? absint(sanitize_text_field($_GET['m'])) : '';
    }

    /**
     * Get the current value of the cat input, used as part of the article query's category taxonomy search
     *
     * @return String  The value of the category dropdown, could be empty
     */
    public function get_cat($search_criteria)
    {
        return (isset($_GET['cat']) && sanitize_text_field($_GET['cat']) !== '' && sanitize_text_field($_GET['cat']) !== '0' && is_numeric(sanitize_text_field($_GET['cat'])) && $search_criteria === $this->old_keyword) ? absint(sanitize_text_field($_GET['cat'])) : '';
    }

    /**
     * Get the current post status that the user wants to view
     *
     * @return String  The value of the status link, could be empty
     */
    public function get_post_status($search_criteria)
    {
        return (isset($_GET['post_status']) && sanitize_text_field($_GET['post_status']) !== '' && $search_criteria === $this->old_keyword) ? sanitize_text_field($_GET['post_status']) : '';
    }

    /**
     * Get the current value that the user wants to order the articles by
     *
     * @return String  The value of the orderby link, could be empty
     */
    public function get_order_by($search_criteria)
    {
        return (isset($_GET['orderby']) && sanitize_text_field($_GET['orderby']) !== '' && $search_criteria === $this->old_keyword) ? sanitize_text_field($_GET['orderby']) : '';
    }

    /**
     * Get the current direction that that user want to order the articles by, ASC or DESC
     *
     * @return String  The value of the orderby link, could be empty
     */
    public function get_order($search_criteria)
    {
        return (isset($_GET['order']) && sanitize_text_field($_GET['order']) !== '' && $search_criteria === $this->old_keyword) ? sanitize_text_field($_GET['order']) : '';
    }

    /**
     * Get the current value of the keyword search
     *
     * @return String  The value of the keyword search input, could be empty
     */
    public function get_search_criteria()
    {
        return (isset($_GET['s']) && sanitize_text_field($_GET['s']) !== '') ? sanitize_text_field($_GET['s']) : $this->old_keyword;
    }

    /**
     * Get the current page the table is on
     *
     * @return integer  The page the table is currently on, or 1 if pagenum is not set
     */
    public function get_page_number($search_criteria)
    {
        return (isset($_GET['pagenum']) && is_numeric(sanitize_text_field($_GET['pagenum'])) && $search_criteria === $this->old_keyword) ? absint(sanitize_text_field($_GET['pagenum'])) : 1;
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
        return esc_attr($date->format('Y/m/d'));
    }

    /**
     * Format the timestamps of articles for the date dropdown
     *
     * @param [array] $post  The list of articles
     * @return array  The list of dates the user can select
     */
    public function format_timestamp_for_dropdown($posts)
    {
        $list = array();
        $check = array();
        foreach ($posts as $post) {
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $post->post_date);
            $item = array(
                'value' => esc_attr($date->format('Ym')),
                'label' => esc_attr($date->format('F Y')),
            );

            if (empty($check) || !in_array($item['value'], $check)) {
                $list[] = $item;
                $check[] = $item['value'];
            }
        }

        return $list;
    }
}
