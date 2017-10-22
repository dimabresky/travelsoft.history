<?php

namespace travelsoft;
use Bitrix\Main\Config\Option;

/**
 * Класс HistoryEeventsHandlers
 * 
 * Класс для обработки событий bitrix для ведения истории
 * @author dimabresky
 * @copyright (c) 2017, travelsoft
 */
class HistoryEeventsHandlers {
    
    protected static function clearCache () {
        global $CACHE_MANAGER;
        $CACHE_MANAGER->ClearByTag("__tshistory");
    }
    
    public static function onAfterIBlockElementDelete (array $arFields) {
        
        $arIblocks = explode(";", Option::get("travelsoft.history", "follow_by_iblocks"));
        
        if ( in_array($arFields["IBLOCK_ID"], $arIblocks) && $arFields['ID'] > 0) {
            
            $parameters = array (
                "UF_STORE_ID" => $arFields['IBLOCK_ID'],
                "UF_ELEMENT_ID" => $arFields['ID'],
                "UF_OBJECT" => "IBLOCK_ELEMENT",
                "UF_ACTION" => "DELETE",
                "UF_DETAIL_INFO" => ats(array("FIELDS" => $arFields))
            );

            History::getInstance()->save($parameters);
            self::clearCache();
        }
        
    }
    
    public static function onAfterIBlockElementAdd (array $arFields) {
        
        $arIblocks = explode(";", Option::get("travelsoft.history", "follow_by_iblocks"));
        
        if ( in_array($arFields["IBLOCK_ID"], $arIblocks) && $arFields['ID'] > 0) {
            
            $dbElement = \CIBlockElement::GetByID($arFields['ID'])->GetNextElement();
            
            if ($dbElement) {
                
                $parameters = array (
                    "UF_STORE_ID" => $arFields['IBLOCK_ID'],
                    "UF_ELEMENT_ID" => $arFields['ID'],
                    "UF_OBJECT" => "IBLOCK_ELEMENT",
                    "UF_ACTION" => "ADD",
                    "UF_DETAIL_INFO" => ats(array(
                        "FIELDS" => $dbElement->GetFields(),
                        "PROPERTIES" => $dbElement->GetProperties()
                    ))
                );

                History::getInstance()->save($parameters);
                self::clearCache();
            }
        }
        
    }
    
    public static function onAfterIBlockElementUpdate (array $arFields) {
        
        $arIblocks = explode(";", Option::get("travelsoft.history", "follow_by_iblocks"));
        
        if ( in_array($arFields["IBLOCK_ID"], $arIblocks) &&  $arFields['ID'] > 0) {
            
            $dbElement = \CIBlockElement::GetByID($arFields['ID'])->GetNextElement();
            
            if ($dbElement) {
                
                $parameters = array (
                    "UF_STORE_ID" => $arFields['IBLOCK_ID'],
                    "UF_ELEMENT_ID" => $arFields['ID'],
                    "UF_OBJECT" => "IBLOCK_ELEMENT",
                    "UF_ACTION" => "UPDATE",
                    "UF_DETAIL_INFO" => ats(array(
                        "FIELDS" => $dbElement->GetFields(),
                        "PROPERTIES" => $dbElement->GetProperties()
                    ))
                );

                History::getInstance()->save($parameters);
                self::clearCache();
            }
        }
        
    }
    
    public static function onBeforeHighloadElementDelete ($storeId, $arElement) {
        
        $arHighloadblocks = explode(";", Option::get("travelsoft.history", "follow_by_highloadblocks"));
        
        if ( in_array($storeId, $arHighloadblocks) ) {
            Option::delete("travelsoft.history",  array('name' => 'tmp_highloadblock_element_fields_before_delete'));
            $dataClass = getHLDataClass($storeId);
            $arFields = $dataClass::getList(array(
                "filter" => array("ID" => $arElement["ID"])
            ))->fetchAll();
            Option::set("travelsoft.history", "tmp_highloadblock_element_fields_before_delete", ats($arFields[0]));
        }
    }
    
    public static function onAfterHighloadElementDelete ($storeId, $arElement) {
        
        $arHighloadblocks = explode(";", Option::get("travelsoft.history", "follow_by_highloadblocks"));
        if ( in_array($storeId, $arHighloadblocks) ) {
            $arFieldsStr = Option::get("travelsoft.history", "tmp_highloadblock_element_fields_before_delete");
            Option::delete("travelsoft.history",  array('name' => 'tmp_highloadblock_element_fields_before_delete'));
            $parameters = array (
                "UF_STORE_ID" => $storeId,
                "UF_ELEMENT_ID" => $arElement["ID"],
                "UF_OBJECT" => "HIGHLOADBLOCK_ELEMENT",
                "UF_ACTION" => "DELETE",
                "UF_DETAIL_INFO" => $arFieldsStr ? $arFieldsStr : ""
            );

            History::getInstance()->save($parameters);
            self::clearCache();
        }
        
    }
    
    public static function onAfterHighloadElementUpdate ($storeId, $arElement, $arFields) {
        
        $arHighloadblocks = explode(";", Option::get("travelsoft.history", "follow_by_highloadblocks"));
        
        if ( in_array($storeId, $arHighloadblocks) ) {
            
            $parameters = array (
                "UF_STORE_ID" => $storeId,
                "UF_ELEMENT_ID" => $arElement["ID"],
                "UF_OBJECT" => "HIGHLOADBLOCK_ELEMENT",
                "UF_ACTION" => "UPDATE",
                "UF_DETAIL_INFO" => ats($arFields)
            );

            History::getInstance()->save($parameters);
            self::clearCache();
        }
        
    }
    
    public static function onAfterHighloadElementAdd ($storeId, $elementId, $arFields) {
        
        $arHighloadblocks = explode(";", Option::get("travelsoft.history", "follow_by_highloadblocks"));

        if ( in_array($storeId, $arHighloadblocks) ) {
            
            $parameters = array (
                "UF_STORE_ID" => $storeId,
                "UF_ELEMENT_ID" => $elementId,
                "UF_OBJECT" => "HIGHLOADBLOCK_ELEMENT",
                "UF_ACTION" => "ADD",
                "UF_DETAIL_INFO" => ats($arFields)
            );

            History::getInstance()->save($parameters);
            self::clearCache();
        }
    }
    
    public static function onBeforeUserDelete (int $ID) {
        Option::delete("travelsoft.history",  array('name' => 'tmp_users_fields_before_delete'));
        Option::set("travelsoft.history", "tmp_users_fields_before_delete", ats($GLOBALS["USER"]->GetByID($ID)->Fetch()));
    }
    
    public static function onAfterUserDelete (int $ID) {
        
        if ($ID > 0) {
            $arFieldsStr = Option::get("travelsoft.history", "tmp_users_fields_before_delete");
            Option::delete("travelsoft.history",  array('name' => 'tmp_users_fields_before_delete'));
            $parameters = array (
                "UF_ELEMENT_ID" => $ID,
                "UF_OBJECT" => "USER",
                "UF_ACTION" => "DELETE",
                "UF_DETAIL_INFO" => $arFieldsStr ? $arFieldsStr : ""
            );

            History::getInstance()->save($parameters); 
            self::clearCache();
        }
    }
    
    public static function onAfterUserUpdate (array $arFields) {
        
        if ($arFields["ID"] > 0) {
            
            $parameters = array (
                "UF_ELEMENT_ID" => $arFields['ID'],
                "UF_OBJECT" => "USER",
                "UF_ACTION" => "UPDATE",
                "UF_DETAIL_INFO" => ats($arFields)
            );

            History::getInstance()->save($parameters);
            self::clearCache();
        }
        
    }
    
    public static function onAfterUserAdd (array $arFields) {
        
        if ($arFields["ID"] > 0) {
            
            $parameters = array (
                "UF_ELEMENT_ID" => $arFields['ID'],
                "UF_OBJECT" => "USER",
                "UF_ACTION" => "ADD",
                "UF_DETAIL_INFO" => ats($arFields)
            );
            History::getInstance()->save($parameters);
            self::clearCache();
        }    
    }
    
}

