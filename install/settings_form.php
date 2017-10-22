
<?

Bitrix\Main\Loader::includeModule("iblock");
Bitrix\Main\Loader::includeModule("highloadblock");

$arIblocks = $arHighloadblocks = array("nofollow" => "Не отслеживать");

$ibTable = new CIBlock;

$dbIblocks = $ibTable->GetList(array("NAME" => "ASC"), array("ACTIVE" => "Y"));

while ($arRes = $dbIblocks->Fetch()) {
    $arIblocks[$arRes['ID']] = $arRes['NAME'];
}

$dbHLRes = Bitrix\Highloadblock\HighloadBlockTable::getList(array(
            "order" => array("ID" => "ASC")
        ))->fetchAll();

$HL_ID = Bitrix\Main\Config\Option::get("travelsoft.history", "history_highloadblock");
foreach ($dbHLRes as $arHL) {
    if ($HL_ID != $arHL['ID']) {
        $arHighloadblocks[$arHL['ID']] = $arHL['NAME'];
    }
}

?>

<form action="<?echo $GLOBALS["APPLICATION"]->GetCurPage()?>" name="form1">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?echo LANG?>">
    <input type="hidden" name="id" value="<?= $GLOBALS['MODULE_ID']?>">
    <input type="hidden" name="install" value="Y">
    
    <table class="pre-options" cellpadding="3" cellspacing="0" border="0" width="100%">
        
        <tr>
            
            <td style="font-size: 16px" width="50%" align="right">
                <b>Отслеживать изменения элементов инфоблоков:</b>
            </td>
            
            <td width="50%" align="left">
                <select multiple="" name="settings[follow_by_iblocks][]">
                    <?foreach ($arIblocks as $value => $title):?>
                    <option value="<?= $value?>"><?= $title?></option>
                    <? endforeach;?>
                </select>
            </td>
            
        </tr>
        
        <tr>
            
            <td style="font-size: 16px" width="50%" align="right">
                <b>Отслеживать изменения элементов higloadblock'ов:</b>
            </td>
            
            <td width="50%" align="left">
                <select multiple="" name="settings[follow_by_highloadblocks][]">
                    <?foreach ($arHighloadblocks as $value => $title):?>
                    <option value="<?= $value?>"><?= $title?></option>
                    <? endforeach;?>
                </select>
            </td>
            
        </tr>
        
        <tr>
            
            <td style="font-size: 16px" width="50%" align="right">
                <b>Отслеживать изменения пользователей:</b>
            </td>
            
            <td width="50%" align="left">
                <input type="checkbox" name="settings[follow_by_users]" value="Y">
            </td>
            
        </tr>
        
        <tr class="next-btn"><td colspan="2" align="right"><br><input type="submit" name="next" value="Далее"></td></tr>
    </table>
    
</form>
    