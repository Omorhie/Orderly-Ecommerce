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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO officer VALUES('4','admin','admin@gmail.com','012345678','$2y$10$FSqiLRINkgVsYoe7cwFhweGHHgNW6u6N5unofbVAx6mkMlh//cxgG','admin','2026-02-12 21:34:27');
INSERT INTO officer VALUES('5','fauzan','fauzan@gmail.com','08913819313','$2y$10$.RDsZdpZqcFCj4sPELBEau2vqXR/5VzPuQazo.9vV5iNq9s51jt5m','admin','2026-02-19 16:28:09');


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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO orders VALUES('16','2','sepatuu','2026-02-25 14:19:20','jakarta','COD','100000','Pending');
INSERT INTO orders VALUES('17','2','sepatuu','2026-02-25 15:17:47','beji','COD','200000','Pending');
INSERT INTO orders VALUES('19','1','Nike Air vapormax Flyknit','2026-02-26 09:33:17','beji','Transfer','3000000','Pending');
INSERT INTO orders VALUES('20','1','Nike Air vapormax Flyknit','2026-02-26 10:19:55','jakarta','COD','500000','Pending');
INSERT INTO orders VALUES('21','1','Nike Air vapormax Flyknit','2026-02-26 11:26:38','beji','COD','500000','Pending');


CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO products VALUES('14','Nike Air vapormax Flyknit','The Nike Air VaporMax Flyknit is a lightweight, high-performance running shoe featuring revolutionary, full-length, direct-to-upper cushioning without a traditional midsole. Key features include a breathable, stretchy Flyknit upper for a sock-like fit and a durable, flexible, and responsive VaporMax Air unit designed for a \"running on air\" sensation.','500000','98','1772072995_ac42d423_9d29_4d9c_bc58_c486316156ed.webp','2026-02-26 09:29:01');


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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO transactions VALUES('7','19','1','Nike Air vapormax Flyknit','6','3000000','uploads/proofs/1772073197_ac42d423_9d29_4d9c_bc58_c486316156ed.webp','2026-02-26 09:33:17');
INSERT INTO transactions VALUES('8','20','1','Nike Air vapormax Flyknit','1','500000','','2026-02-26 10:19:55');
INSERT INTO transactions VALUES('9','21','1','Nike Air vapormax Flyknit','1','500000','','2026-02-26 11:26:38');


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users VALUES('1','fauzan','fauzan@gmail.com','895 3831 14323','$2y$10$og4dhG9ckSN4BDYHQC9XhuVXv39e7xScuYWzC3WoAZpYljcnpiW.m','2026-02-22 21:14:46');
INSERT INTO users VALUES('2','raihan','raihan@gmail.com','895 3831 14322','$2y$10$y9ISwxEbfNNcx8gwKeH0Ye3O704Qms0/B8tR5NLkpiYmuryAJBjzq','2026-02-24 19:38:28');


