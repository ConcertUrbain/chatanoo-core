-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Jeu 14 Mai 2015 à 15:48
-- Version du serveur: 5.5.40
-- Version de PHP: 5.4.35-0+deb7u2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `chatanoo`
--

-- --------------------------------------------------------

--
-- Structure de la table `api_keys`
--

CREATE TABLE IF NOT EXISTS `api_keys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `api_key` varchar(32) NOT NULL,
  `host` text NOT NULL,
  `sessions_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `site` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessions_id` int(11) NOT NULL,
  `content` text,
  `addDate` datetime DEFAULT NULL,
  `setDate` datetime DEFAULT NULL,
  `isValid` tinyint(1) DEFAULT NULL,
  `items_id` int(11) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_comments_items` (`items_id`),
  KEY `fk_comments_users` (`users_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `datas_adress`
--

CREATE TABLE IF NOT EXISTS `datas_adress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessions_id` int(11) NOT NULL,
  `adress` varchar(255) DEFAULT NULL,
  `zipCode` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `addDate` datetime DEFAULT NULL,
  `setDate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `datas_assoc`
--

CREATE TABLE IF NOT EXISTS `datas_assoc` (
  `datas_id` int(11) NOT NULL,
  `dataType` varchar(45) DEFAULT NULL,
  `assoc_id` int(11) NOT NULL,
  `assocType` varchar(45) DEFAULT NULL,
  KEY `fk_datas_assoc_datas_vote` (`datas_id`),
  KEY `fk_datas_assoc_datas_carto` (`datas_id`),
  KEY `fk_datas_assoc_datas_adress` (`datas_id`),
  KEY `fk_datas_assoc_users` (`assoc_id`),
  KEY `fk_datas_assoc_comments` (`assoc_id`),
  KEY `fk_datas_assoc_items` (`assoc_id`),
  KEY `fk_datas_assoc_queries` (`assoc_id`),
  KEY `fk_datas_assoc_medias_video` (`assoc_id`),
  KEY `fk_datas_assoc_medias_text` (`assoc_id`),
  KEY `fk_datas_assoc_medias_sound` (`assoc_id`),
  KEY `fk_datas_assoc_medias_picture` (`assoc_id`),
  KEY `datas_id` (`datas_id`),
  KEY `assoc_id` (`assoc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `datas_carto`
--

CREATE TABLE IF NOT EXISTS `datas_carto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessions_id` int(11) NOT NULL,
  `x` float DEFAULT NULL,
  `y` float DEFAULT NULL,
  `addDate` datetime DEFAULT NULL,
  `setDate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `datas_vote`
--

CREATE TABLE IF NOT EXISTS `datas_vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessions_id` int(11) NOT NULL,
  `rate` int(11) DEFAULT NULL,
  `addDate` datetime DEFAULT NULL,
  `setDate` datetime DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_datas_vote_users` (`users_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessions_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `addDate` datetime DEFAULT NULL,
  `setDate` datetime DEFAULT NULL,
  `isValid` tinyint(1) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_items_users` (`users_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `medias_assoc`
--

CREATE TABLE IF NOT EXISTS `medias_assoc` (
  `medias_id` int(11) NOT NULL DEFAULT '0',
  `mediaType` varchar(45) DEFAULT NULL,
  `assoc_id` int(11) NOT NULL,
  `assocType` varchar(45) DEFAULT NULL,
  KEY `fk_medias_assoc_medias_picture` (`medias_id`),
  KEY `fk_medias_assoc_medias_sound` (`medias_id`),
  KEY `fk_medias_assoc_medias_text` (`medias_id`),
  KEY `fk_medias_assoc_medias_video` (`medias_id`),
  KEY `fk_medias_assoc_items` (`assoc_id`),
  KEY `fk_medias_assoc_queries` (`assoc_id`),
  KEY `media_id` (`medias_id`),
  KEY `assoc_Id` (`assoc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `medias_picture`
--

CREATE TABLE IF NOT EXISTS `medias_picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessions_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `url` varchar(255) DEFAULT NULL,
  `width` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `preview` varchar(255) DEFAULT NULL,
  `addDate` datetime DEFAULT NULL,
  `setDate` datetime DEFAULT NULL,
  `isValid` tinyint(1) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_medias_picture_users` (`users_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `medias_sound`
--

CREATE TABLE IF NOT EXISTS `medias_sound` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessions_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `url` varchar(255) DEFAULT NULL,
  `totalTime` int(11) DEFAULT NULL,
  `preview` varchar(255) DEFAULT NULL,
  `addDate` datetime DEFAULT NULL,
  `setDate` datetime DEFAULT NULL,
  `isValid` tinyint(1) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_medias_sound_users` (`users_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `medias_text`
--

CREATE TABLE IF NOT EXISTS `medias_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessions_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `content` text,
  `preview` varchar(255) DEFAULT NULL,
  `addDate` datetime DEFAULT NULL,
  `setDate` datetime DEFAULT NULL,
  `isValid` tinyint(1) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_medias_text_users` (`users_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `medias_video`
--

CREATE TABLE IF NOT EXISTS `medias_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessions_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `url` varchar(255) DEFAULT NULL,
  `width` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `totalTime` int(11) DEFAULT NULL,
  `preview` varchar(255) DEFAULT NULL,
  `addDate` datetime DEFAULT NULL,
  `setDate` datetime DEFAULT NULL,
  `isValid` tinyint(1) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_medias_video_users` (`users_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `metas`
--

CREATE TABLE IF NOT EXISTS `metas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessions_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT 'keyword',
  `content` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `metas_assoc`
--

CREATE TABLE IF NOT EXISTS `metas_assoc` (
  `metas_id` int(11) NOT NULL,
  `assoc_id` int(11) NOT NULL,
  `assocType` varchar(45) DEFAULT NULL,
  KEY `fk_metas_assoc_metas` (`metas_id`),
  KEY `fk_metas_assoc_medias_video` (`assoc_id`),
  KEY `fk_metas_assoc_medias_text` (`assoc_id`),
  KEY `fk_metas_assoc_medias_sound` (`assoc_id`),
  KEY `fk_metas_assoc_medias_picture` (`assoc_id`),
  KEY `fk_metas_assoc_items` (`assoc_id`),
  KEY `fk_metas_assoc_queries` (`assoc_id`),
  KEY `meta_id` (`metas_id`),
  KEY `assoc_id` (`assoc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `queries`
--

CREATE TABLE IF NOT EXISTS `queries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) DEFAULT NULL,
  `description` text,
  `addDate` datetime DEFAULT NULL,
  `setDate` datetime DEFAULT NULL,
  `publishDate` datetime DEFAULT NULL,
  `endDate` datetime DEFAULT NULL,
  `isValid` tinyint(1) NOT NULL,
  `users_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_queries_users` (`users_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `queries_assoc_items`
--

CREATE TABLE IF NOT EXISTS `queries_assoc_items` (
  `queries_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  PRIMARY KEY (`queries_id`,`items_id`),
  KEY `fk_queries_assoc_items_queries` (`queries_id`),
  KEY `fk_queries_assoc_items_items` (`items_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `addDate` datetime DEFAULT NULL,
  `setDate` datetime DEFAULT NULL,
  `publishDate` datetime DEFAULT NULL,
  `endDate` datetime DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sessions_users` (`users_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `sessions_assoc_queries`
--

CREATE TABLE IF NOT EXISTS `sessions_assoc_queries` (
  `sessions_id` int(11) NOT NULL,
  `queries_id` int(11) NOT NULL,
  PRIMARY KEY (`sessions_id`,`queries_id`),
  KEY `fk_sessions_assoc_queries_sessions` (`sessions_id`),
  KEY `fk_sessions_assoc_queries_queries` (`queries_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sessions_id` int(11) NOT NULL,
  `firstName` varchar(255) DEFAULT NULL,
  `lastName` varchar(255) DEFAULT NULL,
  `pseudo` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `addDate` datetime DEFAULT NULL,
  `setDate` datetime DEFAULT NULL,
  `isBan` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
