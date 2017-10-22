<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters["PARAMETERS"]['PAGE_ELEMENT_COUNT'] = array(
            "PARENT" => "BASE",
            "NAME" => "Количество выводимых элементов на страницу",
            "TYPE" => "STRING"
        );

$arComponentParameters["PARAMETERS"]["CACHE_TIME"] = array("DEFAULT"=>36000000);

$arComponentParameters["PARAMETERS"]['FILTER_NAME'] = array(
            "PARENT" => "BASE",
            "NAME" => "Название внешнего фильтра",
            "TYPE" => "STRING"
        );

$arTemplatesList = CComponentUtil::GetTemplatesList('bitrix:main.pagenavigation');

foreach ($arTemplatesList as $arTemplate) {
    $arTemplates[$arTemplate["NAME"]] = $arTemplate["NAME"];
}

$arComponentParameters["PARAMETERS"]['PAGE_TEMPLATE'] = array(
            "PARENT" => "BASE",
            "NAME" => "Шаблон постраничной навигации",
            "TYPE" => "LIST",
            "VALUES" => $arTemplates
        );

$arComponentParameters["PARAMETERS"]['BOOTSTRAP'] = array(
            "PARENT" => "BASE",
            "NAME" => "Подключить bootstrap css из cdn",
            "TYPE" => "CHECKBOX"
        );