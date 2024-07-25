<?php declare(strict_types=1);

namespace MelasistemaMultiSearch;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class MelasistemaMultiSearch extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        //
    }
    public function activate(ActivateContext $activateContext): void
    {
        //
    }
    public function deactivate(DeactivateContext $deactivateContext): void
    {
        //
    }
    public function uninstall(UninstallContext $uninstallContext): void
    {
        if(!$uninstallContext->keepUserData())
        {
            //
        }
    }
}