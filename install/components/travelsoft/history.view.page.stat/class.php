<?php

/**
 * Класс TravelsoftHistoryPageViewStatistic
 * Класс компонента статистики просмотра страниц
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class TravelsoftHistoryPageViewStatistic extends CBitrixComponent {
    
    protected function _getFilter () {
        
        if (strlen($_REQUEST["HISTORY_VP_STAT"]['RESET']) > 0) {
            LocalRedirect($GLOBALS["APPLICATION"]->GetCurPageParam("", array("HISTORY_VP_STAT")));
        }
        
        $month = 86400*31;
        $dateFrom = time() - $month;
        $dateTo = time() + 86400;
        
        $this->arResult["DATE_FROM"] = date("d.m.Y", $dateFrom);
        $this->arResult["DATE_TO"] = date("d.m.Y", $dateTo);
        
        if (strlen($_REQUEST["HISTORY_VP_STAT"]['SUBMIT']) > 0 && $_REQUEST['HISTORY_VP_STAT']['DATE_FROM'] && $_REQUEST['HISTORY_VP_STAT']['DATE_TO']) {
            $dateFrom = MakeTimeStamp ($_REQUEST['HISTORY_VP_STAT']['DATE_FROM']);
            $dateTo = MakeTimeStamp ($_REQUEST['HISTORY_VP_STAT']['DATE_TO']);
        }
        
        return array(
            "UF_ELEMENT_ID" => $this->arParams["ID"],
            "UF_OBJECT" => "PAGE",
            "UF_ACTION" => "VIEW_PAGE",
            "><UF_DATE" => array($dateFrom, $dateTo) 
        );
    }
    
    public function executeComponent() {
        
        try {
            
            if (!Bitrix\Main\Loader::includeModule("travelsoft.history")) {
                throw new Exception("history page view stat.: Модуль travelsoft.history не найден");
            }
            
            $this->arParams["ID"] = intVal($this->arParams["ID"]);
            if ($this->arParams["ID"] <= 0) {
                throw new Exception("history page view stat.: Укажите ID страницы (ID > 0)");
            }
            
            $this->_includeComponentProlog();
            
            $dbList = travelsoft\History::getInstance()->get(array(
                "filter" => $this->_getFilter(),
                "order" => array("UF_DATE" => "ASC")
            ), false);
            
            while ($arResult = $dbList->fetch()) {
                
                $date = date("d.m.Y", $arResult["UF_DATE"]);
                $this->arResult["ITEMS"][$date][] = $arResult;
                $this->arResult['COUNT']['BY_DATES'][$date]++;
                $this->arResult['COUNT']["TOTAL"]++;
                
            }
            
            $this->IncludeComponentTemplate();
            
            if ($this->arParams["BOOTSTRAP"] == "Y") {
                \Bitrix\Main\Page\Asset::getInstance()->addString('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">', true);
                \Bitrix\Main\Page\Asset::getInstance()->addString('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">', true);
            }
            
            if ($this->arParams["CHARTS_JS"] == "Y") {
                \Bitrix\Main\Page\Asset::getInstance()->addString('<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/4.8.0/d3.min.js"></script>');
                \Bitrix\Main\Page\Asset::getInstance()->addString('<script src="https://cdnjs.cloudflare.com/ajax/libs/dimple/2.3.0/dimple.latest.min.js"></script>');
            }
            
        } catch (\Exception $e) {
            ShowError($e->getMessage());
        }
    
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
