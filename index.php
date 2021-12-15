<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $APPLICATION;
$APPLICATION->IncludeComponent(
    "mokin:address.list",
    "",
    [
        "SHOW_DEACTIVATED" => "N",
    ],
    false
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
