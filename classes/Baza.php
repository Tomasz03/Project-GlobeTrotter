<?php
class Baza {
    private $mysqli;

    public function __construct($serwer, $user, $pass, $baza) {
        $this->mysqli = new mysqli($serwer, $user, $pass, $baza);
        if ($this->mysqli->connect_errno) {
            die("Błąd połączenia z bazą danych.");
        }
        $this->mysqli->set_charset("utf8");
    }

    public function __destruct() {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }

    public function select($sql) {
        $result_array = [];
        if ($result = $this->mysqli->query($sql)) {
            while ($row = $result->fetch_assoc()) {
                $result_array[] = $row;
            }
            $result->free();
        }
        return $result_array;
    }

    public function execute($sql) {
        if ($this->mysqli->query($sql) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

    public function escape_string($value) {
        return $this->mysqli->real_escape_string($value);
    }

    public function get_last_id() {
        if ($this->mysqli) {
            return $this->mysqli->insert_id;
        }
        return false;
    }
}
?>