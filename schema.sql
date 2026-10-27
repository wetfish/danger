--
-- Table structure for table `Posts`
--
-- Only run when a database is first created, so changes here never reach
-- a database that already exists
--
-- utf8mb4, so posts can hold any character, such as emoji
-- Author holds the poster's address, up to 45 characters for IPv6

CREATE TABLE IF NOT EXISTS `Posts` (
  `Hash` varchar(32) NOT NULL,
  `Author` varchar(45) NOT NULL,
  `Content` longtext NOT NULL,
  `Time` int(10) NOT NULL,
  PRIMARY KEY  (`Hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
