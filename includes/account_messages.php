<?php
if (!$user->is_logged_in()) { die("Błąd dostępu."); }

$user_id = (int)$user->get_user_id(); 
$conversations = $messenger->getConversations($user_id);
$current_conversation_id = filter_input(INPUT_GET, 'conv', FILTER_VALIDATE_INT);
$current_conversation = null;
$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message']) && $current_conversation_id) {
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
    if (!empty($content)) {
        $messenger->sendMessage($current_conversation_id, $user_id, $content);
        header("Location: account.php?action=messages&conv=" . $current_conversation_id);
        exit;
    }
}

if ($current_conversation_id) {
    $messages = $messenger->getMessages($current_conversation_id,$user_id);
    foreach ($conversations as $conv) {
        if ($conv['id'] == $current_conversation_id) {
            $current_conversation = $conv;
            break;
        }
    }
}
?>

<style>
    .mailbox-layout { display: flex; gap: 20px; min-height: 450px; background: #fff; border: 1px solid #eee; border-radius: 8px; }
    .conversation-list { flex: 1; border-right: 1px solid #eee; background: #fafafa; }
    .conversation-window { flex: 3; display: flex; flex-direction: column; background: #fff; }
    .conv-item { padding: 15px; border-bottom: 1px solid #eee; transition: 0.2s; display: block; text-decoration: none; color: #333; }
    .conv-item.active { background: #fff; border-left: 4px solid #0779e4; }
    .messages-container { height: 380px; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; background: #f9f9f9; }
    .message { margin-bottom: 15px; padding: 12px; border-radius: 12px; max-width: 75%; }
    .message.is-me { background-color: #0779e4; color: white; align-self: flex-end; border-bottom-right-radius: 2px; }
    .message.is-other { background-color: #e9e9eb; color: #333; align-self: flex-start; border-bottom-left-radius: 2px; }
</style>

<div class="mailbox-layout">
    <div class="conversation-list">
        <div style="padding: 15px; border-bottom: 1px solid #eee; font-weight: bold;">Wątki</div>
        <?php foreach ($conversations as $conv): ?>
            <a href="account.php?action=messages&conv=<?= $conv['id'] ?>" class="conv-item <?= ($conv['id'] == $current_conversation_id) ? 'active' : ''; ?>">
                <strong><?= htmlspecialchars($conv['subject']) ?></strong><br>
                <small>Status: <?= htmlspecialchars($conv['status']) ?></small>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="conversation-window">
        <?php if ($current_conversation): ?>
            <div style="padding: 15px; border-bottom: 1px solid #eee;"><strong>Temat: <?= htmlspecialchars($current_conversation['subject']) ?></strong></div>
            <div class="messages-container" id="userChat">
                <?php foreach ($messages as $msg): ?>
                    <?php 
                        $is_me = ((int)$msg['sender_id'] === $user_id); 
                        if ($is_me) {
                            $sender_name = "Ty";
                        } else {
                            $sender_name = ($user->is_admin()) ? htmlspecialchars($current_conversation['username']) : "Biuro Podróży (Admin)";
                        }
                    ?>
                    <div class="message <?= $is_me ? 'is-me' : 'is-other'; ?>">
                        <p style="margin: 0;"><?= nl2br(htmlspecialchars($msg['content'])) ?></p>
                        <small style="display: block; margin-top: 5px; opacity: 0.8;">
                            <strong><?= $sender_name ?></strong> • <?= date('H:i', strtotime($msg['sent_at'])) ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            </div>
            <form action="account.php?action=messages&conv=<?= $current_conversation_id ?>" method="POST" style="padding: 15px;">
                <textarea name="content" rows="3" required style="width: 100%; padding: 10px;" placeholder="Twoja odpowiedź..."></textarea>
                <input type="hidden" name="send_message" value="1">
                <button type="submit" class="button" style="float: right; margin-top: 5px;">Wyślij</button>
            </form>
            <script>var c = document.getElementById('userChat'); c.scrollTop = c.scrollHeight;</script>
        <?php else: ?>
            <div style="padding: 50px; text-align: center; color: #999;">Wybierz rozmowę</div>
        <?php endif; ?>
    </div>
</div>