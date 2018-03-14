<?php
/**
 *  Yogurt : MVC Development Framework with PHP<http://www.yogurtframework.com/>
 *
 * @author          rick <158672319@qq.com>
 * @copyright		Copyright (c)2009-2013
 * @link			http://www.yogurtframework.com
 * @license         http://www.yogurtframework.com/license/
 */
/**
 * base Model class yogurt framework.
 * @filesource		yogurt/core/model/Model.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */

abstract class Model extends Object {
    private static $daoType = Config::DB_DEFAULT_DAO_TYPE;
    private static $gameDao = Config::DB_DEFAULT_GAME_DAO_TYPE;
    private static $mysqli = Config::DB_MYSQLI_;
    private $moduleName = null;

    /**
     * construts of BaseBusinessService
       p	 */
    public function Model($moduleName = null) {
    	parent::Object();
    	$this->moduleName = $moduleName;
    }

    /**
     * get module name
     * @return string
     */
    public function getModuleName() {
    	return $this->moduleName;
    }

    /**
     * set module name
     */
    public function setModuleName($moduleName) {
    	$this->moduleName = $moduleName;
    }

    /**
     * get dao from business service
     * @param string $daoType Dao type name
     * @return object
     */
    public static function getDao($schema = "main",$daoType = null) {
        switch($schema) {
            case "game":
                return self::getGameDao(R::gid(), R::sdkid(), R::serverid());
            case 'analysis':
            case "main":
            case "slave":
            default:
                $dao = Y::factory(null != $daoType?$daoType: self::$daoType,'Dao');
                $dao->connect($schema);
                return $dao;
        }
    }

    /**
     * 连接对应的游戏服数据库
     */
    public static function getGameDao($gid, $sdkid, $serverId) {
    	$dao = Y::factory(self::$gameDao, 'Dao');
    	$dao->connectGame($gid, $sdkid, $serverId);
    	return $dao;
    }
}
?>
