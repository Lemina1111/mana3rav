<?php
session_start();
include 'conn.php';

if (!isset($_SESSION['id_client']) && !isset($_SESSION['id_commercant'])) {
    header("Location: login.php");
    exit;
}

$is_client = isset($_SESSION['id_client']);

if ($is_client) {
    $id_client = $_SESSION['id_client'];
    $id_commercant = intval($_GET['id_commercant'] ?? 0);
} else { // Is commercant
    $id_commercant = $_SESSION['id_commercant'];
    $id_client = intval($_GET['id_client'] ?? 0);
}

if (!$id_client || !$id_commercant) {
    die("Invalid chat participants.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; display: flex; flex-direction: column; height: 100vh; background-color: #f5f5f5; }
        .chat-container { flex-grow: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; }
        .message { margin-bottom: 15px; padding: 10px 15px; border-radius: 20px; max-width: 70%; line-height: 1.4; }
        .sent { background-color: #dcf8c6; align-self: flex-end; }
        .received { background-color: #fff; align-self: flex-start; box-shadow: 0 1px 1px rgba(0,0,0,0.1); }
        .chat-form { display: flex; padding: 10px; border-top: 1px solid #ddd; background-color: #eee; }
        .chat-form input { flex-grow: 1; padding: 10px; border: 1px solid #ccc; border-radius: 20px; }
        .chat-form button { padding: 10px 20px; margin-left: 10px; background-color: #007bff; color: white; border: none; border-radius: 20px; cursor: pointer; }
    </style>
</head>
<body>

<div class="chat-container" id="chat-box">
    <!-- Messages will be loaded here -->
</div>

<form class="chat-form" id="message-form">
    <input type="text" id="message-input" placeholder="Écrivez un message..." autocomplete="off">
    <button type="submit">Envoyer</button>
</form>

<script>
    const chatBox = document.getElementById('chat-box');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const id_client = <?= json_encode($id_client) ?>;
    const id_commercant = <?= json_encode($id_commercant) ?>;
    const is_client_session = <?= json_encode($is_client) ?>;

    async function getMessages() {
        const response = await fetch(`chat_api.php?action=getMessages&id_client=${id_client}&id_commercant=${id_commercant}`);
        const data = await response.json();
        if (data.success) {
            chatBox.innerHTML = '';
            data.messages.forEach(msg => {
                const msgDiv = document.createElement('div');
                msgDiv.classList.add('message');
                const senderClass = (is_client_session && msg.type_Chat === 'client') || (!is_client_session && msg.type_Chat === 'commercant') ? 'sent' : 'received';
                msgDiv.classList.add(senderClass);
                msgDiv.textContent = msg.contenu_Chat;
                chatBox.appendChild(msgDiv);
            });
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    }

    messageForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (message) {
            const response = await fetch('chat_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: 'sendMessage', 
                    message: message, 
                    id_client: id_client,
                    id_commercant: id_commercant 
                })
            });
            const data = await response.json();
            if (data.success) {
                messageInput.value = '';
                await getMessages();
            }
        }
    });

    getMessages();
    setInterval(getMessages, 3000);
</script>

</body>
</html>
