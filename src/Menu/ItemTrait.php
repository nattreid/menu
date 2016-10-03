<?php

namespace NAttreid\Menu\Menu;

use NAttreid\Utils\Arrays;
use Nette\Utils\Strings;

/**
 *
 *
 * @author Attreid <attreid@gmail.com>
 */
trait ItemTrait
{

	/** @var Item[] */
	protected $items = [];

	/**
	 * Prida polozku
	 * @param Item $item
	 * @param int $position
	 * @return Item
	 */
	protected function addItem(Item $item, $position)
	{
		/* @var $this IParent */
		$item->setParent($this);
		if ($position !== null) {
			Arrays::slice($this->items, $position, $item);
		} else {
			$this->items[] = $item;
		}
		return $item;
	}

	/**
	 * Upravi link
	 * @param string $link
	 */
	protected function prepareLink(&$link)
	{
		if (Strings::endsWith($link, ':default')) {
			$link = substr($link, 0, -7);
		}
		$pos = strrpos($link, ':');
		if ($pos !== count($link)) {
			$link = substr($link, 0, $pos + 1);
		}
	}

}
