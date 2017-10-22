<?php

/**
 * Класс TravelsoftHistoryList
 * Класс компонента просмотра и фильтрации списка элементов истории
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class TravelsoftHistoryList extends CBitrixComponent {

    /**
     * @var array 
     */
    protected $_cacheUsers = null;
    
    protected function _prepareInputParameters () {
        
        $this->arParams["PAGE_ELEMENT_COUNT"] = $this->arParams["PAGE_ELEMENT_COUNT"] > 0 ? $this->arParams["PAGE_ELEMENT_COUNT"] : 10;
        $this->arParams["BOOTSTRAP"] = $this->arParams["BOOTSTRAP"] == "Y";
    }
    
    /**
     * Подключает необъодимые модули
     * @throws Exception
     */
    public function includeModules () {
        if (!\Bitrix\Main\Loader::includeModule("travelsoft.history")) {
            throw new Exception("history list: Модуль travelsoft.history не найден");
        }
    }
    
    public function executeComponent() {
        
        try {
            
            $this->includeModules();
            
            $this->_includeComponentProlog();
            
            $this->_prepareInputParameters();
            
            $arOrder = $this->_getOrder();
            
            $this->arResult["OBJECTS"] = \travelsoft\History::getInstance()->getObjects();
            $this->arResult["ACTIONS"] = \travelsoft\History::getInstance()->getActions();
            
            $arFilter = $this->_getFilter();
            
            $cacheId = serialize($arFilter) . serialize($arOrder) . serialize($this->arParams). serialize($_REQUEST["nav-history-list"]);
            
            $cache = Bitrix\Main\Data\Cache::createInstance();
            
            $cacheDir = "/travelsoft/history";
            
            if ($cache->initCache($this->arParams["CACHE_TIME"], $cacheId, $cacheDir)) {
                 
                $this->arResult = $cache->getVars();
                
            } elseif ($cache->startDataCache()) {
                
                $this->arResult["NAV"] = new \Bitrix\Main\UI\PageNavigation("nav-history-list");
                $this->arResult["NAV"]->allowAllRecords(true)->setPageSize($this->arParams["PAGE_ELEMENT_COUNT"])->initFromUri();
                
                $history = \travelsoft\History::getInstance();
                
                $dbList = $history->get(array(
                    "filter" => $arFilter,
                    "order" => $arOrder,
                    "count_total" => true,
                    "offset" => $this->arResult["NAV"]->getOffset(),
                    "limit" => $this->arResult["NAV"]->getLimit()
                ), false);
                
                $arObjectsFns = $history->getObjectsFns();
                while ($arFields = $dbList->fetch()) {
                    
                    $this->arResult["ITEMS"][] = $arFields;
                    $this->arResult["ROWS"][] = $this->prepareFields($arFields);
                    
                }
                
                $this->arResult["NAV"]->setRecordCount($history->getCount()); 
                
                if (empty($this->arResult["ITEMS"])) {
                    if(defined("BX_COMP_MANAGED_CACHE")) {
                            global $CACHE_MANAGER;
                            $CACHE_MANAGER->StartTagCache($cacheDir);
                            $CACHE_MANAGER->RegisterTag("__tshistory");                    
                            $CACHE_MANAGER->EndTagCache();
                    }
                    $cache->abortDataCache();
                } else {
                    $cache->endDataCache($this->arResult);
                }
                
            }
            
            $this->IncludeComponentTemplate();
            
            if ($this->arParams["BOOTSTRAP"]) {
                \Bitrix\Main\Page\Asset::getInstance()->addString('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">', true);
                \Bitrix\Main\Page\Asset::getInstance()->addString('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">', true);
            }
            
        } catch (\Exception $e) {
            ShowError ($e->getMessage());
        }
        
    }
    
    /**
     * Возвращает подготовленные данные для отображения
     * @param array $arFields
     * @return array
     */
    public function prepareFields (array $arFields) {
        
        $arPrepared["ID"] = $arFields["ID"];
        
        $arPrepared["OBJECT"] = $arFields["UF_OBJECT"];
        
        $arPrepared["ACTION"] = $arFields["UF_ACTION"];
        
        $arPrepared["USER"] = $arFields["UF_USER_ID"];
        
        $arPrepared["USER"] = $arFields["USER"];
        
        $arPrepared["ELEMENT"] = $arFields["UF_ELEMENT_ID"];
        
        $arPrepared["STORE"] = $arFields["UF_STORE_ID"];
        
        if ($arFields["UF_USER_ID"] > 0) {
            $arPrepared["USER"] = $this->_setAndGetUserName($arFields["UF_USER_ID"]);
        }
        
        $arPrepared["DATE"] = "";
        
        if ($arFields["UF_DATE"]) {
            $arPrepared["DATE"] = date("d.m.Y H:i:s", $arFields["UF_DATE"]);
        }
        
        $arPrepared["IP"] = $arFields["UF_IP"];
        
        return $arPrepared;
    }
    
    /**
     * @param int $id
     * @return string|null
     */
    protected function _setAndGetUserName (int $id) {
        if (!$this->_cacheUsers[$id]) {
            $this->_cacheUsers[$id] = $id;
            $arUser = CUser::GetByID($id)->Fetch();
            if ($arUser["NAME"]) {
                $this->_cacheUsers[$id] = implode(" ", array_filter(array($arUser["NAME"], $arUser["LAST_NAME"]), function ($val) { return strlen($val) > 0; }));
            }
        }
        return $this->_cacheUsers[$id];
    }
    
    /**
     * @return array
     */
    protected function _getFilter () {
        
        if (strlen($_REQUEST["HISTORY_FILTER"]["RESET"]) > 0) {
            LocalRedirect($GLOBALS["APPLICATION"]->GetCurPage(false));
        }
        
        $arFilter = array();
        if (strlen($_REQUEST["HISTORY_FILTER"]["SUBMIT"]) > 0) {

            if (in_array($_REQUEST["HISTORY_FILTER"]["OBJECT"], $this->arResult["OBJECTS"])) {
                $arFilter["UF_OBJECT"] = $_REQUEST["HISTORY_FILTER"]["OBJECT"];
            }
            if (in_array($_REQUEST["HISTORY_FILTER"]["ACTION"], $this->arResult["ACTIONS"])) {
                $arFilter["UF_ACTION"] = $_REQUEST["HISTORY_FILTER"]["ACTION"];
            }
            if ($_REQUEST["HISTORY_FILTER"]["HELEMENT_ID"] > 0) {
                $arFilter["UF_ELEMENT_ID"] = $_REQUEST["HISTORY_FILTER"]["HELEMENT_ID"];
            }
            if ($_REQUEST["HISTORY_FILTER"]["STORE_ID"] > 0) {
                $arFilter["UF_STORE_ID"] = $_REQUEST["HISTORY_FILTER"]["STORE_ID"];
            }
            if ($_REQUEST["HISTORY_FILTER"]["IP"] > 0) {
                $arFilter["UF_IP"] = $_REQUEST["HISTORY_FILTER"]["IP"];
            }
            if ($_REQUEST["HISTORY_FILTER"]["USER_ID"] > 0) {
                $arFilter["UF_USER_ID"] = $_REQUEST["HISTORY_FILTER"]["USER_ID"];
            }
            if ($_REQUEST["HISTORY_FILTER"]["DATE_FROM"] > 0) {
                $arFilter[">=UF_DATE"] = MakeTimeStamp($_REQUEST["HISTORY_FILTER"]["DATE_FROM"]);
            }
            if ($_REQUEST["HISTORY_FILTER"]["DATE_TO"] > 0) {
                if ($arFilter[">=UF_DATE"]) {
                    $arFilter["><UF_DATE"] = array(MakeTimeStamp($_REQUEST["HISTORY_FILTER"]["DATE_FROM"]), MakeTimeStamp($_REQUEST["HISTORY_FILTER"]["DATE_TO"]));
                    unset($arFilter[">=UF_DATE"]);
                } else {
                    $arFilter["=<UF_DATE"] = MakeTimeStamp($_REQUEST["HISTORY_FILTER"]["DATE_TO"]);
                }
            }
        }
        return $arFilter;
        
    }
    
    /**
     * @return array
     */
    protected function _getOrder () {
        
        $arOrder = array("UF_DATE" => "DESC");
        if ($_REQUEST["SORT_BY"] == "DATE") {
            $arOrder = array("UF_DATE" => $_REQUEST["ORDER"] == "ASC" ? "ASC" : "DESC");
        }
        
        return $arOrder;
        
    }
    
    # include component_prolog.php
    protected function _includeComponentProlog() {
        $file = "component_prolog.php";

        $template_name = $this->GetTemplateName();

        if ($template_name == "")
            $template_name = ".default";

        $relative_path = $this->GetRelativePath();

        $dr = Bitrix\Main\Application::getDocumentRoot();

        $file_path = $dr . SITE_TEMPLATE_PATH . "/components" . $relative_path . "/" . $template_name . "/" . $file;

        $arParams = &$this->arParams;

        if(file_exists($file_path))
            require $file_path;
        else {

            $file_path = $dr . "/bitrix/templates/.default/components" . $relative_path . "/" . $template_name . "/" . $file;

            if(file_exists($file_path))
                require $file_path;
            else {
                $file_path = $dr . $this->__path . "/templates/" . $template_name . "/" . $file;
                if(file_exists($file_path))
                    require $file_path;
                else {

                    $file_path = $dr . "/local/components" . $relative_path . "/templates/" . $template_name . "/" . $file;

                    if(file_exists($file_path))
                        require $file_path;
                }

            }
        }
    }
    
}
