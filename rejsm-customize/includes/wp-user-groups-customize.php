<?php

if ( function_exists('wp_register_default_user_group_taxonomy') ){
    remove_action( 'init', 'wp_register_default_user_group_taxonomy' );
    add_action ('init','wp_register_default_user_dane_demograficzne_taxonomy');
}
else{
    add_action( 'admin_notices', 'my_plugin_patch_error' );
}
function my_plugin_patch_error() {
    $class = 'notice notice-error';
    $message = __( ' plugin patch "wp-user-groups/inludes/taxonomies.php line 21" not workng any longer</BR>');
    printf( '%2$s', $class, $message );
} 
function wp_register_default_user_dane_demograficzne_taxonomy() {
        new Rejsm_User_Taxonomy( 'dane_demograficzne',  'dane_demograficzne',  array(
                'singular' => __( 'Dane demograficzne',  'wp-user-groups' ),
                'plural'   => __( 'Dane demograficzne', 'wp-user-groups' )
        ) );
}





if ( function_exists('wp_register_default_user_type_taxonomy') ){
    remove_action( 'init', 'wp_register_default_user_type_taxonomy' );
    add_action ('init','wp_register_default_user_wywiad_taxonomy');
}
else{
    add_action( 'admin_notices', 'my_plugin_patch_error2' );
}
function my_plugin_patch_error2() {
    $class = 'notice notice-error';
    $message = __( ' plugin patch "wp-user-groups/inludes/taxonomies.php line 37" not workng any longer</BR>');
    printf( '%2$s', $class, $message );
}
function wp_register_default_user_wywiad_taxonomy() {
        new Rejsm_User_Taxonomy( 'wywiad',  'wywiad',  array(
                'singular' => __( 'Wywiad',  'wp-user-groups' ),
                'plural'   => __( 'Wywiad', 'wp-user-groups' )
        ) );
}
add_action ('init','wp_register_default_user_szpital_taxonomy');
function wp_register_default_user_szpital_taxonomy() {
        new Rejsm_User_Taxonomy( 'szpital',  'szpital',  array(
                'singular' => __( 'szpital',  'wp-user-groups' ),
                'plural'   => __( 'szpital', 'wp-user-groups' )
        ) );
}








if ( function_exists('wp_user_groups_add_profile_section') ){
    remove_filter( 'wp_user_profiles_sections', 'wp_user_groups_add_profile_section' );
    add_filter ('wp_user_profiles_sections','rejsm_user_groups_add_profile_section');
}
else{
    add_action( 'admin_notices', 'my_plugin_patch_error3' );
}
function my_plugin_patch_error3() {
    $class = 'notice notice-error';
    $message = __( ' plugin patch "wp-user-groups/inludes/admin.php line 51" not workng any longer</BR>');
    printf( '%2$s', $class, $message );
}
function rejsm_user_groups_add_profile_section( $sections = array() ) {
        
	// Copy for modifying
	$new_sections = $sections;

	// Add the "Activity" section
	$new_sections['groups'] = array(
		'id'    => 'groups',
		'slug'  => 'groups',
		'name'  => esc_html__( 'Dane', 'wp-user-activity' ),
		'cap'   => 'edit_profile',
		'icon'  => 'dashicons-groups',
		'order' => 90
	);
//        $new_sections['groups2'] = array(
//		'id'    => 'groups2',
//		'slug'  => 'groups2',
//		'name'  => esc_html__( 'Wywiad', 'wp-user-activity' ),
//		'cap'   => 'edit_profile',
//		'icon'  => 'dashicons-groups',
//		'order' => 91
//	);

	// Filter & return
	return apply_filters( 'wp_user_groups_add_profile_section', $new_sections, $sections );
}








class Rejsm_User_Taxonomy extends WP_User_Taxonomy {
    protected function table_contents( $user, $tax, $terms, $typ = 'radio' ) {
                    ?>

                    <table class="wp-list-table widefat fixed striped user-groups">
                            <thead>
                                    <tr>
                                            <td id="cb" class="manage-column column-cb check-column">
                                                    <label class="screen-reader-text" for="cb-select-all-1"><?php esc_html_e( 'Select All', 'wp-user-groups' ); ?></label>
                                                    <!--<input id="cb-select-all-1" type="checkbox">-->
                                            </td>
                                            <th scope="col" class="manage-column column-name column-primary"><?php esc_html_e( 'Name', 'wp-user-groups' ); ?></th>
                                            <th scope="col" class="manage-column column-description"><?php esc_html_e( 'Description', 'wp-user-groups' ); ?></th>
                                            <th scope="col" class="manage-column column-users"><?php esc_html_e( 'Users', 'wp-user-groups' ); ?></th>
                                    </tr>
                            </thead>
                            <tbody>

                                    <?php if ( ! empty( $terms ) ) :
                                            $term_description_previous = $terms[0]->description;
                                            foreach ( $terms as $term ) :
//                                                $term_childs = get_term_children( $term->term_id, $tax->name );
//                                                $desc = term_description( $term->term_id, $tax->name );
                                                if ( empty ( $term->description ) || $term->description !== $term_description_previous ) {
                                                    $term_description_previous = $term->description;
                                                    ?>
                                                    <tr class="inactive"><td> </td><th </th><th </th><th </th></tr>
                                                        <?php
                                                }
                                                
                                                    $active = is_object_in_term( $user->ID, $this->taxonomy, $term->slug ); ?>

                                                    <tr class="<?php echo ( true === $active ) ? 'active' : 'inactive'; ?>">
                                                            <th scope="row" class="check-column">
                                                                    <input type="radio" name="<?php echo esc_attr( $term->description ); ?>" id="<?php echo esc_attr( $this->taxonomy ); ?>-<?php echo esc_attr( $term->slug ); ?>" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( $active ); ?> />
                                                                    <label for="<?php echo esc_attr( $this->taxonomy ); ?>-<?php echo esc_attr( $term->slug ); ?>"></label>
                                                            </th>
                                                            <td class="column-primary">
                                                                    <strong><?php echo ($term->icon); echo esc_html( $term->name ); ?></strong>
                                                                    <div class="row-actions">
                                                                            <?php echo $this->row_actions( $tax, $term ); ?>
                                                                    </div>
                                                            </td>
                                                            <td class="column-description"><?php echo ! empty( $term->description ) ? esc_html( $term->description ) : '&#8212;'; ?></td>
                                                            <td class="column-users"><?php echo esc_html( $term->count ); ?></td>
                                                    </tr>

                                                <?php

//                                                }
                                                
                                            endforeach;

                                    // If there are no user groups
                                    else : ?>

                                            <tr>
                                                    <td colspan="4">

                                                            <?php echo esc_html( $tax->labels->not_found ); ?>

                                                    </td>
                                            </tr>

                                    <?php endif; ?>

                            </tbody>
                            <tfoot>

                                    <tr>
                                            <td class="manage-column column-cb check-column">
                                                    <label class="screen-reader-text" for="cb-select-all-2"><?php esc_html_e( 'Select All', 'wp-user-groups' ); ?></label>
                                                    <!--<input id="cb-select-all-2" type="checkbox">-->
                                            </td>
                                            <th scope="col" class="manage-column column-name column-primary"><?php esc_html_e( 'Name', 'wp-user-groups' ); ?></th>
                                            <th scope="col" class="manage-column column-description"><?php esc_html_e( 'Description', 'wp-user-groups' ); ?></th>
                                            <th scope="col" class="manage-column column-users"><?php esc_html_e( 'Users', 'wp-user-groups' ); ?></th>
                                    </tr>
                            </tfoot>
                    </table>

                    <?php
    }
//
//    public function __construct() {
////        parent::__construct();
//        $this->unregister_parent_hook();
//
//    }
//    public function unregister_parent_hook() {
//        global $p;
//        remove_action( 'admin_init',  array( $p, 'bulk_edit_action' ) );
//        remove_filter( 'views_users', array( $p, 'bulk_edit'        ) );
//    }

////$c = new Rejsm_User_Taxonomy();
//$q = new Rejsm_User_Taxonomy();



    public function save_terms_for_user( $user_id = 0 ) {

                    // Additional checks if User Profiles is active
                    if ( function_exists( 'wp_user_profiles_get_section_hooknames' ) ) {

                            // Bail if no page
                            if ( empty( $_GET['page'] ) ) {
                                    return;
                            }

                            // Bail if not saving this section
                            if ( sanitize_key( $_GET['page'] ) !== 'groups' ) {
                                    return;
                            }
                    }

                    // Set terms for user
                    $terms_array = array();
                    $taxonomy = get_taxonomy($this->taxonomy);
                    foreach (get_terms($taxonomy) as $term ) {
                        var_dump($term);
                        $term_description_previous = $terms[0]->description;
                        foreach ($this->terms as $term ){
                            if ( empty ( $term->description ) || $term->description !== $term_description_previous ) {
                                $term_description_previous = $term->description;
                            } else {
                                $terms_array = array_merge($terms_array, $term->slug);
                            }
                    }
                    wp_set_terms_for_user( $user_id, $this->taxonomy, $terms_array  );
            }
    }
}


//function wp_set_terms_for_user( $user_id, $taxonomy, $terms = array(), $bulk = false ) {
//
//	// Get the taxonomy
//	$tax = get_taxonomy( $taxonomy );
//
//	// Make sure the current user can edit the user and assign terms before proceeding.
//	if ( ! current_user_can( 'edit_user', $user_id ) && current_user_can( $tax->cap->assign_terms ) ) {
//		return false;
//	}
//
//	if ( empty( $terms ) && empty( $bulk ) ) {
//		$terms = isset( $_POST[ $taxonomy ] )
//			? $_POST[ $taxonomy ]
//			: null;
//	}
//
//	// Delete all user terms
//	if ( is_null( $terms ) || empty( $terms ) ) {
//		wp_delete_object_term_relationships( $user_id, $taxonomy );
//
//	// Set the terms
//	} else {
//		$_terms = array_map( 'sanitize_key', $terms );
//
//		// Sets the terms for the user
//		wp_set_object_terms( $user_id, $_terms, $taxonomy, false );
//	}
//
//	// Clean the cache
//	clean_object_term_cache( $user_id, $taxonomy );
//}

?>