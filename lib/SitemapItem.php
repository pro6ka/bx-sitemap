<?php
    
    
    namespace Pro6ka\Sitemap;
    
    
    class SitemapItem
    {
        public $URL = '';
        public $NAME = '';
        private $children = [];
        
        public function __construct($params)
        {
            foreach ($params as $prop => $value) {
                if (! preg_match('/^~/', $prop) && $value) {
                    $this->$prop = $value;
                }
            }
        }
        
        public function __get($prop) {
            return $this->$prop;
        }
        
        public function pushChild(SitemapItem $child) {
            $this->children[] = $child;
        }
    
        /**
         * @return array
         */
        public function getChildren(): array
        {
            return $this->children;
        }
    }