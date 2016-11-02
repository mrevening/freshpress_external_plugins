<?php


//add_action( 'wp_enqueue_scripts', 'wpb_adding_js' );
//admin_enqueue_scripts
//login_enqueue_scripts

add_action( 'login_enqueue_scripts', 'rejsm_pesel_verification');
function rejsm_pesel_verification(){
    $handle = 'js_pesel_verification';
    $src =  '/wp-content/plugins/rejsm_superwtyczka/includes/js/js_pesel_verification.js';
    wp_enqueue_script( $handle, $src);
}

//1. Add a new form element...
//<input onkeypress='return isNumber(event)' type="text" name="key_pesel" id="user_login" maxlength="11" value="<?php echo esc_attr( wp_unslash( $pesel ) ); 
add_action( 'register_form', 'myplugin_register_form' );
function myplugin_register_form() {
    $pesel = ( ! empty( $_POST['key_pesel'] ) ) ? trim( $_POST['key_pesel'] ) : '';
?>
<p>
    <label for="pesel">
        <?php _e( 'Pesel', 'mydomain' ) ?>
        <br />
        <input name="user_login" id="user_login" class="input"  size="20" type="text" maxlength="11" value="<?php echo esc_attr( wp_unslash( $pesel ) ); ?>" onkeypress='return isNumber(event)'>
    </label>
</p>
<?php
}
//2. Add validation. In this case, we make sure pesel is required.
add_filter( 'registration_errors', 'myplugin_registration_errors', 10, 3 );
function myplugin_registration_errors( $errors, $sanitized_user_login, $user_email ) {
    //require_once dirname(__FILE__) . '/rejsm_validate_pesel.php';
    if ( empty( $_POST['user_login'] ) || ! empty( $_POST['user_login'] ) && trim( $_POST['user_login'] ) == '' ) {
        $errors->add( 'pesel_error', __( '<strong>BŁĄD</strong>: Proszę wprowadzić pesel.', 'mydomain' ) );
    }
    else if (! validatepesel($_POST['user_login'] ) ) {
        $errors->add( 'pesel_error', __( '<strong>BŁĄD</strong>: Nieprawidłowy format numeru pesel.', 'mydomain' ) );
    }
    else if ( pesel_exists($_POST['user_login'] ) ) {
        $errors->add( 'pesel_error', __( '<strong>BŁĄD</strong>: Pesel jest już w systemie.', 'mydomain' ) );
    }
    if (!empty( $errors->errors ) ) {
        add_action('login_head', 'wpb_add_loginshake');
        function wpb_add_loginshake() {
            add_action('login_head', 'wp_shake_js', 12);
        }
    }
    return $errors;
}

//3. Finally, save our extra registration user meta.
add_action( 'user_register', 'myplugin_user_register' );
function myplugin_user_register( $user_id ) {
    if ( ! empty( $_POST['user_login'] ) ) {
        update_user_meta( $user_id, 'user_login', trim( $_POST['user_login'] ) );
    }
}





function validatepesel($pesel) {
    $reg = '/^[0-9]{11}$/';
    if(preg_match($reg, $pesel)==false)
        return false;
    else
    {
        $dig = str_split($pesel);
        $kontrola = (1*intval($dig[0]) + 3*intval($dig[1]) + 7*intval($dig[2]) + 9*intval($dig[3]) + 1*intval($dig[4]) + 3*intval($dig[5]) + 7*intval($dig[6]) + 9*intval($dig[7]) + 1*intval($dig[8]) + 3*intval($dig[9]))%10;
        if($kontrola == 0) $kontrola = 10;
        $kontrola = 10 - $kontrola;
        if(intval($dig[10]) == $kontrola)
            return true;
        else
            return false;
    }
}

function pesel_exists( $pesel ) {
    if ( get_users(array('meta_key' => 'pesel', 'meta_value' => $pesel) ) ) {
        return true;
    }
    return false;
}






//remove username input

add_action('login_head', function(){
?>
    <style>
        #registerform > p:first-child{
            display:none;
        }
    </style>

    <script type="text/javascript" src="<?php echo site_url('/wp-includes/js/jquery/jquery.js'); ?>"></script>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            $('#registerform > p:first-child').css('display', 'none');
        });
    </script>
<?php

//Remove error for username, only show error for email only.
add_filter('registration_errors', function($wp_error, $sanitized_user_login, $user_email){
    if(isset($wp_error->errors['empty_username'])){
        unset($wp_error->errors['empty_username']);
    }

    if(isset($wp_error->errors['username_exists'])){
        unset($wp_error->errors['username_exists']);
    }
    return $wp_error;
}, 10, 3);


});



?>
