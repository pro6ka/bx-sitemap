<?php
    
    
    namespace Pro6ka\Sitemap;

    use \Bitrix\Main\Loader;
    use \Bitrix\Main\Context;
    use Bitrix\Main\LoaderException;

    defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

    /**
     * Class Sitemap
     *
     * @package Pro6ka\Sitemap
     */
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
        
        public function getXml() : array
        {
            $result = [];
            foreach ($this->menu->getItems() as $menuItem) {
                $menuItem->URL = $this->getFullUrl($menuItem);
                $menuItem->DEPTH_LEVEL = 0;
                $menuItem->LAST_MODIFIED = $this->getLastMod($menuItem);
                $menuItem->CHANGEFREQ = $this->getChangeFreq($menuItem);
                $menuItem->PRIORITY = $this->getPriority($menuItem);
                $result[] = $menuItem;
            }
            foreach ($this->arParams['STATIC'] as $staticItem) {
                $menuItem = new SitemapItem($staticItem);
                $menuItem->URL = $this->getFullUrl($menuItem);
                $menuItem->DEPTH_LEVEL = 0;
                $menuItem->LAST_MODIFIED = $this->getLastMod($menuItem);
                $menuItem->CHANGEFREQ = $this->getChangeFreq($menuItem);
                $menuItem->PRIORITY = $this->getPriority($menuItem);
                $result[] = $menuItem;
            }
            foreach ($this->iBlock->getItems() as $item) {
                $item->URL = $this->getFullUrl($item);
                $item->LAST_MODIFIED = $this->getLastMod($item);
                $item->URL = $menuItem->URL . Context::getCurrent()->getRequest()->getHttpHost();
                $item->CHANGEFREQ = $this->getChangeFreq($item);
                $item->PRIORITY = $this->getPriority($item);
                $result[] = $item;
            }
            return $result;
        }
        
        private function getFullUrl($element)
        {
            $request = Context::getCurrent()->getRequest();
            $protocol = $request->isHttps() ? 'https://' : 'http://';
            $host = $request->getHttpHost();
            return $protocol . $host . $element->URL;
        }
    
        /**
         * @return array
         */
        public function htmlSort() : array
        {
            $result = [];
            foreach ($this->menu->getItems() as $menuItem) {
                $menuItem->DEPTH_LEVEL = 0;
                $result[] = $menuItem;
            }
            foreach ($this->arParams['STATIC'] as $staticItem) {
                $menuItem = new SitemapItem($staticItem);
                $menuItem->DEPTH_LEVEL = 0;
                $result[] = $menuItem;
            }
            foreach ($this->iBlock->getItems() as $item) {
                if ($this->arParams['HTML_NEED_ELEMENTS'] == 'Y') {
                    if ($item->IS_SECTION) {
                        $this->putSection($item, $result);
                    } elseif ($item->IS_ELEMENT) {
                        if ($this->arParams['HTML_INCLUDE_ELEMENTS_IBLOCK']) {
                            if (in_array($item->IBLOCK_ID, $this->arParams['HTML_INCLUDE_ELEMENTS_IBLOCK'])) {
                                $this->putElement($item, $result);
                            }
                        } else {
                            $this->putElement($item, $result);
                        }
                    }
                } else {
                    if ($item->IS_SECTION) {
                        $this->putSection($item, $result);
                    }
                }
            }
            return $result;
        }
    
        /**
         * @param SitemapItem $section
         * @param $result
         */
        private function putSection(SitemapItem $section, &$result) : void
        {
            /** @var SitemapItem $resultItem */
            foreach ($result as $key => $resultItem) {
                if (preg_match('~^' . $resultItem->URL . '~', $section->URL)) {
                    $itemElement = $result[$key];
                    if (! $section->IBLOCK_SECTION_ID) {
                        $section->DEPTH_LEVEL = $itemElement->DEPTH_LEVEL + 1;
                        $itemElement->pushChild($section);
                        break;
                    } else {
                        $this->section2Section($section, $result);
                    }
                    break;
                }
            }
        }
    
        /**
         * @param SitemapItem $section
         * @param $result
         * @param array $sectionChildren
         */
        private function section2Section(SitemapItem $section, &$result, $sectionChildren = []) : void
        {
            if (! $sectionChildren) {
                foreach ($result as $key => $resultItem) {
                    $itemElement = $result[$key];
                    if ($itemElement->children) {
                        foreach ($itemElement->children as $childKey => $child) {
                            if ($child->ID == $section->IBLOCK_SECTION_ID) {
                                $child->pushChild($section);
                                break 2;
                            } elseif ($child->children) {
                                $this->section2Section($section, $result, $child->children);
                            }
                        }
                    }
                }
            } else {
                foreach ($sectionChildren as $childKey => $child) {
                    if ($child->ID == $section->IBLOCK_SECTION_ID) {
                        $child->pushChild($section);
                        return;
                    } elseif ($child->children) {
                        $this->section2Section($section, $result, $child->children);
                    }
                }
            }
        }
    
        /**
         * @param SitemapItem $element
         * @param $result
         * @param SitemapItem|null $sectionChildren
         */
        private function putElement(SitemapItem $element, &$result, SitemapItem $sectionChildren = null) : void
        {
            if (! $sectionChildren) {
                if ($element->IBLOCK_SECTION_ID) {
                    /** @var  SitemapItem $resultItem */
                    foreach ($result as $key => $resultItem) {
                        if ($resultItem->ID == $element->IBLOCK_SECTION_ID) {
                            $element->DEPTH_LEVEL = $resultItem->DEPTH_LEVEL + 1;
                            $resultItem->pushChild($element);
                        } elseif ($resultItem->children) {
                            $this->putElement($element, $result, $resultItem);
                        }
                    }
                } else {
                    /** @var  SitemapItem $resultItem */
                    foreach ($result as $key => $resultItem) {
                        if (preg_match('~^' . $resultItem->URL . '~', $element->URL)) {
                            $element->DEPTH_LEVEL = $resultItem->DEPTH_LEVEL + 1;
                            $resultItem->pushChild($element);
                            break;
                        }
                    }
                }
            } else {
                /** @var  SitemapItem $child */
                foreach ($sectionChildren->getChildren() as $childKey => $child) {
                    if ($child->ID == $element->IBLOCK_SECTION_ID) {
                        $element->DEPTH_LEVEL = $child->DEPTH_LEVEL + 1;
                        $child->pushChild($element);
                        break;
                    } elseif ($child->getChildren()) {
                        $this->putElement($element, $result, $child);
                    }
                }
            }
        }
    
        /**
         * @param SitemapItem $element
         * @return string
         */
        private function getLastMod(SitemapItem $element) : string
        {
            /** TODO: make lastmod from shestpa.lastmodified module */
            return date('Y-m-d', time());
        }
    
        /**
         * @param SitemapItem $element
         * @return string
         */
        private function getPriority(SitemapItem $element) : float
        {
            /** TODO: create priority calculation */
            return 0.5;
        }
    
        /**
         * @param SitemapItem $element
         * @return string
         */
        private function getChangeFreq(SitemapItem $element) : string
        {
            /** TODO: create changefreq calculation */
            return 'daily';
        }
    
        /**
         * @param array $arParams
         * @return Sitemap
         * @throws LoaderException
         */
        public static function getInstance($arParams = []) : Sitemap
        {
            if (! self::$instance) {
                self::$instance = new self($arParams);
            }
            return self::$instance;
        }
    
        /**
         * @param $arParams
         * @return int
         * @throws LoaderException
         */
        public static function countItems($arParams) : int
        {
            $instance = static::getInstance($arParams);
            return count($instance->menu->getItems()) + $instance->iBlock->getItemsCount();
        }
    }