<?php

/**
 * Класс TravelsoftPageCounter
 * Класс компонента подсчёта просмотров страницы
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class TravelsoftHistoryPageCounter extends CBitrixComponent {
    
    protected $robots = array(
        "google",
        "yandex",
        "facebook",
        "butterfly",
        "Sitemaps Generator",
        "UnwindFetchor",
        "JS-Kit URL Resolver",
        "MetaURI",
        "Crowsnest",
        "TweetmemeBot",
        "Twitterbot",
        "StackRambler",
        "MRSPUTNIK",
        "Mail.RU",
        "MailRuConnect",
        "Slurp",
        "WebCrawler",
        "ZyBorg",
        "scooter",
        "Aport",
        "lycos",
        "WebAlta",
        "yahoo",
        "msnbot",
        "ia_archiver",
        "FAST"
    );
    
    protected function _checkRights () {
        
        $this->arParams["USER_GROUPS"] = array_filter($this->arParams["USER_GROUPS"], function ($val) { return $val > 0; });
        if (!empty($this->arParams["USER_GROUPS"])) {
            $arGroups = $GLOBALS["USER"]->GetUserGroupArray();
            foreach ($this->arParams["USER_GROUPS"] as $groupId) {
                if (in_array($groupId, $arGroups)) {
                    return false;
                }
            }
        }
        return true;
    }
    
    protected function isRobot () {
        foreach ($this->robots as $robot) {
            if(stristr($_SERVER['HTTP_USER_AGENT'], $robot)) {
                 return true;
            }
        }
        return false;
    }
    
    public function executeComponent() {
        
        try {
            
            if (!Bitrix\Main\Loader::includeModule("travelsoft.history")) {
                throw new Exception("history page counter: Модуль travelsoft.history не найден");
            }
            
            $this->arParams["ID"] = intVal($this->arParams["ID"]);
            if ($this->arParams["ID"] <= 0) {
                throw new Exception("history page counter: Укажите ID страницы (ID > 0)");
            }
            
            if ($this->_checkRights() && !$this->isRobot() && Bitrix\Main\Loader::includeModule("travelsoft.history")) {
                travelsoft\History::getInstance()->save(array(
                    "UF_OBJECT" => "PAGE",
                    "UF_ACTION" => "VIEW_PAGE",
                    "UF_USER_ID" => $GLOBALS["USER"]->GetID(),
                    "UF_ELEMENT_ID" => $this->arParams["ID"],
                    "UF_DETAIL_INFO" => \travelsoft\ats($_SERVER)
                ));
            }
            
        } catch (\Exception $e) {
            ShowError($e->getMessage());
        }
    
    }
        
}
