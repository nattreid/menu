<?php

declare(strict_types = 1);

namespace NAttreid\Menu\Breadcrumb;

use Nette\SmartObject;

/**
 * Trida linku navigace
 *
 * @property-read string $name
 * @property-read string $link
 * @property-read string $arguments
 * @property string $icon
 *
 * @author Attreid <attreid@gmail.com>
 */
class Link
{
	use SmartObject;

	/** @var string */
	private $name;

	/** @var string */
	private $link;

	/** @var array */
	private $arguments;

	/** @var string */
	private $icon;

	public function __construct(string $name, string $link = null, array $arguments = [])
	{
		$this->name = $name;
		$this->link = $link;
		$this->arguments = $arguments;
	}

	/**
	 * @return string|null
	 */
	protected function getLink()
	{
		return $this->link;
	}

	/**
	 * @return string
	 */
	protected function getArguments(): array
	{
		return $this->arguments;
	}

	/**
	 * @return string
	 */
	protected function getName(): String
	{
		return $this->name;
	}

	/**
	 * @return string|null
	 */
	protected function getIcon()
	{
		return $this->icon;
	}

	/**
	 * @param string $icon
	 * @return self
	 */
	protected function setIcon(string $icon): self
	{
		$this->icon = $icon;
		return $this;
	}
}