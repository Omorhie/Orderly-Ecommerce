<?php
// Function to add a notification
// user_id: NULL for global admin notifications, INT for specific user notifications
function add_notification($conn, $user_id, $type, $message) {
    if ($user_id === null) {
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, type, message) VALUES (NULL, ?, ?)");
        $stmt->bind_param("ss", $type, $message);
    } else {
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $type, $message);
    }
    
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

// Function to fetch unread notification count
function get_unread_count($conn, $user_id = null) {
    if ($user_id === null) {
        // Admin: count where user_id is NULL
        $q = "SELECT COUNT(*) as count FROM notifications WHERE user_id IS NULL AND is_read = 0";
    } else {
        // User: count where user_id matches
        $q = "SELECT COUNT(*) as count FROM notifications WHERE user_id = " . intval($user_id) . " AND is_read = 0";
    }
    $res = $conn->query($q);
    if($res) {
        return $res->fetch_assoc()['count'];
    }
    return 0;
}
?>
