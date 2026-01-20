<?php
class Messenger {
    private $db;
    private $admin_user_id = 1; 

    public function __construct($db) {
        $this->db = $db;
    }

    public function startNewConversation($user_id, $subject, $content) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        $subject_safe = $this->db->escape_string($subject);
        $content_safe = $this->db->escape_string($content);

        $conv_sql = "INSERT INTO conversations (user_id, admin_id, subject) 
                     VALUES ($user_id, {$this->admin_user_id}, '$subject_safe')";
        
        if ($this->db->execute($conv_sql)) {
            $conversation_id = $this->db->get_last_id(); 
            $msg_sql = "INSERT INTO messages (conversation_id, sender_id, content) 
                        VALUES ($conversation_id, $user_id, '$content_safe')";
            
            return $this->db->execute($msg_sql);
        }
        return false;
    }

    public function getConversations($user_id, $isAdmin = false) {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        if (!$user_id) return [];

        if ($isAdmin) {
            $sql = "SELECT c.*, u.username 
                    FROM conversations c 
                    JOIN users u ON c.user_id = u.id 
                    ORDER BY c.updated_at DESC";
        } else {
            $sql = "SELECT c.*, u.username,
                    (SELECT COUNT(*) FROM messages m WHERE m.conversation_id = c.id AND m.is_read = 0 AND m.sender_id != $user_id) AS unread_count
                    FROM conversations c 
                    JOIN users u ON c.user_id = u.id
                    WHERE c.user_id = $user_id OR c.admin_id = $user_id
                    ORDER BY c.updated_at DESC";
        }
        return $this->db->select($sql);
    }
    
 
    public function getMessages($conversation_id, $user_id = null) {
        $conversation_id = filter_var($conversation_id, FILTER_VALIDATE_INT);
        if (!$conversation_id) return [];

        $messages = $this->db->select("SELECT * FROM messages WHERE conversation_id = $conversation_id ORDER BY sent_at ASC");
        
        if (!empty($messages) && $user_id) {
            $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
            $this->db->execute("UPDATE messages SET is_read = 1 
                                WHERE conversation_id = $conversation_id AND sender_id != $user_id");
        }
        
        return $messages;
    }

    public function sendMessage($conversation_id, $sender_id, $content) {
        $conversation_id = filter_var($conversation_id, FILTER_VALIDATE_INT);
        $sender_id = filter_var($sender_id, FILTER_VALIDATE_INT);
        $content_safe = $this->db->escape_string($content);

        if (!$conversation_id || !$sender_id || empty($content)) return false;

        $msg_sql = "INSERT INTO messages (conversation_id, sender_id, content) 
                    VALUES ($conversation_id, $sender_id, '$content_safe')";

        $this->db->execute("UPDATE conversations SET status = 'open', updated_at = NOW() WHERE id = $conversation_id");
        
        return $this->db->execute($msg_sql);
    }
}