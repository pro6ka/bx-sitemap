<?php
    /**
     * @var array $arParams
     * @global CMain $APPLICATION
     */
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    
    $arDefaultUrlTemplates404 = [
        'html'    => 'index.php',
        'index' => 'sitemap.xml',
        'urls' => 'sitemap-#IBLOCK_ID#.xml',
    ];
    
    $arDefaultVariableAliases404 = [];
    $arDefaultVariableAliases    = [];
    $arComponentVariables        = [];
    $SEF_FOLDER                  = '';
    $arUrlTemplates              = [];
    
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
        d($componentPage);
        
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
        'FOLDER'        => $SEF_FOLDER,
        'URL_TEMPLATES' => $arUrlTemplates,
        'VARIABLES'     => $arVariables,
        'ALIASES'       => $arVariableAliases,
    ];
    
    $this->IncludeComponentTemplate($componentPage);