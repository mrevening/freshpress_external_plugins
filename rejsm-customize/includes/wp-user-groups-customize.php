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
                                                    <input id="cb-select-all-1" type="checkbox">
                                            </td>
                                            <th scope="col" class="manage-column column-name column-primary"><?php esc_html_e( 'Name', 'wp-user-groups' ); ?></th>
                                            <th scope="col" class="manage-column column-description"><?php esc_html_e( 'Description', 'wp-user-groups' ); ?></th>
                                            <th scope="col" class="manage-column column-users"><?php esc_html_e( 'Users', 'wp-user-groups' ); ?></th>
                                    </tr>
                            </thead>
                            <tbody>

                                    <?php if ( ! empty( $terms ) ) :

                                            foreach ( $terms as $term ) :
                                                $term_childs = get_term_children( $term->term_id, $tax->name );

                                                if ( empty ( $term_childs )) {

                                                    $active = is_object_in_term( $user->ID, $this->taxonomy, $term->slug ); ?>

                                                    <tr class="<?php echo ( true === $active ) ? 'active' : 'inactive'; ?>">
                                                            <th scope="row" class="check-column">
                                                                    <input type="radio" name="<?php echo esc_attr( $this->taxonomy ); ?>[]" id="<?php echo esc_attr( $this->taxonomy ); ?>-<?php echo esc_attr( $term->slug ); ?>" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( $active ); ?> />
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

                                                }
                                                else { ?>
                                                    <tr class="inactive">asfdj
                                                    </tr>
                                                        <?php
                                                }
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
                                            <td 
                                            </td>
                                            <th </th>
                                            <th </th>
                                            <th </th>
                                    </tr>
                                    <tr>
                                            <td class="manage-column column-cb check-column">
                                                    <label class="screen-reader-text" for="cb-select-all-2"><?php esc_html_e( 'Select All', 'wp-user-groups' ); ?></label>
                                                    <input id="cb-select-all-2" type="checkbox">
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
}
////$c = new Rejsm_User_Taxonomy();
//$q = new Rejsm_User_Taxonomy();
?>