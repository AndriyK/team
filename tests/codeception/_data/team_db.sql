SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
--
-- Database: `team`
--

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE IF NOT EXISTS `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT 'training',
  `datetime` datetime NOT NULL,
  `location` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `team_id` (`team_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

INSERT INTO `team_test`.`games` (`id`, `team_id`, `title`, `datetime`, `location`) VALUES
(1, '26', 'training', NOW() + INTERVAL 1 HOUR, 'field1'),
(2, '26', 'evening game', NOW() + INTERVAL 4 HOUR, 'field2');

-- --------------------------------------------------------

--
-- Table structure for table `game_has_player`
--

CREATE TABLE IF NOT EXISTS `game_has_player` (
  `game_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `presence` tinyint(4) NOT NULL,
  PRIMARY KEY (`game_id`,`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE IF NOT EXISTS `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `token` varchar(250) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`id`, `email`, `password`, `token`, `name`, `created_at`) VALUES
(33, 'q@q.q', '$2y$13$5JKE8pm9kDRuOofk5ryZ3u0sdpookEGIWwBQv0LurKw9MGcileWDW', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJpYXQiOjE0NTExMTk4NzEsImV4cCI6MTQ1MTEyMzQ3MSwidWlkIjozMywibWFpbCI6InFAcS5xIn0.xJb1Kg7DOJlPurceODydeisIBENrOFcRbdnAp18NJEQ', 'q', '2015-12-22 14:58:44'),
(34, 'w@w.w', '$2y$13$E4MRMOYyBvEF3u2wvQXnTOOEEt8MfL85oQ0ZuNEUS.grmyy3BMLLu', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJhdWQiOiJodHRwOlwvXC9sb2NhbGhvc3QiLCJpYXQiOjE0NTExMTQ3NDcsImV4cCI6MTQ1MTExODM0NywidWlkIjozNCwibWFpbCI6IndAdy53In0.IagxrRbj0XO4e4lRx2_Gvc9dRK4ptgs4oFO2mbYcjyM', 'Super Player', '2015-12-24 08:33:18');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE IF NOT EXISTS `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sport` enum('football','basketball','voleyball') NOT NULL DEFAULT 'football',
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sport_name` (`sport`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

INSERT INTO `team_test`.`teams` (`id`, `sport`, `name`) VALUES
(26, 'football', 'FridayPlay'),
(27, 'football', 'MondayPlay');

-- --------------------------------------------------------

--
-- Table structure for table `team_has_player`
--

CREATE TABLE IF NOT EXISTS `team_has_player` (
  `team_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `is_capitan` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`team_id`,`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `team_test`.`team_has_player` (`team_id`, `player_id`, `is_capitan`) VALUES
('26', '33', '1'),
('26', '34', '1');

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_has_player`
--
ALTER TABLE `game_has_player`
  ADD CONSTRAINT `game_has_player_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `team_has_player`
--
ALTER TABLE `team_has_player`
  ADD CONSTRAINT `team_has_player_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;