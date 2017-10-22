<?php
namespace travelsoft;

\Bitrix\Main\Loader::includeModule("highloadblock");

/**
 * Class History
 * Класс для работы с хранилищем истории
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class History {
    
    /**
     * @var array
     */
    protected $_objects = null;
    
    /**
     * @var array
     */
    protected $_actions = null;
    
    /**
     * @var int
     */
    protected $_count = null;
    
    protected static $_instance = null;
    
    public static function getInstance() {
        
        if (self::$_instance === null)
            self::$_instance = new self();
        
        return self::$_instance;
        
    }
    
    private function __construct() {
        
        $this->_objects = @include __DIR__ . '/../history_objects.php';
        $this->_actions = @include __DIR__ . '/../history_actions.php';
        
    }
    
    private function __clone() {}
    
    /**
     * @param array $parameters
     */
    protected function forSave (array $parameters) {
       if (!in_array($parameters["UF_OBJECT"], $this->_objects)) {
           throw new \Exception("Для сохранения записи в историю следует указать верный тип объекта истории");
       }
       
       if (!in_array($parameters["UF_ACTION"], $this->_actions)) {
           throw new \Exception("Для сохранения записи в историю следует указать верный тип события истории");
       }
       
       $parameters["UF_USER_ID"] = $GLOBALS["USER"]->GetID();
       $parameters["UF_IP"] = $_SERVER["REMOTE_ADDR"];
       $parameters["UF_DATE"] = time();
       
       return $parameters;
       
    }
    
    /**
     * Сохраняет событие в хранилище истории
     * @param array $parameters
     * @return boolean|int
     */ 
    public function save (array $parameters) {
        $dataClass = getHLDataClass(\Bitrix\Main\Config\Option::get("travelsoft.history", "history_highloadblock"));
        $arFields = $this->forSave($parameters);
        return $dataClass::add($arFields);
    }
    
    /**
     * Очищает хранилище истории
     * @param array $parameters
     */
    public function clear (array $parameters = null) {
        $dataClass = getHLDataClass(\Bitrix\Main\Config\Option::get("travelsoft.history", "history_highloadblock"));
        $arResult = $this->get($parameters);
        foreach ($arResult as $arFields) {
            $dataClass::delete($arFields["ID"]);
        }
    }
    
    /**
     * Получает записи из хранилища истории
     * @param array $parameters
     * @param boolean $likeArray
     * @return \Bitrix\Main\DB\Result|array|null
     */
    public function get (array $parameters = null, bool $likeArray = true) {
        $dataClass = getHLDataClass(\Bitrix\Main\Config\Option::get("travelsoft.history", "history_highloadblock"));
        if (!$parameters["count_total"]) {
            $parameters["count_total"] = true;
        }
        $dbList= $dataClass::getList($parameters);
        $this->_count = $dbList->getCount();
        
        if ($likeArray) {
            return $dbList->fetchAll();
        } else {
            return $dbList;
        }
    }
    
    /**
     * Возвращает количество элементов истории, полученных при запросе
     * @return int
     */
    public function getCount () {
        return $this->_count;
    }
    
    /**
     * Возвращает доступные объекты истории
     * @return array
     */
    public function getObjects () {
        return $this->_objects;
    }
    
    /**
     * Возвращает доступные действия истории
     * @return array
     */
    public function getActions () {
        return $this->_actions;
    }
    
    /**
     * Возвращает набор функций по объектам истории
     * @return array
     */
    public function getObjectsFns () {
       return $this->_objectsFns; 
    }
    
}
