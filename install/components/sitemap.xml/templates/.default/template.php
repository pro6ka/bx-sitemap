<?php
    /**
     * @var array $arResult
     * @var array $arParams
     * @var CBitrixComponentTemplate $this
     * @var CBitrixComponent $component
     * @global CMain $APPLICATION
     */
    
    header('Content-type: application/xml');
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
    $APPLICATION->RestartBuffer();
?>
<?= '<?xml version="1.0" encoding="utf-8"?>' . "\n" ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($arResult as $item) { ?>
        <url>
            <loc><?= $item->URL ?></loc>
            <lastmod><?= $item->LAST_MODIFIED ?></lastmod>
            <changefreq><?= $item->CHANGEFREQ ?></changefreq>
            <priority><?= $item->PRIORITY ?></priority>
        </url>
    <?php } ?>
</urlset>
<?php
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
    die;
