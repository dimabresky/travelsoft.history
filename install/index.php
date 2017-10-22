<?php

use Bitrix\Main\ModuleManager,
                Bitrix\Main\Loader,
                    Bitrix\Main\Config\Option;

class travelsoft_history extends CModule
{
    public $MODULE_ID = "travelsoft.history";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "N";
    protected $namespaceFolder = "travelsoft";
    protected $componentsList = array(
        "history.detail",
        "history.list",
        "history.page.counter",
        "history.view.page.stat"
    );

    function __construct()
    {
        $arModuleVersion = array();
        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        @include($path."/version.php");
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = "История изменений";
        $this->MODULE_DESCRIPTION = "Модуль для отслеживания истории изменений (элементы инфоблоков, элементы highloadblock'ов, пользователей)";
        $this->PARTNER_NAME = "dimabresky (travelsoft)";
        $this->PARTNER_URI = "https://github.com/dimabresky/";
        
        Loader::includeModule('highloadblock');
        
    }
    
    public function copyFiles() {
        
        foreach ($this->componentsList as $componentName) {
            CopyDirFiles(
                $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/components/" .$this->namespaceFolder ."/" . $componentName,
                $_SERVER["DOCUMENT_ROOT"]."/local/components/".$this->namespaceFolder . "/" . $componentName,
                true, true
            );
        }
        
        
    }
    
    public function deleteFiles() {
        foreach ($this->componentsList as $componentName) {
            DeleteDirFilesEx("/local/components/". $this->namespaceFolder . "/" . $componentName);
        }
        if(!glob($_SERVER["DOCUMENT_ROOT"]."/local/components/". $this->namespaceFolder ."/*")) {
            DeleteDirFilesEx("/local/components/". $this->namespaceFolder);
        }
        return true;
    }
    
    public function DoInstall()
    {
        try {
            
            # проверка зависимостей
            if (!Loader::includeModule("iblock")) {
                throw new Exception("Для установки необходим модуль инфоблоков");
            }
            if (!Loader::includeModule("highloadblock")) {
                throw new Exception("Для установки необходим модуль highloadblock");
            }
            
            #преднастройка модуля
            if (!$_REQUEST["settings"] || !strlen($_REQUEST["next"]) || !check_bitrix_sessid()) {
                
                $GLOBALS['MODULE_ID'] = $this->MODULE_ID;
                $GLOBALS['APPLICATION']->IncludeAdminFile('Настройки', $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/settings_form.php");

            } else {
                
                #регистрируем модуль
                ModuleManager::registerModule($this->MODULE_ID);
                
                #разворачиваем highloadblock истории
                $HLBLOCK = @include_once "history_highloadblock.php";
                $result = Bitrix\Highloadblock\HighloadBlockTable::add(array(
                    'NAME' => $HLBLOCK["NAME"],
                    'TABLE_NAME' => $HLBLOCK["SMALL_NAME"],
                ));

                if (!$result->isSuccess()) {
                    throw new Exception (implode("<br>", $result->getErrorMessages()));
                }

                $HL_ID = $result->getId();
                
                #сохраняем id highloadblock истории в параметры модуля
                Option::set($this->MODULE_ID, "history_highloadblock", $HL_ID);
                
                $oUserTypeEntity = new CUserTypeEntity();
                
                $HLBLOCK_FIELDS = @include_once 'history_highloadblock_fields.php';
                foreach ($HLBLOCK_FIELDS as $aUserFields) {

                    if (!$oUserTypeEntity->Add( $aUserFields )) {
                        throw new Exception("Возникла ошибка при добавлении свойства " .$aUserFields["ENTITY_ID"] . "[".$aUserFields["FIELD_NAME"]."]" . $oUserTypeEntity->LAST_ERROR);
                    }

                }
                
                #сохраняем параметры модуля из формы
                @include_once __DIR__ . '/../save_module_parameters_from_settings_form.php';
                
                $this->copyFiles();
                
                return true;
            }
            
            
        } catch (Exception $ex) {
            $GLOBALS["APPLICATION"]->ThrowException($ex->getMessage());
            $this->DoUninstall();
            return false;
        }
        
    }
    
    public function DoUninstall()
    {
        #удаление таблицы истории
        Bitrix\Highloadblock\HighloadBlockTable::delete(Option::get($this->MODULE_ID, "history_highloadblock"));
        
        @include_once __DIR__ . '/../functions.php';
        travelsoft\unRegisterAllModuleDependences();
        travelsoft\unsetModuleOptions();
        ModuleManager::UnRegisterModule($this->MODULE_ID);
        $this->deleteFiles();
        return true;
    }
}
