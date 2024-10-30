<?php
//checks to make sure Wordpress is the one requesting the uninstall
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

if (!defined('CS1_AR_PATH')) {
    define('CS1_AR_PATH', trailingslashit(plugin_dir_path(__FILE__)));
}
//files that needed to includes to uninstall properly
//config file
include_once CS1_AR_PATH . 'app/classes/config/class-cs-ar-config.php';
// the custom db tables class
include_once CS1_AR_PATH . 'app/classes/db/class-cs-ar-db-tables.php';
// the custom post type class
include_once CS1_AR_PATH . 'app/classes/posttype/class-cs-ar-post-type.php';
//the cron scheduler
include_once CS1_AR_PATH . 'app/classes/retrieval/class-cs-ar-article-retrieval-cron.php';
//the custom table crud controllers
include_once CS1_AR_PATH . 'app/classes/db/class-cs-ar-crud-controller.php';
include_once CS1_AR_PATH . 'app/classes/db/class-cs-ar-config-table-controller.php';
include_once CS1_AR_PATH . 'app/classes/db/class-cs-ar-article-info-table-controller.php';
include_once CS1_AR_PATH . 'app/classes/db/class-cs-ar-article-attachment-info-table-controller.php';
//the uninstaller
include_once CS1_AR_PATH . 'app/classes/config/class-cs-ar-uninstaller.php';
//uninstall the plugin, GO!
$cs_ar_uninstaller = new csARPluginUninstaller();
$cs_ar_uninstaller->uninstall_plugin();

//OLD CODE - makes a custom popup appear in the uninstall process - currently doesn't work - may try to get this working in the future
/*function show_uninstall_passthru(){
include CS1_AR_PATH . 'app/templates/cs-ar-uninstall-popup.php';
}

function cs_ar_uninstall_redirect() {
//echo var_dump($_POST);
//echo var_dump($_GET);
//echo var_dump($_SERVER);
if($_POST['slug'] === 'contentstream-article-retriever'){
wp_redirect(menu_page_url( 'cs_ar_uninstall_passthru', false ));
exit();
} else {
echo 'everything is fine!';
}
}
add_submenu_page(null, 'ContentStream Settings', 'ContentStream Uninstall Passthru', 'manage_options', 'cs_ar_uninstall_passthru', 'show_uninstall_passthru');
add_action( 'admin_init', 'cs_ar_uninstall_redirect' );
do_action('admin_init');*/
/*do_action( 'pre_uninstall_plugin', function( $plugin ) {
if ( $plugin === 'contentstream-article-retriever/contentstream-article-retriever.php' ) {

} else {
echo 'uninstalling plugins normally';
}
} );
if(!isset($_POST['cs-ar-delete-content'])){
//wp_redirect( CS1_AR_URL . 'app/templates/cs-ar-uninstall-popup.php' );
//include CS1_AR_PATH . 'app/templates/cs-ar-uninstall-popup.php';
exit();
}

echo 'The form worked!!! SO MUCH WOOT RIGHT NOW!!!';*/
//END OF OLD CODE
