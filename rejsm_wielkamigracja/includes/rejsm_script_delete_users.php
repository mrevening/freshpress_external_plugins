<?php
function my_delete_user($user_id) {
//    echo 'halo3<BR/>';

    $args = array (
        'numberposts' => -1,
        'post_type' => array ('msis_29', 'eq5d','dane_demograficzne'),
        'author' => $user_id
    );


    // get all posts by this user: posts, pages, attachments, etc..
    $user_posts = get_posts($args);
//    echo 'halo4<BR/>';
//    echo "jestem w funckji delete user <BR/>";
//    if (empty($user_posts)) echo "bład <BR/>";
//    else{
    // delete all the user posts
    foreach ($user_posts as $user_post) {
        wp_delete_post($user_post->ID, true);
//        echo 'usuwam post <BR/>';
       }
//    }
    wp_delete_user( $user_id);
}
function rejsm_delete_users_oldrejsm( $roles = array ('pacjent','lekarz') ) {
    foreach ($roles as $role) {
        $users = get_users(
            array(
                'role' => $role
            )
        );
        if (is_array($users)) {
            foreach ($users as $user) {
                my_delete_user($user->ID);
//            add_action('delete_user', $user->ID);
            }
        } else {
            return new WP_Error('broke', __("Brak użytkowników."));
        }
    }





}
$return = rejsm_delete_users_oldrejsm();
    if (is_wp_error( $return )) {
        echo '<div id="message" class="error">Zaistniał błąd podczas wykonywania skryptu. '.$return->get_error_message().'</div>';
    }
    else {
        echo '<div id="message" class="updated">Pomyślnie wykonano skrypt - usunięto wszystkich dotychczasowych użytkowników. </div>';
    }
?>