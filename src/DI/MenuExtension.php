<?php

namespace NAttreid\Menu\DI;

/**
 * Rozsireni Menu
 *
 * @author Attreid <attreid@gmail.com>
 */
class MenuExtension {

    /** @var array */
    private $defaults = [
        'items' => []
    ];

    public function loadConfiguration() {
        $config = $this->validateConfig($this->defaults, $this->config);

        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('menu'))
                ->setImplement(IMenuFactory::class)
                ->setFactory(Menu::class)
                ->addSetup('setMenu', [$config['items']]);
    }

}
