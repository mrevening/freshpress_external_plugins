<?php
require_once dirname(__FILE__) . '/add_styles.php';
require_once dirname(__FILE__) . '/rejsm_class_form_fields.php';

abstract class rejsm_parent {
    public function kategoria_dane($post_type){
        $cat = array();
        switch ($post_type){
            case 'msis_29':
                $cat = array(
                   array ('bardzo złe', 1 ,25),
                   array ('złe', 26 ,50),
                   array ('dobre',51 ,75),
                   array ('bardzo dobre', 76 ,100),
               );
               break;
            case 'eq5d':
                $cat =array(
                    array ('minimalny', 0 ,29),
                    array ('średni', 30 ,58),
                    array ('mocny',59 ,87),
                    array ('bardzo mocny', 88 ,116),
                );
                break;
        }
        return $cat;
    }
}
class new_custom_post_type extends rejsm_parent {
    protected $roles = array ('administrator', 'pacjent', 'lekarz');
    static protected $menu_position = 101;
    static protected $metabox_nr = 1;
    public function __construct(){

//        add_filter('rewrite_rules_array', array($this, 'add_rewrite_rules'));
        add_action ('init', array($this,'rejsm_add_custom_post_types'));
        add_action( 'add_meta_boxes', array($this,'rejsm_create_metaboxes'));
        add_action( 'save_post', array($this, 'rejsm_save_meta') );
        add_action( 'admin_enqueue_scripts', array($this, 'rejsm_css_add_style'));
        add_filter( 'redirect_post_location', array($this, 'rejsm_redirect_after_save') );
        add_filter( 'wp_insert_post_data' , array($this, 'rejsm_modify_title'));
//        add_action( 'personal_options_update', array($this, 'rejsm_update_metadata'));
//        add_action( 'edit_user_profile_update', array($this, 'rejsm_update_metadata'));

    }
    public function __destructor(){
        new_custom_post_type::$metabox_nr = 1;
    }
    public function rejsm_add_custom_post_types(){
        register_post_type( $this->post_type_name, array (
            'labels'             => $this->labels,
            'description'        => $this->description,
            'public'             => false, //it's not public, it shouldn't have it's own permalink, and so on
            'exclude_from_search'=> true, // you should exclude it from search results
            'publicly_queryable' => false, // you should be able to query it
            'show_ui'            => true, // you should be able to edit it in wp-admin
            'show_in_menu'       => true,
            'show_in_nav_menus'  => false,  // you shouldn't be able to add it to menus
            'show_in_admin_bar'  => true,
            'menu_position'      => new_custom_post_type::$menu_position++,
            'menu_icon'          => $this->menu_icon,
            'query_var'          => false,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'has_archive'        => false,  // it shouldn't have archive page
            'rewrite'            => false,  // it shouldn't have rewrite rules
            'capabilities'       => array(
                'read_post'          => 'read_'.$this->post_type_name,
                'edit_post'          => 'edit_'.$this->post_type_name,
                'edit_posts'         => 'edit_'.$this->post_type_name.'s',
                'delete_posts'       => 'delete_'.$this->post_type_name,
                'edit_others_posts'  => 'edit_others_'.$this->post_type_name,
                'publish_posts'      => 'publish_'.$this->post_type_name,
                'read_private_posts' => 'read_private_'.$this->post_type_name,
                'create_posts'       => 'edit_'.$this->post_type_name,
            ),
            'supports'           => array( '' ),
        ) );
        foreach ($this->roles as $rola){
            $role = get_role( $rola );
            if ( !empty ($role) ){
                $role->add_cap('read_'.$this->post_type_name);
                $role->add_cap('read_private_'.$this->post_type_name);
                $role->add_cap('edit_'.$this->post_type_name);
                $role->add_cap('edit_'.$this->post_type_name.'s');
                $role->add_cap('edit_others_'.$this->post_type_name);
                $role->add_cap('publish_'.$this->post_type_name);
                $role->add_cap('delete_'.$this->post_type_name);
            }
        }
    }
    public function rejsm_create_metaboxes() {


        remove_meta_box('slugdiv', $this->post_type_name, 'normal');
        $metabox = new_custom_post_type::$metabox_nr;
        foreach ($this->lista_naglowkow as $naglowek){
            add_meta_box( $this->post_type_name.'_'.$metabox++, $naglowek, array($this,'rejsm_print_metabox'), $this->post_type_name, 'normal', 'high' );     
        }
    }
    public function rejsm_save_meta( $post_id ) {
//        get_query_var('user_id')
        $liczba_naglowkow = count($this->lista_naglowkow);
        for ($i =1; $i<=$liczba_naglowkow; $i++){
            $name='rejsm_'.$this->post_type_name.'_'.$i;
            if ( isset( $_POST[$name] ) ) {
                update_post_meta($post_id, '_'.$name, intval(strip_tags($_POST[$name])));
            }
        }
        $wynik_name=$this->post_type_name.'_wynik';
        if ( isset( $_POST[$wynik_name] ) ) {
            update_post_meta($post_id, '_'.$wynik_name, intval(strip_tags($_POST[$wynik_name])));
        }
    }
    public function rejsm_modify_title( $data ) {
//        var_dump(get_userdata($data['post_author'])->user_login);
//        $title = $data['post_type'] . '_' . get_userdata($data['post_author'])->user_login . '_' . $data['post_date'];
        $title = get_userdata($data['post_author'])->user_login;
        $data['post_title'] =  $title ;
        return $data; 
    }
    public function add_rewrite_rules($aRules) {
        $aNewRules = array('user_id=([^/]+)/?$' => 'user_id=$matches[1]');
        $aRules = $aNewRules + $aRules;
        return $aRules;
    }
    public function rejsm_redirect_after_save( $location, $post_id ) {
//        if ( 'eq5d' == get_post_type( $post_id ) ) {
            $location = admin_url( 'edit.php?post_type='.get_post_type( $post_id ) );
//        }
//        if ( 'msis_29' == get_post_type( $post_id ) ) {
//            $location = admin_url( 'edit.php?post_type=msis_29' );
//        }
        return $location;
    }
    public function rejsm_css_add_style(){
        $handle = 'css_custom_post_type';
        $src =  '/wp-content/plugins/rejsm_superwtyczka/includes/css/css_custom_post_type.css';
        wp_enqueue_style( $handle, $src);
        //add_filter( 'wpcf7_support_html5_fallback', '__return_true' );
    }
    public function rejsm_print_metabox ($user){
//        global $wp_query;
//        if( !isset($wp_query->query_vars['user_id'])) {
//        echo 'nie ustawiona user_id';}
//        if( !isset($wp_query)) {
//        echo 'nie ustawiona query';}
//        else    var_dump($wp_query);
//        
//        $post_type_object = get_post_type_object( $wp_query->query_vars['user_id'] );
//      echo $post_type_object;
//            
//        $userid= get_query_var( 'paged','true' );
//        var_dump ($user_id);
//            $sMsdsCat = urldecode($wp_query->query_vars['msds_pif_cat']);
//}
        ?> 
        <!--<div class="ankieta">--> 
        <table class="form-table">
            <tbody>

        <?php 
        $metabox = new_custom_post_type::$metabox_nr;
        foreach ($this->lista_pytan[$metabox-1] as $key => $value){
            new form_field($user, $key, $value[0], $value[1], $this->wybory[$metabox-1][$key]);
        }
        foreach (form_field::get_lista_objektow() as $objekt) {
           ?> <p> <?php 
            $objekt->print_it();
            ?> </p> <?php
        }
        
        
        
        ?>
        <!--</div>-->
            </tbody>
        </table>        <?php

        
        
        
        form_field::reset_lista_objektow();
        new_custom_post_type::$metabox_nr++;
    }
    public function rejsm_update_metadata( $userid ) {
        foreach ($this->lista_nazw as $nazwa => $value)
            if( isset( $_POST['key_'.$nazwa] ) ) {
                update_user_meta( $userid, $nazwa, $_POST['key_'.$nazwa]);
            }
    }

}
class ankieta extends new_custom_post_type{
    public function __construct() {
        parent::__construct();
        $this->labels = $this->rejsm_labels();
        add_action( 'init', array($this, 'rejsm_register_taxonomy' ));
        add_action( 'init', array($this, 'rejsm_add_taxonomy_terms' ));
        add_action( 'publish_'.$this->post_type_name, array($this, 'rejsm_add_taxonomy_to_post' ));
        add_filter( 'manage_edit-'.$this->post_type_name.'_columns', array($this, 'rejsm_edit_columns' )) ;
        add_action( 'manage_'.$this->post_type_name.'_posts_custom_column', array($this, 'rejsm_manage_columns'), 10, 2 );
        add_filter( 'manage_edit-'.$this->post_type_name.'_sortable_columns', array($this, 'rejsm_sortable_column') );
        add_action( 'pre_get_posts', array($this, 'rejsm_orderby') );
    }
    public function rejsm_register_taxonomy() {
        $args = array(
                'label'             => $this->kategoria_label,
                'hierarchical'      => false,
                'show_ui'           => true,
                'show_admin_column' => true,
                'meta_box_cb'       => array($this,'rejsm_wynik_meta_box'),
                'show_admin_column ' => true,
        );
        register_taxonomy( 'kategoria_'.$this->post_type_name, $this->post_type_name, $args );
    }
    public function rejsm_add_taxonomy_terms () {
        foreach (($this->kategoria_dane($this->post_type_name)) as $cat){
            wp_insert_term ($cat[0], 'kategoria_'.$this->post_type_name, array( 'description' => $cat[0] .'', 'slug' => $cat[1].' - '.$cat[2] ));
        }
    }
    public function rejsm_add_taxonomy_to_post($post_id ) {
    //    $wynik = eq5d_get_result($post_id);
        $wynik=0;
        if ( isset( $_POST['rejsm_'.$this->post_type_name.'_wynik'] ) ) {
            $wynik = intval (strip_tags( $_POST['rejsm_'.$this->post_type_name.'_wynik'] ) );
        }
        $kategoria ='';
        foreach ($this->kategoria_dane($this->post_type_name) as $cat){
            if ($wynik >= $cat[1] && $wynik <= $cat[2]) $kategoria = $cat[0];
        }
        wp_set_object_terms( $post_id, $kategoria, 'kategoria_'.$this->post_type_name,  false );
        update_post_meta($post_id, '_'.$this->post_type_name.'_wynik', $wynik );//strip_tags($_POST[$name3]));
    }
    public function rejsm_labels (){
        $nazwa = $this->post_type_title;
        $labels = array(
            'name'               => 'Ankieta '.$nazwa,
            'singular_name'      => 'Ankieta '.$nazwa,
            'menu_name'          => 'Ankiety '.$nazwa,
            'name_admin_bar'     => 'Ankiety '.$nazwa, 
            'add_new'            => 'Dodaj nową ankietę',
            'add_new_item'       => 'Wypełnij nową ankietę '.$nazwa,
            'new_item'           => 'Nowa ankieta',
            'edit_item'          => 'Edytuj ankietę',
            'view_item'          => 'Zobacz ankietę',
            'all_items'          => 'Wszystkie ankiety',
            'search_items'       => 'Szukaj ankiety',
            'parent_item_colon'  => 'Ankiety dziedziczone',
            'not_found'          => 'Nie znaleziono ankiety.',
            'not_found_in_trash' => 'Nie znaleziono ankiety w koszu.',
         );
         return $labels;
    }
    public function rejsm_edit_columns( $columns ) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
        );
        $columns = array_merge($columns, array( 'title' => 'Pacjent', 'author' => __( 'Pacjent' ), 'kategoria' => __( $this->kategoria_label ),  $this->post_type_name.'_wynik' => __( 'Wynik' ), 'date' => __( 'Date' )  ));
        return $columns;
    }
    public function rejsm_manage_columns( $column, $post_id ) {
        global $post;
        switch( $column ) {
            case 'kategoria' :
                $terms = get_the_terms( $post_id, 'kategoria_'.$this->post_type_name );
                if ( !empty( $terms ) ) {
                    $out = array();
                    foreach ( $terms as $term ) {
                        $out[] = sprintf( '<a href="%s">%s</a>',
                            esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'kategoria_'.$this->post_type_name => $term->slug ), 'edit.php' ) ),
                            esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'kategoria_'.$this->post_type_name, 'display' ) )
                        );
                    }
                    echo join( ', ', $out );
                }
                else {
                    _e( 'Brak wyniku' );
                }
            break;
            case $this->post_type_name.'_wynik' :
                $wynik = get_post_meta($post_id, '_rejsm_'.$this->post_type_name.'_wynik', true);
                if (is_null($wynik)) {
                    $out = sprintf('<a href="%s">%s</a>',
                        esc_url(add_query_arg(array('post' => $post_id, 'action' => 'edit'), 'post.php')), 'N/A');
                }
                else {
                    $out = sprintf('<a href="%s">%s</a>',
                        esc_url( add_query_arg( array( 'post' => $post_id, 'action' => 'edit' ), 'post.php' ) ), $wynik    );
    //                    esc_html( sanitize_term_field( 'name', $wynik->name, $term->term_id, 'wynik_msis_29', 'display' ) )
                }
                echo $out;
                break;
//            case 'wynik_msis_29' :
//                   $wynik = get_post_meta($post_id, '_rejsm_msis_29_wynik', true);
//                   if (is_null($wynik)) {
//                       $out = sprintf('<a href="%s">%s</a>',
//                           esc_url(add_query_arg(array('post' => $post_id, 'action' => 'edit'), 'post.php')), 'N/A');
//                   }
//
//                   else {
//                       $out = sprintf('<a href="%s">%s</a>',
//                           esc_url( add_query_arg( array( 'post' => $post_id, 'action' => 'edit' ), 'post.php' ) ), $wynik    ); ///$wynik
//                       //                    esc_html( sanitize_term_field( 'name', $wynik->name, $term->term_id, 'wynik_msis_29', 'display' ) )
//
//                   }
//                   echo $out;
//                   break;
            
            /* Just break out of the switch statement for everything else. */
            default :
            break;
        }
    }
    public function rejsm_sortable_column( $columns ) {
        $columns[$this->post_type_name.'_wynik'] = $this->post_type_name.'_wynik';
        return $columns;
    }
    public function rejsm_orderby( $query ) {
        if( ! is_admin() )  return;
//        var_dump($query);
        $orderby = $query->get( 'orderby');
        if( $this->post_type_name.'_wynik' == $orderby ) {
            $query->set('meta_key','_rejsm_'.$this->post_type_name.'_wynik');
            $query->set('orderby','meta_value_num');
        }
    }
}
class dane_pacjenta extends new_custom_post_type{
    public function __construct() {
        parent::__construct();
        add_filter( 'manage_edit-'.$this->post_type_name.'_columns', array($this, 'rejsm_edit_columns' )) ;
        add_action( 'manage_'.$this->post_type_name.'_posts_custom_column', array($this, 'rejsm_manage_columns'), 10, 2 );
//        add_filter( 'query_vars', array($this, 'add_query_vars'));
//        add_action( 'pre_get_posts', array($this, 'rejsm_add_query_variable'));
    }    
    public function add_query_vars($aVars) {
        $aVars[] = "user_id"; 
        return $aVars;
    }
    function rejsm_add_query_variable( $query ) {
        if( ! is_admin() )  return;
//        global $wp_query;
//        $userid = $wp_query->query_vars[ 'user_id'];
        $userid= get_query_var( 'user_id','true' );
        if ($userid != 'NULL'){
            $query->set('author',$userid);
            $query->set('author_name',$userid);
            $query->set('user_login',$userid);
            $query->set('post_author',$userid);
        }
//        echo '<pre>';  var_dump($query);    echo '</pre>';
//        echo "sfjaslkfdjlaf";var_dump($query);
    }
    public function rejsm_save_meta( $user_id ) {
        $liczba_naglowkow = count($this->lista_naglowkow);
        foreach ($this->lista_pytan as $key => $value){
            $name=$key;
            $name_formularz = 'key_'.$name;
            if ( isset( $_POST[$name_formularz] ) ) {
                update_user_meta($user_id, $name, intval(strip_tags($_POST[$name_formularz])));
            }
        }
    }
    public function rejsm_edit_columns( $columns ) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => 'Pacjent',
//            'author' => __( 'Pacjent' ),
            'date' => __( 'Date' ),
        );
//        $columnyKeys=array();
//        $columnyValues=array();
//        $col=array();
//        foreach ($this->lista_pytan as $tablica_asoc){
//            $columnyKeys = array_merge($columnyKeys,array_keys ($tablica_asoc));
//            $columnyValues = array_merge($columnyValues, array_values ($tablica_asoc)); 
//        }
//        foreach ($columnyValues as $arr){
//            $col = array_merge($col,$arr[0]);
//        }
//        $columny = array_combine($columnyKeys, $col);
//        $columns = array_merge($columns, $columny);
        return $columns;
    }
    public function rejsm_manage_columns( $column, $post_id ) {
	global $post;
        switch( $column ) {
            case $this->post_type_name.'_wynik' :
                //$wynik = msis_29_get_result($post_id);
                $wynik = get_post_meta($post_id, '_'.$this->post_type_name.'_wynik', true);
                if (is_null($wynik)) {
                    $out = sprintf('<a href="%s">%s</a>',
                        esc_url(add_query_arg(array('post' => $post_id, 'action' => 'edit'), 'post.php')), 'N/A');
                }
                else {
                    $out = sprintf('<a href="%s">%s</a>',
                        esc_url( add_query_arg( array( 'post' => $post_id, 'action' => 'edit' ), 'post.php' ) ), $wynik    );
    //                    esc_html( sanitize_term_field( 'name', $wynik->name, $term->term_id, 'wynik_msis_29', 'display' ) )
                }
                echo $out;
                break;
            case 'wynik_msis_29' :
                   $wynik = get_post_meta($post_id, '_msis_29_wynik', true);
                   if (is_null($wynik)) {
                       $out = sprintf('<a href="%s">%s</a>',
                           esc_url(add_query_arg(array('post' => $post_id, 'action' => 'edit'), 'post.php')), 'N/A');
                   }
                   else {
                       $out = sprintf('<a href="%s">%s</a>',
                           esc_url( add_query_arg( array( 'post' => $post_id, 'action' => 'edit' ), 'post.php' ) ), $wynik    ); ///$wynik
                       //                    esc_html( sanitize_term_field( 'name', $wynik->name, $term->term_id, 'wynik_msis_29', 'display' ) )

                   }
                   echo $out;
                   break;
            case 'kategoria' :
                $terms = get_the_terms( $post_id, 'kategoria_'.$this->post_type_name );
                if ( !empty( $terms ) ) {
                    $out = array();
                    /* Loop through each term, linking to the 'edit posts' page for the specific term. */
                    foreach ( $terms as $term ) {
                        $out[] = sprintf( '<a href="%s">%s</a>',
                            esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'kategoria_'.$this->post_type_name => $term->slug ), 'edit.php' ) ),
                            esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'kategoria_'.$this->post_type_name, 'display' ) )
                        );
                    }
                    echo join( ', ', $out );
                }
                else {
                    _e( 'Brak wyniku' );
                }
                break;
            /* Just break out of the switch statement for everything else. */
            default :
            break;
        }
    }

    
}
class msis_29 extends ankieta {
    public function __construct() {
        parent::__construct();
        add_action( 'admin_enqueue_scripts', array($this, 'rejsm_js_wynik_actualize'));
    }
    protected $numer_pytania = 1;
    protected $post_type_name = 'msis_29';
    protected $post_type_title = 'MSIS-29';
    protected $kategoria_label = 'Wpływ choroby';
    protected $description = "Skala wpływu stwardnienia rozsianego (MSIS-29).";
    protected $menu_icon = 'dashicons-format-aside';
    protected $lista_naglowkow = array(
        'W ciągu ostatnich 14 dni jak bardzo stwardnienie rozsiane ograniczyło Pani/Pana zdolność do:',
        'Wciągu ostatnich 14 dni jak bardzo przeszkadzały Pani/Panu:',
        'Wciągu ostatnich 14 dni jak bardzo przeszkadzały Pani/Panu:',
    );
    protected $lista_pytan = array(
        array(
            'Wykonywania czynności wymagających wysiłku fizycznego:',
            'Silnego chwytania przedmiotów (np. odkręcania kurków):',
            'Noszenia przedmiotów ?',
        ),
        array(
            'Problemy z równowagą ?',
            'Trudności z poruszaniem się w pomieszczeniach ?',
            'Niezręczność:',
            'Sztywność:',
            'Uczucie ciężkości rąk i/lub nóg:',
            'Drżenie rąk lub nóg:',
            'Kurcze rąk lub nóg:',
            'Brak kontroli nad swoim ciałem:',
            'Zależność od innych związana z wykonywaniem różnych czynności za Pana/Panią:',
        ),
        array (
            'Ograniczenia odwiedzin w Pani/Pana domu oraz czynności wykonywanych w czasie wolnym:',
            'Konieczność pozostania w domu dłużej niż by Pan/Pani sobie życzył/a:',
            'Trudności z używaniem rąk w codziennych czynnościach:',
            'Konieczność ograniczenia czasu, który Pan/i poświęca na pracę lub inne czynności codzienne:',
            'Trudności w korzystaniu środków transportu (np. samochodu, autobusu, pociągu, taksówki itp.):',
            'Dłuższy niż normalnie czas wykonywaniu czynności:',
            'Trudności ze spontanicznym wykonywaniem czynności (np. pójcie gdzieś pod wpływem impulsu chwili):',
            'Nagła potrzeba pójścia do toalety:',
            'Złe samopoczucie:',
            'Problemy ze snem :',
            'Poczucie psychicznego zmęczenia :',
            'Obawy związane z Pani/Pana chorobą (SM):',
            'Uczucie niepokoju lub napięcia :',
            'Uczucie rozdrażnienia, zniecierpliwienia lub wybuchowość:',
            'Problemy z koncentracją uwagi:',
            'Brak pewności siebie :',
            'Uczucie depresji :',
        ),
    );
    public function rejsm_print_metabox ($post){
        $metabox = new_custom_post_type::$metabox_nr;
        ?> <div class="ankieta"> <?php
        foreach ($this->lista_pytan[$metabox-1] as $pytanie){
            $nazwa = '_rejsm_'.$this->post_type_name.'_'.$this->numer_pytania;
            $wartosc_w_bazie_danych = get_post_meta( $post->ID, $nazwa, true );
            ?><p><?php echo $pytanie; ?><p>Wcale nie<?php
            for ($i=0; $i<5; $i++){?>
                <input class="msis_check" name="<?php echo 'rejsm_'.$this->post_type_name.'_'.$this->numer_pytania; ?>" value="<?php echo $i;?>" type="radio" <?php checked( $wartosc_w_bazie_danych, $i ); ?> </input>
            <?php
            } ?>  Bardzo mocno</p>
            <?php $this->numer_pytania++;
        } ?></div><?php
        new_custom_post_type::$metabox_nr++;
    }
    public function rejsm_wynik_meta_box( $post ) {
        ?><progress id="health_status_msis" class="scale_result" name ="rejsm_msis_29_wynik" value="" max="116"  ></progress> <?php
    }
    function rejsm_js_wynik_actualize($hook){
        global $post;
        if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
            if ( 'msis_29' === $post->post_type ) {     
                $handle = 'js_wynik_actualize';
                $src =  '/wp-content/plugins/rejsm_superwtyczka/includes/js/js_wynik_actualize.js';
                wp_enqueue_script( $handle, $src);
            }
        }
    }

}
class eq5d extends ankieta {
    protected $nr_pytania = 1;
    protected $post_type_name = 'eq5d';
    protected $post_type_title = 'EQ-5D';
    protected $kategoria_label = 'Samopoczucie';
    protected $description = "Skala wpływu stwardnienia rozsianego (EQ-5D).";
    protected $menu_icon = 'dashicons-clipboard';
    protected $lista_naglowkow = array(
        'Zdolność poruszania się',
        'Samoopieka',
        'Zwykła działalność (np. praca, nauka, zajęcia domowe, aktywność rodzinna, zajęcia w czasie wolnym)',
        'Ból/Dyskomfort',
        'Niepokój/Przygnębienie',
    );
    protected $lista_pytan = array(
        array( 'Nie mam problemów z chodzeniem', 'Mam trochę problemów z chodzeniem', 'Jestem zmuszony pozostawać w łóżku' ),
        array( 'Nie mam żadnych problemów z samoopieką', 'Mam trochę problemów z myciem i ubieraniem się', 'Nie mogę sam się umyć ani ubrać'),
        array( 'Nie mam problemów z wykonywaniem moich zwykłych czynności', 'Mam trochę problemów z wykonywaniem moich zwykłych czynności', 'Nie mogę wykonywać moich zwykłych czynności' ),
        array( 'Nie odczuwam bólu ani dyskomfortu', 'Odczuwam umiarkowany ból lub dyskomfort', 'Odczuwam krańcowy ból lub dyskomfort' ),
        array( 'Nie jestem niespokojny ani przygnębiony', 'Jestem umiarkowanie niespokojny lub przygnębiony', 'Jestem krańcowo niespokojny lub przygnębiony'),
    );

    public function rejsm_print_metabox ($post){
        ?> <div class="ankieta"> <?php
        $i=0;
        $metabox = new_custom_post_type::$metabox_nr;
        foreach ($this->lista_pytan[$metabox-1] as $pytanie){
            $wartosc_w_bazie_danych = get_post_meta( $post->ID, '_rejsm_eq5d_'.$metabox , true );
            $oczekiwana_wartosc = ($i++)%3; 
            ?> <p><label for="<?php  echo $this->post_type_name.'_'.$metabox.'_'.$i; ?>"><?php  echo $pytanie; ?>
            <input id=" <?php  echo $this->post_type_name.'_'.$metabox.'_'.$i; ?>" class="radio_button" name="<?php echo 'rejsm_'.$this->post_type_name . '_' . $metabox;?>" value="<?php echo $oczekiwana_wartosc; ?>" type="radio" <?php checked( $wartosc_w_bazie_danych, $oczekiwana_wartosc ); if ($oczekiwana_wartosc == 0) {echo 'checked';} ?> 
            </label></p> <?php
        }
        ?></div><?php
        new_custom_post_type::$metabox_nr++;
    }
    public function rejsm_wynik_meta_box( $post ) {
    $wynik = get_post_meta( $post->ID, '_'.$this->post_type_name.'_wynik', true );
    ?>
        <span float="left" >Złe  </span>
        <span float="center"> <input name="<?php echo 'rejsm_'.$this->post_type_name.'_wynik'; ?>"  type="range" min="1" max="100" value="<?php echo $wynik; ?>"> </input></span>
        <span float="right" >Dobre  </span>
    <?php
    }
}
class dane_demograficzne extends dane_pacjenta {
    public function __construct() {
        parent::__construct();
    }
    protected $post_type_name = 'dane_demograficzne';
    protected $labels = array(
        'name'               => 'Dane demograficzne',
        'singular_name'      => 'Dane demograficzne',
        'menu_name'          => 'Dane demograficzne',
        'name_admin_bar'     => 'Dane demograficzne',
        'add_new'            => 'Dodaj pacjenta',
        'add_new_item'       => 'Wypełnij nową daną demograficzną',
        'new_item'           => 'Nowy pacjent',
        'edit_item'          => 'Edytuj pacjenta',
        'view_item'          => 'Zobacz pacjenta',
        'all_items'          => 'Wszyscy pacjenci',
        'search_items'       => 'Szukaj pacjenta', 
        'parent_item_colon'  => 'Rodzic:',
        'not_found'          => 'Nie znaleziono pacjenta.',
        'not_found_in_trash' => 'Nie znaleziono pacjenta w koszu.',
    );
    protected $description = "Wszystkie dane demograficzne";
    protected $menu_icon = 'dashicons-groups';
    protected $lista_naglowkow = array(
        'Podstawowe dane',
        'Dane demograficzne', 
        'Aktywność'
    );
    protected $lista_pytan = array(
        array(
            'DataUrodzenia'=> array('Data urodzenia','calendar-data-urodzenia'),
            'Wiek'=> array('Wiek','wiek'),
            'Plec'=> array('Płeć','drop-down-plec'),
        ),
        array(
            'MiejsceZamieszkania' => array('Miejsce zamieszkania','drop-down'),
            'Wojewodztwo' => array('Województwo','drop-down'),
            'StanRodzinny'=>array('Stan rodzinny','drop-down'),
            'Recznosc'=>array('Ręczność','drop-down'),
        ),
         array(
            'Porody'=>array('Porody','drop-down-porody'),
            'Wyksztalcenie'=>array('Wykształcenie','drop-down'),
            'SMwRodzinie'=>array('SM w rodzinie','drop-down'),
            'Zatrudnienie'=>array('Zatrudnienie','drop-down'),
            'PracaZarobek'=>array('Dochód','drop-down-dochod'),
        ),
//        'Data_zgonu'=>array('Data Zgonu','calendar-zgon'),
    );
    protected $wybory = array (
        array(
            'DataUrodzenia'=> array(),
            'Wiek'=> array(),
            'Plec'=> array('Kobieta','Mężczyzna' ),
        ),
        array(
            'MiejsceZamieszkania' => array('Miasto', 'Wieś',),
            'Wojewodztwo' => array('dolnośląskie', 'kujawsko-pomorskie', 'lubelskie', 'lubuskie', 'łódzkie', 'małopolskie', 'mazowieckie', 'opolskie', 'podkarpackie', 'podlaskie', 'pomorskie', 'śląskie', 'świętokrzyskie', 'warmińsko-mazurskie', 'wielkopolskie', 'zachodniopomorskie'),
            'StanRodzinny'=>array('Panna/Kawaler', 'Zamężna/Żonaty', 'Rozwiedziona/Rozwiedziony', 'Wdowa/Wdowiec'),
            'Recznosc'=>array('Praworęczny', 'Leworęczny',),
        ),
        array(
            'Porody'=>array('0', '1', '2', '3 lub więcej',),
            'Wyksztalcenie'=>array('Podstawowe', 'Średnie', 'Wyższe'),
            'SMwRodzinie'=>array('Tak', 'Nie',),
            'Zatrudnienie'=>array('Nie pracuje', 'Pracuje', 'Renta', 'Emerytura'),
            'PracaZarobek'=>array('Tak', 'Nie',),
        ),
//        'Data_zgonu'=>array(),
    );
}
class wywiad extends dane_pacjenta{
    public function __construct() {
        parent::__construct();
    }
    protected $post_type_name = 'wywiad';
    protected $labels = array(
        'name'               => 'Wywiad',
        'singular_name'      => 'Wywiad',
        'menu_name'          => 'Wywiad',
        'name_admin_bar'     => 'Wywiad',
        'add_new'            => 'Dodaj wywiad',
        'add_new_item'       => 'Wypełnij nowy wywiad',
        'new_item'           => 'Nowy wywiad',
        'edit_item'          => 'Edytuj wywiad',
        'view_item'          => 'Zobacz wywiad',
        'all_items'          => 'Wszystkie wywiady',
        'search_items'       => 'Szukaj wywiadu', 
        'parent_item_colon'  => 'Rodzic:',
        'not_found'          => 'Nie znaleziono wywiadu.',
        'not_found_in_trash' => 'Nie znaleziono wywiadu w koszu.',
    );
    protected $description = "Wszystkie wywiady";
    protected $menu_icon = 'dashicons-format-chat';
    protected $lista_naglowkow = array(
        'Wywiad',
        'Choroby współistniejące', 

    );
    protected $lista_pytan = array(
        array (
            'PierwszeObjawy'=> array( 'Pierwsze objawy', 'drop-down'),
            'PierwszeObjawyData' => array ('Pierwsze objawy (miesiąc / rok)', 'calendar'),
            'DiagnozaSM' => array ('Data diagnozy SM (miesiąc / rok)', 'calendar'),
            'ZapalenieNerwowWzrokowych'=>array('Zapalenie nerwów wzrokowych:', 'drop-down'),
            'PostacSM'=>array('Postać SM:','drop-down'),
            'KryteriumMcDonald'=>array('Czy spełnia kryteria McDonalda (2010):','drop-down'),
        ),
        array (
            'NadcisnienieTetnicze'=>array ('Nadciśnienie tętnicze', 'drop-down'),
            'Cukrzyca'=>array('Cukrzyca','drop-down'),
            'Tarczyca'=>array('Choroby tarczycy','drop-down'),
            'ZakrzepowoZatorowe'=>array('Choroby zakrzepowo-zatorowe','drop-down'),
            'Nowotwory'=>array('Nwotwory','drop-down'),
        ),

    );
    protected $wybory = array(
        array(
            'PierwszeObjawy' => array( 'Wzrokowe','Czuciowe','Piramidowe','Móżdżkowe','Inne'),
            'PierwszeObjawyData' =>array(),
            'DiagnozaSM' => array(),
            'ZapalenieNerwowWzrokowych'=>array('Tak', 'Nie'),
            'PostacSM'=> array( 'Rzutowo-Remisyjna (RR)', 'Wtórnie Przewlekła (SP)', 'Pierwotnie Przewlekła (PP)', 'Rzutowo-Przewlekła' ),
            'KryteriumMcDonald'=> array( 'Tak', 'Nie'),
        ),
        array(
            'NadcisnienieTetnicze'=>array( 'Tak', 'Nie' ),
            'Cukrzyca'=>array( 'Tak', 'Nie' ),
            'Tarczyca'=>array( 'Tak', 'Nie' ),
            'ZakrzepowoZatorowe'=>array( 'Tak', 'Nie' ),
            'Nowotwory'=> array('Tak', 'Nie'), 
        ),
    );
}
class diagnostyka extends dane_pacjenta {
    public function __construct() {
        parent::__construct();
    }
    protected $post_type_name = 'diagnostyka';
    protected $labels = array(
        'name'               => 'Diagnostyka',
        'singular_name'      => 'Diagnostyka',
        'menu_name'          => 'Diagnostyka',
        'name_admin_bar'     => 'Diagnostyka',
        'add_new'            => 'Dodaj diagnostyke',
        'add_new_item'       => 'Wypełnij nową diagnostyke',
        'new_item'           => 'Nowa diagnostyka',
        'edit_item'          => 'Edytuj diagnostyke',
        'view_item'          => 'Zobacz diagnostyke',
        'all_items'          => 'Wszystkie diagnostyki',
        'search_items'       => 'Szukaj diagnostyke', 
        'parent_item_colon'  => 'Rodzic:',
        'not_found'          => 'Nie znaleziono diagnostyki.',
        'not_found_in_trash' => 'Nie znaleziono diagnostyki w koszu.',
    );
    protected $description = "Wszystkie dane diagnostyczne";
    protected $menu_icon = 'dashicons-welcome-view-site';
    protected $lista_naglowkow = array(
        'Rezonans Magnetyczny - data badania',
        'Badanie potencjałów wzrokowych', 
        'Badanie płynu mózgowo-rdzeniowego'
    );
    protected $lista_pytan = array(
        array(
            
        ),
        array(
           
        ),
         array(
            
        ),    
    );
    protected $wybory = array (
        array(
         
        ),
        array(
          
        ),
        array(
       
        ),
    );
}
class leczenie extends dane_pacjenta {
    public function __construct() {
        parent::__construct();
    }
    protected $post_type_name = 'leczenie';
    protected $labels = array(
        'name'               => 'Leczenie',
        'singular_name'      => 'Leczenie',
        'menu_name'          => 'Leczenie',
        'name_admin_bar'     => 'Leczenie',
        'add_new'            => 'Dodaj leczenie',
        'add_new_item'       => 'Wypełnij nowe leczenie',
        'new_item'           => 'Nowe leczenie',
        'edit_item'          => 'Edytuj leczenie',
        'view_item'          => 'Zobacz leczenie',
        'all_items'          => 'Wszystkie leczenia',
        'search_items'       => 'Szukaj leczenia', 
        'parent_item_colon'  => 'Rodzic:',
        'not_found'          => 'Nie znaleziono leczenia.',
        'not_found_in_trash' => 'Nie znaleziono leczenia w koszu.',
    );
    protected $description = "Wszystkie leczenia";
    protected $menu_icon = 'dashicons-shield';
    protected $lista_naglowkow = array(
        'Immunomodulujące',
        'Immunosupresyjne - Solu-Medrol', 
        'Objawowe'
    );
    protected $lista_pytan = array(
        array(

        ),
        array(

        ),
         array(

        ),
    );
    protected $wybory = array (
        array(

        ),
        array(

        ),
        array(

        ),
    );
}
class aktualne_wyniki extends dane_pacjenta {
    public function __construct() {
        parent::__construct();
    }
    protected $post_type_name = 'aktualne_wyniki';
    protected $labels = array(
        'name'               => 'Aktualne wyniki',
        'singular_name'      => 'Aktualne wyniki',
        'menu_name'          => 'Aktualne wyniki',
        'name_admin_bar'     => 'Aktualne wyniki',
        'add_new'            => 'Dodaj aktualne wyniki',
        'add_new_item'       => 'Wypełnij nowe wyniki',
        'new_item'           => 'Nowe wyniki',
        'edit_item'          => 'Edytuj wyniki',
        'view_item'          => 'Zobacz wyniki',
        'all_items'          => 'Wszystkie wyniki',
        'search_items'       => 'Szukaj wyniki', 
        'parent_item_colon'  => 'Rodzic:',
        'not_found'          => 'Nie znaleziono wyników.',
        'not_found_in_trash' => 'Nie znaleziono wyników w koszu.',
    );
    protected $description = "Wszystkie aktualne wyniki";
    protected $menu_icon = 'dashicons-chart-line';
    protected $lista_naglowkow = array(
        'Stan funkcjonalny',
        'Zmęczenie i depresja', 
    );
    protected $lista_pytan = array(
        array(

        ),
        array(

        ),
    );
    protected $wybory = array (
        array(

        ),
        array(

        ),
    );
}
class ocena_klinimetryczna extends dane_pacjenta {
    public function __construct() {
        parent::__construct();
    }
    protected $post_type_name = 'ocena_klinimetryczna';
    protected $labels = array(
        'name'               => 'Ocena klinimetryczna',
        'singular_name'      => 'Ocena klinimetryczna',
        'menu_name'          => 'Ocena klinimetryczna',
        'name_admin_bar'     => 'Ocena klinimetryczna',
        'add_new'            => 'Dodaj ocenę klinimetryczną',
        'add_new_item'       => 'Wypełnij nową ocenę klinimetryczną',
        'new_item'           => 'Nowa ocena klinimetryczna',
        'edit_item'          => 'Edytuj ocenę klinimetryczną',
        'view_item'          => 'Zobacz ocenę klinimetryczną',
        'all_items'          => 'Wszystkie oceny klinimetryczne',
        'search_items'       => 'Szukaj oceny klinimetrycznej', 
        'parent_item_colon'  => 'Rodzic:',
        'not_found'          => 'Nie znaleziono oceny klinimetrycznej.',
        'not_found_in_trash' => 'Nie znaleziono oceny klinimetrycznej w koszu.',
    );
    protected $description = "Wszystkie oceny klinimetryczne";
    protected $menu_icon = 'dashicons-forms';
    protected $lista_naglowkow = array(
        'EDSS',
        'Ambulation Index', 
        'MSFC',
        'VFT/SDMT',
        'Zmęczenie i depresja'
    );
    protected $lista_pytan = array(
        array(  ),
        array(  ),
        array(  ),      
        array(  ),   
        array(  ),   
    );
    protected $wybory = array (
    );
}
$dane_demograficzne = new dane_demograficzne();
$wywiad = new wywiad();
$diagnostyka = new diagnostyka();
$lecznie = new leczenie();
$aktualne_wyniki = new aktualne_wyniki();
$ocena_klinimetryczna = new ocena_klinimetryczna();
$msis_29 = new msis_29();
$eq5d = new eq5d();



