<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Query;
use \Bitrix\Highloadblock\HighloadBlockTable;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\SystemException;
use \Bitrix\Main\Engine\CurrentUser;
use \Bitrix\Main\ORM\Entity;
use \Bitrix\Main\Grid\Options as GridOptions;
use \Bitrix\Main\UI\PageNavigation;

class AddressList extends \CBitrixComponent
{
    const TABLE_NAME = "b_list_addresses";
    const GRID_ID = "ADDRESS_LIST";
    const TTL = 3600;

    const P_USER = "UF_USER_ID";
    const P_ADDRESS = "UF_ADDRESS";
    const P_ACTIVE = "UF_ACTIVE";

    protected int $idHlBlock = 0;
    protected Entity $entityHlBlock;
    protected int $idUser = 0;

    /**
     * @param CBitrixComponent|NULL $component
     */
    public function __construct(CBitrixComponent $component = NULL)
    {
        parent::__construct($component);

        $this->idUser = intval(CurrentUser::get()->getId());
    }

    /**
     * @return void
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     */
    protected function getResult()
    {
        $arResult = [];
        $arResult["GRID_ID"] = self::GRID_ID;

        $arParams = $this->arParams;

        $gridOptions = new GridOptions($arResult["GRID_ID"]);
        $navParams = $gridOptions->GetNavParams();
        $sort = $gridOptions->getSorting();
        $arResult["NAV"] = new PageNavigation($arResult["GRID_ID"]);
        $arResult["NAV"]->allowAllRecords(true)
            ->setPageSize($navParams['nPageSize'])
            ->initFromUri();

        if ($arResult["NAV"]->allRecordsShown()) {
            $navParams = false;
        } else {
            $navParams['iNumPage'] = $arResult["NAV"]->getCurrentPage();
        }

        $query = new Query($this->entityHlBlock);
        $arFilter = [
            self::P_USER => $this->idUser
        ];
        if ($arParams["SHOW_DEACTIVATED"] !== "Y") {
            $arFilter[self::P_ACTIVE] = true;
        }
        $query->setFilter($arFilter);
        $query->setSelect(["ID", self::P_ADDRESS]);
        $query->setCacheTtl(self::TTL);
        $query->setLimit($navParams['nPageSize']);
        $query->setOffset($navParams['iNumPage'] - 1);
        if (isset($sort["sort"])) {
            $query->setOrder($sort["sort"]);
        }
        $arResult["COUNT_TOTAL"] = $query->queryCountTotal();
        $arResult["NAV"]->setRecordCount($arResult["COUNT_TOTAL"]);
        $query->exec();

        foreach ($query->fetchAll() as $item) {
            $arResult["ROWS"][] = [
                "data" => $item
            ];
        };
        if (is_array($arResult["ROWS"]) and count($arResult["ROWS"])) {
            $sort = 1;
            foreach (array_keys(current($arResult["ROWS"])["data"]) as $key) {
                $name = Loc::getMessage("COLUMN_$key");
                $arResult["COLUMNS"][] = [
                    "id" => $key,
                    "name" => $name,
                    "content" => $name,
                    "title" => $name,
                    "sort" => $key,
                    "column_sort" => $sort,
                    "default" => true
                ];
                $sort++;
            }
        }

        $this->arResult = $arResult;
        $this->SetResultCacheKeys(
            [
                'COLUMNS',
                'COUNT_TOTAL',
                'GRID_ID',
                'ROWS',
                'NAV'
            ]
        );
    }

    /**
     * Установлен ли модуль highloadblock
     * @return void
     * @throws SystemException
     * @throws \Bitrix\Main\LoaderException
     */
    protected function checkModules()
    {
        if (!Loader::includeModule('highloadblock')) {
            $this->AbortResultCache();
            throw new SystemException(Loc::getMessage('MODULE_NOT_INCLUDED', ['#MODULE#' => 'highloadblock']));
        }
    }

    /**
     * Есть ли highloadblock, проверка по имени таблицы b_list_addresses
     * @return void
     * @throws SystemException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     */
    protected function checkHighLoadBlock()
    {
        $query = new Query(HighloadBlockTable::getEntity());
        $query->setFilter(["TABLE_NAME" => self::TABLE_NAME]);
        $query->setSelect(["*"]);
        $query->exec();
        $arHighLoadBlock = $query->fetch();
        if (empty($arHighLoadBlock)) {
            $this->AbortResultCache();
            throw new SystemException(Loc::getMessage('HLBLOCK_404'));
        }
        $this->idHlBlock = $arHighLoadBlock["ID"];
        $this->entityHlBlock = HighloadBlockTable::compileEntity($arHighLoadBlock);
    }

    /**
     * @return mixed|void|null
     * @throws \Bitrix\Main\LoaderException
     */
    public function executeComponent()
    {
        try {
            if ($this->idUser) {
                if ($this->StartResultCache(false, $this->idUser)) {
                    $this->checkModules();
                    $this->checkHighLoadBlock();
                    $this->getResult();
                }

                $this->includeComponentTemplate();
            } else {
                $this->AbortResultCache();
            }
        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }
}
