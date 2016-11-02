<?php
/**
 * Registers the 'profession' taxonomy for users.  This is a taxonomy for the 'user' object type rather than a 
 * post being the object type.
 */
add_action( 'init', 'my_register_user_taxonomy' );
function my_register_user_taxonomy() {

	 register_taxonomy(
		'szpital',
		'user',
		array(
			'public' => true,
			'labels' => array(
				'name' => __( 'Szpitale' ),
				'singular_name' => __( 'Szpital' ),
				'menu_name' => __( 'Szpitale' ),
				'search_items' => __( 'Szukaj szpitali' ),
				'popular_items' => __( 'Popularne szpitale' ),
				'all_items' => __( 'Wszystkie szpitale' ),
				'edit_item' => __( 'Edytuj szpitale' ),
				'update_item' => __( 'Wczytaj szpitale' ),
				'add_new_item' => __( 'Dodaj nowy szpital' ),
				'new_item_name' => __( 'Nowa nazwa szpitala' ),
				'separate_items_with_commas' => __( 'Oddziel szpitale przecinkiem' ),
				'add_or_remove_items' => __( 'Dodaj lub usuñ szpital' ),
				'choose_from_most_used' => __( 'Wybierz spoœród najpopularniejszych szpitali' ),
			),
			'rewrite' => array(
				'with_front' => true,
				'slug' => 'user/szpital' // Use 'author' (default WP user slug).
			),
			'capabilities' => array(
//				'manage_terms' => 'read', // Using 'edit_users' cap to keep this simple.
//				'edit_terms'   => 'edit_user',
//				'delete_terms' => 'edit_user',
//				'assign_terms' => 'read',
//				'manage_szpital' => 'manage_categories',
//				'edit_szpital'   => 'manage_categories',
//				'delete_szpital' => 'manage_categories',
				'manage_terms' => 'manage_szpital',
				'assign_terms' => 'assign_szpital',
				'edit_terms'   => 'edit_szpital',
				'delete_terms' => 'delete_szpital',
			),
			'update_count_callback' => 'my_update_szpital_count' // Use a custom function to update the count.
		)
	);
}



////sprawdzenie czy zarejestronano taksonomie
//function debug_in_footer_ok ()
//{
//    echo '<span id="footer-thankyou">Wszystko ok</span>';
//}
//function debug_in_footer_error ()
//{
//    echo '<span id="footer-thankyou">Error</span>';
//}

//if ( 'taxonomy_exists' ){
//    add_filter( 'admin_footer_text', 'debug_in_footer_ok' );
//}
//else add_filter( 'admin_footer_text', 'debug_in_footer_error' );




/**
 * Function for updating the 'profession' taxonomy count.  What this does is update the count of a specific term 
 * by the number of users that have been given the term.  We're not doing any checks for users specifically here. 
 * We're just updating the count with no specifics for simplicity.
 *
 * See the _update_post_term_count() function in WordPress for more info.
 *
 * @param array $terms List of Term taxonomy IDs
 * @param object $taxonomy Current taxonomy object of terms
 */
function my_update_szpital_count( $terms, $taxonomy ) {
    global $wpdb;

    foreach ( (array) $terms as $term ) {

        $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $term ) );

        do_action( 'edit_term_taxonomy', $term, $taxonomy );
        $wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );
        do_action( 'edited_term_taxonomy', $term, $taxonomy );
    }
}



/* Adds the taxonomy page in the admin. */
add_action( 'admin_menu', 'my_add_szpital_admin_page' );

/**
 * Creates the admin page for the 'profession' taxonomy under the 'Users' menu.  It works the same as any 
 * other taxonomy page in the admin.  However, this is kind of hacky and is meant as a quick solution.  When 
 * clicking on the menu item in the admin, WordPress' menu system thinks you're viewing something under 'Posts' 
 * instead of 'Users'.  We really need WP core support for this.
 */
function my_add_szpital_admin_page() {

    $tax = get_taxonomy( 'szpital' );

    add_users_page(
        esc_attr( $tax->labels->menu_name ),
        esc_attr( $tax->labels->menu_name ),
        $tax->cap->manage_terms,
        'edit-tags.php?taxonomy=' . $tax->name
    );
}




///* Create custom columns for the manage profession page. */
//add_filter( 'manage_edit-profession_columns', 'my_manage_profession_user_column' );

///**
// * Unsets the 'posts' column and adds a 'users' column on the manage profession admin page.
// *
// * @param array $columns An array of columns to be shown in the manage terms table.
// */
//function my_manage_profession_user_column( $columns ) {

//    unset( $columns['posts'] );

//    $columns['users'] = __( 'Users' );

//    return $columns;
//}

///* Customize the output of the custom column on the manage professions page. */
//add_action( 'manage_profession_custom_column', 'my_manage_profession_column', 10, 3 );

///**
// * Displays content for custom columns on the manage professions page in the admin.
// *
// * @param string $display WP just passes an empty string here.
// * @param string $column The name of the custom column.
// * @param int $term_id The ID of the term being displayed in the table.
// */
//function my_manage_profession_column( $display, $column, $term_id ) {

//    if ( 'users' === $column ) {
//        $term = get_term( $term_id, 'profession' );
//        echo $term->count;
//    }
//}



/* Add section to the edit user page in the admin to select profession. */
add_action( 'show_user_profile', 'my_edit_user_profession_section' );
add_action( 'edit_user_profile', 'my_edit_user_profession_section' );

/**
 * Adds an additional settings section on the edit user/profile page in the admin.  This section allows users to 
 * select a profession from a checkbox of terms from the profession taxonomy.  This is just one example of 
 * many ways this can be handled.
 *
 * @param object $user The user object currently being edited.
 */
function my_edit_user_profession_section( $user ) {

	$tax = get_taxonomy( 'szpital' );

	/* Make sure the user can assign terms of the profession taxonomy before proceeding. */
	if ( !current_user_can( $tax->cap->assign_terms ) )
		return;

	/* Get the terms of the 'profession' taxonomy. */
	$terms = get_terms( 'szpital', array( 'hide_empty' => false ) ); ?>

	<h3><?php _e( 'Szpital' ); ?></h3>

	<table class="form-table">

		<tr>
			<th><label for="profession"><?php _e( 'Select Szpital' ); ?></label></th>

			<td><?php

            if ( !empty( $terms ) ) {
                ?> <select id="szpital" name="szpital" > <?php
                ?> <option disabled selected value></option> <?php
                foreach ( $terms as $term ) { ?>
                     <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( true, is_object_in_term( $user->ID, 'szpital', $term ) ); ?> /> <?php echo $term->name; ?> </option>
                <?php } ?> </select> <?php
			}
			/* If there are no profession terms, display a message. */
			else {
				_e( 'There are no hospitals available.' );
			}
			?></td>
		</tr>

	</table>
<?php }


/* Update the profession terms when the edit user page is updated. */
add_action( 'personal_options_update', 'my_save_user_profession_terms' );
add_action( 'edit_user_profile_update', 'my_save_user_profession_terms' );

/**
 * Saves the term selected on the edit user/profile page in the admin. This function is triggered when the page 
 * is updated.  We just grab the posted data and use wp_set_object_terms() to save it.
 *
 * @param int $user_id The ID of the user to save the terms for.
 */
function my_save_user_profession_terms( $user_id ) {

    $tax = get_taxonomy( 'szpital' );

    /* Make sure the current user can edit the user and assign terms before proceeding. */
    if ( !current_user_can( 'edit_user', $user_id ) && current_user_can( $tax->cap->assign_terms ) )
        return false;

    $term = esc_attr( $_POST['szpital'] );

    /* Sets the terms (we're just using a single term) for the user. */
    wp_set_object_terms( $user_id, array( $term ), 'szpital', false);

    clean_object_term_cache( $user_id, 'szpital' );
}


function cods() { 
    //////////////////usun
    /* If there are any profession terms, loop through them and display checkboxes. */
    if ( !empty( $terms ) ) {
        
        foreach ( $terms as $term ) { ?>
					<input 
                        type="radio" 
                        name="profession" 
                        id="profession-<?php echo esc_attr( $term->slug ); ?>" 
                        value="<?php echo esc_attr( $term->slug ); ?>" 
                        <?php checked( true, is_object_in_term( $user->ID, 'szpital', $term ) ); ?> /> 
                        <label for="profession-<?php echo esc_attr( $term->slug ); ?>">
                        <?php echo $term->name; ?>
                        </label> 
                        <br />
				<?php }
    }
}
//////////////////////dot¹d mo¿esz usun¹æ.


//wp_tag_cloud( array( 'taxonomy' => 'profession' ) ); 





///**
// * Function for outputting the correct text in a tag cloud.  Use as the 'update_topic_count_callback' argument 
// * when calling wp_tag_cloud().  Instead of 'topics' it displays 'users'.
// *
// * @param int $count The count of the objects for the term.
// */
//function my_profession_count_text( $count ) {
//    return sprintf( _n('%s user', '%s users', $count ), number_format_i18n( $count ) );
//}




?>