<?php
/*
Plugin Name: Rejsm Customize
Plugin URI: https://github.com/mrevening/rejsm_superwtyczka
Description: Dostosowuje panel administracyjny i funkcjonalność backendu Wordpress do potrzeb platformy Rejsm
Version: 0.1
Author: Dominik Wieczorek
Author URI: https://github.com/mrevening/
License: GNU
 */


// Exit if accessed directly
defined( 'ABSPATH' ) || exit;


error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once ABSPATH . '/wp-includes/pluggable.php' ; //błąd wordpressa, musi być ta komenda.

require_once WP_PLUGIN_DIR.'/wp-user-profiles/wp-user-profiles.php';
require_once WP_PLUGIN_DIR.'/wp-user-groups/wp-user-groups.php';

if (function_exists('_wp_user_groups') && function_exists('_wp_user_profiles') ) {
    add_action( 'plugins_loaded', '_rejsm_customize' );
}
function _rejsm_customize() {
    $plugin_path = plugin_dir_path( __FILE__ );
    require_once $plugin_path . 'includes/wp-user-groups-customize.php';
    require_once $plugin_path . 'includes/wp-user-profiles-customize.php';   
}



?>