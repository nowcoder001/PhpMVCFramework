<?php

defined('TBL_PREFIX') or define('TBL_PREFIX',''); // 数据表前缀

#管理用户表
defined('USERS') or define('USERS', 'users');
define('USER_FIELD', 'uid, username, password, cid, lasttime, lastip, salt, status, groupid, remember');
#用户分组管理表
defined('USERS_GROUP') or define('USERS_GROUP', 'users_group');
#系统模块表
defined('SYSTEM_MODULE') or define('SYSTEM_MODULE', 'system_module');
define('SYSTEM_MODULE_FIELD', 'mod_id as id, mod_name as text, mod_parent_id , mod_alias, mod_url, mod_enable, mod_display, icon_cls');
#游戏列表
defined('GAME_LIST') or define('GAME_LIST', 'game_list');
#平台 列表
defined('PLATFORM_LIST') or define('PLATFORM_LIST', 'platform_list');
#SDK 列表
defined('SDK_LIST') or define('SDK_LIST', 'sdk_list');
#SDK渠道关系 列表
defined('SDK_CHANNEL_LIST') or define('SDK_CHANNEL_LIST', 'sdk_channel_list');
#游戏服列表
defined('GAME_SERVERS') or define('GAME_SERVERS', 'game_servers');
#XML配置道具资源表
defined('XML_RESOURCE_ITEM') or define('XML_RESOURCE_ITEM', 'xml_resource_item');
#货币汇率资源表
defined('RESOURCE_EXCHANGE_RATE') or define('RESOURCE_EXCHANGE_RATE', 'resource_exchange_rate');
#服白名单表
defined('SERVER_WHITE') or define('SERVER_WHITE', 'server_white');
#礼包模板表
defined('GIFT_BAG_TEMPLATE') or define('GIFT_BAG_TEMPLATE', 'gift_bag_template');
#礼包投放记录表
defined('GIFT_BAG_PUT_RECORD') or define('GIFT_BAG_PUT_RECORD', 'gift_bag_put_record');
#礼包投放详情记录表
defined('GIFT_BAG_PUT_RECORD_DETAIL') or define('GIFT_BAG_PUT_RECORD_DETAIL', 'gift_bag_put_record_detail');
#资源申请审批管理记录表
defined('RESOURCE') or define('RESOURCE', 'resource');
#邮件记录表
defined('EMAIL_LIST') or define('EMAIL_LIST', 'email_list');
#禁言封号记录表
defined('BAN_LIST') or define('BAN_LIST', 'ban_list');
#GM邮件记录表
defined('GM_EMAIL') or define('GM_EMAIL', 'gm_email');
#XML资源道具表
defined('XML_RESOURCE_ITEM') or define('XML_RESOURCE_ITEM', 'xml_resource_item');
#系统白名单表
defined('SYSTEM_WHITE') or define('SYSTEM_WHITE', 'system_white');
#活动礼包表
defined('ACTIVITY_GIFT_BAG') or define('ACTIVITY_GIFT_BAG', 'activity_gift_bag');
#客户端日志表
defined('CLIENT_LOG') or define('CLIENT_LOG', 'client_log');


#游戏服 -- CD_KEY表
defined('CD_KEY') or define('CD_KEY', 'cd_key');
#SDK 服 -- SDK 配置表
defined('SDK_CONFIG') or define('SDK_CONFIG', 'sdk_config');
#SDK 服 -- SDK 公告表
defined('SDK_NOTICE') or define('SDK_NOTICE', 'sdk_notice');
#SDK 服 -- 账号白名单表
defined('ACCOUNT_WHITE') or define('ACCOUNT_WHITE', 'account_white');

?>
