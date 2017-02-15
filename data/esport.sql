/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50620
Source Host           : localhost:3306
Source Database       : esport

Target Server Type    : MYSQL
Target Server Version : 50620
File Encoding         : 65001

Date: 2017-02-14 15:46:42
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for category_video
-- ----------------------------
DROP TABLE IF EXISTS `category_video`;
CREATE TABLE `category_video` (
  `video_id` int(11) NOT NULL,
  `taxonomy_id` int(11) NOT NULL,
  PRIMARY KEY (`video_id`,`taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of category_video
-- ----------------------------

-- ----------------------------
-- Table structure for grant
-- ----------------------------
DROP TABLE IF EXISTS `grant`;
CREATE TABLE `grant` (
  `group` char(100) NOT NULL,
  `role` char(100) NOT NULL,
  `permission` char(100) NOT NULL,
  PRIMARY KEY (`group`,`role`,`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of grant
-- ----------------------------
INSERT INTO `grant` VALUES ('administrator', 'news-manager', 'create');
INSERT INTO `grant` VALUES ('administrator', 'user-manager', 'create');
INSERT INTO `grant` VALUES ('administrator', 'user-manager', 'delete');
INSERT INTO `grant` VALUES ('administrator', 'user-manager', 'edit');
INSERT INTO `grant` VALUES ('administrator', 'user-manager', 'read');

-- ----------------------------
-- Table structure for groups
-- ----------------------------
DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` char(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of groups
-- ----------------------------
INSERT INTO `groups` VALUES ('1', 'Quản trị viên', 'administrator');
INSERT INTO `groups` VALUES ('2', 'Quản lý', 'moderator');
INSERT INTO `groups` VALUES ('3', 'Thành viên', 'member');

-- ----------------------------
-- Table structure for homepage
-- ----------------------------
DROP TABLE IF EXISTS `homepage`;
CREATE TABLE `homepage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `type` char(20) DEFAULT NULL,
  `label_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `refer_id` int(11) DEFAULT NULL,
  `position` tinyint(3) DEFAULT NULL,
  `limit` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of homepage
-- ----------------------------
INSERT INTO `homepage` VALUES ('19', 'Cuif bap group', 'team_video', 'overview', '1', '0', '3', '2', null);
INSERT INTO `homepage` VALUES ('20', 'Giải league 1', 'league_video', 'overview', '3', '0', '3', '3', null);
INSERT INTO `homepage` VALUES ('22', 'Những video hot mới nhất', 'recent_video', 'overview', '1', '0', null, '1', null);

-- ----------------------------
-- Table structure for league
-- ----------------------------
DROP TABLE IF EXISTS `league`;
CREATE TABLE `league` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` char(100) DEFAULT NULL,
  `introduction` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of league
-- ----------------------------
INSERT INTO `league` VALUES ('1', 'Hạng 1', 'hang-1', 'introduction');
INSERT INTO `league` VALUES ('2', 'Hạng 2', 'hang-2', null);
INSERT INTO `league` VALUES ('3', 'Champion league', 'champion-league.Y2hhb', 'Champion league');

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` char(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES ('1', 'Create', 'create');
INSERT INTO `permissions` VALUES ('2', 'Read', 'read');
INSERT INTO `permissions` VALUES ('3', 'Edit', 'edit');
INSERT INTO `permissions` VALUES ('4', 'Delete', 'delete');

-- ----------------------------
-- Table structure for player
-- ----------------------------
DROP TABLE IF EXISTS `player`;
CREATE TABLE `player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` char(120) DEFAULT NULL,
  `character` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `biography` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `position_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `thumbnail` varchar(500) DEFAULT NULL,
  `tag` varchar(1000) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of player
-- ----------------------------
INSERT INTO `player` VALUES ('1', 'Ca sĩ trân tâm', 'ca-si-tran-tam.Y2Etc', 'Hue.Vir', 'Feed all lane', '6', '2', '54c2f1a1eb6f12d681a5c7078421a5500cee02ad_1486629053.jpg', null, '#tran-tam #mid-lane #feeder', '2017-02-09 09:48:09', '2017-02-09 09:48:09');
INSERT INTO `player` VALUES ('3', 'Faker', 'faker.ZmFrZ', 'Hide in bush', 'đây là faker hàn cuốc', '4', '3', '1b4605b0e20ceccf91aa278d10e81fad64e24e27_1486646393.jpg', null, '#faker #korea #han-quoc', '2017-02-10 04:32:39', '2017-02-10 04:32:39');

-- ----------------------------
-- Table structure for refer_video
-- ----------------------------
DROP TABLE IF EXISTS `refer_video`;
CREATE TABLE `refer_video` (
  `ref_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `type` char(20) NOT NULL,
  PRIMARY KEY (`ref_id`,`video_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of refer_video
-- ----------------------------
INSERT INTO `refer_video` VALUES ('1', '2', 'player');
INSERT INTO `refer_video` VALUES ('1', '4', 'player');
INSERT INTO `refer_video` VALUES ('2', '4', 'team');
INSERT INTO `refer_video` VALUES ('3', '3', 'player');
INSERT INTO `refer_video` VALUES ('3', '3', 'team');
INSERT INTO `refer_video` VALUES ('3', '4', 'player');
INSERT INTO `refer_video` VALUES ('3', '4', 'team');

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` char(200) DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES ('1', 'Quản lý tài khoản', 'user-manager', 'Quản lý tài khoản người dùng: tạo,xem, sửa, xóa');
INSERT INTO `roles` VALUES ('2', 'Quản lý bài viết', 'news-manager', null);

-- ----------------------------
-- Table structure for taxonomy
-- ----------------------------
DROP TABLE IF EXISTS `taxonomy`;
CREATE TABLE `taxonomy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` char(80) DEFAULT NULL,
  `type` char(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of taxonomy
-- ----------------------------
INSERT INTO `taxonomy` VALUES ('1', 'Siêu hot', 'sieu-hot.ancd', 'label');
INSERT INTO `taxonomy` VALUES ('3', 'điêm sáng', 'diem-sang.ZGllb', 'label');
INSERT INTO `taxonomy` VALUES ('4', 'Đường giữa', 'mid-lane', 'position');
INSERT INTO `taxonomy` VALUES ('5', 'Đường trên', 'top-lane', 'position');
INSERT INTO `taxonomy` VALUES ('6', 'Đường dưới', 'bottom-lane', 'position');
INSERT INTO `taxonomy` VALUES ('7', 'Đường hỗ trợ', 'support-lane', 'position');
INSERT INTO `taxonomy` VALUES ('8', 'Đường rừng', 'jungle-lane', 'position');

-- ----------------------------
-- Table structure for team
-- ----------------------------
DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` char(200) DEFAULT NULL,
  `introduction` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `league_id` int(11) DEFAULT NULL,
  `tag` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of team
-- ----------------------------
INSERT INTO `team` VALUES ('2', 'ĐỘi tuyển Siêu sao việt nam', '.ZG9pL', '<p>ĐỘi tuyển Si&ecirc;u sao việt nam</p>', '1', '2', '#sieu-sao-viet-nam #hang2-viet-nam');
INSERT INTO `team` VALUES ('3', 'đổi siêu sao 2 HQ', '.ZG9pL', '<p>đổi si&ecirc;u sao 2 HQ</p>', '2', '2', '#sieu-sao-2-han-xeng');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` char(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar` varchar(300) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `salt` varchar(25) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `group` char(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('3', 'son.le', 'az', 'asaszzz', '3b15be84aff20b322a93c0b9aaa62e25ad33b4b4.jpg', 'nero1@gmail.com', 'X2vJz#PEeUdUP$5rV6Ww', '$1$aN1.bq4.$pYWJla4mpGA7/RhCvzJIy.', '1', 'administrator', '2017-02-06 09:57:49', '2017-02-06 09:57:49');
INSERT INTO `users` VALUES ('4', 'admin', 'ten', 'ho', 'f5f8ad26819a471318d24631fa5055036712a87e_1486615188.jpg', 'nerocasten@gmail.com', '8abiwC9Z3okaMz6$V7jl', '$1$kX3.dt0.$g/.weyYppYAXFcRELBqgH0', '1', null, '2017-02-09 05:39:48', '2017-02-09 05:39:48');

-- ----------------------------
-- Table structure for video
-- ----------------------------
DROP TABLE IF EXISTS `video`;
CREATE TABLE `video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` char(255) DEFAULT NULL,
  `introduction` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `label_id` int(11) DEFAULT NULL,
  `league_id` int(11) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `video_url` varchar(1000) DEFAULT NULL,
  `tag` varchar(500) DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `publish_date` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of video
-- ----------------------------
INSERT INTO `video` VALUES ('2', 'video 2', 'video-2.dmlkZ', 'Mô tả về video 2 abcs', '<p>zvideo2</p>', '1', '1', '30420d1a9afb2bcb60335812569af4435a59ce17_1486543913.jpg', 'urlvideo2', '#video2 #video234 #faker', '1', '2017-02-13 09:33:16', '2017-02-08 09:51:53', '2017-02-14 04:33:38');
INSERT INTO `video` VALUES ('3', 'video 3', 'video-3.dmlkZ', '', '', '1', null, 'df7be9dc4f467187783aca68c7ce98e4df2172d0_1486650027.jpg', 'videourl', '', '0', '2017-02-12 09:33:19', '2017-02-09 15:20:27', '2017-02-09 15:20:27');
INSERT INTO `video` VALUES ('4', 'video 3', 'video-3.jI1OTI=', '', '', '3', '3', 'd997e1c37edc05ad87d03603e32ad495ee2cfce1_1486650222.jpg', 'videourl', '', '0', '2017-02-13 09:33:23', '2017-02-09 15:23:42', '2017-02-10 05:08:17');

-- ----------------------------
-- Table structure for zone
-- ----------------------------
DROP TABLE IF EXISTS `zone`;
CREATE TABLE `zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` char(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zone
-- ----------------------------
INSERT INTO `zone` VALUES ('1', 'Việt nam', 'viet-namz.dmlld');
INSERT INTO `zone` VALUES ('2', 'Hàn Quốc', 'vn-2');
INSERT INTO `zone` VALUES ('3', 'Khu vực Mỹ', 'khu-vuc-my.a2h1L');
