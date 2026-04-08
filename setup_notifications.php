<?php
require_once "config/database.php";

$sql = "CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'NULL for global admin notification, ID for specific user',
  `type` varchar(50) NOT NULL COMMENT 'new_order, refund, delivery, chat',
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql) === TRUE) {
    echo "Table notifications created successfully\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}
?>
