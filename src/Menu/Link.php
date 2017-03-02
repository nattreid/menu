<?php

declare(strict_types = 1);

namespace NAttreid\Menu\Menu;

use Nette\InvalidArgumentException;
use Tracy\Debugger;

/**
 * Polozka Menu
 *
 * @property-read string $link
 * @property-read array $arguments
 * @property-read int $count
 * @property-read string $type
 * @property-read bool $toBlank
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
	private $count = 0;

	/** @var string */
	private $type;

	/** @var bool */
	private $toBlank = false;

	public function __construct(string $name, string $link = null, array $arguments = [])
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
			$parent->allowed = $this->allowed || $parent->allowed;
		}
	}

	/** @return string */
	protected function getLink(): string
	{
		return $this->createLink($this->link);
	}

	/** @return int */
	protected function getCount(): int
	{
		return $this->count;
	}

	/** @return string|null */
	public function getType()
	{
		return $this->type;
	}

	/** @return bool */
	public function getToBlank(): bool
	{
		return $this->toBlank;
	}

	/** @return array */
	public function getArguments(): array
	{
		return $this->arguments;
	}

	/**
	 * Nastavi pocet (cislo za text linku)
	 * @param int $count
	 * @param string $type
	 */
	public function setCount(int $count, string $type = self::INFO)
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
	public function isGroup(): bool
	{
		return false;
	}

}
