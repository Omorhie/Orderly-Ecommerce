CREATE TABLE `backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO cart VALUES('11','4','11','1','2026-02-24 21:12:20');


CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_id` (`cart_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `chats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sender` enum('user','admin') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO chats VALUES('16','2','hi','user','2026-04-08 11:36:36');
INSERT INTO chats VALUES('17','2','test','user','2026-04-08 11:36:38');
INSERT INTO chats VALUES('18','2','min','user','2026-04-08 13:19:40');
INSERT INTO chats VALUES('19','2','1','user','2026-04-08 13:52:59');
INSERT INTO chats VALUES('20','2','hi','user','2026-04-08 14:39:21');


CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'NULL for global admin notification, ID for specific user',
  `type` varchar(50) NOT NULL COMMENT 'new_order, refund, delivery, chat',
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO notifications VALUES('1','','chat','Pesan baru dari User (ID: 2): raihan','1','2026-04-08 14:39:21');
INSERT INTO notifications VALUES('2','','order','Pesanan baru telah masuk! Order ID: #00049','1','2026-04-08 14:39:54');


CREATE TABLE `officer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO officer VALUES('4','admin','admin@gmail.com','012345678','$2y$10$FSqiLRINkgVsYoe7cwFhweGHHgNW6u6N5unofbVAx6mkMlh//cxgG','admin','2026-02-12 21:34:27');
INSERT INTO officer VALUES('5','fauzan','fauzan@gmail.com','08913819313','$2y$10$.RDsZdpZqcFCj4sPELBEau2vqXR/5VzPuQazo.9vV5iNq9s51jt5m','admin','2026-02-19 16:28:09');
INSERT INTO officer VALUES('6','petugas','petugas@gmail.com','1234567654411','$2y$10$N3OrO4JAZXY9mvKLz88Uhu56EiW27eBDetCZRh3qdfaGcXYe/m1UK','petugas','2026-03-31 20:09:41');
INSERT INTO officer VALUES('8','vito','vito@gmail.com','0891381931322','$2y$10$8gbnVsJV/54LGwX4VvoFqeDEFXlJPyMIiYw7rbOTeqyZatB7BkSti','admin','2026-04-08 13:00:37');


CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `address` text DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `total_price` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `delivery_status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO orders VALUES('39','2','Star Player 76','2026-04-08 07:23:53','bandung','COD','1299000','Refunded','');
INSERT INTO orders VALUES('40','2','CONS Louie Lopez Pro 2 Suede','2026-04-08 07:28:48','bandung','COD','1049300','Rejected','');
INSERT INTO orders VALUES('41','2','Nike Air Force 1 \'07','2026-04-08 07:47:49','Depok','COD','1999999','Refund Requested','');
INSERT INTO orders VALUES('42','2','Converse Chuck Taylor Throwback','2026-04-08 08:10:06','Depok','COD','1199000','Refunded','');
INSERT INTO orders VALUES('43','2','Chuck Taylor All Star','2026-04-08 10:25:08','Depok','COD','899000','Confirmed','');
INSERT INTO orders VALUES('44','2','Chuck Taylor All Star','2026-04-08 11:30:23','Depok','COD','899000','Rejected','');
INSERT INTO orders VALUES('45','2','Nike Air Force 1 \'07','2026-04-08 11:39:22','Depok','COD','3999998','Rejected','');
INSERT INTO orders VALUES('46','2','Nike Air Force 1 \'07','2026-04-08 13:08:02','Depok (Shipping: SiCepat - Express [Rp 35.000])','Transfer','2034999','Confirmed','');
INSERT INTO orders VALUES('47','2','Nike Air Force 1 \'07','2026-04-08 13:17:09','Depok (Shipping: JNE - Regular [Rp 15.000])','Transfer','2014999','Refunded','');
INSERT INTO orders VALUES('48','2','CONS Louie Lopez Pro 2 Suede','2026-04-08 13:32:49','Depok (Shipping: JNE - Regular [Rp 15.000])','COD','1064300','Confirmed','Dalam Perjalanan');
INSERT INTO orders VALUES('49','2','Nike Air Force 1 \'07','2026-04-08 14:39:54','Depok (Shipping: JNE - Express [Rp 35.000])','COD','4034998','Pending','');


CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO products VALUES('14','Nike Air vapormax Flyknit','Nike','The Nike Air VaporMax Flyknit is a lightweight, high-performance running shoe featuring revolutionary, full-length, direct-to-upper cushioning without a traditional midsole. Key features include a breathable, stretchy Flyknit upper for a sock-like fit and a durable, flexible, and responsive VaporMax Air unit designed for a \"running on air\" sensation.','5000000','96','40','1772072995_ac42d423_9d29_4d9c_bc58_c486316156ed.webp','2026-02-26 09:29:01');
INSERT INTO products VALUES('16','Air Jordan 1 Low','Nike','Always in, always fresh. The Air Jordan 1 Low sets you up with a piece of Jordan history and heritage that\'s comfortable all day. Choose your colours, then step out in the iconic profile that\'s built with a high-end mix of materials and encapsulated Air in the heel.','1429000','18','39','1775439524_WMNS_AIR_JORDAN_1_LOW__1_.jpg','2026-04-06 08:38:44');
INSERT INTO products VALUES('17','Nike V2K Run','Nike','Fast-forward. Rewind. Doesn\'t matter—this shoe takes retro into the future. The V2K remasters everything you love about the Vomero in a look pulled straight from an early \'00s running catalogue. It\'s layered up in a mixture of flashy metallics, referential plastic details and a soft midsole with a perfectly aged aesthetic. And the chunky heel makes sure wherever you go, it\'s in comfort.','2099000','47','40','1775439727_W_NIKE_V2K_RUN.jpg','2026-04-06 08:42:07');
INSERT INTO products VALUES('18','Nike Pegasus 42','Nike','Responsive full-length cushioning sculpted to energise an icon.\r\nResponsive cushioning in the Pegasus 42 provides an energised ride for everyday road runs. Experience power in every stride thanks to the propulsive feel of a curved, full-length Air Zoom unit and a ReactX foam midsole. An updated fit gives you more space in the forefoot and toe box.','2199000','96','45','1775440662_W_NIKE_AIR_ZOOM_PEGASUS_42.jpg','2026-04-06 08:57:42');
INSERT INTO products VALUES('19','Converse Chuck Taylor Throwback','Converse','The Converse Chuck Taylor Throwback is a classic sneaker with a vintage style. It features a durable canvas upper, a rubber toe cap, and a comfortable sole for everyday wear.\r\nWith its retro design and timeless look, this shoe is easy to match with any outfit, making it perfect for casual activities and daily use.','1199000','90','41','1775440796_0888-CONA19787C00W09H-3.webp','2026-04-06 08:59:56');
INSERT INTO products VALUES('20','Star Player 76','Converse','converse','1299000','99','43','1775530383_0888-CONA21458CIDG09H-1.webp','2026-04-07 09:53:03');
INSERT INTO products VALUES('21','CONS Louie Lopez Pro 2 Suede','Converse','converse','1049300','98','42','1775530509_0888-CONA14324CIVO09H-1.webp','2026-04-07 09:55:09');
INSERT INTO products VALUES('23','Chuck Taylor All Star','Converse','converse\r\n','899000','97','41','1775532939_conm7650c-1.webp','2026-04-07 10:28:15');
INSERT INTO products VALUES('25','Nike Air Force 1 \'07','Nike','NIKE\r\n\r\n','1999999','92','41','1775533293_AIR_FORCE_1__07.jpg','2026-04-07 10:41:33');


CREATE TABLE `refunds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO refunds VALUES('1','42','mau ganti\r\n','approved','2026-04-08 08:20:05');
INSERT INTO refunds VALUES('2','41','test','pending','2026-04-08 08:37:12');
INSERT INTO refunds VALUES('3','39','mau ganti','approved','2026-04-08 09:34:53');
INSERT INTO refunds VALUES('4','47','mau ganti','approved','2026-04-08 13:18:47');


CREATE TABLE `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `proof_payment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO transactions VALUES('38','48','2','CONS Louie Lopez Pro 2 Suede','1','1049300','','2026-04-08 13:32:49');
INSERT INTO transactions VALUES('39','49','2','Nike Air Force 1 \'07','2','3999998','','2026-04-08 14:39:54');


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users VALUES('1','fauzan','fauzan@gmail.com','895 3831 14323','cagar alam','$2y$10$QM1XlscUOdye3Vf3ii2h3eWpA9rLzzNqmsPm17VRjMjdDgsyK90cC','2026-02-22 21:14:46');
INSERT INTO users VALUES('2','raihan','raihan@gmail.com','895 3831 14322','Depok','$2y$10$y9ISwxEbfNNcx8gwKeH0Ye3O704Qms0/B8tR5NLkpiYmuryAJBjzq','2026-02-24 19:38:28');
INSERT INTO users VALUES('3','vito','yazidauff@gmail.com','431758193681','','$2y$10$m7ZSjvSJMMCqXUnzy1LVGu3kD.k8FbMXhFV5YarrxewzDpY1ez6n2','2026-04-03 21:17:56');


