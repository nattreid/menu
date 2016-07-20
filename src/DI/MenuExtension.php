<?php

namespace NAttreid\Menu\DI;

use NAttreid\Menu\Module\IMenuFactory as IModuleMenuFactory,
    NAttreid\Menu\Module\Menu as ModuleMenu,
    NAttreid\Menu\Menu\IMenuFactory,
    NAttreid\Menu\Menu\Menu,
    NAttreid\Menu\Breadcrumb\IBreadcrumb,
    NAttreid\Menu\Breadcrumb\Breadcrumb;

/**
 * Nastaveni Menu
 * 
 * @author Attreid <attreid@gmail.com>
 */
class MenuExtension extends \Nette\DI\CompilerExtension {

    /** @var array */
    private $defaults = [
        'items' => []
    ];

    public function loadConfiguration() {
        $config = $this->validateConfig($this->defaults, $this->config);

        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('moduleMenu'))
                ->setImplement(IModuleMenuFactory::class)
                ->setFactory(ModuleMenu::class)
                ->setArguments(['%namespace%'])
                ->addSetup('setMenu', [$config['items']]);

        $builder->addDefinition($this->prefix('menu'))
                ->setImplement(IMenuFactory::class)
                ->setFactory(Menu::class)
                ->setArguments(['%namespace%'])
                ->addSetup('setMenu', [$config['items']]);

        $builder->addDefinition($this->prefix('breadcrumb'))
                ->setImplement(IBreadcrumb::class)
                ->setFactory(Breadcrumb::class);
    }

}
