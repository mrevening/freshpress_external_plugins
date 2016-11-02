<?php

add_theme_support( 'post-formats', array( 'link', 'quote' ) );
$metaboxes = array(
    'link_url' => array(
        'title' => __('link information', 'twentyeleven'),
        'applicableto' => 'post',
        'location' => 'normal',
        'display_condition' => 'post-format-link',
        'priority' => 'low',
        'fields' => array(
            'l_url' => array(
                'title' => __('link url:', 'twentyeleven'),
                'type' => 'text',
                'description' => '',
                'size' => 60
            )
        )
    ),
    'quote_author' => array(
        'title' => __('quote author', 'twentyeleven'),
        'applicableto' => 'post',
        'location' => 'normal',
        'display_condition' => 'post-format-quote',
        'priority' => 'low',
        'fields' => array(
            'q_author' => array(
                'title' => __('quote author:', 'twentyeleven'),
                'type' => 'text',
                'description' => '',
                'size' => 20
            )
        )
    )
);
add_action( 'admin_init', 'add_post_format_metabox' );
function add_post_format_metabox() {
    global $metaboxes;
    if ( ! empty( $metaboxes ) ) {
        foreach ( $metaboxes as $id => $metabox ) {
            add_meta_box( $id, $metabox['title'], 'show_metaboxes', $metabox['applicableto'], $metabox['location'], $metabox['priority'], $id );
        }
    }
}
function show_metaboxes( $post, $args ) {
    global $metaboxes;
    $custom = get_post_custom( $post->ID );
    $fields = $tabs = $metaboxes[$args['id']]['fields'];
    /** Nonce **/
    $output = '<input type="hidden" name="post_format_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';
    if ( sizeof( $fields ) ) {
        foreach ( $fields as $id => $field ) {
            switch ( $field['type'] ) {
                default:
                case "text":
                   $output .= '<label for="' . $id . '">' . $field['title'] . '</label><input id="' . $id . '" type="text" name="' . $id . '" value="' . $custom[$id][0] . '" size="' . $field['size'] . '" />';
                    break;
            }
        }
    }
    echo $output;
}
add_action( 'save_post', 'save_metaboxes' );
function save_metaboxes( $post_id ) {
    global $metaboxes;
    // verify nonce
    if ( ! wp_verify_nonce( $_POST['post_format_meta_box_nonce'], basename( __FILE__ ) ) )
        return $post_id;
    // check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;
    // check permissions
    if ( 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) )
            return $post_id;
    } elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }
    $post_type = get_post_type();
    // loop through fields and save the data
    foreach ( $metaboxes as $id => $metabox ) {
        // check if metabox is applicable for current post type
        if ( $metabox['applicableto'] == $post_type ) {
            $fields = $metaboxes[$id]['fields'];
            foreach ( $fields as $id => $field ) {
                $old = get_post_meta( $post_id, $id, true );
                $new = $_POST[$id];
                if ( $new && $new != $old ) {
                    update_post_meta( $post_id, $id, $new );
                }
                elseif ( '' == $new && $old || ! isset( $_POST[$id] ) ) {
                    delete_post_meta( $post_id, $id, $old );
                }
            }
        }
    }
}
add_action( 'admin_print_scripts', 'display_metaboxes', 1000 );
function display_metaboxes() {
    global $metaboxes;
    if ( get_post_type() == "post" ) :
        ?>
        <script type="text/javascript">// <![CDATA[
            $ = jQuery;
            <?php
            $formats = $ids = array();
            foreach ( $metaboxes as $id => $metabox ) {
                array_push( $formats, "'" . $metabox['display_condition'] . "': '" . $id . "'" );
                array_push( $ids, "#" . $id );
            }
            ?>
            var formats = { <?php echo implode( ',', $formats );?> };
            var ids = "<?php echo implode( ',', $ids ); ?>";
            function displayMetaboxes() {
                // Hide all post format metaboxes
                $(ids).hide();
                // Get current post format
                var selectedElt = $("input[name='post_format']:checked").attr("id");
                // If exists, fade in current post format metabox
                if ( formats[selectedElt] )
                    $("#" + formats[selectedElt]).fadeIn();
            }
            $(function() {
                // Show/hide metaboxes on page load
                displayMetaboxes();
                // Show/hide metaboxes on change event
                $("input[name='post_format']").change(function() {
                    displayMetaboxes();
                });
            });
        // ]]></script>
        <?php
    endif;
}