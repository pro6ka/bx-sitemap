<?php
    
    
    namespace Pro6ka\Sitemap;
    
    
    class MenuSitemap
    {
        private $items = [];
        
        public function __construct($paramsMenu)
        {
            $this->items = $this->setMenu($paramsMenu);
        }
        
        private function setMenu($paramsMenu)
        {
            global $APPLICATION;
            $result = [];
            foreach ($paramsMenu as $menuType) {
                $menu = $APPLICATION->GetMenu($menuType);
                foreach ($menu->arMenu as $menuItem) {
                    $url = preg_replace('~index\.php$~', '', $menuItem[1]);
                    if (! array_key_exists($url, $this->items)) {
                        $result[$url] = new SitemapItem([
                            'NAME' => $menuItem[0],
                            'URL' => $url,
                        ]);
                    }
                }
            }
            return $result;
        }
    
        /**
         * @return array
         */
        public function getItems(): array
        {
            return $this->items;
        }
    }