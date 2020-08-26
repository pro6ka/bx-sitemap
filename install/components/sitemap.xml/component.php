<?php
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
        die();
    }
    
    if ($this->StartResultCache()) {
        $arResult = $this->getResult();
        d($arResult);
        $this->IncludeComponentTemplate();
    }