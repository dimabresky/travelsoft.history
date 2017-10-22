<?php

/**
 * Класс TravelsoftHistoryDetail
 * Класс компонента просмотра детальной информации элементов истории
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class TravelsoftHistoryDetail extends CBitrixComponent {

    protected function _prepareInputParameters () {
        $this->arParams["ID"] = intVal($this->arParams["ID"]);
        if ($this->arParams["ID"] <= 0) {
            throw new Exception("Укажите ID элемета истории для детального просмотра");
        }
        $this->arParams["BOOTSTRAP"] = $this->arParams["BOOTSTRAP"] == "Y";
    }
    
    /**
     * Подключает необъодимые модули
     * @throws Exception
     */
    public function includeModules () {
        if (!\Bitrix\Main\Loader::includeModule("travelsoft.history")) {
            throw new Exception("history detail: Модуль travelsoft.history не найден");
        }
    }
    
    public function executeComponent() {
        
        try {
            
            $this->includeModules();
            
            $this->_includeComponentProlog();
            
            $this->_prepareInputParameters();
            
            $this->IncludeComponentTemplate();
            
            if ($this->arParams["BOOTSTRAP"]) {
                \Bitrix\Main\Page\Asset::getInstance()->addString('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">', true);
                \Bitrix\Main\Page\Asset::getInstance()->addString('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">', true);
            }
            
        } catch (\Exception $e) {
            ShowError ($e->getMessage());
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
