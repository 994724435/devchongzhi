/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : devchongzhi

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2017-11-30 23:23:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for p_article
-- ----------------------------
DROP TABLE IF EXISTS `p_article`;
CREATE TABLE `p_article` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `type` int(11) DEFAULT '1' COMMENT '1首页 2公告 3值班团队 4分析专家 5公司简介',
  `cont` text,
  `addtime` varchar(128) DEFAULT NULL,
  `addymd` varchar(128) DEFAULT NULL,
  `admin` varchar(64) DEFAULT NULL,
  `num` int(11) DEFAULT '1',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_article
-- ----------------------------
INSERT INTO `p_article` VALUES ('1', '公司简介', '1', '1<img src=\"/devmuchang/Public/Admin/js/attached/image/20171121/20171121124432_79040.png\" alt=\"\" /><img src=\"/dev/devmuchang/Public/Admin/js/attached/image/20171125/20171125135922_56452.jpg\" alt=\"\" />', '2017-11-25 13:59:24', '2017-11-25', 'admin', '1');
INSERT INTO `p_article` VALUES ('6', '分析专家李云龙标题', '2', '1<img src=\"/dev/devmuchang/Public/Admin/js/attached/image/20171125/20171125135939_82358.jpg\" alt=\"\" />', '2017-11-25 13:59:40', '2017-11-25', 'admin', '1');
INSERT INTO `p_article` VALUES ('7', '公告', '2', '1', '2017-11-21 20:37:22', '2017-11-21', 'admin', '1');

-- ----------------------------
-- Table structure for p_config
-- ----------------------------
DROP TABLE IF EXISTS `p_config`;
CREATE TABLE `p_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `value` varchar(128) DEFAULT NULL,
  `complan` varchar(255) DEFAULT NULL COMMENT '注释说明',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_config
-- ----------------------------
INSERT INTO `p_config` VALUES ('1', '结束收益总额', null, '结束收益');
INSERT INTO `p_config` VALUES ('2', '每日动态收益', null, '动态收益');
INSERT INTO `p_config` VALUES ('3', '资金冻结', '2', '1');
INSERT INTO `p_config` VALUES ('4', '黑牦牛利率', null, '2');
INSERT INTO `p_config` VALUES ('5', '母牦牛利率', null, '3');
INSERT INTO `p_config` VALUES ('6', '幼崽牦牛基准', null, '4');
INSERT INTO `p_config` VALUES ('7', '黑牦牛基准', null, '5');
INSERT INTO `p_config` VALUES ('8', '母牦牛基准', null, '6');
INSERT INTO `p_config` VALUES ('9', '推荐奖 7代', null, '7');
INSERT INTO `p_config` VALUES ('10', '推荐奖 8代', null, '8');
INSERT INTO `p_config` VALUES ('11', '推荐奖 9代', null, '9');
INSERT INTO `p_config` VALUES ('12', '推荐奖 10代', null, '10');
INSERT INTO `p_config` VALUES ('13', '资金上限', null, '资金上限');
INSERT INTO `p_config` VALUES ('14', '回馈奖6代', null, null);
INSERT INTO `p_config` VALUES ('15', '最低提现金额', null, '最大提现金额');
INSERT INTO `p_config` VALUES ('16', '每日最大提现次数', null, '每日最大提现次数');
INSERT INTO `p_config` VALUES ('17', '公排价格', null, '公排价格');
INSERT INTO `p_config` VALUES ('18', '提现手续费', null, '积分提现手续费');
INSERT INTO `p_config` VALUES ('19', '最高体现金额', null, '最高体现金额');

-- ----------------------------
-- Table structure for p_incomelog
-- ----------------------------
DROP TABLE IF EXISTS `p_incomelog`;
CREATE TABLE `p_incomelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT '1' COMMENT '1分红收益2充值 3静态提现  4牛气奖  5 注册下级 6下单购买 7积分体现 8积分转账 9 回馈奖 10积分商城购买',
  `state` int(11) DEFAULT '1' COMMENT '1收入   2支出 3失败',
  `reson` varchar(255) DEFAULT NULL COMMENT '原因',
  `addymd` date DEFAULT NULL,
  `addtime` int(12) DEFAULT NULL,
  `orderid` varchar(100) DEFAULT '1' COMMENT '1 卖方 2 买方',
  `userid` int(11) DEFAULT NULL,
  `income` varchar(64) DEFAULT '0' COMMENT '金额',
  `cont` varchar(1000) NOT NULL COMMENT '后台备注',
  `username` varchar(100) DEFAULT NULL,
  `tel` varchar(100) DEFAULT NULL,
  `commitid` varchar(64) DEFAULT '1',
  `weixin` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=686 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_incomelog
-- ----------------------------
INSERT INTO `p_incomelog` VALUES ('669', '6', '2', '下单购买', '2017-11-30', '1512031727', '87', '1', '200', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('670', '6', '2', '下单购买', '2017-11-30', '1512032076', '88', '1', '100', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('668', '6', '2', '下单购买', '2017-11-30', '1512031063', '86', '1', '100', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('671', '10', '2', '下单购买', '2017-11-30', '1512032178', '89', '1', '100', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('672', '2', '1', '充值', '2017-11-30', '1512032223', '201711301657033082', '1', '100', 'http://chongzhi.dev.com/index.php/Home/User/recharge.html?', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('673', '7', '0', '余额提现', '2017-11-30', '1512032386', '1', '1', '100', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('674', '2', '0', '充值', '2017-11-30', '1512033941', '201711301725418707', '1', '100', 'http://chongzhi.dev.com/index.php/Home/User/recharge.html?', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('685', '1', '1', '分红收益', '2017-11-30', '1512047655', '0', '1', '2.00', '', null, null, '1', null);

-- ----------------------------
-- Table structure for p_login
-- ----------------------------
DROP TABLE IF EXISTS `p_login`;
CREATE TABLE `p_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `pwd` text CHARACTER SET utf8,
  `addymd` date DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  `ip` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of p_login
-- ----------------------------
INSERT INTO `p_login` VALUES ('1', 'admin', '123asd', '2017-09-16', '1505552484', null);
INSERT INTO `p_login` VALUES ('2', 'admin', '123asd', '2017-09-16', '1505552539', '127.0.0.1');

-- ----------------------------
-- Table structure for p_menber
-- ----------------------------
DROP TABLE IF EXISTS `p_menber`;
CREATE TABLE `p_menber` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `pwd` varchar(100) DEFAULT NULL,
  `tel` varchar(64) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `type` int(4) DEFAULT '1' COMMENT '1普通 2高级 3豪华 4至尊',
  `dongbag` varchar(50) DEFAULT '' COMMENT '商城积分',
  `jingbag` varchar(50) DEFAULT '0' COMMENT '智能余额',
  `djbag` varchar(50) DEFAULT '0' COMMENT '冻结钱包',
  `fuid` int(11) DEFAULT '0' COMMENT '注册上家',
  `fuids` varchar(1000) DEFAULT NULL COMMENT '上家',
  `two` int(11) DEFAULT '0' COMMENT '二级父类',
  `three` int(11) DEFAULT '0' COMMENT '三级父类',
  `four` int(11) DEFAULT '0' COMMENT '四级父类',
  `addtime` int(12) DEFAULT NULL,
  `addymd` date DEFAULT NULL,
  `pwd2` varchar(255) DEFAULT NULL,
  `chargebag` varchar(50) DEFAULT '0' COMMENT '个人钱包',
  `realname` varchar(100) DEFAULT NULL COMMENT '真实姓名',
  `zhifubao` varchar(100) DEFAULT NULL COMMENT '支付宝账号',
  `zhifubaoname` varchar(100) DEFAULT NULL COMMENT '支付宝姓名',
  `weixin` varchar(64) DEFAULT NULL,
  `bank` varchar(100) DEFAULT NULL COMMENT '银行卡号',
  `bankname` varchar(64) DEFAULT NULL COMMENT '银行卡姓名',
  `bankfrom` varchar(100) DEFAULT NULL COMMENT '银行卡开户行',
  `isdelete` int(1) DEFAULT '0' COMMENT '0 未经用 1禁用',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_menber
-- ----------------------------
INSERT INTO `p_menber` VALUES ('1', '100', '3', '100', null, '1', '800.00', '0.00', '0.00', '0', '1,', null, '0', '0', null, null, '6', '2008.00', 'fsda', '1121', '121', '12121', null, null, null, '0');
INSERT INTO `p_menber` VALUES ('2', '101', '1', '101', null, '1', '4.00', '0.00', '0.00', '1', '1,2,', null, '0', '0', '1502892880', '2017-08-16', '1', '716.00', null, null, null, null, null, null, null, '0');
INSERT INTO `p_menber` VALUES ('34', '102', '1', '102', null, '1', '0', '0.00', '0.00', '2', '1,2,34,', '1', '0', '0', null, null, '1', '0', null, null, null, null, null, null, null, '0');
INSERT INTO `p_menber` VALUES ('35', '103', '1', '103', null, '1', '4.00', '0.00', '0.00', '34', '1,2,34,35,', '2', '1', '0', null, null, '1', '16.00', null, null, null, null, null, null, null, '0');
INSERT INTO `p_menber` VALUES ('36', '104', '1', '104', null, '1', '0', '0.00', '0.00', '35', '1,2,34,35,36,', '34', '2', '1', null, null, '1', '0', null, null, null, null, null, null, null, '0');
INSERT INTO `p_menber` VALUES ('37', '105', '1', '105', null, '1', '0', '0.00', '0.00', '36', '1,2,34,35,36,37,', '35', '34', '2', null, null, '1', '0', null, null, null, null, null, null, null, '0');
INSERT INTO `p_menber` VALUES ('38', '18883287644', '1', '18883287644', null, '1', '0', '0.00', '0.00', '0', '38,', '0', '0', '0', '1511792471', '2017-11-27', '1', '0', null, null, null, null, null, null, null, '0');

-- ----------------------------
-- Table structure for p_message
-- ----------------------------
DROP TABLE IF EXISTS `p_message`;
CREATE TABLE `p_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `cont` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `tel` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `time` int(12) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `state` int(1) DEFAULT '1' COMMENT '1有效  2 无效',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of p_message
-- ----------------------------
INSERT INTO `p_message` VALUES ('11', 'af814b00d0a1a723cfd2773f998c85c3', '7056', '188832876441', null, '1502616589', '2017-08-13', '1');
INSERT INTO `p_message` VALUES ('12', '6d5975dfcd0b523497d7e09fcbb01003', '2876', '15538867970', null, '1502616778', '2017-08-13', '1');
INSERT INTO `p_message` VALUES ('13', '30cdaa5bc1b5618d7e824e7fbae56b57', '1936', '18883287644', null, '1511792183', '2017-11-27', '1');

-- ----------------------------
-- Table structure for p_orderlog
-- ----------------------------
DROP TABLE IF EXISTS `p_orderlog`;
CREATE TABLE `p_orderlog` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '用户id',
  `productid` int(11) NOT NULL,
  `productname` varchar(64) DEFAULT NULL,
  `productmoney` varchar(32) DEFAULT NULL COMMENT '产品带来的利润',
  `state` int(1) NOT NULL DEFAULT '0' COMMENT '0待支付 1收益中 2已完成',
  `orderid` varchar(128) NOT NULL COMMENT '订单id',
  `addtime` int(12) DEFAULT NULL,
  `num` int(5) DEFAULT NULL COMMENT '购买数量',
  `price` varchar(40) DEFAULT NULL COMMENT '购买单价',
  `totals` varchar(40) DEFAULT NULL,
  `addymd` date DEFAULT NULL,
  `type` int(2) DEFAULT '1' COMMENT '1买地  2 1000买幼崽 3 成年5000 4母牦牛10000  10买商城物品',
  `option` varchar(1000) DEFAULT NULL COMMENT '其他说明',
  PRIMARY KEY (`logid`)
) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_orderlog
-- ----------------------------
INSERT INTO `p_orderlog` VALUES ('88', '1', '2', '钱付贰号', null, '1', '1512032076', '1512032076', '1', '100', '100', '2017-11-30', '2', '2');
INSERT INTO `p_orderlog` VALUES ('86', '1', '2', '钱付贰号', '100', '1', '1512031063', '1512031063', '1', '100', '100', '2017-11-30', '10', '');
INSERT INTO `p_orderlog` VALUES ('87', '1', '3', '钱付叁号', '100', '1', '1512031726', '1512031726', '1', '200', '200', '2017-11-30', '1', '1');
INSERT INTO `p_orderlog` VALUES ('89', '1', '2', '钱付贰号', null, '1', '1512032178', '1512032178', '1', '100', '100', '2017-11-30', '2', '2');

-- ----------------------------
-- Table structure for p_product
-- ----------------------------
DROP TABLE IF EXISTS `p_product`;
CREATE TABLE `p_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '产品名',
  `cont` text COMMENT '产品描述',
  `type` int(1) DEFAULT '1' COMMENT '1空冲商城 2积分商城',
  `pic` varchar(255) DEFAULT NULL COMMENT '产品图片',
  `price` varchar(100) DEFAULT NULL COMMENT '售卖价格',
  `effectdays` varchar(30) DEFAULT NULL COMMENT '理财有效天数',
  `daycome` varchar(100) DEFAULT NULL COMMENT '理财每日收益',
  `daynum` int(11) DEFAULT NULL COMMENT '每日发放数量',
  `one` varchar(50) DEFAULT NULL COMMENT '一代每日返利',
  `two` varchar(50) DEFAULT NULL,
  `state` int(3) DEFAULT '1' COMMENT '1上架  2下架',
  `addtime` varchar(100) DEFAULT NULL,
  `salenum` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_product
-- ----------------------------
INSERT INTO `p_product` VALUES ('2', '手机', null, '1', '/register/Public/Uploads/2017-03-31/58ddce2af1148.png', '100', null, null, null, null, null, '1', '2017-11-30 22:14:38', '0');
INSERT INTO `p_product` VALUES ('3', '钱付叁号', null, '1', '/register/Public/Uploads/2017-03-31/58ddce371bfd2.png', '200', null, null, null, null, null, '1', '2017-11-30 22:14:16', '0');
INSERT INTO `p_product` VALUES ('8', '手表', null, '1', '/Public/Uploads/2017-11-30/5a2010be48b86.jpg', '100', null, null, null, null, null, '1', '2017-11-30 22:14:11', '0');

-- ----------------------------
-- Table structure for p_quan
-- ----------------------------
DROP TABLE IF EXISTS `p_quan`;
CREATE TABLE `p_quan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cont` varchar(100) DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  `addymd` date DEFAULT NULL,
  `price` decimal(10,0) DEFAULT NULL,
  `state` int(1) DEFAULT '1' COMMENT '1有效 2无效',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of p_quan
-- ----------------------------

-- ----------------------------
-- Table structure for p_rite
-- ----------------------------
DROP TABLE IF EXISTS `p_rite`;
CREATE TABLE `p_rite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cont` varchar(30) DEFAULT NULL COMMENT '利率',
  `date` varchar(30) DEFAULT NULL COMMENT '日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_rite
-- ----------------------------
INSERT INTO `p_rite` VALUES ('1', '0.01', '07-01');
INSERT INTO `p_rite` VALUES ('2', '0.02', '07-02');
INSERT INTO `p_rite` VALUES ('3', '0.03', '07-03');
INSERT INTO `p_rite` VALUES ('4', '0.02', '07-04');
INSERT INTO `p_rite` VALUES ('5', '0.02', '07-05');
INSERT INTO `p_rite` VALUES ('6', '0.03', '07-06');
INSERT INTO `p_rite` VALUES ('7', '0.02', '07-07');
INSERT INTO `p_rite` VALUES ('10', '0.04', '08-12');
INSERT INTO `p_rite` VALUES ('12', '0.3', '08-13');
INSERT INTO `p_rite` VALUES ('13', '0.8', '08-14');
INSERT INTO `p_rite` VALUES ('14', '0.09', '08-15');
INSERT INTO `p_rite` VALUES ('15', '0..08', '08-16');
INSERT INTO `p_rite` VALUES ('16', '0.3', '08-17');
INSERT INTO `p_rite` VALUES ('17', '30', '11-01');
INSERT INTO `p_rite` VALUES ('18', '20', '11-30');

-- ----------------------------
-- Table structure for p_user
-- ----------------------------
DROP TABLE IF EXISTS `p_user`;
CREATE TABLE `p_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT '登录名',
  `openid` varchar(255) DEFAULT NULL COMMENT '微信ID',
  `nickname` varchar(255) DEFAULT NULL COMMENT '微信昵称',
  `address` varchar(255) DEFAULT NULL COMMENT '微信地址',
  `userface` varchar(255) DEFAULT NULL COMMENT '维信头像',
  `addtime` varchar(255) DEFAULT NULL COMMENT '注册时间',
  `manager` int(2) DEFAULT '1' COMMENT '0 禁用账号 1管理员 2 超级管理员',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_user
-- ----------------------------
INSERT INTO `p_user` VALUES ('1', '123asd', 'admin', null, null, null, null, '2017-03-10 13:56:27', '2');
INSERT INTO `p_user` VALUES ('2', '123456', 'admin2', null, null, null, null, '2017-03-10 13:56:27', '2');
