<?php
require_once dirname(__FILE__) . '/rejsm_class_pesel.php';

/**
 * Created by PhpStorm.
 * User: mrevening
 * Date: 2016-09-16
 * Time: 18:52
 */
class form_field {
    private $post;
    public $nazwa_id;
    private $Nazwa;
    private $typ;
    private $wybory = array();
    private $metadata;
//    private $show_hide = '';
    static private $lista_objektow = array();
    public function __construct($post, $nazwa_id, $Nazwa, $typ, $wybory){
        $this->post = $post;
        $this->nazwa_id = $nazwa_id;
        $this->Nazwa = $Nazwa;
        $this->typ = $typ;
        $this->wybory = $wybory;
        $this->metadata = get_user_meta($this->post->post_author, $this->nazwa_id, true);
        array_push ( self::$lista_objektow, $this );
    }
//    public function _destruct() {
//        $this->reset_lista_objektow();
//    }
    private function print_dropdown_list () {?>
        <tr <?php echo $this->show_hide; ?>>
            <th scope="row"><?php echo $this->Nazwa ?></th>
            <td>
                <select id="<?php echo $this->nazwa_id ?>" class="drop-down" name="<?php echo 'key_'.$this->nazwa_id ?>">
                    <option disabled selected value></option>
                    <?php $i = 1; foreach ($this->wybory as $wybor) { ?>
                        <option value="<?php echo $i ?>"<?php selected($i, $this->metadata);?>><?php echo $wybor; $i++; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <?php
    }
    private function print_calendar ($dane) { ?>
        <tr>
            <th scope="row"><?php echo $this->Nazwa ?></th>
            <td>
                <input id="<?php echo $this->nazwa_id ?>" type="text" class="MyDate" name="key_<?php echo $this->nazwa_id ?>"
                       value="<?php echo $dane; ?>"/>
            </td>
        </tr>
        <?php
    }

    public function get_nazwa_id(){
        return $this->nazwa_id;
    }
//    public function get_plec(){
//        $pesel = get_the_author_meta('user_login',$this->post->post_author);
//        $plec = 0;
//        if (substr($pesel, 9, 1) % 2 == 0) $plec = 1;
//        else if (substr($pesel, 9, 1) % 2 == 1) $plec = 2;
//        return $plec;
//    }
    public static function get_lista_objektow () {
        return self::$lista_objektow;
    }
    public function update_metadata ( $userid ) {
        if( isset( $_POST['key_'.$this->nazwa_id] ) ) {
            $this->metadata = update_user_meta(  $userid , $this->nazwa_id, $_POST['key_'.$this->nazwa_id]);
        }
    }
    public function print_it () {
        $pesel = new Pesel(get_the_author_meta('user_login',$this->post->post_author));
        
        $disabled ='';
        $show_hide ='';
        switch ($this->typ){
            case 'drop-down-plec':
                $this->metadata = $pesel->getSex();
                $disabled ='disabled';
                break;
            case 'drop-down-dochod':
                $show_hide = 'id="row-hide"';
                $zatrudnienie = get_user_meta($this->post->post_author, 'zatrudnienie', true);
                if ($zatrudnienie != "2")   $show_hide .= ' style="display:none"';
                break;
            case 'drop-down-porody':
                if ($pesel->getSex() == 2) $show_hide .= ' style="display:none"';
                break;
            case 'drop-down':
                break;
            
            case 'wiek':
                $this->metadata = $pesel->getAge("%y");
                
                break;
            case 'calendar-data-urodzenia':
                $this->metadata = $pesel->getBirthday()->format("d-m-Y");
                $disabled ='disabled';
                break;
            case 'calendar-mri':
                $daty = explode(', ', $this->metadata);
                foreach ($daty as $data) $this->print_calendar($data);
                break;
            case 'calendar':
                break;
        }
        
        

        ?><tr <?php echo $show_hide; ?>>
        <th scope="row">
            <label for="<?php echo $this->nazwa_id;?>"><?php echo $this->Nazwa ?></label>
        </th>
        <td><?php
        if (substr($this->typ , 0, 9) == 'drop-down'){
            ?>
                <select id="<?php echo $this->nazwa_id ?>" class="<?php echo $this->typ ?>" name="<?php echo 'key_'.$this->nazwa_id ?>" <?php echo $disabled; ?>>
                    <option disabled selected value></option>
                    <?php $i = 1; ?>
                    <?php foreach ($this->wybory as $wybor) { 
//                        if ($this->typ == 'drop-down') ?>
                        <option value="<?php echo $i ?>"<?php selected($i, $this->metadata);?>><?php echo $wybor; $i++; ?></option><?php
                     } ?>
                </select>
            <?php
        }
        if (substr($this->typ , 0, 8) == 'calendar'){
            ?>
                <input id="<?php echo $this->nazwa_id ?>" type="text" class="MyDate" name="key_<?php echo $this->nazwa_id ?>" value="<?php echo $this->metadata; ?>" <?php echo $disabled; ?>/>
            <?php
        }
        if ($this->typ =='wiek'){
            echo $this->metadata;
        }
        
        ?>
        </td></tr><?php
    }
    public function print_it2 () {
        switch ($this->typ){
            case 'calendar':
                $this->print_calendar($this->metadata);
                break;
            case 'calendar-zgon':
                if (!current_user_can('pacjent')) {
                    $this->print_calendar($this->metadata);
                }
                break;
            case 'drop-down-plec':
                $plec = $this->get_plec();
                ?>
                <tr>
                    <th scope="row"><?php echo $this->Nazwa ?></th>
                    <td>
                        <select id="<?php echo $this->nazwa_id ?>" name="<?php echo 'key_'.$this->nazwa_id ?>" disabled>
                                <?php
                                $i = 1;
                                foreach ($this->wybory as $wybor) {
                                    if ($plec == $i) { ?>
                                    <option value="<?php echo $i; ?>"><?php echo $wybor;  ?></option>
                            <?php   } 
                                $i++; 
                                }?>
                        </select>
                    </td>
                </tr>
                <?php
                break;
            case 'drop-down-dochod':
                $zatrudnienie = get_user_meta($this->user->ID, 'zatrudnienie', true);
                $this->show_hide = 'id="row-hide"';
                if ($zatrudnienie != "2")   $this->show_hide .= ' style="display:none"';
                else $this->show_hide = 'id="row-hide"';
                $this->print_dropdown_list();
                break;
            case 'drop-down':
                $this->print_dropdown_list();
                break;
            case 'calendar-mri':
                $daty = explode(', ', $this->metadata);
                foreach ($daty as $data) $this->print_calendar($data);
                break;
        }
    }
    public static function reset_lista_objektow(){
        self::$lista_objektow = array();
    }
}
