<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$arElement = \travelsoft\History::getInstance()->get(array("filter" => array("ID" => $arParams["ID"])));
if ($arElement[0]) { $arResult = $arElement[0]; }
if ($arResult["ID"]) {
    if (!function_exists("SPropertyScreen")) {

        function SPropertyToRender (array $arData) {
            if ($arData["USER_TYPE"] == "HTML") {
                return array_map(function ($val) { return htmlspecialchars($val["TEXT"]); }, $arData["~VALUE"]);
            } else {
                return array_map(function ($val) { return htmlspecialchars($val);}, $arData["~VALUE"]);
            }

        }

    }

    if (!function_exists("compareRender")) {
        function compareRender ($val1, $val2) {
            $cnt = count($val1) > count($val2) ? count($val1) : count($val2);
            for ($i = 0; $i < $cnt; $i++) {
                $style = "";
                if (md5($val1[$i]) !== md5($val2[$i])) {
                    $style = "style='color: red'";
                }
                wrapper($val1[$i], $style);
                wrapper($val2[$i], $style);
            }
            echo "<td>".implode($val1)."</td>"
                    . "<td>".implode($val2)."</td>";
        }
    }
    
    if (!function_exists("render")) {
        function render ($val) {
            foreach ($val as &$v) {
                wrapper($v);
            }
            echo "<td>".implode($val)."</td>";
        }
    }

    if (!function_exists("wrapper")) {
        function wrapper (&$val, $style = "") {
            if ($val) {
                $val = "<div ".$style.">".$val."</div>";
            }
        }
    }

    if (!function_exists("screen")) {
        function screen ($arData) {
            if ($arData["PROPERTY_TYPE"] == "S") {
                return SPropertyToRender($arData);
            } else {
                return array_map(function ($val) { return htmlspecialchars($val); }, $arData["VALUE"]);
            }
        }
    }
    
    if (!function_exists("normalize")) {
        function normalize (array $array) {
            $result = null;
            foreach ($array as $k => $arElement) {
                
                if (isset($arElement["FIELDS"])) {
                    foreach ($arElement["FIELDS"] as $code => $value) {
                        if (strpos($code, "~") !== 0) {
                            $result[$k][$code] = array("VALUE" => is_array($value) ? $value : array($value));
                        }
                    }
                    foreach ($arElement["PROPERTIES"] as $code => $arValue) {
                        $arValue["VALUE"] = is_array($arValue["VALUE"]) ? $arValue["VALUE"] : array($arValue["VALUE"]);
                        $result[$k][$code] = $arValue;
                    }
                } else {
                    foreach ($arElement as $code => $value) {
                        $result[$k][$code] = array("VALUE" => is_array(unserialize($value)) ? unserialize($value) : (is_array($value) ? $value : array($value)));
                    }
                }
            }
            
            return $result;
        }
    }
    
    if ($arResult["UF_USER_ID"]) {
        $arResult["USER"] = $USER->GetByID($arResult["UF_USER_ID"])->Fetch();
    }
    ?>
<div class="container">
    <div class="white-area">
        <div class="main-info">
            <b>ID</b>: <?= $arResult["ID"]?><br>
            <b><?= GetMessage("HISTORY_ELEMENT_TITLE")?></b>: <?= $arResult["UF_OBJECT"]?><br>
            <b><?= GetMessage("ID_HISTORY_ELEMENT_TITLE")?></b>: <?= $arResult["UF_ELEMENT_ID"]?><br>
            <b><?= GetMessage("ACTION_TITLE")?></b>: <?= $arResult["UF_ACTION"]?><br>
            <b><?= GetMessage("USER_TITLE")?></b>: <?= $arResult["USER"]["NAME"] . " " . $arResult["USER"]["LAST_NAME"]?><br>
            <b><?= GetMessage("USER_IP_TITLE")?></b>: <?= $arResult["UF_IP"]?> 
        </div>
        <?
        $file = strtolower($arResult["UF_OBJECT"]) . ".php";
        switch ($arResult["UF_ACTION"]) {
    
            # добавление
            case "ADD":
            
            # просмотр страницы
            case "VIEW_PAGE":

            # удаление
            case "DELETE":

                $arResult["RENDER_TABLE_DATA"][] = travelsoft\sta($arResult["UF_DETAIL_INFO"]);

                break;

            # обновление
            case "UPDATE":

                $arDBFieldsBeforeUpdate = travelsoft\History::getInstance()->get(array(
                    "filter" => array("<ID" => $arResult["ID"], "UF_ELEMENT_ID" => $arResult["UF_ELEMENT_ID"], "UF_OBJECT" => $arResult["UF_OBJECT"]),
                    "order" => array("ID" => "DESC"),
                    "limit" => 1
                ));

                if ($arDBFieldsBeforeUpdate) {
                    $arResult["RENDER_TABLE_DATA"][] = travelsoft\sta($arDBFieldsBeforeUpdate[0]["UF_DETAIL_INFO"]);
                }
                $arResult["RENDER_TABLE_DATA"][] = travelsoft\sta($arResult["UF_DETAIL_INFO"]);

                break;
        }
        if ($arResult["RENDER_TABLE_DATA"]) {
            $arResult["RENDER_TABLE_DATA"] = normalize($arResult["RENDER_TABLE_DATA"])?>
        <h4><b><?= GetMessage("TABLE_TITLE")?></b></h4>
        <table class="table table-bordered">
        <?if (count($arResult["RENDER_TABLE_DATA"]) > 1) {?>
                <thead>
                    <tr>
                        <th><?= GetMessage("TABLE_FIRST_COLUMN_TITLE")?></th>
                        <th><?= GetMessage("TABLE_COMPARE_SECOND_COLUMN_TITLE")?></th>
                        <th><?= GetMessage("TABLE_COMPARE_THIRD_COLUMN_TITLE")?></th>
                    </tr>
                </thead>
                <tbody>
                    <?foreach ($arResult["RENDER_TABLE_DATA"][0] as $code => $arData) :?>
                        <tr>
                            <td><?= $code?></td>
                            <?compareRender(screen($arData), screen($arResult["RENDER_TABLE_DATA"][1][$code]));?>
                        </tr>
                    <?endforeach?>
                </tbody>

            <?} else {?>

                <thead>
                    <tr>
                        <th><?= GetMessage("TABLE_FIRST_COLUMN_TITLE")?></th>
                        <th><?= GetMessage("TABLE_SECOND_COLUMN_TITLE")?></th>
                    </tr>
                </thead>
                <tbody>
                    <?foreach ($arResult["RENDER_TABLE_DATA"][0] as $code => $arData) :?>
                        <tr>
                            <td><?= $code?></td>
                            <?render(screen($arData))?>
                        </tr>
                    <?endforeach?>
                </tbody>

            <?}?>
        </table>
        <?}?>
    </div>
</div>
<?}?>

