<?php
    
    class SitemapHtmlComponent extends CBitrixComponent
    {
        private $siteMap = null;
        
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
            return $this->plainRecursive($this->siteMap->htmlSort());
        }
        
        public function plainRecursive($data, &$result = [], $withChildren = false)
        {
            foreach ($data as $item) {
                $result[] = $item;
                if ($item->children) {
                    $this->plainRecursive($item->children, $result, true);
                }
            }
            return $result;
        }
    }