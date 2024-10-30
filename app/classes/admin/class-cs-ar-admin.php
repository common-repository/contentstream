<?php

/**
 * The class that handles the admin screen for the ContentStream Article Retriever
 *
 * @author Josh Balcitis <jbalcitis@cfetechnology.com>
 */
class csARAdmin
{
    public $cs_ar_settings_form;
    public $cs_ar_log_table;
    public $cs_ar_article_listing;

    /**
     *  Initialize the admin section and hook all actions
     *
     * @global object $cs_article_retriever_admin
     */
    public function __construct()
    {
        $this->cs_ar_settings_form = new csARSettingsForm();
        $this->cs_ar_log_table = new csARLogTableView();
        $this->cs_ar_article_listing = new csARArticleListingView();
    }

    /**
     * Adds the action to wordpress so it knows to create the admin menu option for contentstream plugin
     *
     * @return void
     */
    public function init()
    {
        // add to admin menu
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    /**
     * Add to admin menu in WordPress interface, current placement for menu item is under the custom post type 'ContentStream Article'
     *
     * @return void
     */
    public function admin_menu()
    {
        $menu_page = 'edit.php?post_type=' . csARConfig::CS_ARTICLE_POST_TYPE;

        if (!(new csARConfigTableController())->should_cs_article_exist()) {
            $menu_page = csARConfig::CS_LISTING_SLUG;
            add_menu_page('ContentStream Articles', 'ContentStream', 'manage_options', $menu_page, array($this, 'listing'), CS1_AR_URL . csARConfig::MENU_ICON_PATH, 7);
            add_submenu_page($menu_page, 'ContentStream Articles', 'Articles', 'manage_options', $menu_page, array($this, 'listing'));
        }
		add_action( 'admin_enqueue_scripts', array($this, 'add_scripts_for_cs_ar_screen') );
        add_submenu_page($menu_page, 'ContentStream Settings', 'Settings', 'manage_options', csARConfig::CS_SETTINGS_SLUG, array($this, 'screen'));
    }

    /**
     * Display the screen/ui when the user clicks on the articles sub menu option
     *
     * @return boolean  This return is for testing purposes
     */
    public function listing()
    {
        $this->cs_ar_article_listing->display_list_view();
        return true;
    }

    /**
     * Display the screen/ui when the user clicks on the settings sub menu option
     *
     * @return boolean  This return is for testing purposes
     */
    public function screen()
    {
        $this->cs_ar_settings_form->display_form();
        $this->cs_ar_log_table->display_table_view();
        return true;
    }
    
    /**
     * @codeCoverageIgnore
     * Add scripts and styles to settings form page
     * 
     * @param String $hook  The identifier for the admin page the user is currently on
     * @return void
     */
    public function add_scripts_for_cs_ar_screen($hook) {		
		if( 'contentstream_page_cs_ar_settings' !== $hook && 'cs_article_page_cs_ar_settings' !== $hook ) {
			return;
		}
		
		wp_enqueue_script( 'cs_ar_setting_form_script', CS1_AR_URL . 'app/js/cs-ar-settings-form.js', array(), CS1_AR_VERSION );
		wp_enqueue_style( 'cs_ar_setting_form_styles', CS1_AR_URL . 'app/css/cs-ar-settings-form.css', array(), CS1_AR_VERSION );
	}
}
