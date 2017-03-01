<?php

declare(strict_types = 1);

namespace NAttreid\Menu\Menu;

use Nette\InvalidStateException;
use Nette\SmartObject;
use Nette\Utils\Strings;

/**
 * Polozka Menu
 *
 * @property-read string $name
 * @property bool $allowed
 * @property-read bool $current
 * @property-read bool $group
 * @property-read Item $parent
 * @property-read Item[] $items
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class Item implements IParent
{

	use SmartObject,
		ItemTrait;

	/** @var string */
	private $name;

	/** @var IParent */
	private $parent;

	/** @var bool */
	protected $allowed = false;

	/** @var bool */
	private $current = false;

	/** @var string */
	private $namespace;

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	/**
	 * Nastavi namespace
	 * @param string $namespace
	 */
	public function setNamespace(string $namespace)
	{
		$this->namespace = $namespace;
	}

	/**
	 * Vrati namespace
	 * @return string
	 */
	public function getNamespace(): string
	{
		$namespace = ($this->namespace !== null ? $this->namespace . '.' : '') . $this->name;

		$parent = $this->parent;
		if ($parent instanceof Item) {
			$namespace = $this->parent->getNamespace() . '.' . $namespace;
		}
		return $namespace;
	}

	/**
	 * Vrati Menu
	 * @return Menu
	 */
	protected function getMenu(): Menu
	{
		$parent = $this->parent;
		if ($parent instanceof Menu) {
			return $parent;
		} elseif ($parent instanceof Item) {
			return $parent->getMenu();
		}
		throw new InvalidStateException();
	}

	/**
	 * Prida skupinu
	 * @param string $name
	 * @param int $position
	 * @return Group|Item
	 */
	public function addGroup(string $name, int $position = null): Item
	{
		return $this->addItem(new Group($name), $position);
	}

	/**
	 * Prida link
	 * @param string $name
	 * @param string $link
	 * @param array $arguments
	 * @param int $position
	 * @return Link
	 */
	public function addLink(string $name, string $link, array $arguments = [], int $position = null): Link
	{
		/* @var $item Link */
		$item = $this->addItem(new Link($name, $link, $arguments), $position);
		return $this->getMenu()->attachLink($item);
	}

	/**
	 * Nastavi rodice
	 * @param IParent $parent
	 */
	public function setParent(IParent $parent)
	{
		$this->parent = $parent;
	}

	/**
	 * @param bool $translate
	 * @return string
	 */
	protected function getName(bool $translate = true): string
	{
		$translator = $this->getMenu()->getTranslator();
		if ($translator !== null) {
			$message = $this->getNamespace() . '.title';
			if ($translate) {
				return $translator->translate($message);
			} else {
				return $message;
			}
		} else {
			return $this->name;
		}
	}

	/**
	 * Vrati link
	 * @param string $link
	 * @return string
	 */
	protected function createLink(string $link): string
	{
		$this->prepareLink($link);

		if ($this instanceof Link) {
			$link = Strings::firstUpper($this->name) . ':' . $link;
		}
		if ($this->namespace !== null) {
			$link = Strings::firstUpper($this->namespace) . ':' . $link;
		}

		$parent = $this->parent;
		if ($parent instanceof Menu) {
			return ':' . $link;
		} elseif ($parent instanceof Item) {
			return $parent->createLink($link);
		}
		throw new InvalidStateException();
	}

	/** @return bool */
	protected function isAllowed(): bool
	{
		return $this->allowed;
	}

	/** @return Item[] */
	public function getItems(): array
	{
		return $this->items;
	}

	/** @return bool */
	public function isCurrent(): bool
	{
		return $this->current;
	}

	/** @return bool */
	public abstract function isGroup(): bool;

	/**
	 * Linky (od aktualniho po vsechny rodice)
	 * @return Link[]
	 */
	public function getActualLinks(): array
	{
		$arr = [];
		if ($this instanceof Link) {
			$arr[] = $this;
		}
		$parent = $this->parent;
		if ($parent instanceof Item) {
			return array_merge($arr, $this->parent->getActualLinks());
		} else {
			return $arr;
		}
	}

	/**
	 * Nastavi jako aktivni i s rodici
	 */
	public function setCurrent()
	{
		$this->current = true;
		$parent = $this->parent;
		if ($parent instanceof Item) {
			$parent->setCurrent();
		}
	}

}
