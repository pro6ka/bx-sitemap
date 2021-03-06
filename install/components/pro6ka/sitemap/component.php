<?php
    /**
     * @var array $arParams
     * @global CMain $APPLICATION
     */
    
    use \Bitrix\Main\Loader;
    use Bitrix\Main\Context;
    use \Pro6ka\Sitemap\Sitemap;
    use Bitrix\Main\Data\Cache;
    
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
    
    try {
        Loader::includeModule('pro6ka.sitemap');
    } catch (\Bitrix\Main\LoaderException $e) {
        echo $e->getMessage();
    }
    
    global $USER;
    $cacheTime = intval($arParams['CACHE_TIME']);
    $cacheId = implode('|', [
        Context::getCurrent()->getSite(),
        Context::getCurrent()->getRequest()->getRequestUri(),
        $USER->GetGroups(),
        'countItems',
    ]);
    foreach ($arParams as $param => $value) {
        if (strncmp('~', $param, 1)) {
            $cacheId .= ',' . $param . '=' . $value;
        }
    }
    $cacheDir = '/' . Context::getCurrent()->getSIte() . $this->GetRelativePath();
    
    $cache = Cache::createInstance();
    if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) {
        $itemsCount = $cache->getVars();
    } elseif ($cache->startDataCache()) {
        $itemsCount = Sitemap::countItems($arParams);
    } else {
        $itemsCount = 0;
    }
    
    if ($itemsCount > (int) $arParams['INDEX_LIMIT']) {
        $arDefaultUrlTemplates404 = [
            'html' => 'index.php',
            'index' => 'sitemap.xml',
            'urls' => 'sitemap-#IBLOCK_ID#.xml',
        ];
    } else {
        $arDefaultUrlTemplates404 = [
            'html' => 'index.php',
            'urls' => 'sitemap.xml',
        ];
    }
    
    $arDefaultVariableAliases404 = [];
    $arDefaultVariableAliases = [];
    $arComponentVariables = [];
    $SEF_FOLDER = '';
    $arUrlTemplates = [];
    
    if ($arParams['SEF_MODE'] == 'Y') {
        
        $arVariables = [];
        
        $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates(
            $arDefaultUrlTemplates404,
            $arParams['SEF_URL_TEMPLATES']
        );
        
        $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
            $arDefaultVariableAliases404,
            $arParams['VARIABLE_ALIASES']
        );
        
        $componentPage = CComponentEngine::ParseComponentPath(
            $arParams['SEF_FOLDER'],
            $arUrlTemplates,
            $arVariables
        );
        
        if (strlen($componentPage) <= 0) {
            $componentPage = 'index';
        }
        
        CComponentEngine::InitComponentVariables(
            $componentPage,
            $arComponentVariables,
            $arVariableAliases,
            $arVariables);
        
        $SEF_FOLDER = $arParams['SEF_FOLDER'];
    } else {
        $arVariables = [];
        
        $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
            $arDefaultVariableAliases,
            $arParams['VARIABLE_ALIASES']
        );
        
        CComponentEngine::InitComponentVariables(
            false,
            $arComponentVariables,
            $arVariableAliases,
            $arVariables
        );
        
        $componentPage = '';
        
        if (intval($arVariables['ELEMENT_ID']) > 0) {
            $componentPage = 'element';
        } else {
            $componentPage = 'html';
        }
        
    }
    
    $arResult = [
        'FOLDER' => $SEF_FOLDER,
        'URL_TEMPLATES' => $arUrlTemplates,
        'VARIABLES' => $arVariables,
        'ALIASES' => $arVariableAliases,
    ];
    
    $this->IncludeComponentTemplate($componentPage);
