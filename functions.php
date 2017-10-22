<?php

namespace travelsoft;

use Bitrix\Main\Config\Option;
\Bitrix\Main\Loader::includeModule("highloadblock");

if (!function_exists("travelsoft\\doHLEvHandlersRegUnReg")) {
    function doHLEvHandlersRegUnReg (string $function) {
        if ( ($HL_ID = explode(";", Option::get("travelsoft.history", "follow_by_highloadblocks"))) ) {
            foreach ($HL_ID as $ID) {
                $hlblock = \Bitrix\Highloadblock\HighloadBlockTable ::getById($ID)->fetch();
                if ($hlblock["NAME"]) {
                    $function("", $hlblock["NAME"] . "OnAfterAdd", "travelsoft.history", "travelsoft\\HistoryEeventsHandlers", "onAfterHighloadElementAdd", 100, null, array($ID));
                    $function("", $hlblock["NAME"] . "OnAfterUpdate", "travelsoft.history", "travelsoft\\HistoryEeventsHandlers", "onAfterHighloadElementUpdate", 100, null, array($ID));
                    $function("", $hlblock["NAME"] . "OnBeforeDelete", "travelsoft.history", "travelsoft\\HistoryEeventsHandlers", "onBeforeHighloadElementDelete", 100, null, array($ID));
                    $function("", $hlblock["NAME"] . "OnAfterDelete", "travelsoft.history", "travelsoft\\HistoryEeventsHandlers", "onAfterHighloadElementDelete", 100, null, array($ID));
                }
            }
        }
    }  
}

if (!function_exists("travelsoft\\doIBlockEvHandlersRegUnReg")) {

    function doIBlockEvHandlersRegUnReg (string $function) {
        $function("iblock", "OnAfterIBlockElementAdd", "travelsoft.history", "travelsoft\\HistoryEeventsHandlers", "onAfterIBlockElementAdd");
        $function("iblock", "OnAfterIBlockElementDelete", "travelsoft.history", "travelsoft\\HistoryEeventsHandlers", "onAfterIBlockElementDelete");
        $function("iblock", "OnAfterIBlockElementUpdate", "travelsoft.history", "travelsoft\\HistoryEeventsHandlers", "onAfterIBlockElementUpdate");
    }
    
}

if (!function_exists("travelsoft\\doUsersEvHandlersRegUnReg")) {
    function doUsersEvHandlersRegUnReg (string $function) {
        $function("main", "OnBeforeUserDelete", "travelsoft.history", "travelsoft\\HistoryEeventsHandlers", "onBeforeUserDelete");
        $function("main", "OnAfterUserDelete", "travelsoft.history", "travelsoft\\HistoryEeventsHandlers", "onAfterUserDelete");
        $function("main", "OnAfterUserUpdate", "travelsoft.history", "travelsoft\\HistoryEeventsHandlers", "onAfterUserUpdate");
        $function("main", "OnAfterUserAdd", "travelsoft.history", "travelsoft\\HistoryEeventsHandlers", "onAfterUserAdd");
    }
}

if (!function_exists("travelsoft\\RegisterHighloadblocksEventsHandlers")) {
    function RegisterHighloadblocksEventsHandlers () {
        doHLEvHandlersRegUnReg("RegisterModuleDependences");
    }
}

if (!function_exists("travelsoft\\UnRegisterHighloadblocksEventsHandlers")) {
    function UnRegisterHighloadblocksEventsHandlers () {
        doHLEvHandlersRegUnReg("UnRegisterModuleDependences");
    }
}

if (!function_exists("travelsoft\\RegisterIBlocksEventsHandlers")) {
    function RegisterIBlocksEventsHandlers () {
        doIBlockEvHandlersRegUnReg("RegisterModuleDependences");
    }
}

if (!function_exists("travelsoft\\UnRegisterIBlocksEventsHandlers")) {
    function UnRegisterIBlocksEventsHandlers () {
        doIBlockEvHandlersRegUnReg("UnRegisterModuleDependences");
    }
}

if (!function_exists("travelsoft\\RegisterUsersEventsHandlers")) {
    function RegisterUsersEventsHandlers () {
        doUsersEvHandlersRegUnReg("RegisterModuleDependences");
    }
}

if (!function_exists("travelsoft\\UnRegisterUsersEventsHandlers")) {
    function UnRegisterUsersEventsHandlers () {
        doUsersEvHandlersRegUnReg("UnRegisterModuleDependences");
    }
}

if (!function_exists("travelsoft\\ReinstallIBlockModuleDependences")) {
    function ReinstallIBlockModuleDependences () {

        UnRegisterIBlocksEventsHandlers();
        RegisterIBlocksEventsHandlers();

    }
}

if (!function_exists("travelsoft\\ReinstallHighloadblocksModuleDependences")) {
    function ReinstallHighloadblocksModuleDependences () {

        UnRegisterHighloadblocksEventsHandlers();
        RegisterHighloadblocksEventsHandlers();

    }
}

if (!function_exists("travelsoft\\ReinstallUsersModuleDependences")) {
    function ReinstallUsersModuleDependences () {

        UnRegisterUsersEventsHandlers();
        RegisterUsersEventsHandlers();

    }
}

if (!function_exists("travelsoft\\unRegisterAllModuleDependences")) {
    function unRegisterAllModuleDependences () {
        UnRegisterIBlocksEventsHandlers();
        UnRegisterUsersEventsHandlers();
        UnRegisterHighloadblocksEventsHandlers();
    }
}

if (!function_exists("travelsoft\\unsetFollowOptions")) {
    function unsetFollowOptions () {
        Option::delete("travelsoft.history",  array('name' => 'follow_by_iblocks'));
        Option::delete("travelsoft.history",  array('name' => 'follow_by_highloadblocks'));
        Option::delete("travelsoft.history",  array('name' => 'follow_by_users'));
    }
}

if (!function_exists("travelsoft\\unsetModuleOptions")) {
    function unsetModuleOptions () {
        Option::delete("travelsoft.history",  array('name' => 'history_highloadblock'));
        unsetFollowOptions ();
    }
}

if (!function_exists("ats")) {
    function ats (array $arFields) {
        return base64_encode(gzcompress(serialize($arFields), 9));
    }
}

if (!function_exists("sta")) {
    function sta (string $str) {
        return unserialize(gzuncompress(base64_decode($str)));
    }
}

if (!function_exists("getHLDataClass")) {
    function getHLDataClass (int $ID) {
        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity(\Bitrix\Highloadblock\HighloadBlockTable::getById($ID)->fetch());
        return $entity->getDataClass();
    }
}
