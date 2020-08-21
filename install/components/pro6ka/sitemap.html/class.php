<?php
    
    class SitemapHtmlComponent extends CBitrixComponent
    {
        private $siteMap = null;
        
        public function __construct($component = null)
        {
            parent::__construct($component);
            
            $this->siteMap = \Pro6ka\Sitemap\Sitemap::getInstance($this->arParams);
        }
    
        public function getResult()
        {
            return $this->siteMap->htmlSort();
        }
    }