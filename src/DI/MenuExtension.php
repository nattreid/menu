<?php

namespace NAttreid\Menu\DI;

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
                ->setImplement('\NAttreid\Menu\Module\IMenuFactory')
                ->setFactory('\NAttreid\Menu\Module\Menu')
                ->setArguments(['%namespace%'])
                ->addSetup('setMenu', [$config['items']]);

        $builder->addDefinition($this->prefix('menu'))
                ->setImplement('\NAttreid\Menu\Menu\IMenuFactory')
                ->setFactory('\NAttreid\Menu\Menu\Menu')
                ->setArguments(['%namespace%'])
                ->addSetup('setMenu', [$config['items']]);

        $builder->addDefinition($this->prefix('breadcrumb'))
                ->setImplement('\NAttreid\Menu\Breadcrumb\IBreadcrumb')
                ->setFactory('\NAttreid\Menu\Breadcrumb\Breadcrumb');
    }

}
