CREATE TABLE `user` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `password` text NULL DEFAULT NULL,
    `status` ENUM('created','active','inactive','suspended','deleted') NOT NULL DEFAULT 'created',
    `created` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `completion` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `description` varchar(20) NOT NULL,
    `order` int NULL DEFAULT NULL,
    `created` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `category` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `description` varchar(20) NOT NULL,
    `created` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `genre` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_category` int(11) DEFAULT NULL,
    `description` varchar(20) NOT NULL,
    `created` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_genre_category_idx` (`id_category`),
    CONSTRAINT `fk_genre_category` FOREIGN KEY (`id_category`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE `midia` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_category` int(11) NOT NULL,
    `id_main_midia` int(11) DEFAULT NULL,
    `title` text NOT NULL,
    `description` text NULL DEFAULT NULL,
    `release_year` char(4) NULL DEFAULT NULL,
    `release_month` char(2) NULL DEFAULT NULL,
    `release_day` char(2) NULL DEFAULT NULL,
    `created` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_midia_category_idx` (`id_category`),
    KEY `fk_main_midia_idx` (`id_main_midia`),
    CONSTRAINT `fk_midia_category` FOREIGN KEY (`id_category`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_main_midia` FOREIGN KEY (`id_main_midia`) REFERENCES `midia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE `midia_genre` (
    `id_midia` int(11) NOT NULL,
    `id_genre` int(11) NOT NULL,
    `main` ENUM('no','yes') NOT NULL DEFAULT 'no',
    `created` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated` timestamp NULL DEFAULT NULL,
    KEY `fk_midia_genre_midia_idx` (`id_midia`),
    KEY `fk_midia_genre_genre_idx` (`id_genre`),
    CONSTRAINT `fk_midia_genre_midia` FOREIGN KEY (`id_midia`) REFERENCES `midia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_midia_genre_genre` FOREIGN KEY (`id_genre`) REFERENCES `genre` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE `user_midia` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_user` int(11) NOT NULL,
    `id_midia` int(11) NOT NULL,
    `id_completion` int(11) NULL DEFAULT NULL,
    `score` decimal(10,2) NULL DEFAULT NULL,
    `created` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_user_midia_user_idx` (`id_user`),
    KEY `fk_user_midia_midia_idx` (`id_midia`),
    KEY `fk_user_midia_completion_idx` (`id_completion`),
    CONSTRAINT `fk_user_midia_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_user_midia_midia` FOREIGN KEY (`id_midia`) REFERENCES `midia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_user_midia_completion` FOREIGN KEY (`id_completion`) REFERENCES `completion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);


CREATE TABLE `session` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_user` int(11) NOT NULL,
    `id_midia` int(11) NOT NULL,
    `code` varchar(20) NOT NULL,
    `start` timestamp NOT NULL DEFAULT current_timestamp(),
    `end` timestamp NULL DEFAULT NULL,
    `created` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_session_user_idx` (`id_user`),
    KEY `fk_session_midia_idx` (`id_midia`),
    CONSTRAINT `fk_session_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_session_midia` FOREIGN KEY (`id_midia`) REFERENCES `midia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE `coment` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_user` int(11) NOT NULL,
    `id_midia` int(11) NOT NULL,
    `id_session` int(11) NULL DEFAULT NULL,
    `text` longtext NOT NULL,
    `created` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_coment_user_idx` (`id_user`),
    KEY `fk_coment_midia_idx` (`id_midia`),
    KEY `fk_coment_session_idx` (`id_session`),
    CONSTRAINT `fk_coment_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_coment_midia` FOREIGN KEY (`id_midia`) REFERENCES `midia` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_coment_session` FOREIGN KEY (`id_session`) REFERENCES `session` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
);