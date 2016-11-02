<?php

class uzytkownicy_list{
    public function __construct() {
//        add_filter( 'query_vars', array($this, 'add_query_vars'));
//        add_action( 'pre_get_posts', array($this, 'rejsm_add_query_variable'));

        add_action( 'login_head',  array($this,'my_login_head'));
        add_filter( 'login_headerurl', array($this,'loginpage_custom_link'));
        add_filter( 'login_headertitle',  array($this,'change_title_on_logo'));
        add_filter( 'admin_footer_text',  array($this,'remove_footer_admin'));
        add_action( 'admin_head',  array($this,'custom_admin_logo'));
        add_action( 'admin_head-user-edit.php',  array($this,'remove_website_row_wpse_94963_css' ));
        add_action( 'admin_head-profile.php',    array($this,'remove_website_row_wpse_94963_css' ));
        add_action( 'login_head',  array($this,'rejsm_login_head' ));
        add_action( 'personal_options',  array($this,'rejsm_login_head'));
        add_action( 'user_new_form',  array($this,'rejsm_login_head'));
        add_filter( 'gettext',  array($this,'remove_admin_stuff'), 20, 3);
        add_action( 'admin_init',  array($this,'remove_posts_menu'));
        add_action( 'admin_menu',  array($this,'hide_the_dashboard' ));
        add_filter( 'manage_users_columns',  array($this,'new_modify_user_table' ));
        add_filter( 'manage_users_custom_column',  array($this,'new_modify_user_table_row'), 10, 3 );
//        add_action( 'manage_users_custom_column',  array($this,'my_manage_users_columns'), 10, 2 );
        add_action('manage_users_columns', array($this,'remove_user_posts_column'));
//        add_filter( 'login_redirect', array($this,'your_login_redirect'), 10, 3 );
        

        add_action( 'wp_before_admin_bar_render', array($this,'remove_wp_logo' ));
    }
    function remove_wp_logo() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');
}
    public function my_login_head() {
        echo "
        <style>
        #login h1 a {
            background: url('".plugin_dir_url( __FILE__ )."/images/logo-login.png') ;
            height: 88px;
            width:265px;
        }
        </style>
        ";
    }
    public function loginpage_custom_link() {
        return home_url();
    }
    public function change_title_on_logo() {
	return 'Rejestr chorych na stwardnienie rozsiane';
    }
    public function remove_footer_admin (){
        echo '<span id="footer-thankyou">Content-related supervision by dr n. med. Waldemar Brola, dr n. med. Małgorzata Fudala. Technological supervision by dr inż. Stanisław Flaga. Designed by <a href="http://www.github.com/mrevening" target="_blank">mrevening</a></span>';
    }
    public function custom_admin_logo() {
        echo '
            <style type="text/css">
                #header-logo { background-image: url('.get_bloginfo('stylesheet_directory').'/images/logo.png) !important; }
            </style>
        ';
    }
    public function remove_website_row_wpse_94963_css(){
        echo '<style>tr.user-url-wrap{ display: none; }</style>';
        echo '<style>tr.user-description-wrap{ display: none; }</style>';
        echo '<style>tr.user-profile-picture-wrap{ display: none; }</style>';
        echo '<style>tr.user-first-name-wrap{ display: none; }</style>';
        echo '<style>tr.user-last-name-wrap{ display: none; }</style>';
    }
    public function rejsm_login_head() {
        add_filter( 'gettext', 'rejsm_username_label', 20, 3 );
        function rejsm_username_label( $translated_text, $text, $domain ) {
            if ( 'Username or Email' === $text  ) {
                $translated_text = __( 'Pesel / Email' , 'user_login' );
            }
            if ( 'Username' === $text ) {
                $translated_text = __( 'Pesel' , 'user_login' );
            }
            if ( '<strong>ERROR</strong>: Please enter a username.' === $text ) {
                $translated_text = __( 'Pesel' , 'user_login' );
            }
            if ( 'Usernames cannot be changed.' === $text ) {
                $translated_text = __( 'Pesel nie może być zmieniony. ' , 'user_login' );
            }
            return $translated_text;
        }
    }
    public function remove_admin_stuff( $translated_text, $untranslated_text, $domain ) {
        $custom_field_text = 'Username';

        if ( is_admin() && $untranslated_text === 'Username' ) {
            return "Pesel pacjenta <br>Imie i nazwisko lekarza";
        }

        return $translated_text;
    }
    public function remove_posts_menu() {
        if ( ! function_exists( 'unregister_post_type' ) ) :
        function unregister_post_type( $post_type ) {
            global $wp_post_types;
            if ( isset( $wp_post_types[ $post_type ] ) ) {
                unset( $wp_post_types[ $post_type ] );
                return true;
            }
            return false;
        }
        endif;
        unregister_post_type( 'page' );
    }
    public function hide_the_dashboard(){
        global $current_user;
        // is there a user ?
        if ( is_array( $current_user->roles ) ) {
            // substitute your role(s):
            if ( in_array( 'pacjent', $current_user->roles ) ) {
                // hide the dashboard:
                remove_menu_page( 'index.php' );
            }
        }
    }
    public function new_modify_user_table( $column ) {
        $column['szpital'] = 'Szpital';
        $column['dane_demograficzne'] = 'Dane demograficzne';
        $column['wywiad'] = 'Wywiad';
        $column['diagnostyka'] = 'Diagnostyka';
        $column['leczenie'] = 'Leczenie';        
        $column['aktualne_wyniki'] = 'Aktualne wyniki';
        $column['ocena_klinimetryczna'] = 'Ocema klinimetryczna';
        $column['msis_29'] = 'MSIS-29';
        $column['eq5d'] = 'EQ-5D';
        return $column;
    }
    public function new_modify_user_table_row( $val, $column_name, $user_id ) {
        switch ($column_name) {
            case 'szpital' :
            case 'dane_demograficzne' :
            case 'wywiad' :
            case 'diagnostyka' :
            case 'lecznie' :
            case 'aktualne_wyniki' :
            case 'ocena_klinimetryczna' :
            case 'diagnostyka' :
            case 'msis_29' :
            case 'eq5d' :
                return $this->my_manage_users_columns ($column_name, $user_id);
            default:
                break;
        }
        return $val;
    }
    protected function szpital_substr($string){
        return $string = substr($string, 0, strstr($string, ','));
    }
    public function custom_count_posts_by_author($user_id, $post_type ){
    $args = array(
        'post_type' => $post_type,
        'author'    => $user_id,
        'post_staus'=> 'publish',
        'posts_per_page' => -1
    );

    $query = new WP_Query($args);

    return $query->found_posts;
}
    public function my_manage_users_columns( $column, $author_id ) {
        global $post;
        $out = '';
        switch( $column ) {
            /* If displaying the 'duration' column. */
            case 'eq5d' :
            case 'msis_29' :
                $ilosc_badan = $this->custom_count_posts_by_author( $author_id, $column);
                if ($ilosc_badan == 0){
                    $out = '';
                }
                else {
                    $out = sprintf('<a href="%s">%s</a>', esc_url( add_query_arg( array( 'post_type' => $column, 'author' => $author_id ), 'edit.php' ) ), $ilosc_badan);
                }
                break;
            case 'szpital':
                $i = 'dodaj';
                $term_list = wp_get_object_terms($author_id, 'szpital', array("fields" => "names"));
                foreach($term_list as $term_single) {
                    if ( term_exists ($term_single) ) $i = $term_single;
                }
                //var_dump ($term_list);
                $out = sprintf('<a href="%s#szpital">%s</a>',
                            esc_url( add_query_arg( array( 'user_id' => $author_id ), 'user-edit.php' ) ), strstr($i, ',', true));
                            //esc_url( add_query_arg( array( 'taxonomy' => $column ), 'edit-tags.php' ) ), $i);
                break;
            case 'dane_demograficzne' :
            case 'wywiad' :
            case 'diagnostyka' :
            case 'lecznie' :
            case 'aktualne_wyniki' :
            case 'ocena_klinimetryczna' :
                if ( !count_user_posts( $author_id , $column )){
//                    add_filter( 'post_author', 'filter_post_author' ); 
//                    $out = sprintf('<a href="%s">%s</a>', esc_url( add_query_arg( array( 'post_type' => $column, 'user_id' => $author_id ), 'post-new.php' ) ), 'dodaj');
                }
                else {
                    $out = sprintf('<a href="%s">%s</a>', esc_url( add_query_arg( array( 'post_type' => $column, 'author' => $author_id ), 'edit.php' ) ), 'zobacz');
                }
                break;
        }
        return $out;
    }
//    public function filter_post_author( $author_id ) {
//        return $author_id; 
//    }
    public function remove_user_posts_column($column_headers) {
        unset($column_headers['posts']);
        unset($column_headers['name']);
        return $column_headers;
    }
    public function your_login_redirect( $redirect_to, $request, $user ){
        // is there a user ?
        if ( is_array( $user->roles ) ) {
            // substitute your role(s):
            if ( in_array( 'pacjent', $user->roles ) ) {
                // pick where to redirect to, in the example: Posts page
                return admin_url( 'post-new.php?post_type=msis_29' );
            } else {
                return admin_url();
            }
        }
    }
//    public function add_query_vars($aVars) {
//        $aVars[] = "user_id"; 
//        return $aVars;
//    }
//    public function rejsm_add_query_variable( $query ) {
//        if( ! is_admin() )  return;
//        if ( ! $query->is_main_query() ) return;
//        
////        global $wp_query;
////        $userid = $wp_query->query_vars[ 'user_id'];
//        $userid= get_query_var( 'user_id','true' );
//        if ($userid != 'NULL'){
////            $query->set('author',$userid);
////            $query->set('author_name',$userid);
////            $query->set('user_login',$userid);
////            $query->set('post_author',$userid);
//        }
////        echo '<pre>';  var_dump($query);    echo '</pre>';
////        echo "sfjaslkfdjlaf";var_dump($query);
//    }

}
$uzytkownicy_list = new uzytkownicy_list();





// Admin footer modification





///*-----------------------------------------------------------------------------------*/
///* Remove Unwanted Admin Menu Items */
///*-----------------------------------------------------------------------------------*/

//function remove_admin_menu_items() {
//    $remove_menu_items = array(__('Posts'));
//    global $menu;
//    end ($menu);
//    while (prev($menu)){
//        $item = explode(' ',$menu[key($menu)][0]);
//        if(in_array($item[0] != NULL?$item[0]:"" , $remove_menu_items)){
//        unset($menu[key($menu)]);}
//    }
//}

//add_action('admin_menu', 'remove_admin_menu_items');






/**
 * Changes 'Username' to 'Email Address' on wp-admin login form
 * and the forgotten password form
 *
 * @return null
 */



/**
 * Remove the text at the bottom of the Custom fields box in WordPress Post/Page Editor.
 *
 * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/gettext
 */






//ładownie ankiet automatycznie dla każdego pacjenta


////przekierowuje użytkownika na stronę ankiety msis_29




//function new_contact_methods( $contactmethods ) {
//    $contactmethods['phone'] = 'Phone Number';
//    return $contactmethods;
//}
//add_filter( 'user_contactmethods', 'new_contact_methods', 10, 1 );









// define the post_author callback 


// add the filter 











?>