<?php

declare(strict_types=1);

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
	protected function addItem(Item $item, int $position = null): Item
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
	protected function prepareLink(string &$link = null): void
	{
		if ($link !== null) {
			if (Strings::endsWith($link, ':default')) {
				$link = substr($link, 0, -7);
			}
			$pos = strrpos($link, ':');
			if ($pos !== strlen($link)) {
				$link = substr($link, 0, $pos + 1);
			}
		}
	}

}
