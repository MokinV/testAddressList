<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arResult
 * @global CMain $APPLICATION
 */
if (isset($arResult["COLUMNS"])) {
    $APPLICATION->IncludeComponent(
        'bitrix:main.ui.grid',
        '',
        [
            'GRID_ID' => $arResult["GRID_ID"],
            'COLUMNS' => $arResult["COLUMNS"],
            'ROWS' => $arResult["ROWS"],
            'FOOTER' => [
                'TOTAL_ROWS_COUNT' => $arResult["COUNT_TOTAL"],
            ],
            'SHOW_ROW_CHECKBOXES' => false,
            'NAV_OBJECT' => $arResult["NAV"],
            'AJAX_MODE' => 'Y',
            'PAGE_SIZES' => [
                ['NAME' => '5', 'VALUE' => '5'],
                ['NAME' => '10', 'VALUE' => '10'],
                ['NAME' => '20', 'VALUE' => '20'],
            ],
            'AJAX_OPTION_JUMP' => 'N',
            'SHOW_CHECK_ALL_CHECKBOXES' => false,
            'SHOW_ROW_ACTIONS_MENU' => false,
            'SHOW_GRID_SETTINGS_MENU' => false,
            'SHOW_NAVIGATION_PANEL' => true,
            'SHOW_PAGINATION' => true,
            'SHOW_SELECTED_COUNTER' => false,
            'SHOW_TOTAL_COUNTER' => true,
            'SHOW_PAGESIZE' => true,
            'SHOW_ACTION_PANEL' => false,
            'ALLOW_COLUMNS_SORT' => true,
            'ALLOW_COLUMNS_RESIZE' => true,
            'ALLOW_HORIZONTAL_SCROLL' => true,
            'ALLOW_SORT' => true,
            'ALLOW_PIN_HEADER' => true,
            'AJAX_OPTION_HISTORY' => 'N',
            'ACTION_PANEL' => [],
        ]
    );
    ?>
    <script>
        BX.ready(function () {
            var gridObject = BX.Main.gridManager.getById('<?= $arResult["GRID_ID"] ?>');
            if (gridObject.hasOwnProperty('instance')) {
                gridObject.instance.reloadTable(null, null);
            }
        })
    </script>
    <?php
}
?>

