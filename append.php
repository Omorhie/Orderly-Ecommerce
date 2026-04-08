<?php
$sql = "\n\n--\n-- Table structure for table `notifications`\n--\n\nCREATE TABLE `notifications` (\n  `id` int(11) NOT NULL,\n  `user_id` int(11) DEFAULT NULL,\n  `type` varchar(50) NOT NULL,\n  `message` text NOT NULL,\n  `is_read` tinyint(1) NOT NULL DEFAULT 0,\n  `created_at` timestamp NOT NULL DEFAULT current_timestamp()\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\nALTER TABLE `notifications`\n  ADD PRIMARY KEY (`id`);\n\nALTER TABLE `notifications`\n  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;\n";
file_put_contents('ecommerce_ukk.sql', $sql, FILE_APPEND);
echo "Done";
?>
