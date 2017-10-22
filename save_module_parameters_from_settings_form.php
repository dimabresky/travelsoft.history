<?php
@include_once 'functions.php';
use Bitrix\Main\Config\Option;
#сохраняем id инфоблоков для истории
if (!empty($_REQUEST["settings"]["follow_by_iblocks"]) && is_array($_REQUEST["settings"]["follow_by_iblocks"]) 
        && !in_array("nofollow", $_REQUEST["settings"]["follow_by_iblocks"])) {
        Option::set("travelsoft.history", "follow_by_iblocks", implode(";", $_REQUEST["settings"]["follow_by_iblocks"]));
        travelsoft\ReinstallIBlockModuleDependences();
} else {
    Option::set("travelsoft.history", "follow_by_iblocks", "");
    travelsoft\UnRegisterIBlocksEventsHandlers();
}

#сохраняем id highloadblock для истории
if (!empty($_REQUEST["settings"]["follow_by_highloadblocks"]) && is_array($_REQUEST["settings"]["follow_by_highloadblocks"]) &&
        !in_array("nofollow", $_REQUEST["settings"]["follow_by_highloadblocks"])) {
    Option::set("travelsoft.history", "follow_by_highloadblocks", implode(";", $_REQUEST["settings"]["follow_by_highloadblocks"]));
    travelsoft\ReinstallHighloadblocksModuleDependences();
} else {
    travelsoft\UnRegisterHighloadblocksEventsHandlers();
    Option::set("travelsoft.history", "follow_by_highloadblocks", "");
}

#сохраняем id групп пользователей для истории
if ($_REQUEST["settings"]["follow_by_users"] == "Y") {
    Option::set("travelsoft.history", "follow_by_users", "Y");
    travelsoft\ReinstallUsersModuleDependences();
} else {
    Option::set("travelsoft.history", "follow_by_users", "");
    travelsoft\UnRegisterUsersEventsHandlers();
}