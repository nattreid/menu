<?php

namespace NAttreid\Menu\Menu;

use Nette\InvalidArgumentException;

/**
 * Polozka Menu
 *
 * @property-read string $link
 * @property-read array $arguments
 * @property-read int $count
 * @property-read string $type
 * @property-read boolean $toBlank
 *
 * @author Attreid <attreid@gmail.com>
 */
class Link extends Item
{

	const
		INFO = 'info',
		WARNING = 'warning';

	/** @var string */
	private $link;

	/** @var array */
	private $arguments;

	/** @var int */
	private $count;

	/** @var string */
	private $type;

	/** @var boolean */
	private $toBlank = false;

	public function __construct($name, $link, array $arguments = [])
	{
		parent::__construct($name);
		$this->link = $link;
		$this->arguments = $arguments;
	}

	/**
	 * {@inheritdoc }
	 */
	public function setParent(IParent $parent)
	{
		parent::setParent($parent);
		$namespace = $this->getNamespace();
		$this->allowed = $this->getMenu()->isAllowed($namespace, $namespace . '.title');

		if ($parent instanceof Group) {
			$parent->allowed |= $this->allowed;
		}
	}

	/** @return string */
	public function getLink()
	{
		return $this->createLink($this->link);
	}

	/** @return int */
	public function getCount()
	{
		return $this->count;
	}

	/** @return string */
	public function getType()
	{
		return $this->type;
	}

	/** @return boolean */
	public function getToBlank()
	{
		return $this->toBlank;
	}

	/** @return array */
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * Nastavi pocet (cislo za text linku)
	 * @param int $count
	 * @param string $type
	 */
	public function setCount($count, $type = self::INFO)
	{
		if ($count > 0) {
			switch ($type) {
				default:
					throw new InvalidArgumentException("Wrong argument 'type'");
				case self::INFO:
				case self::WARNING:
					break;
			}
			$this->count = $count;
			$this->type = $type;
		}
	}

	/**
	 * Nastavi link do noveho okna
	 */
	public function toBlank()
	{
		$this->toBlank = true;
	}

	/**
	 * {@inheritdoc }
	 */
	public function isGroup()
	{
		return false;
	}

}
