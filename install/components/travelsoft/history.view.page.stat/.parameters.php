<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters["PARAMETERS"]['ID'] = array(
            "PARENT" => "BASE",
            "NAME" => "Числовой ID страницы для просмотра статистики (ID > 0)",
            "TYPE" => "STRING"
        );
 
$arComponentParameters["PARAMETERS"]['BOOTSTRAP'] = array(
            "PARENT" => "BASE",
            "NAME" => "Подключить bootstrap css из cdn",
            "TYPE" => "CHECKBOX"
        );

$arComponentParameters["PARAMETERS"]['CHARTS_JS'] = array(
            "PARENT" => "BASE",
            "NAME" => "Подключить js-библиотеки d3.js и dimple.js из cdn для отрисовки графиков",
            "TYPE" => "CHECKBOX"
        );