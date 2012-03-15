-- phpMyAdmin SQL Dump
-- version 2.11.7.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Ven 24 Avril 2009 à 12:29
-- Version du serveur: 5.0.41
-- Version de PHP: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `TourATour`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL auto_increment,
  `content` text,
  `addDate` datetime default NULL,
  `setDate` datetime default NULL,
  `isValid` tinyint(1) default NULL,
  `items_id` int(11) default NULL,
  `users_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_comments_items` (`items_id`),
  KEY `fk_comments_users` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `datas_adress`
--

CREATE TABLE `datas_adress` (
  `id` int(11) NOT NULL auto_increment,
  `adress` varchar(255) default NULL,
  `zipCode` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `country` varchar(255) default NULL,
  `addDate` datetime default NULL,
  `setDate` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `datas_assoc`
--

CREATE TABLE `datas_assoc` (
  `datas_id` int(11) NOT NULL,
  `dataType` varchar(45) default NULL,
  `assoc_id` int(11) NOT NULL,
  `assocType` varchar(45) default NULL,
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

CREATE TABLE `datas_carto` (
  `id` int(11) NOT NULL auto_increment,
  `x` float default NULL,
  `y` float default NULL,
  `addDate` datetime default NULL,
  `setDate` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `datas_vote`
--

CREATE TABLE `datas_vote` (
  `id` int(11) NOT NULL auto_increment,
  `rate` int(11) default NULL,
  `addDate` datetime default NULL,
  `setDate` datetime default NULL,
  `users_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_datas_vote_users` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `description` text,
  `addDate` datetime default NULL,
  `setDate` datetime default NULL,
  `isValid` tinyint(1) default NULL,
  `users_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_items_users` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `medias_assoc`
--

CREATE TABLE `medias_assoc` (
  `medias_id` int(11) NOT NULL default '0',
  `mediaType` varchar(45) default NULL,
  `assoc_id` int(11) NOT NULL,
  `assocType` varchar(45) default NULL,
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

CREATE TABLE `medias_picture` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `description` text,
  `url` varchar(255) default NULL,
  `width` float default NULL,
  `height` float default NULL,
  `preview` varchar(255) default NULL,
  `addDate` datetime default NULL,
  `setDate` datetime default NULL,
  `isValid` tinyint(1) default NULL,
  `users_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_medias_picture_users` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `medias_sound`
--

CREATE TABLE `medias_sound` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `description` text,
  `url` varchar(255) default NULL,
  `totalTime` int(11) default NULL,
  `preview` varchar(255) default NULL,
  `addDate` datetime default NULL,
  `setDate` datetime default NULL,
  `isValid` tinyint(1) default NULL,
  `users_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_medias_sound_users` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `medias_text`
--

CREATE TABLE `medias_text` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `description` text,
  `content` text,
  `preview` varchar(255) default NULL,
  `addDate` datetime default NULL,
  `setDate` datetime default NULL,
  `isValid` tinyint(1) default NULL,
  `users_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_medias_text_users` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `medias_video`
--

CREATE TABLE `medias_video` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `description` text,
  `url` varchar(255) default NULL,
  `width` float default NULL,
  `height` float default NULL,
  `totalTime` int(11) default NULL,
  `preview` varchar(255) default NULL,
  `addDate` datetime default NULL,
  `setDate` datetime default NULL,
  `isValid` tinyint(1) default NULL,
  `users_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_medias_video_users` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `metas`
--

CREATE TABLE `metas` (
  `id` int(11) NOT NULL auto_increment,
  `content` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `metas_assoc`
--

CREATE TABLE `metas_assoc` (
  `metas_id` int(11) NOT NULL,
  `assoc_id` int(11) NOT NULL,
  `assocType` varchar(45) default NULL,
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

CREATE TABLE `queries` (
  `id` int(11) NOT NULL auto_increment,
  `content` varchar(255) default NULL,
  `description` text,
  `addDate` datetime default NULL,
  `setDate` datetime default NULL,
  `publishDate` datetime default NULL,
  `endDate` datetime default NULL,
  `isValid` tinyint(1) NOT NULL,
  `users_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_queries_users` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `queries_assoc_items`
--

CREATE TABLE `queries_assoc_items` (
  `queries_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  PRIMARY KEY  (`queries_id`,`items_id`),
  KEY `fk_queries_assoc_items_queries` (`queries_id`),
  KEY `fk_queries_assoc_items_items` (`items_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `description` text,
  `addDate` datetime default NULL,
  `setDate` datetime default NULL,
  `publishDate` datetime default NULL,
  `endDate` datetime default NULL,
  `users_id` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fk_sessions_users` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `sessions_assoc_queries`
--

CREATE TABLE `sessions_assoc_queries` (
  `sessions_id` int(11) NOT NULL,
  `queries_id` int(11) NOT NULL,
  PRIMARY KEY  (`sessions_id`,`queries_id`),
  KEY `fk_sessions_assoc_queries_sessions` (`sessions_id`),
  KEY `fk_sessions_assoc_queries_queries` (`queries_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `firstName` varchar(255) default NULL,
  `lastName` varchar(255) default NULL,
  `pseudo` varchar(255) default NULL,
  `password` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `role` varchar(255) default NULL,
  `addDate` datetime default NULL,
  `setDate` datetime default NULL,
  `isBan` tinyint(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
