<?php
    /**
     * @var array $arResult
     * @var array $arParams
     * @var SitemapHtmlComponent $component
     * @var CBitrixComponentTemplate $this
     * @global CMain $APPLICATION
     */
    
    $previousLevel = 0;
?>
<div class="b-sitemap">
    <ul class="b-sitemap__container">
        <?php foreach ($arResult as $item) { ?>
            <?php if ($previousLevel && $item->DEPTH_LEVEL < $previousLevel) { ?>
                <?= str_repeat('</ul></li>', ($previousLevel - $item->DEPTH_LEVEL)); ?>
            <?php } ?>
            <li class="b-sitemap__item">
                <?php if ($item->children) { ?>
                    <a href="<?= $item->URL ?>" class="b-sitemap__link"><?= $item->NAME ?></a>
                    <?= '<ul class="b-sitemap__sub">' ?>
                <?php } else { ?>
                    <a href="<?= $item->URL ?>" class="b-sitemap__link"><?= $item->NAME ?></a>
                <?php } ?>
            </li>
            <?php $previousLevel = $item->DEPTH_LEVEL ?>
        <?php } ?>
        <?php if ($previousLevel > 1) { ?>
            <?= str_repeat('</ul></li>', ($previousLevel - 1)); ?>
        <?php } ?>
    </ul>
</div>
