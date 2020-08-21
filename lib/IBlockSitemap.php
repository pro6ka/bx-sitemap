<?php
    
    
    namespace Pro6ka\Sitemap;
    
    use \Bitrix\Main\Loader;
    use Bitrix\Main\LoaderException;

    class IBlockSitemap
    {
        private $items = [];
        private $itemsCount = 0;
        private $iBlockList = [];
    
        /**
         * IBlockSitemap constructor.
         *
         * @param $iBlockList
         * @throws LoaderException
         */
        public function __construct($iBlockList)
        {
            Loader::includeModule('iblock');
            $this->iBlockList = $iBlockList;
        }
    
        /**
         * @return int
         */
        public function getItemsCount(): int
        {
            if (! $this->items) {
                $this->setItems();
            }
            return count($this->items);
        }
    
        private function setItems()
        {
            foreach ($this->iBlockList as $iBlockId) {
                $obSections = \CIBlockSection::GetTreeList(
                    [
                        'IBLOCK_ID' => $iBlockId,
                        'ACTIVE' => 'Y',
                        'GLOBAL_ACTIVE' => 'Y',
                        'CHECK_PERMISSIONS' => 'Y',
                    ],
                    ['ID', 'IBLOCK_ID', 'NAME', 'SECTION_PAGE_URL', 'DEPTH_LEVEL', 'IBLOCK_SECTION_ID']
                );
                while ($section = $obSections->GetNext()) {
                    $section['FROM_IBLOCK'] = true;
                    $section['IS_SECTION'] = true;
                    $element['URL'] = $section['SECTION_PAGE_URL'];
                    $this->itemsCount += 1;
                    $this->items[] = new SitemapItem($section);
                    $this->setElements($section['IBLOCK_ID'], $section['ID']);
                }
            }
        }
        
        private function setElements($iBlocKId, $sectionId)
        {
            $obElements = \CIBlockElement::GetList(
                ['SORT' => 'ASC'],
                [
                    'IBLOCK_ID' => $iBlocKId,
                    'SECTION_ID' => $sectionId,
                    'ACTIVE' => 'Y',
                    'GLOBAL_ACTIVE' => 'Y',
                    'CHECK_PERMISSIONS' => 'Y',
                    'ACTIVE_DATE' => 'Y',
                ],
                false,
                false,
                ['ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL', 'IBLOCK_SECTION_ID']
            );
            while ($element = $obElements->GetNext()) {
                $element['FROM_IBLOCK'] = true;
                $element['IS_ELEMENT'] = true;
                $element['URL'] = $element['DETAIL_PAGE_URL'];
                $this->itemsCount += 1;
                $this->items[] = new SitemapItem($element);
            }
        }
    
        /**
         * @return array
         */
        public function getItems(): array
        {
            return $this->items;
        }
    }