<?php


add_role( 'pacjent',
    'Pacjent',
    array(
        'read' => true,
        'read_dane_pacjenta' => true,
        'edit_dane_pacjenta' => true,
        'publish_dane_pacjenta' => true,
        'edit_dane_pacjentas' => true,
        'delete_dane_pacjenta' => false,
        'read_private_dane_pacjenta' => false,
        'edit_others_dane_pacjenta' => false,
        'read_msis_29' => true,
        'edit_msis_29' => true,
        'publish_msis_29' => true,
        'edit_msis_29s' => true,
        'delete_msis_29' => false,
        'read_private_msis_29' => false,
        'edit_others_msis_29' => false,
        'read_eq5d' => true,
        'edit_eq5d' => true,
        'publish_eq5d' => true,
        'edit_eq5ds' => true,
        'delete_eq5d' => false,
        'read_private_eq5d' => false,
        'edit_others_eq5d' => false,
    )
);

if ( !current_user_can( 'pacjent') ) {
    add_action( 'admin_bar_menu', 'wpse126922_remove_newpost', 999 );
    add_action( 'admin_menu', 'hide_add_new');
    add_action( 'load-post-new.php', 'disable_new_post' );
}

add_role( 'lekarz',
    'Lekarz',
    array(
        'read' => true,
        'list_users' => true,
        'add_users' => true,
        'create_users' => true,
        'edit_users' => true,
        'read_dane_pacjenta' => true,
        'edit_dane_pacjenta' => true,
        'publish_dane_pacjenta' => true,
        'edit_dane_pacjentas' => true,
        'delete_dane_pacjenta' => false,
        'read_private_dane_pacjenta' => false,
        'edit_others_dane_pacjenta' => false,
        'read_private_msis_29' => true,
        'edit_others_msis_29' => true,
        'read_msis_29' => true,
        'edit_msis_29s' => true,
        'delete_msis_29' => true,
        'edit_msis_29' => true,
        'publish_msis_29' => false,
        'read_private_eq5d' => true,
        'edit_others_eq5d' => true,
        'read_eq5d' => true,
        'edit_eq5ds' => true,
        'delete_eq5d' => true,
        'edit_eq5d' => true,
        'publish_eq5d' => false,
    ));

$role = get_role( 'administrator' );
if ( !empty ($role) ){

    $role->add_cap('manage_szpital');
    $role->add_cap('assign_szpital');
    $role->add_cap('edit_szpital');
    $role->add_cap('delete_szpital');

    $role->add_cap('read_dane_pacjenta');
    $role->add_cap('read_private_dane_pacjenta');
    $role->add_cap('edit_dane_pacjenta');
    $role->add_cap('edit_dane_pacjenta');
    $role->add_cap('edit_others_dane_pacjenta');
    $role->add_cap('publish_dane_pacjenta');
    $role->add_cap('delete_dane_pacjenta');
    $role->add_cap('delete_dane_pacjenta' );

    $role->add_cap('read_msis_29');
    $role->add_cap('read_private_msis_29');
    $role->add_cap('edit_msis_29');
    $role->add_cap('edit_msis_29s');
    $role->add_cap('edit_others_msis_29');
    $role->add_cap('publish_msis_29');
    $role->add_cap('delete_msis_29');

    $role->add_cap('read_eq5d');
    $role->add_cap('edit_eq5d');
    $role->add_cap('publish_eq5d');
    $role->add_cap('edit_eq5ds');
    $role->add_cap('delete_eq5d');
    $role->add_cap('read_private_eq5d');
    $role->add_cap('edit_others_eq5d');

    $role->remove_cap('read_post');
    $role->remove_cap('edit_post');
    $role->remove_cap('delete_post');
    $role->remove_cap('edit_posts');
    $role->remove_cap('publish_post');
    $role->remove_cap('read_private_post');
    $role->remove_cap('edit_others_post');
    $role->remove_cap('manage_categories');
    $role->remove_cap('edit_others_post');
    $role->remove_cap('delete_others_posts');
    $role->remove_cap('delete_others_pages');
    $role->remove_cap('delete_private_pages');
    $role->remove_cap('delete_private_posts');
    $role->remove_cap('delete_posts');
    $role->remove_cap('delete_published_posts');
    $role->remove_cap('edit_others_post');
    $role->remove_cap('edit_posts');
    $role->remove_cap('edit_private_posts');
    $role->remove_cap('edit_published_posts');
    $role->remove_cap('read_private_posts');
}



remove_role( 'subscriber');
remove_role( 'contributor');
remove_role( 'author');
remove_role( 'editor');

update_option( 'default_role', 'pacjent');
update_option( 'users_can_register', '1');




function wpse126922_remove_newpost($wp_admin_bar) {
    $wp_admin_bar->remove_node('new-eq5d');
    $wp_admin_bar->remove_node('new-msis_29');
    $wp_admin_bar->remove_node('new-dane_pacjenta');

}
function hide_add_new() {
    //    global $submenu;
    //    print "<pre>";
    //    print_r($submenu); exit;
    //    print "</pre>";

    //    remove_submenu_page( 'edit.php?post_type=msis_29', 'post-new.php?post_type=msis_29' );
    //    remove_submenu_page( 'edit.php?post_type=eq5d', 'post-new.php?post_type=eq5d' );
    //    remove_submenu_page( 'edit.php?post_type=eq5d', 'post-new.php?post_type=eq5d' );
    //    unset($submenu['post-new.php?post_type=eq5d']);
    //    unset($submenu['post-new.php?post_type=msis_29'][10]);

}
function disable_new_post() {
    if ( get_current_screen()->post_type == 'eq5d' )
        wp_die( "Ankiety EQ-5D są przeznaczone do indywidualnego wypełniania przez pacjentów." );
    if ( get_current_screen()->post_type == 'msis_29' )
        wp_die( "Ankiety MSIS-29 są przeznaczone do indywidualnego wypełniania przez pacjentów." );
}


//add_action('after_setup_theme','my_add_role_function');
//function my_add_role_function(){
//    $roles_set = get_option('my_roles_are_set');
//    if(!$roles_set){
//        add_role('my_role', 'my_roleUser', array(
//            'read' => true, // True allows that capability, False specifically removes it.
//            'read_post' => false,

//        ));
//        update_option('my_roles_are_set',true);
//    }
//}

///**
// * Remove capabilities from administators.
// *
// * Call the function when your plugin/theme is activated.
// */
//function wpcodex_set_capabilities() {

//    // Get the role object.
//    $editor = get_role( 'administrator' );

//    // A list of capabilities to remove from administrators
//    $caps = array(
//        'read_post',
//        'edit_post',
//        'publish_post',
//        'read_private_post',
//        'delete_posts',
//    );

//    foreach ( $caps as $cap ) {

//        // Remove the capability.
//        $editor->remove_cap( $cap );
//    }
//}
//add_action( 'init', 'wpcodex_set_capabilities' );



//
//$role = get_role( 'lekarz' );
//if ( !empty ($role) ){
//    //$role->add_cap('read');
//
//
////    $role->add_cap('read_book');
////    $role->add_cap('delete_book');
////    $role->add_cap('edit_books');
////    $role->add_cap('edit_others_books');
////    $role->add_cap('publish_books');
////    $role->add_cap('read_private_books');
////    $role->add_cap('edit_books');
//
////    $role->add_cap('list_users');
////    $role->add_cap('remove_users');
////    $role->add_cap('delete_users');
////    $role->add_cap('edit_users');
////    $role->add_cap('create_users');
//
//    $role->add_cap('read_msis_29');
//    $role->add_cap('edit_msis_29');
//    $role->add_cap('edit_msis_29s');
//    $role->add_cap('delete_msis_29');
//    $role->add_cap('edit_others_msis_29');
//    $role->add_cap('publish_msis_29');
//    $role->add_cap('read_private_msis_29');
//
//}


///*Pobranie roli pacjenta*/
//$role = get_role( 'pacjent' );
//if ( !empty ($role) ){
//
//
//    $role->add_cap('read_msis_29');
//    $role->add_cap('edit_msis_29');
//    $role->add_cap('edit_msis_29s');
//    $role->add_cap('delete_msis_29');
//    $role->add_cap('publish_msis_29');
//    //$role->add_cap('edit_others_msis_29');
//    //$role->add_cap('read_private_msis_29');
//
//    //$role->remove_cap('read_msis_29');
//    //$role->remove_cap('edit_msis_29');
//    //$role->remove_cap('edit_msis_29s');
//    //$role->remove_cap('delete_msis_29');
//    //$role->remove_cap('edit_others_msis_29');
//    //$role->remove_cap('publish_msis_29');
//    //$role->remove_cap('read_private_msis_29');
//}

//function posts_for_current_author($query) {
//	global $pagenow;
//
//	if( 'edit.php' != $pagenow || !$query->is_admin )
//	    return $query;
//
//	if( !current_user_can( 'manage_options' ) ) {
//		global $user_ID;
//		$query->set('author', $user_ID );
//	}
//	return $query;
//}
//add_filter('pre_get_posts', 'posts_for_current_author');




class JPB_User_Caps {

  // Add our filters
  function JPB_User_Caps(){
    add_filter( 'editable_roles', array(&$this, 'editable_roles'));
    add_filter( 'map_meta_cap', array(&$this, 'map_meta_cap'),10,4);
  }

  // Remove 'Administrator' from the list of roles if the current user is not an admin
  function editable_roles( $roles ){
    if( isset( $roles['administrator'] ) && !current_user_can('administrator') ){
      unset( $roles['administrator']);
    }
    return $roles;
  }

// If someone is trying to edit or delete and admin and that user isn't an admin, don't allow it
  function map_meta_cap( $caps, $cap, $user_id, $args ){

    switch( $cap ){
        case 'edit_user':
        case 'remove_user':
        case 'promote_user':
            if( isset($args[0]) && $args[0] == $user_id )
                break;
            elseif( !isset($args[0]) )
                $caps[] = 'do_not_allow';
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) ){
                if(!current_user_can('administrator')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        case 'delete_user':
        case 'delete_users':
            if( !isset($args[0]) )
                break;
            $other = new WP_User( absint($args[0]) );
            if( $other->has_cap( 'administrator' ) ){
                if(!current_user_can('administrator')){
                    $caps[] = 'do_not_allow';
                }
            }
            break;
        default:
            break;
    }
    return $caps;
  }

}

$jpb_user_caps = new JPB_User_Caps();

?>