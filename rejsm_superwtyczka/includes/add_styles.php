<?php
/**
 * Created by PhpStorm.
 * User: mrevening
 * Date: 2016-09-16
 * Time: 18:54
 */





add_action( 'admin_enqueue_scripts', 'rejsm_css_add_style');
function rejsm_css_add_style(){

    $handle = 'css_style_form_dane_demograficzne';
    $src = '/wp-content/plugins/rejsm_superwtyczka/includes/css/css_style_dane_demograficzne.css';
    wp_enqueue_style($handle, $src);

    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

    $handle2 = 'rejsm_ankieta';
    $src2 = '/wp-content/plugins/rejsm_superwtyczka/includes/js/rejsm_ankieta.js';
    wp_enqueue_script($handle2, $src2);

    $handle = 'rejsm_datepicker';
    $src = '/wp-content/plugins/rejsm_superwtyczka/includes/js/rejsm_datepicker.js';
    wp_enqueue_script($handle, $src);

    wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js');

    $handle2 = 'format-metabox-hide';
    $src2 = '/wp-content/plugins/rejsm_superwtyczka/includes/js/format-metabox-hide.js';
    wp_enqueue_script($handle, $src);
}
?>