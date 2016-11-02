<?php
/**
 * Class that validate and retrieve data from PESEL number in object-oriented style
 * 
 * Example:
 * $pesel = new Pesel('85042510356');
 * $pesel->getBirthday()->format("d-m-Y")
 * $pesel->getSex()
 * $pesel->getAge("%a")
 * 
 *
 * @author Bartosz 'B3k' Pietrzak 
 * @license MIT
 */
class Pesel {
    private static $scales = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3);
    private $pesel, $birthday, $sex, $vaild;
    /**
     * Constructor
     * 
     * @throws InvalidArgumentException
     * @param string $pesel
     * @return void
     */
    public function __construct($pesel) {
        if (!is_numeric($pesel) || strlen($pesel) != 11 || $pesel == '00000000000') {
            throw new InvalidArgumentException("Invaild PESEL number");
        } else {
            $this->pesel = $pesel;
            $this->extract();
        }
    }
    /**
     * Return actual PESEL number
     * 
     * @return string
     */
    public function getPesel() {
        return $this->pesel;
    }
    /**
     * Return birthday DateTime object or date in format as parameter
     * 
     * Example:
     * $pesel->getBirthday("Y-m-d");
     * $pesel->getBirthday()->format('Y-m-d')
     * 
     * @see http://www.php.net/manual/en/datetime.format.php
     * @param bool|string $format
     * @return Mixed
     */
    public function getBirthday($format = FALSE) {
        return ($format ? $this->birthday->format($format) : $this->birthday);
    }
    /**
     * Return diff between date from pesel and current time
     * as DateInterval object or string in typed format
     * 
     * Example:
     * $pesel->getAge('%a days');
     * $pesel->getAge()->format("%y years");
     * 
     * @see http://www.php.net/manual/en/dateinterval.format.php
     * @params string 
     * @return DateInterval
     */
    public function getAge($format = FALSE) {
        $interval = $this->birthday->diff(new DateTime("now"));
        return ($format ? $interval->format($format) : $interval);
    }
    /**
     * Return sex: 1 - female , 2 - male
     * 
     * @return int
     */
    public function getSex() {
        return $this->sex;
    }
    /**
     * Return that if pesel checksum control is vaild.
     * 
     * @return bool
     */
    public function isVaild() {
        return $this->vaild;
    }
    /**
     * Extract data from PESEL
     * 
     * @return void
     */
    private function extract() {
        if (($this->pesel [9] % 2) == 0) {
            $this->sex = 1;
        } else {
            $this->sex = 2;
        }
        if (intval($this->pesel [2] . $this->pesel [3]) >= 81 && intval($this->pesel [2] . $this->pesel [3]) <= 92) {
            $month = (($this->pesel [2] . $this->pesel [3]) - 80);
            $year = (($this->pesel [0] . $this->pesel [1])) + 1800;
        } elseif (intval($this->pesel [2] . $this->pesel [3]) >= 1 && intval($this->pesel [2] . $this->pesel [3]) <= 12) {
            $month = ($this->pesel [2] . $this->pesel [3]);
            $year = (($this->pesel [0] . $this->pesel [1])) + 1900;
        } elseif (intval($this->pesel [2] . $this->pesel [3]) >= 21 && intval($this->pesel [2] . $this->pesel [3]) <= 32) {
            $month = (intval($this->pesel [2] . $this->pesel [3]) - 20);
            $year = (intval($this->pesel [0] . $this->pesel [1])) + 2000;
        } else {
            throw new InvalidArgumentException("Invaild PESEL number - birthday part out of range");
        }
        if (!checkdate($month, intval($this->pesel [4] . $this->pesel [5]), $year)) {
            throw new InvalidArgumentException("Invaild PESEL number - birthday part is invaild");
        }
        $this->birthday = new DateTime($year . "-" . $month . "-" . intval($this->pesel [4] . $this->pesel [5]));
        $sum = 0;
        for ($x = 0; $x < 10; $x++) {
            $sum += self::$scales [$x] * $this->pesel [$x];
        }
        $res = 10 - $sum % 10;
        $res = ($res == 10 ? 0 : $res);
        if ($res == $this->pesel [10]) {
            $this->vaild = TRUE;
        } else {
            $this->vaild = FALSE;
        }
    }
}