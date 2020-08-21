<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Pro6ka\Sitemap\ExampleTable;

Loc::loadMessages(__FILE__);

class pro6ka_sitemap extends CModule
{
    public function __construct()
    {
        $arModuleVersion = array();
        
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        
        $this->MODULE_ID = 'pro6ka.sitemap';
        $this->MODULE_NAME = Loc::getMessage('PRO6KA_sitemap_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('PRO6KA_sitemap_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('PRO6KA_sitemap_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'https://github.com/pro6ka';
    }

    public function doInstall()
    {
        if ($this->InstallFiles()) {
            ModuleManager::registerModule($this->MODULE_ID);
        } else {
            throw new \Bitrix\Main\SystemException('not installed');
        }
//        $this->installDB();
    }
    
    public function InstallFiles()
    {
        $modulePath = $this->getModulePath();
        if ($this->isLocal($modulePath)) {
            $copyTo = Application::getDocumentRoot() . '/local/components/';
        } else {
            $copyTo = Application::getDocumentRoot() . "/bitrix/components";
        }
        $copyResult = CopyDirFiles(
            $this->getModulePath() . "install/components/",
            $copyTo,
            true,
            true
        );
        return $copyResult;
    }
    
    public function doUninstall()
    {
        //$this->uninstallDB();
        ModuleManager::unRegisterModule($this->MODULE_ID);
        $unInstallFiles = $this->UnInstallFiles();
        return true;
    }
    
    public function UnInstallFiles()
    {
        $modulePath = $this->getModulePath();
        if ($this->isLocal($modulePath)) {
            $toRemove = Application::getDocumentRoot() . '/local/components/' . strtolower($this->PARTNER_NAME) . '/';
        } else {
            $toRemove = Application::getDocumentRoot() . "/bitrix/components/" . strtolower($this->PARTNER_NAME) . '/';
        }
        return \Bitrix\Main\IO\Directory::deleteDirectory($toRemove);
    }
    
    public function installDB()
    {
        try {
            if (Loader::includeModule($this->MODULE_ID)) {
                ExampleTable::getEntity()->createDbTable();
            }
        } catch (\Bitrix\Main\LoaderException $e) {
        }
    }

    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID))
        {
            $connection = Application::getInstance()->getConnection();
            $connection->dropTable(ExampleTable::getTableName());
        }
    }
    
    private function isLocal($modulePath = '') {
        if (! $modulePath) {
            $modulePath = $this->getModulePath();
        }
        return preg_match('~\/local\/~', $modulePath);
    }
    
    private function getModulePath()
    {
        return preg_replace('~install$~', '', __DIR__);
    }
}
