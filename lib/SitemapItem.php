<?php
    
    
    namespace Pro6ka\Sitemap;
    
    
    class SitemapItem
    {
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
    }