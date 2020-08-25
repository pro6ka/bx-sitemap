<?php
    
    
    namespace Pro6ka\Sitemap;

    use \Bitrix\Main\Loader;
    use \Bitrix\Main\Context;
    use Bitrix\Main\LoaderException;

    defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();
    
    class Sitemap
    {
        private $menu = null;
        private $iBlock = null;
        private $arParams = [];
        private $rootSections = [];
    
        private static $instance = null;
    
        /**
         * Sitemap constructor.
         *
         * @param $arParams
         * @throws LoaderException
         */
        
        private function __construct($arParams)
        {
            $this->arParams = $arParams;
            $this->menu = new MenuSitemap($arParams['MENU_TYPE']);
            $this->iBlock = new IBlockSitemap($arParams['IBLOCK_ID']);
        }
        
        public function htmlSort() : array
        {
            $result = [];
            foreach ($this->menu->getItems() as $menuItem) {
                $result[] = $menuItem;
            }
            foreach ($this->arParams['STATIC'] as $staticItem) {
                $result[] = new SitemapItem($staticItem);
            }
            foreach ($this->iBlock->getItems() as $item) {
                if ($item->IS_SECTION) {
                    $this->putSection($item, $result);
                } elseif ($item->IS_ELEMENT) {
                    $this->putElement($item, $result);
                }
            }
            d($result);
            return $result;
        }
        
        private function putSection(SitemapItem $section, &$result) : void
        {
            /** @var SitemapItem $resultItem */
            foreach ($result as $key => $resultItem) {
                if (preg_match('~^' . $resultItem->URL . '~', $section->URL)) {
                    $itemElement = $result[$key];
                    if (! $section->IBLOCK_SECTION_ID) {
                        $itemElement->pushChild($section);
                        break;
                    } else {
                    
                    }
                    break;
                }
            }
        }
    
        private function putElement(SitemapItem $element, &$result) : void
        {
        
        }
    
        private function getLastMod(SitemapItem $element)
        {
            /** TODO: make lastmod from shestpa.lastmodified module */
            return date('Y-m-d', time());
        }
        
        public static function getInstance($arParams = [])
        {
            if (! self::$instance) {
                self::$instance = new self($arParams);
            }
            return self::$instance;
        }
        
        public static function countItems($arParams)
        {
            $instance = static::getInstance($arParams);
            return count($instance->menu->getItems()) + $instance->iBlock->getItemsCount();
        }
    }