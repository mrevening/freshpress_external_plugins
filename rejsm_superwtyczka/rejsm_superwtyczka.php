<?php
/*
Plugin Name: Rejsm Superwtyczka
Plugin URI: https://github.com/mrevening/rejsm_superwtyczka
Description: Dostosowuje panel administracyjny i funkcjonalność backendu Wordpress do potrzeb platformy Rejsm
Version: 0.1
Author: Dominik Wieczorek
Author URI: https://github.com/mrevening/
License: copyright AGH
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
//load_plugin_textdomain( 'rejsm_superwtyczka', false, 'rejsm_superwtyczka/languages' );

require_once ABSPATH . '/wp-includes/pluggable.php' ; //błąd wordpressa, musi być ta komenda.

//Add bootstrap
//function rejsm_enqueue_bootstrap_scripts() {
//    wp_enqueue_script('admin_js_bootstrap_hack', plugins_url('/rejsm_superwtyczka/includes/js/bootstrap-hack.js'), false, '1.0.0', false);
//}
//add_action( 'admin_enqueue_scripts', 'rejsm_enqueue_bootstrap_scripts' );

//
//require_once dirname(__FILE__) . '/includes/rejsm_registration_form_customize.php';
//require_once dirname(__FILE__) . '/includes/rejsm_custom_design.php';
//require_once dirname(__FILE__) . '/includes/rejsm_users_possibilities.php';
//require_once dirname(__FILE__) . '/includes/rejsm_szpital_taxonomy_for_users.php';
require_once dirname(__FILE__) . '/includes/rejsm_create_new_custom_post_types.php';



require_once dirname(__FILE__) . '/includes/add_styles.php';
require_once dirname(__FILE__) . '/includes/rejsm_display_metaboxes_1.php';
require_once dirname(__FILE__) . '/includes/add_taxonomy.php';




//require_once dirname(__FILE__) . '/includes/rejsm_form_dane_demograficzne.php';
//require_once dirname(__FILE__) . '/includes/rejsm_all_user_info.php';

//require_once dirname(__FILE__) . '/includes/rejsm_msis_29_post_type.php';
//require_once dirname(__FILE__) . '/includes/rejsm_eq5d_post_type.php';
//require_once dirname(__FILE__) . '/includes/rejsm_dane_pacjenta_post_type.php';




//require_once dirname(__FILE__) . '/includes/rejsm_szpital_taxonomy_for_users.php';

//require_once dirname(__FILE__) . '/includes/rejsm_user_taxonomy_szpital.php';

//require_once dirname(__FILE__) . '/includes/rejsm_new_post_type_badanie.php';
//
//require_once dirname(__FILE__) . '/includes/rejsm_form_dane_demograficzne.php';
//require_once dirname(__FILE__) . '/includes/rejsm_szpital_taxonomy_for_users.php';




?>