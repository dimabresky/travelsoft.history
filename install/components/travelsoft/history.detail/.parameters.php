<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arComponentParameters["PARAMETERS"]['ID'] = array(
            "PARENT" => "BASE",
            "NAME" => "ID элемента истории",
            "TYPE" => "STRING",
            "DEFAULT" =>  '={$_REQUEST[\'ID\']}'
        );

$arComponentParameters["PARAMETERS"]['BOOTSTRAP'] = array(
            "PARENT" => "BASE",
            "NAME" => "Подключить bootstrap css из cdn",
            "TYPE" => "CHECKBOX"
        );