# tiktok-to-instagram-stories

Migration :

```sql
-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  Dim 02 mai 2021 à 00:46
-- Version du serveur :  5.7.17
-- Version de PHP :  5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `channel-storage`
--

-- --------------------------------------------------------

--
-- Structure de la table `instagram_stories_channel`
--

CREATE TABLE `instagram_stories_channel` (
  `id` int(11) NOT NULL,
  `action_uploader_account_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `instagram_stories_channel_tiktok_account`
--

CREATE TABLE `instagram_stories_channel_tiktok_account` (
  `id` int(11) NOT NULL,
  `instagram_id` int(11) NOT NULL,
  `tiktok_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `instagram_story`
--

CREATE TABLE `instagram_story` (
  `id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `instagram_id` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `instagram_story_tiktok_video`
--

CREATE TABLE `instagram_story_tiktok_video` (
  `id` int(11) NOT NULL,
  `instagram_id` int(11) NOT NULL,
  `tiktok_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `instagram_stories_channel`
--
ALTER TABLE `instagram_stories_channel`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `instagram_stories_channel_tiktok_account`
--
ALTER TABLE `instagram_stories_channel_tiktok_account`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `instagram_story`
--
ALTER TABLE `instagram_story`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `instagram_story_tiktok_video`
--
ALTER TABLE `instagram_story_tiktok_video`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `instagram_stories_channel`
--
ALTER TABLE `instagram_stories_channel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `instagram_stories_channel_tiktok_account`
--
ALTER TABLE `instagram_stories_channel_tiktok_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `instagram_story`
--
ALTER TABLE `instagram_story`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `instagram_story_tiktok_video`
--
ALTER TABLE `instagram_story_tiktok_video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
 
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
```
