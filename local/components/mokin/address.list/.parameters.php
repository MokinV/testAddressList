<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

$arComponentParameters = [
    "PARAMETERS" => [
        "SHOW_DEACTIVATED" => [
            "PARENT" => "BASE",
            "NAME" => Loc::getMessage('SHOW_DEACTIVATED'),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y"
        ]
    ]
];
