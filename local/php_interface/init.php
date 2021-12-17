<?php

\CBitrixComponent::includeComponentClass("mokin:address.list");
$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('', 'ListAddressesOnBeforeUpdate', [\AddressList::class, "OnBeforeUpdate"]);
$eventManager->addEventHandler('', 'ListAddressesOnAfterAdd', [\AddressList::class, "OnAfterAdd"]);
$eventManager->addEventHandler('', 'ListAddressesOnBeforeDelete', [\AddressList::class, "OnBeforeDelete"]);
