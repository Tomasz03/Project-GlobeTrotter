<?php


class Tour {
    private $db;

    public function __construct($db_instance) {
        $this->db = $db_instance;
    }
    

    private function handleImageUpload(array $file): ?string
{
  
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; 
    }
    

    $upload_dir = 'images/'; 
    

    $target_dir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . $upload_dir;
    

    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            return null; 
        }
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null; 
    }

    $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($imageFileType, $allowed_types)) {
        return null; 
    }

   $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
$safe_original_name = preg_replace('/[^A-Za-z0-9_\-]/', '', $original_name); 

$new_file_name = $safe_original_name . '.' . $imageFileType;
$counter = 1;
$final_file_name = $new_file_name;
while (file_exists($target_dir . $final_file_name)) {
    $final_file_name = $safe_original_name . '_' . $counter++ . '.' . $imageFileType;
}
$new_file_name = $final_file_name;
    $target_file = $target_dir . $new_file_name;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $upload_dir . $new_file_name;
    } else {
        return null;
    }
}
   
    public function saveTour($data) {
        $title = $this->db->escape_string($data['title']);
        $country = $this->db->escape_string($data['country']);
        $description = $this->db->escape_string($data['description']);
        $price = filter_var($data['price'], FILTER_VALIDATE_FLOAT); 
        $start_date = $this->db->escape_string($data['start_date']);
        $end_date = $this->db->escape_string($data['end_date']);
        
        $departure_time = $this->db->escape_string($data['departure_time']);
        $return_time = $this->db->escape_string($data['return_time']);
        $transport_type = $this->db->escape_string($data['transport_type']);
        $max_slots = filter_var($data['max_slots'], FILTER_VALIDATE_INT);
        
       
        
        if (empty($data['start_date']) || empty($data['end_date'])) {
            return "Błąd: Data wylotu/wyjazdu i powrotu są wymagane.";
        }
        
        try {
            $start = new DateTime($data['start_date']);
            $end = new DateTime($data['end_date']);
        } catch (Exception $e) {
            return " Nieprawidłowy format daty.";
        }

       
        if ($start > $end) {
            return " Data powrotu nie może być wcześniejsza niż data wylotu/wyjazdu.";
        }
       
        $interval = $start->diff($end);
        $duration_days = $interval->days + 1; 
        

        $new_image_url = $this->handleImageUpload($data['image_file']);
        $image_url = $data['current_image_url'] ?? null; 
        
        if ($new_image_url) {
            $image_url = $new_image_url; 
        } elseif (!$image_url && !isset($data['id'])) {
             return "Błąd: Wymagane jest przesłanie zdjęcia dla nowej wycieczki.";
        }
        
     
        if (!$title || $price === false || $max_slots === false) {
            return "Błąd walidacji danych wejściowych.";
        }
        
    
        if ($new_image_url && isset($data['id']) && $data['id'] > 0) {
            $old_tour = $this->getTourById($data['id']);
            if ($old_tour && !empty($old_tour['image_url'])) {
                $file_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $old_tour['image_url'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
        
        if (isset($data['id']) && $data['id'] > 0) {
            $id = filter_var($data['id'], FILTER_VALIDATE_INT);
            $sql = "UPDATE tours SET 
                    title='$title', country='$country', description='$description', price=$price, 
                    start_date='$start_date', end_date='$end_date', duration_days=$duration_days, 
                    departure_time='$departure_time', return_time='$return_time', transport_type='$transport_type', 
                    max_slots=$max_slots, image_url='$image_url' WHERE id=$id";
        } else {
            $sql = "INSERT INTO tours (title, country, description, price, start_date, end_date, duration_days, departure_time, return_time, transport_type, max_slots, image_url) 
                    VALUES ('$title', '$country', '$description', $price, '$start_date', '$end_date', $duration_days, '$departure_time', '$return_time', '$transport_type', $max_slots, '$image_url')";
        }
        
        return $this->db->execute($sql);
    }
    
    public function getTours($filter_country = null) {
        $sql = "SELECT * FROM tours";
        $where = [];

        if ($filter_country && $filter_country !== 'all') {
            $country = $this->db->escape_string($filter_country);
            $where[] = "country = '$country'";
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY start_date ASC";
        return $this->db->select($sql);
    }
    
    public function getTourById($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) return false;
        
        $sql = "SELECT * FROM tours WHERE id=$id";
        $result = $this->db->select($sql);
        return count($result) > 0 ? $result[0] : false;
    }
    public function deleteTour($id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) return false;
        
        $tour = $this->getTourById($id);
        if ($tour && !empty($tour['image_url'])) {
             $file_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $tour['image_url']; 
             if (file_exists($file_path)) {
                 unlink($file_path);
             }
        }
        $sql = "DELETE FROM tours WHERE id=$id";
        return $this->db->execute($sql);
    }

    public function getAvailableSlots($tour_id) {
        $tour_id = filter_var($tour_id, FILTER_VALIDATE_INT);
        $sql_total = "SELECT max_slots FROM tours WHERE id = $tour_id";
        $total = $this->db->select($sql_total);
        
        if (empty($total)) return 0;
        $max = $total[0]['max_slots'];

        $sql_reserved = "SELECT SUM(reserved_slots) as reserved FROM reservations WHERE tour_id = $tour_id";
        $reserved_res = $this->db->select($sql_reserved);
        $reserved = $reserved_res[0]['reserved'] ?? 0;

        return $max - $reserved;
    }
public function getAllReservations() {
        $sql = "SELECT r.id as reservation_id, r.reserved_slots, r.reservation_date, r.user_id, r.status, 
                       t.title, u.username
                FROM reservations r 
                JOIN tours t ON r.tour_id = t.id 
                JOIN users u ON r.user_id = u.id
                ORDER BY r.status, r.reservation_date DESC";
        return $this->db->select($sql);
    }
   public function addReservation($user_id, $tour_id, $slots) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        $tour_id = filter_var($tour_id, FILTER_VALIDATE_INT);
        $slots = filter_var($slots, FILTER_VALIDATE_INT);

        if (!$user_id || !$tour_id || $slots <= 0) return false;
        
        $available = $this->getAvailableSlots($tour_id);
        if ($slots > $available) {
            return "Brak wystarczającej liczby wolnych miejsc. Dostępne: $available.";
        }

        $check_sql = "SELECT id FROM reservations WHERE user_id=$user_id AND tour_id=$tour_id";
        if (count($this->db->select($check_sql)) > 0) {
            return "Już masz tę wycieczkę w koszyku.";
        }

        $sql = "INSERT INTO reservations (user_id, tour_id, reserved_slots) VALUES ($user_id, $tour_id, $slots)";
        
    
        if ($this->db->execute($sql)) {
            return true; 
        } else {
            return false; 
        }
    }


    public function getUserPaidReservations($user_id) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        if (!$user_id) return [];
        
        $sql = "SELECT r.id as reservation_id, r.reserved_slots, r.reservation_date, t.* FROM reservations r 
                JOIN tours t ON r.tour_id = t.id 
                WHERE r.user_id = $user_id AND r.status = 'paid'
                ORDER BY r.reservation_date DESC";
        return $this->db->select($sql);
    }
    
       public function updateReservationStatus($reservation_id, $status) {
        $reservation_id = filter_var($reservation_id, FILTER_VALIDATE_INT);
        $valid_statuses = ['pending', 'paid', 'cancelled'];
        
        if (!$reservation_id || !in_array($status, $valid_statuses)) {
            return "Niepoprawne dane lub status.";
        }

  
        $status_safe = $this->db->escape_string($status);

        $sql = "UPDATE reservations SET status = '$status_safe' WHERE id = $reservation_id";
        
        if ($this->db->execute($sql)) {
            return true;
        } else {
            return "Błąd bazy danych podczas aktualizacji statusu.";
        }
    }
    public function getUniqueCountries() {
        $sql = "SELECT DISTINCT country FROM tours ORDER BY country ASC";
        $results = $this->db->select($sql);
        $countries = [];
        foreach ($results as $row) {
            $countries[] = $row['country'];
        }
        return $countries;
    }

   public function getUserReservations($user_id) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        if (!$user_id) return [];
        
       
        $sql = "SELECT r.id as reservation_id, r.reserved_slots, r.reservation_date, t.* FROM reservations r 
                JOIN tours t ON r.tour_id = t.id 
                WHERE r.user_id = $user_id
                ORDER BY r.reservation_date DESC";
        return $this->db->select($sql);
    }
    
    public function deleteReservation($reservation_id, $user_id) {
        $reservation_id = filter_var($reservation_id, FILTER_VALIDATE_INT);
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);

        if (!$reservation_id || !$user_id) return false;

        $sql = "DELETE FROM reservations WHERE id=$reservation_id AND user_id=$user_id";
        return $this->db->execute($sql);
    }

    public function updateReservationSlots($reservation_id, $new_slots, $user_id) {
        $reservation_id = filter_var($reservation_id, FILTER_VALIDATE_INT);
        $new_slots = filter_var($new_slots, FILTER_VALIDATE_INT);
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);

        if (!$reservation_id || $new_slots === false || $new_slots <= 0 || !$user_id) return false;

       
        $res_sql = "SELECT tour_id FROM reservations WHERE id = $reservation_id AND user_id = $user_id";
        $res = $this->db->select($res_sql);
        if (empty($res)) return false;
        $tour_id = $res[0]['tour_id'];

        $available = $this->getAvailableSlots($tour_id);
        
    
        $current_slots_sql = "SELECT reserved_slots FROM reservations WHERE id = $reservation_id";
        $current_slots = $this->db->select($current_slots_sql)[0]['reserved_slots'];
        
        if ($new_slots > ($available + $current_slots)) {
            return false;
        }

        $sql = "UPDATE reservations SET reserved_slots = $new_slots WHERE id = $reservation_id AND user_id = $user_id";
        return $this->db->execute($sql);
    }
}
?>