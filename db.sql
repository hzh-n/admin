# 系统配置
DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
	`id`         INT(10) NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME NOT NULL DEFAULT NOW(),
	`updated_at` DATETIME NOT NULL,
	`status`     TINYINT(1) NOT NULL DEFAULT 1,

	`lang`               TINYINT(2)    DEFAULT 0            COMMENT '语言',
	`m`                  VARCHAR(20)   DEFAULT ''           COMMENT '模块',
	`g`                  VARCHAR(20)   DEFAULT ''           COMMENT '组别',
	`k`                  VARCHAR(50)   DEFAULT ''           COMMENT '名称',
	`v`                  VARCHAR(200)  DEFAULT ''           COMMENT '内容',
	`is_load`            TINYINT(1)    DEFAULT 0            COMMENT '自动加载',

	PRIMARY KEY(`id`)
) ENGINE=InnoDb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '系统配置';


# 管理员
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
	`id`         INT(10) NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME NOT NULL DEFAULT NOW(),
	`updated_at` DATETIME NOT NULL,
	`status`     TINYINT(1) NOT NULL DEFAULT 1,

	`role_id`            INT(10)       DEFAULT 0            COMMENT '@admin_role',
	`username`           VARCHAR(30)   DEFAULT ''           COMMENT '用户名',
	`pwd`                VARCHAR(50)   DEFAULT ''           COMMENT '密码',
	`mobile`             VARCHAR(30)   DEFAULT ''           COMMENT '手机',

	PRIMARY KEY(`id`)
) ENGINE=InnoDb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '管理员';


# 操作日志
DROP TABLE IF EXISTS `admin_log`;
CREATE TABLE `admin_log` (
	`id`         INT(10) NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME NOT NULL DEFAULT NOW(),
	`updated_at` DATETIME NOT NULL,
	`status`     TINYINT(1) NOT NULL DEFAULT 1,

	`admin_id`           INT(10)       DEFAULT 0            COMMENT '@admin@',
	`title`              VARCHAR(200)  DEFAULT ''           COMMENT '标题',
	`url`                VARCHAR(200)  DEFAULT ''           COMMENT '链接',
	`content`            TEXT                               COMMENT '内容',
	`ip`                 VARCHAR(20)   DEFAULT ''           COMMENT 'IP',
	`useragent`          VARCHAR(200)  DEFAULT ''           COMMENT '设备',

	PRIMARY KEY(`id`)
) ENGINE=InnoDb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '操作日志';


# 菜单
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
	`id`         INT(10) NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME NOT NULL DEFAULT NOW(),
	`updated_at` DATETIME NOT NULL,
	`status`     TINYINT(1) NOT NULL DEFAULT 1,

	`parent_id`          INT(10)       DEFAULT 0            COMMENT '@menu@',
	`sort`               INT(10)       DEFAULT 0            COMMENT '排序',
	`type`               TINYINT(1)    DEFAULT 0            COMMENT '菜单类型',
	`title`              VARCHAR(50)   DEFAULT ''           COMMENT '标题',
	`url`                VARCHAR(100)  DEFAULT ''           COMMENT 'URL',
	`icon`               VARCHAR(50)   DEFAULT ''           COMMENT '图标',
	`memo`               VARCHAR(200)  DEFAULT ''           COMMENT '备注',

	PRIMARY KEY(`id`)
) ENGINE=InnoDb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '菜单';


# 角色表
DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role` (
	`id`         INT(10) NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME NOT NULL DEFAULT NOW(),
	`updated_at` DATETIME NOT NULL,
	`status`     TINYINT(1) NOT NULL DEFAULT 1,

	`name`               VARCHAR(50)   DEFAULT ''           COMMENT '角色',
	`mome`               VARCHAR(200)  DEFAULT ''           COMMENT '备注',
	`menu_ids`           VARCHAR(1000) DEFAULT ''           COMMENT '权限',
	`cate_ids`           VARCHAR(1000) DEFAULT ''           COMMENT '栏目内容权限',

	PRIMARY KEY(`id`)
) ENGINE=InnoDb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '角色表';


# 分类管理
DROP TABLE IF EXISTS `cate`;
CREATE TABLE `cate` (
	`id`         INT(10) NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME NOT NULL DEFAULT NOW(),
	`updated_at` DATETIME NOT NULL,
	`status`     TINYINT(1) NOT NULL DEFAULT 1,

	`parent_id`          INT(10)       DEFAULT 0            COMMENT '@cate@',
	`is_nav`             TINYINT(1)    DEFAULT 0            COMMENT '是否导航',
	`is_index`           TINYINT(1)    DEFAULT 0            COMMENT '是否首页',
	`sort`               INT(10)       DEFAULT 0            COMMENT '排序',
	`type`               TINYINT(1)    DEFAULT 0            COMMENT '类型',
	`title`              VARCHAR(50)   DEFAULT ''           COMMENT '标题',
	`sub_title`          VARCHAR(100)  DEFAULT ''           COMMENT '副标题',
	`img_cover`          VARCHAR(200)  DEFAULT ''           COMMENT '封面图',
	`img_bg`             VARCHAR(200)  DEFAULT ''           COMMENT '背景图',
	`alias`              VARCHAR(50)   DEFAULT ''           COMMENT '别名',
	`point`              VARCHAR(50)   DEFAULT ''           COMMENT '锚点',
	`description`        VARCHAR(255)  DEFAULT ''           COMMENT '摘要',
	`page_content`       TEXT                               COMMENT '单页面内容',
	`link_url`           VARCHAR(255)  DEFAULT ''           COMMENT '链接地址',
	`tpl_list`           VARCHAR(50)   DEFAULT ''           COMMENT '列表页模板',
	`tpl_show`           VARCHAR(50)   DEFAULT ''           COMMENT '详情页模板',
	`tpl_page`           VARCHAR(50)   DEFAULT ''           COMMENT '单页面模板',

	PRIMARY KEY(`id`)
) ENGINE=InnoDb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '分类管理';


# 民宿管理
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
	`id`         INT(10) NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME NOT NULL DEFAULT NOW(),
	`updated_at` DATETIME NOT NULL,
	`status`     TINYINT(1) NOT NULL DEFAULT 1,

	`admin_id`           INT(10)       DEFAULT 0            COMMENT '@admin@',
	`cate_id`            INT(10)       DEFAULT 0            COMMENT '@cate@',
	`sort`               INT(10)       DEFAULT 0            COMMENT '排序',
	`type`               TINYINT(1)    DEFAULT 0            COMMENT '类型',
	`name`               VARCHAR(100)  DEFAULT ''           COMMENT '名称',
	`city`               VARCHAR(100)  DEFAULT ''           COMMENT '地市',
	`county`             VARCHAR(100)  DEFAULT ''           COMMENT '区县',
	`address`            VARCHAR(100)  DEFAULT ''           COMMENT '地址',
	`phone`              VARCHAR(100)  DEFAULT ''           COMMENT '电话',
	`summary`            VARCHAR(200)  DEFAULT ''           COMMENT '简介',
    `content`            TEXT COMMENT  '内容',
	`img_cover`          VARCHAR(200)  DEFAULT ''           COMMENT '封面图',
	`img_bg`             VARCHAR(200)  DEFAULT ''           COMMENT '背景图',
	`is_rec`             TINYINT(1)    DEFAULT 0            COMMENT '推荐',
	`add_time`           VARCHAR(20)   DEFAULT ''           COMMENT '发布时间',
	`is_del`             TINYINT(1)    DEFAULT 0            COMMENT '回收站',

	PRIMARY KEY(`id`)
) ENGINE=InnoDb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '民宿管理';


# 相册
DROP TABLE IF EXISTS `news_gallery`;
CREATE TABLE `news_gallery` (
	`id`         INT(10) NOT NULL AUTO_INCREMENT,
	`created_at` DATETIME NOT NULL DEFAULT NOW(),
	`updated_at` DATETIME NOT NULL,
	`status`     TINYINT(1) NOT NULL DEFAULT 1,

	`sort`               INT(10)       DEFAULT 0            COMMENT '排序',
	`news_id`            INT(10)       DEFAULT 0            COMMENT '@news@',
	`img_photo`          VARCHAR(500)  DEFAULT ''           COMMENT '图集',
	`title`              VARCHAR(100)  DEFAULT ''           COMMENT '标题',

	PRIMARY KEY(`id`)
) ENGINE=InnoDb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '相册';


