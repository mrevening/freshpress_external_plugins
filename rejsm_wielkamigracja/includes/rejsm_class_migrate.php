<?php
require_once WP_PLUGIN_DIR.'/rejsm_superwtyczka/includes/rejsm_create_new_custom_post_types.php';
class rejsm_migrate extends rejsm_parent{
    protected $db;
    protected $limit_users = ' ORDER BY PESEL ASC LIMIT 10';//' '; //WHERE PESEL LIKE 57112417752
    protected $limit_patients = ' ORDER BY PESEL ASC LIMIT 10';
    public function __construct() {
        try {
            $this->db = new wpdb('mrevening', 'Pmy2tDLS3Ve5NPYD', 'rejsm_original', 'localhost');
            $this->db->show_errors(); 
        } catch (Exception $e) {   
            echo $e->getMessage();
        }
    }
    protected function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';//!@#$%^&*()_+=-[]}{;:/.,<>?';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 15; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    protected function add_category_msis_29 ($wynik){
//        $kategoria
        $kategoria = '';
        if ($wynik >= 0 && $wynik <= 29) $kategoria = 'minimalny';
        if ($wynik >= 30 && $wynik <= 58) $kategoria = 'średni';
        if ($wynik >= 59 && $wynik <= 87) $kategoria = 'mocny';
        if ($wynik >= 88 && $wynik <= 116) $kategoria = 'bardzo mocny';
        return $kategoria;
    }
    protected function add_category ($wynik){
        foreach ($kategoria as $kat){
            
        }
    }
    protected function add_category_eq5d ($wynik) {
        $kategoria ='';
        if ( $wynik >= 1 && $wynik <= 25 ) $kategoria = 'bardzo złe';
        if ( $wynik >= 26 && $wynik <= 50 ) $kategoria = 'złe';
        if ( $wynik >= 51 && $wynik <= 75 ) $kategoria = 'dobre';
        if ( $wynik >= 76 && $wynik <= 100 ) $kategoria = 'bardzo dobre';
        return $kategoria;
    }
    protected function get_lista($tabela){
        $lista = array();
        switch ($tabela){
            case 'patients_demog':
                $lista = array(
                    //'PESEL',
                    'MiejsceZamieszkania',
                    'Wojewodztwo',
                    'StanRodzinny',
                    'Wyksztalcenie',
                    'Porody',
                    'SMwRodzinie');
                break;
            case 'patients_eurems':
                $lista = array(
                    'PostacSM',
                    'PierwszeObjawyData',
                    'DiagnozaSM',
                    'Mri',
                    'BadaniePlynu',
                    'Immunomodulujace',
                    'Objawowe',
                    'Praca');
                break;
            case 'patients_msis':
                $lista = array (
                    'physical_activity',
                    'grabbing',
                    'carrying',
                    'balance',
                    'room_movement',
                    'clumsiness',
                    'stiffness',
                    'leg_arms_heavy',
                    'leg_arms_shakes',
                    'leg_arms_contraption',
                    'no_body_control',
                    'activity_dependancy',
                    'visitation_limitation',
                    'prolonged_home_stay',
                    'hands_usage_problem',
                    'activity_time_limitation',
                    'transport_usage_problem',
                    'longer_activity_completion',
                    'spontaneous_activity_problem',
                    'urgent_bathroom_visit',
                    'bad_mood',
                    'sleep_problem',
                    'mental_fatigue',
                    'fear_of_SM',
                    'stress_anxiety',
                    'irritation_impatience_impulsiveness',
                    'concentration_problems',
                    'lack_of_confidence',
                    'depression',
                );
                break;
            case 'patients_eq5d':
                $lista = array (
                    'movement',
                    'self_care',
                    'normal_activity',
                    'pain_discomfort',
                    'anxiety_depression',
                    'health_total',
                );
                break;
            case 'danedemograficzne':
                $lista = array(
                    'MiejsceZamieszkania',
                    'Wojewodztwo',
                    'Recznosc',
                    'Porody',
                    'Wyksztalcenie',
                    'StanRodzinny',
                    'Zatrudnienie',
                    'SMwRodzinie',
                    'PracaZarobek',
                    'Data_zgonu');
                break;
            case 'wywiad':
                $lista = array(
                    'PierwszeObjawy',
                    'PierwszeObjawyData',
                    'DiagnozaSM',
                    'ZapalenieNerwowWzrokowych',
                    'NadcisnienieTetnicze',
                    'Cukrzyca',
                    'Tarczyca',
                    'ZakrzepowoZatorowe',
                    'Nowotwory',
                    'PostacSM',
                    'KryteriumMcDonald',
                );
                break;
            case 'mri':
                $lista = array(
                    'Data',
                );
                break;
            case 'potencjaly':
                $lista = array(
                    'Potencjaly',
                    'DodatniUjemny'
                    );
                break;
            case 'plynmozgowy':
                $lista = array(
                    'BadaniePlynu',
                    'Prazki',
                    );
                break;

        }
        return $lista;
    }
    protected function add_user_metas($user_id, $lista, $row, $tryb='single' ){
        if ($row == NULL) return;
        foreach ($lista as $kolumna) {
        switch ($tryb) {
            case 'single':
                add_user_meta($user_id, $kolumna, $row->$kolumna);
                break;
            case 'multiple':
                foreach ($row as $single_row){
                    $prev_value = get_user_meta ($user_id, $kolumna, 'true');
                    if( $prev_value ) {
                        $new_value = $prev_value . ', ' . $single_row->$kolumna;
                        update_user_meta($user_id, $kolumna, $new_value);
                    }
                    else {
                        $new_value = $single_row->$kolumna;
                        add_user_meta($user_id, $kolumna, $new_value);
                    }
                }
            break;
            }
        }
    }
    protected function create_new_user($user_dane, $pesel_nazwa){
        if (username_exists($user_dane->$pesel_nazwa)) echo $user_dane->$pesel_nazwa . " -> Użytkownik już istnieje. ";
            $userdata = array(
                'user_login' => $user_dane->$pesel_nazwa,
                'user_email' => $user_dane->$pesel_nazwa . '_zastepczy@mail.com',
                'user_pass' => self::randomPassword(),
                'role' => 'pacjent',
            );
            return wp_insert_user($userdata);
        }
    protected function add_user_metas_post($post_id, $lista, $row, $tryb='single' ){
        if ($row == NULL) return;
        foreach ($lista as $kolumna) {
        switch ($tryb) {
            case 'single':
                add_post_meta($post_id, $kolumna, $row->$kolumna);
                break;
            case 'multiple':
                foreach ($row as $single_row){
                    $prev_value = get_user_meta ($post_id, $kolumna, 'true');
                    if( $prev_value ) {
                        $new_value = $prev_value . ', ' . $single_row->$kolumna;
                        update_post_meta($post_id, $kolumna, $new_value);
                    }
                    else {
                        $new_value = $single_row->$kolumna;
                        add_post_meta($post_id, $kolumna, $new_value);
                    }
                }
            break;
            }
        }
    }
    protected function insert_ankieta($tytul, $user_id, $row, $list){
        if ($row == NULL) return;
        $my_post = array();
        switch ($tytul) {
            case 'patients_msis':
                $title = 'Ankieta msis_29_'.$user_id.'_'.$row->{'Data'};
                $my_post = array(
                    'post_type' => 'msis_29',
                    'post_title' => $title,
                    'post_status' => 'publish',
                    'post_author' => $user_id,
                    'post_date' => $row->{'Data'},
                );
                break;
            case 'patients_eq5d':
                $title = 'Ankieta eq5d_'.$user_id.'_'.$row->{'Data'};
                $my_post = array(
                    'post_type' => 'eq5d',
                    'post_title' => 'eq5d',
                    'post_status' => 'publish',
                    'post_author' => $user_id,
                    'post_date' => $row->{'Data'},
                );
                break;
        }
        $id = wp_insert_post( $my_post );
        $j=1;
        $wynik = 0;
        switch ($tytul) {
            case 'patients_msis':
                foreach ($list as $name ) {
                    $name2 = '_rejsm_msis_29_'.$j;
                    $value = $row->{$name}-1;
                    update_post_meta($id, $name2, $value );
                    $wynik = $wynik + $value;
                    $j++;
                }
//                var_dump( $wynik);                
                update_post_meta($id, '_rejsm_msis_29_wynik', $wynik );
                wp_set_object_terms( $id, self::add_category_msis_29($wynik), 'kategoria_msis_29',  false ); //add_category_msis_29(intval($wynik))
                break;
            case 'patients_eq5d':
                foreach ($list as $name ) {
                    $name2 = '_rejsm_eq5d_'.$j;
                    $value = $row->{$name}-1;
                    update_post_meta($id, $name2, $value );
                    $wynik = $wynik + $value;
                    $j++;
                }
                update_post_meta($id, '_rejsm_eq5d_wynik', $wynik );
                wp_set_object_terms( $id, self::add_category_eq5d($wynik), 'kategoria_eq5d',  false ); //add_category_eq5d(intval($wynik))
                break;
        }
    }
}
class create_lekarze extends rejsm_migrate{
    public function __construct($limit_lekarze) {
        parent::__construct();
        $this->create_lekarze($limit_lekarze);
    }
    public function create_lekarze ($limit_lekarze){
        $sql = "SELECT UserName, UserPassword, UserImie, UserNazwisko, SzpitalMiasto, Admin FROM users ".$limit_lekarze;
        $dane = $this->db->get_results($sql, OBJECT);
        if ($this->db->last_error) {
            echo "Błąd podczas pobierania metadanych lekarzy. ";
            return new WP_Error('broke', __("Błąd podczas pobierania metadanych. "));
        }
        foreach ($dane as $row) {
            $rola = $row->{'Admin'};
            if ($rola == '2') $rola = 'administrator';
            else if ($rola == '0' || $rola == '1') $rola = 'lekarz';
            //echo $row->{'UserName'} . " created ";
            $userdata = array(
                'user_login' => $row->{'UserImie'} . ' ' . $row->{'UserNazwisko'},
                'user_email' => $row->{'UserName'},
                'user_pass' => $row->{'UserPassword'},
                'role' => $rola,
            );
            if (!email_exists($row->{'UserName'})) {
                $user_created_new_id = wp_insert_user($userdata);
                $term = $row->{'SzpitalMiasto'};
                $taxonomy = 'szpital';
                if (taxonomy_exists($taxonomy) && !term_exists($term, $taxonomy)) {
                    wp_insert_term($term, $taxonomy);
//                    echo 'dodaje nowy term';
                }
                wp_set_object_terms($user_created_new_id, $term, $taxonomy, true);
            }
            echo "</BR>";
        }
    }
}
class create_users extends rejsm_migrate{
    public function __construct() {
        parent::__construct();
        $this->create_users();
    }
       public function create_users (){
        $sql = "SELECT * FROM danedemograficzne " .$this->limit_users;
        $danedemograficzne_wszystkich_pacjentow = $this->db->get_results($sql, OBJECT);
        $user_created_new_id = 0;
        $user_dane = array();
        $nazwy_tabeli = array('danedemograficzne','wywiad', 'mri', 'potencjaly');
        foreach ($danedemograficzne_wszystkich_pacjentow as $dane_pacjenta) {
            wp_insert_post( array(
                'post_type' => 'dane_demograficzne',
                'post_title' => $dane_pacjenta->Pesel,
                'post_status' => 'publish',
                'post_author' => $dane_pacjenta->Pesel,
                 )
            );
            if ( isset( $dane_pacjenta->Pesel )) $pesel = $dane_pacjenta->Pesel;
            else {echo '</BR>nie ma pesla'; return;}
            foreach ($nazwy_tabeli as $tabela) {
                $tryb = 'single';
                switch ($tabela) {
                    case 'danedemograficzne':
                        $user_created_new_id = $this->create_new_user($dane_pacjenta, 'Pesel');
                        $user_dane = $dane_pacjenta;
                        break;
                    case 'mri':
                        $tryb = 'multiple';
                        $sql = "SELECT * FROM ".$tabela." WHERE PESEL = ".$pesel;
                        $user_dane = $this->db->get_results($sql, OBJECT);
                        break;
                    case 'potencjaly':
                    case 'plynmozgowy':
                    case 'wywiad':
                         wp_insert_post( array(
                            'post_type' => '$tabela',
                            'post_title' => $dane_pacjenta->Pesel,
                            'post_status' => 'publish',
                            'post_author' => $dane_pacjenta->Pesel,
                             )
                        );
                        break;
                    default:
                        $sql = "SELECT * FROM ".$tabela." WHERE PESEL = ".$pesel;
                        $user_dane = $this->db->get_row($sql, OBJECT);
                        break;
                }
                //var_dump ($user_dane);
                $lista = $this->get_lista($tabela);
                $this->add_user_metas($user_created_new_id, $lista, $user_dane, $tryb);
            }
        }
    }
}
class create_patients extends rejsm_migrate{
    public function __construct() {
        parent::__construct();
        $this->create_patients();
    }
    public function create_patients(){
        $sql = "SELECT PESEL, email, password FROM patients  ".$this->limit_patients;
        $dane_podstawowe_pacjentow = $this->db->get_results($sql, OBJECT);
        $user_created_new_id = 0;
        $user_dane = array();
        $nazwy_tabeli = array('patients', 'patients_demog', 'patients_eurems');
        $nazwy_tabeli_ankiet = array( 'patients_eq5d', 'patients_msis');
        foreach ($dane_podstawowe_pacjentow as $dane_pacjenta) {
            $pesel = $dane_pacjenta->PESEL;
            foreach ($nazwy_tabeli as $tabela) {
                switch ($tabela) {
                    case 'patients':
                        $user_created_new_id = $this->create_new_user($dane_pacjenta, 'PESEL');
                        $new_post_id = wp_insert_post(array(
                            'post_type'=> 'dane_demograficzne',
                            'post_status' => 'publish',
                            'post_author' => $user_created_new_id,
                            'post_title' => $user_created_new_id,
                        ));
                        $user_dane = $dane_pacjenta;
                        break;
                    case 'patients_eurems':
                    case 'patients_demog':
                        $sql = "SELECT * FROM ".$tabela." WHERE `PESEL` = " . $pesel;
                        $user_dane = $this->db->get_row($sql, OBJECT);
                        break;
                }
                $lista = $this->get_lista($tabela,'lista');
                $this->add_user_metas($user_created_new_id, $lista, $user_dane);
//                $this->add_user_metas_post($user_created_new_id, $lista, $user_dane);

            }
            foreach ($nazwy_tabeli_ankiet as $tabela) {
                $lista = $this->get_lista($tabela, 'lista');
                $sql = "SELECT * FROM ".$tabela." WHERE PESEL = " . $pesel;
                $user_dane = $this->db->get_row($sql, OBJECT);
                $this->insert_ankieta($tabela, $user_created_new_id, $user_dane, $lista);
            }
        }
    }
}






$create_lekarze = new create_lekarze();
//$create_users = new create_users();
//$create_patients = new create_patients();





if ( is_wp_error( $create_patients )) {
    echo '<div id="message" class="error">Zaistniał błąd podczas wykonywania skryptu. '.$return->get_error_message().'</div>';
}
else {
    echo '<div id="message" class="updated">Pomyślnie wykonano skrypt - dodano pacjentów</div>';
}

//if ( is_wp_error( $create_users )) {
//    echo '<div id="message" class="error">Zaistniał błąd podczas wykonywania skryptu. '.$return->get_error_message().'</div>';
//}
//else {
//    echo '<div id="message" class="updated">Pomyślnie wykonano skrypt - dodano użytkowników</div>';
//}


//try {

//} catch (Exception $e) {   
//    echo '<div id="message" class="error">Zaistniał błąd podczas wykonywania skryptu. '.$return->get_error_message().'</div>';
//    echo $e->getMessage();
//        }
//if ( is_wp_error( $create_lekarze )) {
//    echo '<div id="message" class="error">Zaistniał błąd podczas wykonywania skryptu. '.$return->get_error_message().'</div>';
//}
//else {
//    echo '<div id="message" class="updated">Pomyślnie wykonano skrypt - dodano lekarzy</div>';
//}