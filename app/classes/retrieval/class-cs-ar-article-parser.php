<?php

/**
 * The class that handles the article data from contentstream.
 * Converts the json object retrieval from the cs server into a WordPress post with the post type 'cs_article'.
 * Retrieves the images associated with the article and imports them into wordpress and attaches them to the post.
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARArticleParser
{

    private $article_data;

    private $post_id;

    private $post_content;

    private $embedded_image_path;

    private $cs_ar_config_table_controller;

    private $cs_ar_article_info_table_controller;

    private $cs_ar_article_attachment_info_table_controller;

    /**
     * Start this class.
     */
    public function __construct()
    {
        $this->cs_ar_config_table_controller = new csARConfigTableController();
        $this->cs_ar_article_info_table_controller = new csARArticleInfoTableController();
        $this->cs_ar_article_attachment_info_table_controller = new csARArticleAttachmentInfoTableController();
    }

    /**
     * The getter function of the private var embedded_image_path
     *
     * @return array  The embedded_image_path
     */
    public function get_embedded_image_path()
    {
        return $this->embedded_image_path;
    }

    /**
     * The setter function of the private var embedded_image_path
     *
     * @param [String] $embedded_image_path  The value the embedded_image_path should be set to
     * @return void
     */
    public function set_embedded_image_path($embedded_image_path)
    {
        $this->embedded_image_path = $embedded_image_path;
    }

    /**
     * The getter function of the private var article_data
     *
     * @return array  The article_data
     */
    public function get_article_data()
    {
        return $this->article_data;
    }

    /**
     * The setter function of the private var article_data
     *
     * @param [array] $article_data  The value the article_data should be set to
     * @return void
     */
    public function set_article_data($article_data)
    {
        $this->article_data = $article_data;
    }

    /**
     * The getter function of the private var post_id
     *
     * @return String  The post id
     */
    public function get_post_id()
    {
        return $this->post_id;
    }

    /**
     * The setter function of the private var post_id
     *
     * @param [String] $post_id  The value the post id should be set to
     * @return void
     */
    public function set_post_id($post_id)
    {
        $this->post_id = $post_id;
    }

    /**
     * The getter function of the private var post_content
     *
     * @return String  The post_content
     */
    public function get_post_content()
    {
        return $this->post_content;
    }

    /**
     * The setter function of the private var post_content
     *
     * @param [String] $post_content  The value the post_content should be set to
     * @return void
     */
    public function set_post_content($post_content)
    {
        $this->post_content = $post_content;
    }

    /**
     * Parses the data of the article into a post and adds image to that created post
     *
     * @param [array] $article_data  A key-value array that contains the data of the article
     * @return boolean
     */
    public function parse($article_data)
    {
        //sets the article data to class
        $this->set_article_data($article_data);
        $this->set_post_content($article_data->bodytext);
        $this->set_embedded_image_path($article_data->embedded_image_path);
        //parsing!!!
        $this->convert_into_post();
        $this->add_images_to_post();
        //record article in the DB for later use in plugin
        $this->insert_article_info_into_DB();
        return true;
    }

    /**
     * Insert article data in the wordpress DB to be used later by the activate/deactivate hooks
     *
     * @return void
     */
    public function insert_article_info_into_DB()
    {
        $time_stamp = $this->cs_ar_article_info_table_controller->generate_time_stamp();

        $article_info_record = $this->cs_ar_article_info_table_controller->get_one_record_by_post_id($this->get_post_id());

        return ($article_info_record !== false && !empty($article_info_record)) ? $this->cs_ar_article_info_table_controller->update_record($article_info_record[0]->cs_ar_article_info_id, array(
            'cs_ar_article_info_id' => $article_info_record[0]->cs_ar_article_info_id,
            'cs_ar_article_info_config_id' => 1,
            'cs_ar_article_info_post_id' => $this->get_post_id(),
            'cs_ar_article_info_uid' => $this->get_article_data()->uid,
            'cs_ar_article_info_download_number' => ((int) $article_info_record[0]->cs_ar_article_info_download_number + 1),
            'cs_ar_article_info_is_deleted' => 0,
            'cs_ar_article_info_initial_timestamp' => $article_info_record[0]->cs_ar_article_info_initial_timestamp,
            'cs_ar_article_info_update_timestamp' => $time_stamp,
        )) : $this->cs_ar_article_info_table_controller->insert_record(array(
            'cs_ar_article_info_config_id' => 1,
            'cs_ar_article_info_post_id' => $this->get_post_id(),
            'cs_ar_article_info_uid' => $this->get_article_data()->uid,
            'cs_ar_article_info_download_number' => 1,
            'cs_ar_article_info_is_deleted' => 0,
            'cs_ar_article_info_initial_timestamp' => $time_stamp,
            'cs_ar_article_info_update_timestamp' => $time_stamp,
        ));
    }

    /**
     * Checks if the author of the article exists in wordpress
     *
     * @param [String] $author  The name of the author
     * @return [int|boolean]  Returns the id of the author if the author exists, returns false if the author doesn't exist
     */
    public function check_for_author($author)
    {
        $username = preg_replace('/[,|;|\.|\&]/', '', $author);
        $username = strlen($username) >= 60 ? substr($username, 0, 59) : $username;

        return username_exists($username);
    }

    /**
     * Creates an author based on the author name of the article
     *
     * @param [String] $author The name of the author
     * @return int the id of the author
     */
    public function generate_author($author)
    {
        $username = preg_replace('/[,|;|\.|\&]/', '', $author);
        $username = strlen($username) >= 60 ? substr($username, 0, 59) : $username;
        $user_id = wp_create_user($username, wp_generate_password());
        wp_update_user(array(
            'ID' => $user_id,
            'role' => 'contributor',
            'display_name' => $author,
            'description' => 'An author from ContentStream®',
        ));
        return $user_id;
    }

    /**
     * Checks to see if the article already exists as a post in wordpress, either by finding its title or uid
     *
     * @param [String] $title  The title of the article
     * @param [int] $uid  The original id of the article from contentstream
     * @return int returns either 0, if the article isn't a post, and the id of the post if it is
     */
    public function check_for_post($title, $uid)
    {
        $title_check = $this->title_check_for_post($title);

        return $title_check !== 0 ? $title_check : $this->uid_check_for_post($uid);
    }

    /**
     * Checks to see if the article can be found by title
     *
     * @param [String] $title  The title of the article
     * @return int returns either 0, if the article isn't a post, and the id of the post if it is
     */
    public function title_check_for_post($title)
    {
        return post_exists(wp_strip_all_tags($title));
    }

    /**
     * Checks to see if the article can be found by the contentstream uid, which should be stored within the article's metadata
     *
     * @param [int] $uid The original id of the article from contentstream
     * @return int returns either 0, if the article isn't a post, and the id of the post if it is
     */
    public function uid_check_for_post($uid)
    {
        $uid_check = get_posts(array(
            'post_type' => csARConfig::CS_ARTICLE_POST_TYPE,
            'meta_key' => 'uid',
            'meta_value' => $uid)
        );

        return empty($uid_check) ? 0 : $uid_check[0]->ID;
    }

    /**
     * Insert the article into wordpress as a post
     *
     * @param [array] $my_post  an object with all the attributes that make up the article
     * @return int  the id of the post
     */
    public function insert_post($my_post)
    {
        //Insert the post into the database
        return wp_insert_post($my_post);
    }

    /**
     * Updates the post in wordpress
     *
     * @param [int] $post_id  the id of the post
     * @param [array] $my_post  an object with all the attributes that make up the article
     * @return int  the id of the post
     */
    public function update_post($post_id, $my_post)
    {
        //create array for post attributes
        $my_post['ID'] = $post_id;
        //Update the post in the database
        return wp_update_post($my_post);
    }

    /**
     * Creates key-value array with the attributes of the articles
     *
     * @param [String] $author_id  The id of the author of the article
     * @return array  an object with all the attributes that make up the article
     */
    public function build_post_arttibutes($author_id)
    {
        return array(
            'post_author' => $author_id,
            'post_content' => $this->get_post_content(),
            'post_title' => wp_strip_all_tags($this->get_article_data()->title),
            'post_excerpt' => $this->get_article_data()->subheader !== '' ? wp_strip_all_tags($this->get_article_data()->subheader) : wp_strip_all_tags($this->get_article_data()->abstract),
            'post_status' => 'publish',
            'post_type' => $this->cs_ar_config_table_controller->should_cs_article_exist() ? csARConfig::CS_ARTICLE_POST_TYPE : 'post',
            'meta_input' => array(
                'canonical_tag' => $this->get_article_data()->canonical,
                'keywords' => $this->get_article_data()->keywords,
                'original_publication_date' => $this->get_article_data()->publication_date,
                'copyright' => $this->get_article_data()->copyright,
                'canonical_url' => $this->abstract_url_from_tag($this->get_article_data()->canonical),
                'taxonomy' => json_encode($this->get_article_data()->taxonomy),
                'original_publications' => json_encode($this->get_article_data()->publications),
                'uid' => $this->get_article_data()->uid,
                '_yoast_wpseo_canonical' => $this->abstract_url_from_tag($this->get_article_data()->canonical),
            ),
        );
    }

    public function abstract_url_from_tag($str)
    {
        if (preg_match('/href="([^"]+)"/', $str, $m)) {
            return $m[1];
        }
        return '';
    }

    /**
     * Converts the key-value array that makes up the article data into a proper wordpress post
     *
     * @return void
     */
    public function convert_into_post()
    {
        //check to see if the post already exists in wordpress
        $post_check = $this->check_for_post($this->get_article_data()->title, $this->get_article_data()->uid);
        //check for author
        $author_check = $this->check_for_author($this->get_article_data()->author);
        $author_id = !$author_check ? $this->generate_author($this->get_article_data()->author) : $author_check;
        //generate an array of post attributes
        $my_post = $this->build_post_arttibutes($author_id);
        //insert/update post
        $post_id = $post_check === 0 ? $this->insert_post($my_post) : $this->update_post($post_check, $my_post);
        //set the post id
        $this->set_post_id($post_id);
        //insert/update attach info in DB
        $this->insert_article_attachment_info_into_DB($author_id, 'author');
    }

    /**
     * Insert or update attachment info in the plugin's 'cs_ar_article_attachment_info' DB table
     *
     * @param [int] $attachment_id  The id of the attachment
     * @param [string] $attachment_type  The type of the attachment, could be author or image
     * @return void
     */
    public function insert_article_attachment_info_into_DB($attachment_id, $attachment_type)
    {
        $attachment_info = $this->cs_ar_article_attachment_info_table_controller->get_one_records_by_post_id_and_attachement_id($this->get_post_id(), $attachment_id);
        if ($attachment_info !== false && !empty($attachment_info)) {
            $this->cs_ar_article_attachment_info_table_controller->update_record($attachment_info[0]->cs_ar_article_attachment_info_id, array(
                'cs_ar_article_attachment_info_id' => $attachment_info[0]->cs_ar_article_attachment_info_id,
                'cs_ar_article_attachment_info_post_id' => $this->get_post_id(),
                'cs_ar_article_attachment_info_attachment_id' => $attachment_id,
                'cs_ar_article_attachment_info_attachment_type' => $attachment_type,
                'cs_ar_article_attachment_info_is_deleted' => 0,
            ));
        } else {
            $this->cs_ar_article_attachment_info_table_controller->insert_record(array(
                'cs_ar_article_attachment_info_post_id' => $this->get_post_id(),
                'cs_ar_article_attachment_info_attachment_id' => $attachment_id,
                'cs_ar_article_attachment_info_attachment_type' => $attachment_type,
                'cs_ar_article_attachment_info_is_deleted' => 0,
            ));
        }
    }

    /**
     * Loops through the images related to the article and adds them to wordpress. Sets the first image as the thumbnail of the article
     *
     * @return void
     */
    public function add_images_to_post()
    {
        //get the list of images related to the article
        $image_data = $this->get_article_data()->embedded_images;
        $index = 0;
        //loop through images
        foreach ($image_data as $image_info) {
            //add image to post
            $attach_id = $this->add_image_to_post($image_info);
            if ($index === 0 && $attach_id !== false) {
                //set the thumbnail of the post if the index is 0
                set_post_thumbnail($this->get_post_id(), $attach_id);
                $index++;
            }
        }
    }

    /**
     * Adds a single image to wordpress
     *
     * @param [array] $image_info  An array for the image data
     * @return int|boolean  The id of the image, returns false if something goes wrong if the image upload
     */
    public function add_image_to_post($image_info)
    {
        //get base name of the image
        $img_name = basename($image_info->temporary_url);
        //generate the URL of the image
        $file_url = wp_upload_dir()['url'] . '/' . $img_name;
        //generate placeholder URL in the article body
        $replace_url = str_replace('/', '', preg_quote($this->get_embedded_image_path())) . '(?:.*)' . preg_quote($image_info->file_name);
        //check to see if image already exists
        //image exists, get the id from wordpress
        //image doesn't exists, insert image into wordpress
        $attach_id = file_exists(wp_upload_dir()['path'] . '/' . $img_name) ? attachment_url_to_postid($file_url) : $this->insert_image($image_info->temporary_url);
        //check to see if image was properly added, could be false because of the insert
        if (!$attach_id) {
            return false;
        }

        $this->insert_article_attachment_info_into_DB($attach_id, 'image');

        //update body of article with new image path
        $this->update_post_content_with_image_url($replace_url, $file_url);
        //return image id
        return $attach_id;
    }

    /**
     * Updates the article content with the new image url
     *
     * @param [String] $replace_url  The url that needs to be replaced
     * @param [String] $file_url  The new url of the image
     * @return void
     */
    public function update_post_content_with_image_url($replace_url, $file_url)
    {
        $this->set_post_content(preg_replace('/' . $replace_url . '/', $file_url, $this->get_post_content()));
        $this->update_post($this->get_post_id(), array('post_content' => $this->get_post_content()));
    }

    /**
     * Inserts the image into the wordpress systems, creates the images meta data for wordpress, and associates it with the articles
     *
     * @param [String] $temp_url  The URL of the image located on the contentstream server
     * @return int|boolean  The id of the image if the image is successfully uploaded to wordpress, returns false if not
     */
    public function insert_image($temp_url)
    {
        $img_name = basename($temp_url);
        $file_url = wp_upload_dir()['url'] . '/' . $img_name;
        $get = wp_remote_get($temp_url);
        $type = wp_remote_retrieve_header($get, 'content-type');

        $mirror = wp_upload_bits($img_name, '', wp_remote_retrieve_body($get));
        $filename = $mirror['file'];
        // Prepare an array of post data for the attachment.
        $attachment = array(
            'post_mime_type' => $type,
            'post_title' => preg_replace('/\.[^.]+$/', '', $img_name),
            'guid' => $file_url,
            'post_excerpt' => $this->get_article_data()->title, // Set image Caption (Excerpt) to sanitized title
            'post_content' => $this->get_article_data()->title, // Set image Description (Content) to sanitized title
            'post_status' => 'inherit',
        );

        $attach_id = wp_insert_attachment($attachment, $filename, $this->get_post_id());
        update_post_meta($attach_id, '_wp_attachment_image_alt', $this->get_article_data()->title);
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
        wp_update_attachment_metadata($attach_id, $attach_data);
        return $attach_id;
    }
}
