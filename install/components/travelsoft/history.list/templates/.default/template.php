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
CJSCore::Init();
?>

<div class="container">
    
    <div class="filter-area white-area">
        <form id="history-list" method="GET" action="<?= $APPLICATION->GetCurPageParam("", array("HISTORY_FILTER[DATE_FROM]", "HISTORY_FILTER[DATE_TO]", "HISTORY_FILTER[OBJECT]", "HISTORY_FILTER[ACTION]", "HISTORY_FILTER[HELEMENT_ID]", "HISTORY_FILTER[STORE_ID]", "HISTORY_FILTER[IP]", "HISTORY_FILTER[USER_ID]"), false);?>">
            <fieldset>
                <legend><?= GetMessage("FILTER_TITLE")?></legend>
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label for="HISTORY_FILTER[OBJECT]"><?= GetMessage("FILTER_OBJECT_TITLE")?></label>
                            <select class="form-control" id="HISTORY_FILTER[OBJECT]" name="HISTORY_FILTER[OBJECT]">
                                <option value=""><?= GetMessage("FILTER_CHOOSE_TITLE")?></option>
                                <?foreach ($arResult["OBJECTS"] as $object):?>
                                <option <?if ($object == $_REQUEST["HISTORY_FILTER"]["OBJECT"]) { echo "selected"; }?> value="<?= $object?>"><?= GetMessage($object)?></option>
                                <?endforeach?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
                        <div class="form-group">
                            <label for="HISTORY_FILTER[ACTION]"><?= GetMessage("FILTER_ACTION_TITLE")?></label>
                            <select class="form-control" id="HISTORY_FILTER[ACTION]" name="HISTORY_FILTER[ACTION]">
                                <option value=""><?= GetMessage("FILTER_CHOOSE_TITLE")?></option>
                                <?foreach ($arResult["ACTIONS"] as $action):?>
                                    <option <?if ($action == $_REQUEST["HISTORY_FILTER"]["ACTION"]) { echo "selected"; }?> value="<?= $action?>"><?= GetMessage($action)?></option>
                                <?endforeach?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
                        <div class="form-group">
                            <label for="HISTORY_FILTER[HELEMENT_ID]"><?= GetMessage("FILTER_ELEMENT_TITLE")?></label>
                            <input value="<?= htmlspecialchars($_REQUEST["HISTORY_FILTER"]["HELEMENT_ID"])?>" class="form-control" id="HISTORY_FILTER[HELEMENT_ID]" type="text" name="HISTORY_FILTER[HELEMENT_ID]">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
                        <div class="form-group">
                            <label for="HISTORY_FILTER[STORE_ID]"><?= GetMessage("FILTER_STORE_TITLE")?></label>
                            <input value="<?= htmlspecialchars($_REQUEST["HISTORY_FILTER"]["STORE_ID"])?>" class="form-control" id="HISTORY_FILTER[STORE_ID]" type="text" name="HISTORY_FILTER[ELEMENT_ID]">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
                        <div class="form-group">
                            <label for="HISTORY_FILTER[IP]"><?= GetMessage("FILTER_IP_TITLE")?></label>
                            <input value="<?= htmlspecialchars($_REQUEST["HISTORY_FILTER"]["IP"])?>" class="form-control" id="HISTORY_FILTER[IP]" type="text" name="HISTORY_FILTER[IP]">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
                        <div class="form-group">
                            <label for="HISTORY_FILTER[USER_ID]"><?= GetMessage("FILTER_USER_TITLE")?></label>
                            <input value="<?= htmlspecialchars($_REQUEST["HISTORY_FILTER"]["USER_ID"])?>" class="form-control" id="HISTORY_FILTER[USER_ID]" type="text" name="HISTORY_FILTER[USER_ID]">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
                        <div class="form-group has-feedback">
                            <label for="HISTORY_FILTER[DATE_FROM]"><?= GetMessage("FILTER_DATE_FROM_TITLE")?></label>
                            <input onclick="BX.calendar({node: this, field: this, bTime: true})" value="<?if (strlen($_REQUEST["HISTORY_FILTER"]["DATE_FROM"]) > 0) { echo htmlspecialchars($_REQUEST["HISTORY_FILTER"]["DATE_FROM"]); }?>" class="form-control" id="HISTORY_FILTER[DATE_FROM]" type="text" name="HISTORY_FILTER[DATE_FROM]">
                            <span class="glyphicon glyphicon-calendar form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
                        <div class="form-group has-feedback">
                            <label for="HISTORY_FILTER[DATE_TO]"><?= GetMessage("FILTER_DATE_TO_TITLE")?></label>
                            <input onclick="BX.calendar({node: this, field: this, bTime: true})" value="<?if (strlen($_REQUEST["HISTORY_FILTER"]["DATE_TO"]) > 0) { echo htmlspecialchars($_REQUEST["HISTORY_FILTER"]["DATE_TO"]); }?>" class="form-control" id="HISTORY_FILTER[DATE_TO]" type="text" name="HISTORY_FILTER[DATE_TO]">
                            <span class="glyphicon glyphicon-calendar form-control-feedback" aria-hidden="true"></span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" name="HISTORY_FILTER[SUBMIT]" value="submit" class="btn btn-primary"><?= GetMessage("FILTER_SUBMIT_TITLE")?></button>
                    <button type="submit" name="HISTORY_FILTER[RESET]" value="reset" class="btn btn-primary"><?= GetMessage("FILTER_CLEAR_TITLE")?></button>
                </div>
            </fieldset>
        </form>
    </div>
    
    <div class="table-area white-area">
        <h4><b><?= GetMessage("GRID_TITLE")?></b></h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th><?= GetMessage("GRID_OBJECT_TITLE")?></th>
                    <th><?= GetMessage("GRID_STORE_TITLE")?></th>
                    <th><?= GetMessage("GRID_ELEMENT_TITLE")?></th>
                    <th><?= GetMessage("GRID_ACTION_TITLE")?></th>
                    <th><?= GetMessage("GRID_USER_TITLE")?></th>
                    <th><?= GetMessage("GRID_IP_TITLE")?></th>
                    <th><?= GetMessage("GRID_DATE_TITLE")?></th>
                </tr>
            </thead>
            <tbody>
                <?foreach ($arResult["ROWS"] as $arRow):?>
                <tr>
                    <td><a title="<?= GetMessage("GRID_HOVER_TEXT")?>" href="?ID=<?= $arRow["ID"]?>"><?= $arRow["ID"]?></a></td>
                    <td><?= GetMessage($arRow["OBJECT"])?></td>
                    <td><?= $arRow["STORE"]?></td>
                    <td><?= $arRow["ELEMENT"]?></td>
                    <td><?= GetMessage($arRow["ACTION"])?></td>
                    <td><?= $arRow["USER"]?></td>
                    <td><?= $arRow["IP"]?></td>
                    <td><?= $arRow["DATE"]?></td>
                </tr>
                <?endforeach;?>
            </tbody>
        </table>
        <?
            $APPLICATION->IncludeComponent(
               "bitrix:main.pagenavigation",
               $arParams["PAGE_TEMPLATE"],
               array(
                  "NAV_OBJECT" => $arResult["NAV"],
                  "SEF_MODE" => "N",
               ),
               false
            );
        ?>
    </div>
    
    
    
</div>