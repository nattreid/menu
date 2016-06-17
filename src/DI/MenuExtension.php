<?php

namespace NAttreid\Menu\DI;

/**
 * Nastaveni Menu
 * 
 * @author Attreid <attreid@gmail.com>
 */
class MenuExtension extends \Nette\DI\CompilerExtension {

    private $default = [
        'items' => []
    ];

    public function loadConfiguration() {
        $config = $this->getConfig($this->default);

        $builder = $this->getContainerBuilder();

        $module = $builder->addDefinition($this->prefix('moduleMenu'))
                ->setImplement('\NAttreid\Menu\Module\IMenuFactory')
                ->setFactory('\NAttreid\Menu\Module\Menu')
                ->setArguments(['%namespace%'])
                ->setAutowired(TRUE);
        $module->addSetup('setMenu', [$config['items']]);

        $menu = $builder->addDefinition($this->prefix('menu'))
                ->setImplement('\NAttreid\Menu\Menu\IMenuFactory')
                ->setFactory('\NAttreid\Menu\Menu\Menu')
                ->setArguments(['%namespace%'])
                ->setAutowired(TRUE);
        $menu->addSetup('setMenu', [$config['items']]);

        $builder->addDefinition($this->prefix('breadcrumb'))
                ->setImplement('\NAttreid\Menu\Breadcrumb\IBreadcrumb')
                ->setFactory('\NAttreid\Menu\Breadcrumb\Breadcrumb')
                ->setAutowired(TRUE);
    }

}
