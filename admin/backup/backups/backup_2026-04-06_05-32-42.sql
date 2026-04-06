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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO officer VALUES('4','admin','admin@gmail.com','012345678','$2y$10$FSqiLRINkgVsYoe7cwFhweGHHgNW6u6N5unofbVAx6mkMlh//cxgG','admin','2026-02-12 21:34:27');
INSERT INTO officer VALUES('5','fauzan','fauzan@gmail.com','08913819313','$2y$10$.RDsZdpZqcFCj4sPELBEau2vqXR/5VzPuQazo.9vV5iNq9s51jt5m','admin','2026-02-19 16:28:09');
INSERT INTO officer VALUES('6','petugas','petugas@gmail.com','1234567654411','$2y$10$N3OrO4JAZXY9mvKLz88Uhu56EiW27eBDetCZRh3qdfaGcXYe/m1UK','petugas','2026-03-31 20:09:41');


CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `address` text DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `total_price` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO orders VALUES('16','2','sepatuu','2026-02-25 14:19:20','jakarta','COD','100000','Pending');
INSERT INTO orders VALUES('17','2','sepatuu','2026-02-25 15:17:47','beji','COD','200000','Pending');
INSERT INTO orders VALUES('19','1','Nike Air vapormax Flyknit','2026-02-26 09:33:17','beji','Transfer','3000000','Pending');
INSERT INTO orders VALUES('20','1','Nike Air vapormax Flyknit','2026-02-26 10:19:55','jakarta','COD','500000','Confirmed');
INSERT INTO orders VALUES('21','1','Nike Air vapormax Flyknit','2026-02-26 11:26:38','beji','COD','500000','confirmed');
INSERT INTO orders VALUES('22','1','Nike Air vapormax Flyknit','2026-03-31 21:38:26','depok','COD','500000','Confirmed');
INSERT INTO orders VALUES('23','2','Chuck Taylor Throwback','2026-04-06 09:43:31','depok','COD','3597000','Confirmed');
INSERT INTO orders VALUES('24','2','Nike V2K Run','2026-04-06 09:46:53','beji','COD','2099000','Confirmed');


CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `size` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO products VALUES('14','Nike Air vapormax Flyknit','The Nike Air VaporMax Flyknit is a lightweight, high-performance running shoe featuring revolutionary, full-length, direct-to-upper cushioning without a traditional midsole. Key features include a breathable, stretchy Flyknit upper for a sock-like fit and a durable, flexible, and responsive VaporMax Air unit designed for a \"running on air\" sensation.','500000','96','40','1772072995_ac42d423_9d29_4d9c_bc58_c486316156ed.webp','2026-02-26 09:29:01');
INSERT INTO products VALUES('15','Nike Air Force 1 \'07','Comfortable, durable and timeless—it\'s number one for a reason. The classic \'80s construction pairs smooth leather with bold details for style that tracks whether you\'re on court or on the go.\r\n\r\n','1549000','10','41','1775439359_AIR_FORCE_1__07.jpg','2026-04-06 08:35:59');
INSERT INTO products VALUES('16','Air Jordan 1 Low','Always in, always fresh. The Air Jordan 1 Low sets you up with a piece of Jordan history and heritage that\'s comfortable all day. Choose your colours, then step out in the iconic profile that\'s built with a high-end mix of materials and encapsulated Air in the heel.','1429000','20','39','1775439524_WMNS_AIR_JORDAN_1_LOW__1_.jpg','2026-04-06 08:38:44');
INSERT INTO products VALUES('17','Nike V2K Run','Fast-forward. Rewind. Doesn\'t matter—this shoe takes retro into the future. The V2K remasters everything you love about the Vomero in a look pulled straight from an early \'00s running catalogue. It\'s layered up in a mixture of flashy metallics, referential plastic details and a soft midsole with a perfectly aged aesthetic. And the chunky heel makes sure wherever you go, it\'s in comfort.','2099000','49','40','1775439727_W_NIKE_V2K_RUN.jpg','2026-04-06 08:42:07');
INSERT INTO products VALUES('18','Nike Pegasus 42','Responsive full-length cushioning sculpted to energise an icon.\r\nResponsive cushioning in the Pegasus 42 provides an energised ride for everyday road runs. Experience power in every stride thanks to the propulsive feel of a curved, full-length Air Zoom unit and a ReactX foam midsole. An updated fit gives you more space in the forefoot and toe box.','2199000','100','45','1775440662_W_NIKE_AIR_ZOOM_PEGASUS_42.jpg','2026-04-06 08:57:42');
INSERT INTO products VALUES('19','Converse Chuck Taylor Throwback','The Converse Chuck Taylor Throwback is a classic sneaker with a vintage style. It features a durable canvas upper, a rubber toe cap, and a comfortable sole for everyday wear.\r\nWith its retro design and timeless look, this shoe is easy to match with any outfit, making it perfect for casual activities and daily use.','1199000','96','41','1775440796_0888-CONA19787C00W09H-3.webp','2026-04-06 08:59:56');


CREATE TABLE `sales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO sales VALUES('2','tolak angin','1','12000','2026-02-20 16:57:01');


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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO transactions VALUES('12','24','2','Nike V2K Run','1','2099000','','2026-04-06 09:46:53');


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users VALUES('1','fauzan','fauzan@gmail.com','895 3831 14323','$2y$10$og4dhG9ckSN4BDYHQC9XhuVXv39e7xScuYWzC3WoAZpYljcnpiW.m','2026-02-22 21:14:46');
INSERT INTO users VALUES('2','raihan','raihan@gmail.com','895 3831 14322','$2y$10$y9ISwxEbfNNcx8gwKeH0Ye3O704Qms0/B8tR5NLkpiYmuryAJBjzq','2026-02-24 19:38:28');
INSERT INTO users VALUES('3','vito','yazidauff@gmail.com','431758193681','$2y$10$m7ZSjvSJMMCqXUnzy1LVGu3kD.k8FbMXhFV5YarrxewzDpY1ez6n2','2026-04-03 21:17:56');


