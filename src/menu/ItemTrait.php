<?php

namespace NAttreid\Menu;

use Nette\Utils\Strings,
    NAttreid\Utils\Arrays;

/**
 * 
 *
 * @author Attreid <attreid@gmail.com>
 */
trait ItemTrait {

    /** @var Item[] */
    protected $items = [];

    /**
     * Prida polozku
     * @param Item $item
     * @param int $position
     * @return Item
     */
    protected function addItem(Item $item, $position) {
        $item->setParent($this);
        if ($position !== NULL) {
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
    protected function prepareLink(&$link) {
        if (Strings::endsWith($link, ':default')) {
            $link = substr($link, 0, -7);
        }
    }

}
