<?php
class User {
    private $db;

    public function __construct($db_instance) {
        $this->db = $db_instance;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register($username, $email, $password) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Niepoprawny format email.";
        }
        if (strlen($password) < 6) {
            return "Hasło musi mieć co najmniej 6 znaków.";
        }
        
        $username = $this->db->escape_string($username);
        $email_safe = $this->db->escape_string($email);
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO users (username, password_hash, email) VALUES ('$username', '$password_hash', '$email_safe')";
        
        if ($this->db->execute($sql)) {
            return true;
        } else {
            return "Błąd rejestracji (nazwa lub email już zajęte).";
        }
    }

    public function login($username, $password) {
        $username = $this->db->escape_string($username);
        $sql = "SELECT id, password_hash, role FROM users WHERE username = '$username'";
        $result = $this->db->select($sql);

        if (count($result) == 1) {
            $user_data = $result[0];
            if (password_verify($password, $user_data['password_hash'])) {
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $user_data['role']; 
                return true;
            }
        }
        return "Nieprawidłowy login lub hasło.";
    }

    public function logout() {
        session_destroy();
        $_SESSION = array(); 
        header("Location: index.php");
        exit;
    }

    public function is_logged_in() {
        return isset($_SESSION['user_id']);
    }

    public function is_admin() {
        return $this->is_logged_in() && $_SESSION['role'] === 'admin';
    }
    
    public function get_user_id() {
        return $this->is_logged_in() ? $_SESSION['user_id'] : null;
    }

    public function getUserData($user_id) {
    $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        if (!$user_id) return false;

        $sql = "SELECT id, username, email, created_at FROM users WHERE id = $user_id LIMIT 1";
        $result = $this->db->select($sql);

        if ($result) {
            return $result[0]; 
        }
        return false;
    }
private function check_session() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user_id'])) {
            $this->user_id = $_SESSION['user_id'];
        }
    }
    public function changePassword($user_id, $old_pass, $new_pass) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        
        if (strlen($new_pass) < 6) {
            return "Nowe hasło musi mieć co najmniej 6 znaków.";
        }
        if (!$user_id) {
            return "Błąd uwierzytelnienia użytkownika.";
        }

 
        $sql = "SELECT password_hash AS password FROM users WHERE id = $user_id LIMIT 1";
        $user_data = $this->db->select($sql);

        if (empty($user_data)) {
            return "Użytkownik nie istnieje.";
        }
        

        $hashed_password = $user_data[0]['password'];

        
        if (!password_verify($old_pass, $hashed_password)) {
            return "Podane stare hasło jest niepoprawne.";
        }

       
        $new_hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
        
     
        $new_hashed_password_safe = $this->db->escape_string($new_hashed_password);

       
        $update_sql = "UPDATE users SET password_hash = '$new_hashed_password_safe' WHERE id = $user_id";
        
        if ($this->db->execute($update_sql)) {
            return true; 
        } else {
            return "Błąd bazy danych podczas zmiany hasła.";
        }
    }

}
?>