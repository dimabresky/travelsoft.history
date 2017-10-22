<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"));
$arGroups[] = "Учитывать пользователей всех групп";
while($arGroup = $rsGroups->Fetch()) {
    $arGroups[$arGroup["ID"]] = $arGroup["NAME"];
}

$arComponentParameters["PARAMETERS"]['USER_GROUPS'] = array(
            "PARENT" => "BASE",
            "NAME" => "Не учитывать просмотр страницы пользователями групп",
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arGroups
        );

$arComponentParameters["PARAMETERS"]['ID'] = array(
            "PARENT" => "BASE",
            "NAME" => "Числовой ID страницы (ID > 0)",
            "TYPE" => "STRING"
        );
    