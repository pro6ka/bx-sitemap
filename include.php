<?php
    defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
    
    use Bitrix\Main\Loader;
    use Bitrix\Main\EventManager;
    
    Loader::registerAutoLoadClasses('pro6ka.sitemap', array(
        'Pro6ka\Sitemap\Sitemap' => 'lib/Sitemap.php',
        'Pro6ka\Sitemap\MenuSitemap' => 'lib/MenuSitemap.php',
    ));
