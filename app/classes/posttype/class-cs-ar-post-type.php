<?php

/**
 * The class that handles the new post type created for the ContentStream Article Retriever
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */

class csARPostTypeController
{
    private $cs_ar_config_table_controller;

    /**
     *  Start this class
     *
     * @global object $cs_article_retriever_post_type_handler
     */
    public function __construct()
    {
        $this->cs_ar_config_table_controller = new csARConfigTableController();
    }

    /**
     * Initializes the post type handler and add the actions that create the custom post type and overrides
     *
     * @return void
     */
    public function init()
    {

        if ($this->cs_ar_config_table_controller->should_cs_article_exist()) {
            add_action('init', array($this, 'create_cs_article_post_type'));
            add_action('init', array($this, 'create_cs_article_template_override'));
            add_action('wp_head', array($this, 'add_additional_cs_ar_styling_to_post'));
            add_action('pre_get_posts', array($this, 'add_cs_article_post_type_to_query'), 99); //99 is used to set when this action is fired, 99 means that this should be the last action associated with pre_get_posts taken
        }
        //checks if yoast is being used, changes how the canonical tag override is added to wordpress
        //this check was added because yoast is a very common SEO plugin that users have installed on their wordpress, and yoast messes with how the canonical tag is put on the page by adding an additional filter to the process
        $canonical_tag_override = defined('WPSEO_FILE') ? add_filter('wpseo_canonical', array($this, 'create_rel_canonical_override')) : add_action('init', array($this, 'create_rel_canonical_override'));
    }

    /**
     * Creates the new post type 'cs_article' for the article retriever to use
     *
     * @return void
     */
    public function create_cs_article_post_type()
    {
        if (!post_type_exists(csARConfig::CS_ARTICLE_POST_TYPE)) {
            //these are the labels that will show in the menu and on the contentstream article pages
            $labels = array(
                'name' => esc_html('ContentStream Articles'),
                'singular_name' => esc_html('ContentStream Article'),
                'add_new' => esc_html('Add ContentStream Article'),
                'add_new_item' => esc_html('Add New ContentStream Article'),
                'new_item' => esc_html('New ContentStream Article'),
                'all_items' => esc_html('Articles'),
                'view_item' => esc_html('View ContentStream Article'),
                'edit_item' => esc_html('Edit ContentStream Article'),
                'search_items' => esc_html('Search ContentStream Articles'),
                'not_found' => esc_html('No ContentStream Articles found'),
                'not_found_in_trash' => esc_html('No ContentStream Articles found in the Trash'),
                'menu_name' => 'ContentStream',
            );
            //Tells wordpress what users can and can't do with the custom post type
            $capabilities = array(
                'create_posts' => 'do_not_allow', // stops the user from manually adding contentStream articles, probably a bad idea to let users make there own contentstream articles (Maybe not, if we could send the new article they make back into contentstream - discuss with Erich/Paul later)
                'publish_posts' => 'publish_posts',
                'edit_posts' => 'edit_posts',
                'edit_others_posts' => 'edit_others_posts',
                'delete_posts' => 'delete_posts',
                'delete_others_posts' => 'delete_others_posts',
                'read_private_posts' => 'read_private_posts',
                'edit_post' => 'edit_post',
                'delete_post' => 'delete_post',
                'read_post' => 'read_post',
            );

            //this is where the attributes for how the CS article post type behaves goes
            $args = array(
                'labels' => $labels,
                'public' => true, //make it visible to FE users
                'exclude_from_search' => (!$this->cs_ar_config_table_controller->should_cs_article_show_in_search()) ? false : true, //makes cs_article searchable by users
                'has_archive' => ($this->cs_ar_config_table_controller->should_cs_article_have_archive_page()) ? true : false, //creates a default listing page for cs_article
                'hierarchical' => false,
                'menu_position' => 7, //where the menu item shows up in the menu, 7 means after posts but before anything else
                'menu_icon' => CS1_AR_URL . csARConfig::MENU_ICON_PATH, //controls what icon is in the menu - current icon is just a placeholder
                'capability_type' => 'post', //makes the contentstream post type behave like the 'post' post type
                'capabilities' => $capabilities,
                'rewrite' => array('slug' => csARConfig::CS_ARTICLE_POST_TYPE),
                'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author'), //allows the user to edit various parts of the contentStream Article, like changing what image should be used as the article thumbnail, or changing the placement of image in the body text, or changing the blurb about the article itself. (Maybe take out custom fields, if we don't want them messing with the canonical tag - discuss with Erich)
                'show_ui' => true, //make it show in the admin UI
                'map_meta_cap' => true,
            );
            register_post_type(csARConfig::CS_ARTICLE_POST_TYPE, $args);
            $this->add_taxonomy_lists_to_cs_article();
            $set = get_option('post_type_rules_flased_' . csARConfig::CS_ARTICLE_POST_TYPE);
            if ($set !== true) {
                flush_rewrite_rules(false);
                update_option('post_type_rules_flased_' . csARConfig::CS_ARTICLE_POST_TYPE, true);
            }

        }
    }

    /**
     * Add all taxonomy lists found associated with posts to cs_articles
     *
     * @return void
     */
    public function add_taxonomy_lists_to_cs_article()
    {
        foreach (get_object_taxonomies('post') as $taxonomy) {
            register_taxonomy_for_object_type($taxonomy, csARConfig::CS_ARTICLE_POST_TYPE);
        }
    }

    /**
     * Add the custom post type 'cs_article' to the page query
     *
     * @param [object] $query  the query is used by the page
     * @return object  the query that will be used by a page to search the DB
     */
    public function add_cs_article_post_type_to_query($query)
    {
        //is_admin checks to see if the query is part of a call for the admin interface
        //is_main_query is used to see if the query is the primary action of the page
        if (!is_admin() && $query->is_home() && $query->is_main_query() && $this->cs_ar_config_table_controller->should_cs_article_show_on_home_page()) {
            $post_types = $query->get('post_type');
            // Check that the current posts types are stored as an array
            if (!is_array($post_types)) {
                $post_types = empty($post_types) ? array() : explode(',', $post_types);
            }

            if (empty($post_types)) {
                $post_types[] = 'post';
            }

            //checks to see if the query is searching for posts and doesn't already have cs_article in it
            if (array_search('post', $post_types) !== false && array_search(csARConfig::CS_ARTICLE_POST_TYPE, $post_types) === false) {
                $post_types[] = csARConfig::CS_ARTICLE_POST_TYPE;
            }

            $post_types = array_map('trim', $post_types); // Trim every element, just in case
            $post_types = array_filter($post_types); // Remove any empty elements, just in case

            $query->set('post_type', $post_types);
        }

        return $query;
    }

    /**
     * Deletes the post type 'cs_article' when the plugin is disabled or uninstalled
     *
     * @return void
     */
    public function delete_cs_article_post_type()
    {
        if (post_type_exists(csARConfig::CS_ARTICLE_POST_TYPE)) {
            unregister_post_type(csARConfig::CS_ARTICLE_POST_TYPE);
        }
    }

    /**
     * Creates the rel_canonical override action for the CS Articles and removes the original action
     *
     * @return void
     */
    public function create_rel_canonical_override()
    {
        if (function_exists('rel_canonical')) {
            remove_action('wp_head', 'rel_canonical');
        }
        add_action('wp_head', array($this, 'rel_canonical_with_custom_tag_override'));
    }

    /**
     * Removes the rel_canonical override action for the CS Articles and add the original action back
     *
     * @return void
     */
    public function remove_rel_canonical_override()
    {
        remove_action('wp_head', array($this, 'rel_canonical_with_custom_tag_override'));
        if (function_exists('rel_canonical')) {
            add_action('wp_head', 'rel_canonical');
        }
    }

    /**
     * Creates an override filter for cs_article template so that Wordpress can get the custom file
     *
     * @return void
     */
    public function create_cs_article_template_override()
    {
        add_filter('single_template', array($this, 'get_cs_article_template'));
    }

    /**
     * Removes the override filter for the cs_article template when the plugin is uninstalled
     *
     * @return void
     */
    public function remove_cs_article_template_override()
    {
        remove_filter('single_template', array($this, 'get_cs_article_template'));
    }

    /**
     * @codeCoverageIgnore
     * echos out a string onto the page that Wordpress is showing to the user
     * Note: this function exists most for unit testing so the output from phpunit isn't cluttered with echos
     *
     * @param [String] $output  the text output from functions to be added the page that Wordpress is showing to the user
     * @return void
     */
    public function echo_output($output)
    {
        echo $output;
    }

    /**
     * Adds the plugin's custom stylesheet for cs articles if the current global post is of type cs_article
     *
     * @return boolean  Returns true if the post is a cs article, else false
     */
    public function add_additional_cs_ar_styling_to_post()
    {
        global $post;
        if ($post->post_type === csARConfig::CS_ARTICLE_POST_TYPE) {
            $this->echo_output('<link rel="stylesheet" id="cs_ar_single_article_stylesheet" href="' . CS1_AR_URL . 'app/css/cs-ar-single-article.css" type="text/css" media="all" />');
            return true;
        }
        return false;
    }

    /**
     * The custom rel_canonical function used to add the canonical tag to CS articles by echoing it to the page.
     *
     * @param String $url  The url string from wordpress that will be used as part of the canonical tag
     * @return boolean returns true, if a cs_article canonical_tag is present, false if not
     */
    public function rel_canonical_with_custom_tag_override($url = '')
    {
        $canonical_tag = '';
        $has_custom_tag = false;

        if (!is_singular()) {
            $this->echo_output("<link rel='canonical' href='" . $url . "' />\n");
            return;
        }

        global $wp_the_query;
        if (!$id = $wp_the_query->get_queried_object_id()) {
            return;
        }

        $wpseo_canonical = get_post_meta($id, '_yoast_wpseo_canonical', true);
        $url = ($wpseo_canonical && $wpseo_canonical !== '') ? $wpseo_canonical : '';

        // check whether the current post has content in the "canonical_tag" custom field, the field name may change once I get the json from REST client
        $canonical_url = get_post_meta($id, 'canonical_tag', true);
        if ('' != $canonical_url) {
            $canonical_tag = $canonical_url . "\n";
            $has_custom_tag = true;
        } else {
            $canonical_tag = $url === '' ? "<link rel='canonical' href='" . esc_url(get_permalink($id)) . "' />\n" : "<link rel='canonical' href='" . $url . "' />\n";
        }

        $this->echo_output($canonical_tag);
        return $has_custom_tag;
    }

    /**
     * Gets the path to the single template, returns a custom path if the post type is cs_article
     *
     * @param [String] $single_template  The file path to the template file
     * @return String the path to the custom template file if the post is a cs_article, the normal path if the post is not.
     */
    public function get_cs_article_template($single_template)
    {
        global $post;
        if ($post->post_type === csARConfig::CS_ARTICLE_POST_TYPE && $this->cs_ar_config_table_controller->should_cs_article_use_custom_template()) {
            $single_template = CS1_AR_PATH . 'app/templates/cs-ar-single-article.php'; //path to custom template for cs articles
        }
        return $single_template;
    }

    /**
     * Deletes all cs_articles from the wordpress database. Will only be called if the admin user uninstalls our plugin and clicks the button to delete all cs articles when prompted.
     *
     * @return void
     */
    public function delete_cs_articles()
    {
        $cs_ar_article_info_table_controller = new csARArticleInfoTableController();
        $article_info = $cs_ar_article_info_table_controller->get_all_records();
        if ($article_info !== false && !empty($article_info)) {
            foreach ($article_info as $cs_article) {
                $this->delete_cs_article_and_attachments($cs_article);
            }
        }
    }

    /**
     * Deletes the individual article and its related attachments, author and images, if they haven't already been deleted
     *
     * @param [object] $cs_article  the article info record from the DB
     * @return void
     */
    public function delete_cs_article_and_attachments($cs_article)
    {
        $this->delete_cs_article_author($cs_article);
        $this->delete_cs_article_images($cs_article);
        $this->delete_cs_article($cs_article);
    }

    /**
     * Get the attachments for the post that is to be deleted
     *
     * @param [string] $post_id  The ID of the post
     * @param [string] $attachment_type  The type of attachment that is requested
     * @return array  an object array of the record and its data
     */
    public function get_attachments($post_id, $attachment_type)
    {
        $cs_ar_article_attachment_info_table_controller = new csARArticleAttachmentInfoTableController();
        return $cs_ar_article_attachment_info_table_controller->get_all_records_by_post_id_and_attachement_type($post_id, $attachment_type);
    }

    /**
     * Delete the author of the cs articles
     *
     * @param [object] $cs_article  the article info record from the DB
     * @return void
     */
    public function delete_cs_article_author($cs_article)
    {
        $author_info = $this->get_attachments($cs_article->cs_ar_article_info_post_id, 'author');
        if ($author_info !== false && !empty($author_info)) {
            if ((int) $author_info[0]->cs_ar_article_attachment_info_is_deleted === 0) {
                wp_delete_user($author_info[0]->cs_ar_article_attachment_info_attachment_id);
            }
        }
    }

    /**
     * Delete the images of the cs articles
     *
     * @param [object] $cs_article  the article info record from the DB
     * @return void
     */
    public function delete_cs_article_images($cs_article)
    {
        $image_info = $this->get_attachments($cs_article->cs_ar_article_info_post_id, 'image');
        if ($image_info !== false && !empty($image_info)) {
            foreach ($image_info as $image) {
                $this->delete_cs_article_image($image);
            }
        }
    }

    /**
     * Delete an individual image of the cs article
     *
     * @param [object] $image  the attachment info record from the DB
     * @return void
     */
    public function delete_cs_article_image($image)
    {
        if ((int) $image->cs_ar_article_attachment_info_is_deleted === 0) {
            wp_delete_attachment($image->cs_ar_article_attachment_info_attachment_id);
        }
    }

    /**
     * Delete the cs article from Wordpress
     *
     * @param [object] $cs_article  the article info record from the DB
     * @return void
     */
    public function delete_cs_article($cs_article)
    {
        if ((int) $cs_article->cs_ar_article_info_is_deleted === 0) {
            wp_delete_post($cs_article->cs_ar_article_info_post_id, true);
        }
    }
}
