<?php
    
    class SitemapXmlComponent extends CBitrixComponent
    {
        public function __construct($component = null)
        {
            parent::__construct($component);
        
            try {
                $this->siteMap = \Pro6ka\Sitemap\Sitemap::getInstance($this->arParams);
            } catch (\Bitrix\Main\LoaderException $e) {
                echo $e->getMessage();
            }
        }
        
        public function getResult()
        {
            return $this->siteMap->getXml();
        }
    }