<?php

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

	public function __construct($name, $link, array $arguments = [])
	{
		$this->name = $name;
		$this->link = $link;
		$this->arguments = $arguments;
	}

	/**
	 * @return string
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * @return string
	 */
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->icon;
	}

	/**
	 * @param string $icon
	 * @return self
	 */
	public function setIcon($icon)
	{
		$this->icon = $icon;
		return $this;
	}
}