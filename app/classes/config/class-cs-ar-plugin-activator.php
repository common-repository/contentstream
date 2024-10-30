<?php

/**
 * The class that handles the one time events of activating and deactivating the plugin
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARPluginActivator
{
    /**
     * Initialize the handler.
     */
    public function __construct()
    {
        //nothing yet...
    }

    /**
     * Get the config info from the DB
     *
     * @return object  The config info record if it exists, returns null if not
     */
    public function get_config_record()
    {
        $cs_ar_config_table_controller = new csARConfigTableController();
        $config_record = $cs_ar_config_table_controller->get_one_record_by_id(1);
        return ($config_record !== false && !empty($config_record)) ? $config_record[0] : null;
    }

    /**
     * Get a list of the cs articles
     *
     * @return void
     */
    public function get_cs_articles()
    {
        $cs_ar_article_info_table_controller = new csARArticleInfoTableController();
        $article_info_records = $cs_ar_article_info_table_controller->get_all_records_by_is_deleted(0);
        return ($article_info_records !== false && !empty($article_info_records)) ? $article_info_records : array();
    }

    /**
     * Update the cs articles' post type to either cs_article or post based on whether the activate variable is true or false respectively
     *
     * @param boolean $activate  Tells the function whether update the posts as cs_articles or as posts, defaults to true
     * @return void
     */
    public function update_cs_articles($activate = true)
    {
        $article_info_records = $this->get_cs_articles();
        foreach ($article_info_records as $article_info_record) {
            $this->update_cs_article($article_info_record, $activate);
            $this->check_cs_article_author($article_info_record);
            $this->check_cs_article_images($article_info_record);
        }
    }

    /**
     * Gets a list of attachments related to the post
     *
     * @param [string] $cs_ar_post_id  The ID of the post in Wordpress
     * @param [string] $cs_ar_attachment_type  The type of attachment that is being searched for, acan be either author or image
     * @return array  the list of the attachments, could be empty
     */
    public function get_cs_article_attachments($cs_ar_post_id, $cs_ar_attachment_type)
    {
        $cs_ar_article_attachment_info_table_controller = new csARArticleAttachmentInfoTableController();
        $attachments = $cs_ar_article_attachment_info_table_controller->get_all_records_by_post_id_and_attachement_type($cs_ar_post_id, $cs_ar_attachment_type);
        return ($attachments !== false && !empty($attachments)) ? $attachments : array();
    }

    /**
     * Update the attachment's record says that it was deleted
     *
     * @param [object] $attachment  the attachment's record from the DB
     * @return void
     */
    public function update_cs_article_attachment($attachment)
    {
        $cs_ar_article_attachment_info_table_controller = new csARArticleAttachmentInfoTableController();
        $cs_ar_article_attachment_info_table_controller->update_record($attachment->cs_ar_article_attachment_info_id, array(
            'cs_ar_article_attachment_info_id' => $attachment->cs_ar_article_attachment_info_id,
            'cs_ar_article_attachment_info_post_id' => $attachment->cs_ar_article_attachment_info_post_id,
            'cs_ar_article_attachment_info_attachment_id' => $attachment->cs_ar_article_attachment_info_attachment_id,
            'cs_ar_article_attachment_info_attachment_type' => $attachment->cs_ar_article_attachment_info_attachment_type,
            'cs_ar_article_attachment_info_is_deleted' => 1,
        ));
    }

    /**
     * Check if an individual image attachment still exists in Wordpress
     *
     * @param [object] $image  the attachment's record from the DB
     * @return void
     */
    public function check_cs_article_image($image)
    {
        if (wp_get_attachment_image($image->cs_ar_article_attachment_info_attachment_id) === '') {
            $this->update_cs_article_attachment($image);
        }
    }

    /**
     * Loop function that goes throguh the image attachments related to a given article
     *
     * @param [object] $article_info_record  the article's record from the DB
     * @return void
     */
    public function check_cs_article_images($article_info_record)
    {
        $images = $this->get_cs_article_attachments($article_info_record->cs_ar_article_info_post_id, 'image');
        foreach ($images as $image) {
            $this->check_cs_article_image($image);
        }
    }

    /**
     * Check if an author attachment still exists in Wordpress
     *
     * @param [type] $article_info_record  the article's record from the DB
     * @return void
     */
    public function check_cs_article_author($article_info_record)
    {
        $author = $this->get_cs_article_attachments($article_info_record->cs_ar_article_info_post_id, 'author');
        if (!empty($author)) {
            if (get_user_by('ID', $author[0]->cs_ar_article_attachment_info_attachment_id) === false) {
                $this->update_cs_article_attachment($author[0]);
            }
        }
    }

    /**
     * Update the cs article's post type to either cs_article or post based on whether the activate variable is true or false respectively
     *
     * @param [object] $article_info_record  The article record from the DB
     * @param [boolean] $activate  Tells the function whether update the posts as cs_articles or as posts
     * @return void
     */
    public function update_cs_article($article_info_record, $activate)
    {
        $wp_post = get_post($article_info_record->cs_ar_article_info_post_id);
        if ($wp_post !== null) {
            set_post_type($wp_post->ID, $activate ? csARConfig::CS_ARTICLE_POST_TYPE : 'post');
        } else {
            $this->update_cs_article_info($article_info_record);
        }
    }

    /**
     * Updates the article info in the table 'cs_ar_article_info' for the plugin's internal use
     *
     * @param [object] $article_info_record  The article record from the DB
     * @return void
     */
    public function update_cs_article_info($article_info_record)
    {
        $cs_ar_article_info_table_controller = new csARArticleInfoTableController();
        $cs_ar_article_info_table_controller->update_record($article_info_record->cs_ar_article_info_id, array(
            'cs_ar_article_info_id' => $article_info_record->cs_ar_article_info_id,
            'cs_ar_article_info_config_id' => $article_info_record->cs_ar_article_info_config_id,
            'cs_ar_article_info_post_id' => $article_info_record->cs_ar_article_info_post_id,
            'cs_ar_article_info_uid' => $article_info_record->cs_ar_article_info_uid,
            'cs_ar_article_info_download_number' => (int) $article_info_record->cs_ar_article_info_download_number,
            'cs_ar_article_info_is_deleted' => 1,
            'cs_ar_article_info_initial_timestamp' => $article_info_record->cs_ar_article_info_initial_timestamp,
            'cs_ar_article_info_update_timestamp' => $cs_ar_article_info_table_controller->generate_time_stamp()));
    }

    /**
     * Activates the cs articles so they show up in the contentstream section of the interface
     *
     * @return void
     */
    public function activate_cs_articles()
    {
        $this->update_cs_articles();
    }

    /**
     * Deactivates the cs articles so they show up in the post section of the interface
     *
     * @return void
     */
    public function deactivate_cs_articles()
    {
        $this->update_cs_articles(false);
    }

    /**
     * Activate the cron job to retrieve the articles
     *
     * @return void
     */
    public function activate_cron_job()
    {
        global $cs_ar_cron_scheduler;
        $config_record = $this->get_config_record();
        if ($config_record !== null) {
            $cs_ar_cron_scheduler->schedule($config_record->cs_ar_config_retrieval_frequency);
        }
    }

    /**
     * Deactivate the cron job so that no more article are retrieved from contentstream
     *
     * @return void
     */
    public function deactivate_cron_job()
    {
        global $cs_ar_cron_scheduler;
        $cs_ar_cron_scheduler->unschedule();
    }
}
