<?

if (!$USER->isAdmin() || !\Bitrix\Main\Loader::includeModule("highloadblock") || !Bitrix\Main\Loader::includeModule("iblock")) { return; }

global $APPLICATION;

$arIblocks = $arHighloadblocks = array("nofollow" => "Не отслеживать");

$ibTable = new CIBlock;

$dbIblocks = $ibTable->GetList(array("NAME" => "ASC"), array("ACTIVE" => "Y"));

while ($arRes = $dbIblocks->Fetch()) {
    $arIblocks[$arRes['ID']] = $arRes['NAME'];
}

$dbHLRes = Bitrix\Highloadblock\HighloadBlockTable::getList(array(
            "order" => array("ID" => "ASC")
        ))->fetchAll();

foreach ($dbHLRes as $arHL) {
    $arHighloadblocks[$arHL['ID']] = $arHL['NAME'];
}

$tabs = array(
    array(
            "DIV" => "edit",
            "TAB" => "Настройки модуля travelsoft.history",
            "ICON" => "",
            "TITLE" => ""
    ),
);
$o_tab = new CAdminTabControl("TravelsoftHistory", $tabs);
if (strlen($_REQUEST["save"]) > 0) {
    @include_once 'save_module_parameters_from_settings_form.php';
    LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode("travelsoft.history") . "&lang=" . urlencode(LANGUAGE_ID) . "&" . $o_tab->ActiveTabParam());
} else if (strlen($_REQUEST["reset"]) > 0) {
    @include_once 'functions.php';
    travelsoft\unsetFollowOptions();
    travelsoft\unRegisterAllModuleDependences();
}

$settedIblocks = explode(";", Bitrix\Main\Config\Option::get("travelsoft.history", "follow_by_iblocks"));
$settedHighloadblocks = explode(";", Bitrix\Main\Config\Option::get("travelsoft.history", "follow_by_highloadblocks"));
$followByUsers = Bitrix\Main\Config\Option::get("travelsoft.history", "follow_by_users");

?>

<form method="post" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode("travelsoft.history") ?>&amp;lang=<? echo LANGUAGE_ID ?>" name="form1">
    <?
    $o_tab->Begin();
    $o_tab->BeginNextTab();?>
    <?=bitrix_sessid_post()?>

        <tr>
            
            <td width="40%">
                <label for="settings[follow_by_iblocks][]">Отслеживать изменения элементов инфоблоков:</label>
            </td>
            
            <td width="60%">
                <select multiple="" name="settings[follow_by_iblocks][]">
                    <?foreach ($arIblocks as $value => $title):?>
                    <option <?if (in_array($value, $settedIblocks)):?>selected<?endif?> value="<?= $value?>"><?= $title?></option>
                    <? endforeach;?>
                </select>
            </td>
            
        </tr>
        
        <tr>
            
            <td width="40%">
                <label for="settings[follow_by_highloadblocks][]">Отслеживать изменения элементов higloadblock'ов:</label>
            </td>
            
            <td width="60%">
                <select multiple="" name="settings[follow_by_highloadblocks][]">
                    <?
                    $hhb = Bitrix\Main\Config\Option::get("travelsoft.history", "history_highloadblock");
                    foreach ($arHighloadblocks as $value => $title):
                        if ($hhb == $value) {continue;}?>
                    <option <?if (in_array($value, $settedHighloadblocks)):?>selected<?endif?> value="<?= $value?>"><?= $title?></option>
                    <? endforeach;?>
                </select>
            </td>
            
        </tr>
        
        <tr>
            
            <td width="40%">
                <label for="settings[follow_by_users]">Отслеживать изменения пользователей:</label>
            </td>
            
            <td width="60%">
                <input type="checkbox" <?if ($followByUsers == "Y") { echo "checked"; }?> name="settings[follow_by_users]" value="Y">
            </td>
            
        </tr>
        <?$o_tab->Buttons();?>
        <input type="submit" name="save" value="Сохранить" class="adm-btn-save">
        <input type="submit" name="reset" OnClick="return confirm('Вы уверены что хотите сбросить настройки')" value="Сбросить">
    <?$o_tab->End();?>
</form>

    