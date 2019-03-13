/*
Navicat MySQL Data Transfer

Source Server         : mvc.blog.com
Source Server Version : 50557
Source Host           : 192.168.33.10:3306
Source Database       : mvc_vag_com

Target Server Type    : MYSQL
Target Server Version : 50557
File Encoding         : 65001

Date: 2019-03-13 21:59:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `digest` varchar(100) NOT NULL,
  `sendtime` int(11) NOT NULL,
  `click` smallint(6) NOT NULL,
  `thumb` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `category_cid` int(11) NOT NULL,
  PRIMARY KEY (`aid`),
  KEY `category_cid` (`category_cid`),
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`category_cid`) REFERENCES `category` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of article
-- ----------------------------

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `cname` char(25) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of category
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` char(25) NOT NULL,
  `password` int(32) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'aa', '22');
