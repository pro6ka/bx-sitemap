<?php
    /**
     * @var CBitrixComponent $component
     * @var array $arParams
     * @var array $arResult
     * @global CMain $APPLICATION
     */
    
    if (!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true) {
        die();
    }
    
    $APPLICATION->IncludeComponent(
        'pro6ka:sitemap.html',
        '',
        [
            "IBLOCK_ID" => $arParams['IBLOCK_ID'],
            "MENU_TYPE" => $arParams['IBLOCK_ID'],
            "COMPOSITE_FRAME_MODE" => $arParams['COMPOSITE_FRAME_MODE'],
            "COMPOSITE_FRAME_TYPE" => $arParams['COMPOSITE_FRAME_TYPE'],
            "CACHE_TYPE" => $arParams['CACHE_TYPE'],
            "CACHE_TIME" => $arParams['CACHE_TIME'],
            "HTML_PAGE_SIZE" => $arParams['HTML_PAGE_SIZE'],
        ],
        $component
    );
