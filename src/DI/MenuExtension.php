<?php

declare(strict_types = 1);

namespace NAttreid\Menu\DI;

use NAttreid\Menu\Menu\IMenuFactory;
use NAttreid\Menu\Menu\Menu;
use Nette\DI\CompilerExtension;

/**
 * Rozsireni Menu
 *
 * @author Attreid <attreid@gmail.com>
 */
class MenuExtension extends CompilerExtension
{

	/** @var array */
	private $defaults = [
		'items' => []
	];

	public function loadConfiguration()
	{
		$config = $this->validateConfig($this->defaults, $this->config);

		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('menu'))
			->setImplement(IMenuFactory::class)
			->setFactory(Menu::class)
			->addSetup('setMenu', [$config['items']]);
	}

}
