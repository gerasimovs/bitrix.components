<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

class CCatalogSorting extends CBitrixComponent
{
    public function executeComponent()
    {
        $this->initializeParams();

        $this->arResult['COUNT_IN_SECTION'] = $this->getElementsCount();
        $this->arResult['MIN_COUNT'] = $this->getMinCount();
        $this->arResult['MAX_COUNT'] = $this->getMaxCount();
        $this->arResult['SHOW_ALL'] = $this->getShowAll();

        if ($this->startResultCache()) {
            $this->includeComponentTemplate();
        }

        return $this->setPageParams();
    }

    public function initializeParams()
    {
        $this->arParams['ELEMENT_SORT_FIELD'] = isset($this->arParams['ELEMENT_SORT_FIELD'])
            ? $this->arParams['ELEMENT_SORT_FIELD']
            : null
        ;

        $this->arParams['ELEMENT_SORT_ORDER'] = isset($this->arParams['ELEMENT_SORT_ORDER'])
            ? $this->arParams['ELEMENT_SORT_ORDER']
            : null
        ;

        $this->arParams['ELEMENT_SORT_FIELD2'] = isset($this->arParams['ELEMENT_SORT_FIELD2'])
            ? $this->arParams['ELEMENT_SORT_FIELD2']
            : null
        ;

        $this->arParams['ELEMENT_SORT_ORDER2'] = isset($this->arParams['ELEMENT_SORT_ORDER2'])
            ? $this->arParams['ELEMENT_SORT_ORDER2']
            : null
        ;

        $this->arParams["PAGE_ELEMENT_COUNT"] = isset($this->arParams["PAGE_ELEMENT_COUNT"])
            ? $this->arParams["PAGE_ELEMENT_COUNT"]
            : 20
        ;
    }

    /* Set page params */

    public function setPageParams()
    {
        $request = Application::getInstance()
            ->getContext()
            ->getRequest();

        $this->setSortParam(
            $request->getQuery('sort'), 
            $request->getQuery('order')
        );

        $this->arParams['PAGE_ELEMENT_COUNT'] = $this->setPerPageParam(
            $request->getQuery('count')
        );

        return $this->arParams;
    }

    public function setSortParam($sort, $order)
    {
        $sortParam = $this->arParams['ELEMENT_SORT_FIELD'];
        $orderParam = $this->arParams['ELEMENT_SORT_ORDER'];

        if ($sort) {
            if (strtolower($this->arParams['ELEMENT_SORT_FIELD']) == 'catalog_available') {
                $this->arParams['ELEMENT_SORT_FIELD2'] =& $sortParam;
                $this->arParams['ELEMENT_SORT_ORDER2'] =& $orderParam;
            } else {
                $this->arParams['ELEMENT_SORT_FIELD'] =& $sortParam;
                $this->arParams['ELEMENT_SORT_ORDER'] =& $orderParam;
            }

            switch ($sort) {
                case 'price':
                    $sortParam = 'catalog_PRICE_1';
                    $orderParam = 'asc,nulls';
                    break;
                
                case 'popular':
                    $sortParam = 'show_counter';
                    $orderParam = 'desc';
                    break;

                case 'name':
                    $sortParam = 'name';
                    $orderParam = 'asc';
                    break;

                case 'new':
                    $sortParam = 'created';
                    $orderParam = 'desc';
                    break;

                default:
                    @define('ERROR_404', 'Y');
                    break;
            }
        }

        return array(
            $sortParam,
            $orderParam
        );
    }

    public function setPerPageParam($count)
    {
        $countParam = $this->arParams["PAGE_ELEMENT_COUNT"];

        if ($count == 'view_all') {
            if ($this->getShowAll()) {
                $countParam = PHP_INT_MAX;
            } else {
                @define('ERROR_404', 'Y');
            }
        } elseif ($count) {
            if ($count > $this->getMinCount() && $count % $this->getMinCount() === 0) {
                $countParam = $count;
            } else {
                @define('ERROR_404', 'Y');
            }
        }

        return $countParam;
    }

    /* Get component params */

    public function getMinCount()
    {
        return isset($this->arParams['MIN_COUNT']) 
            ? $this->arParams['MIN_COUNT'] 
            : $this->arParams['PAGE_ELEMENT_COUNT']
        ;
    }

    public function getMaxCount()
    {
        $countParam = isset($this->arParams['MAX_COUNT']) 
            ? $this->arParams['MAX_COUNT'] 
            : 100
        ;

        return min($this->arResult['COUNT_IN_SECTION'], $countParam);
    }

    public function getShowAll()
    {
        return isset($this->arParams['SHOW_ALL']) 
            && $this->arParams['SHOW_ALL'] == true
        ;
    }

    public function getElementsCount()
    {
        Loader::includeModule('iblock');
        $arFilter = array_merge(
            $GLOBALS[$this->arParams['FILTER_NAME']] ?: array(),
            array(
                'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                'IBLOCK_ACTIVE' => 'Y',
                'ACTIVE' => 'Y',
                'GLOBAL_ACTIVE' => 'Y',
            )
        );

        if ($this->arParams['SECTION_ID'] > 0) {
            $arFilter['ID'] = $this->arParams['SECTION_ID'];
        } elseif (strlen($this->arParams['SECTION_CODE']) > 0) {
            $arFilter['=CODE'] = $this->arParams['SECTION_CODE'];
        }

        $sectionsRequest = CIBlockSection::GetList(
            array('ID' => 'ASC'),
            $arFilter,
            true,
            array('ELEMENT_CNT'),
            false
        );

        if ($section = $sectionsRequest->Fetch()) {
            return $section['ELEMENT_CNT'];
        } else {
            return 0;
        }
    }
}
