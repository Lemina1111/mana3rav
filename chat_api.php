<?php
session_start();
include 'conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_client']) && !isset($_SESSION['id_commercant'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? $_GET['action'] ?? '';

if ($action == 'getMessages') {
    $id_client = intval($_GET['id_client']);
    $id_commercant = intval($_GET['id_commercant']);

    if (!$id_client || !$id_commercant) {
        echo json_encode(['success' => false, 'message' => 'Invalid participants.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM Chat WHERE (id_client = ? AND id_commercant = ?) ORDER BY date_Chat ASC");
    $stmt->bind_param("ii", $id_client, $id_commercant);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    echo json_encode(['success' => true, 'messages' => $messages]);

} elseif ($action == 'sendMessage') {
    // Debugging
    error_log(print_r($data, true));
    error_log(print_r($_SESSION, true));

    $message = trim($data['message']);
    $id_client = intval($data['id_client']);
    $id_commercant = intval($data['id_commercant']);
    $sender_type = isset($_SESSION['id_client']) ? 'client' : 'commercant';

    if (empty($message) || !$id_client || !$id_commercant) {
        echo json_encode(['success' => false, 'message' => 'Invalid data.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO Chat (contenu_Chat, date_Chat, type_Chat, id_client, id_commercant) VALUES (?, NOW(), ?, ?, ?)");
    $stmt->bind_param("ssii", $message, $sender_type, $id_client, $id_commercant);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}

$conn->close();
?>
