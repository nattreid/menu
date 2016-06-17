<?php

namespace NAttreid\Menu\Menu;

/**
 * Skupina v menu
 *
 * @author Attreid <attreid@gmail.com>
 */
class Group {

    /** @var string */
    public $name;

    /** @var Item */
    public $items = [];

    /** @var bool */
    public $allowed;

    /** @var bool */
    public $itemHidden;

    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Vytvoreni odkazu v menu
     * @param string $name nazev
     * @param string $link adresa
     * @param array $arguments argumenty linku
     * @return Item polozka v menu
     */
    public function addItem($name, $link, array $arguments = []) {
        $item = new Item($this->name . '.' . $name, $link, $arguments);
        $this->items[$name] = $item;
        return $item;
    }

    /**
     * Kontrola
     * @param Menu $menu
     * @param \Nette\Application\UI\Presenter $presenter
     */
    public function check(Menu $menu, $presenter) {
        $session = $menu->session->getSection('menu/groups');
        if (!isset($session->itemHidden[$this->name])) {
            $itemHidden = $session->itemHidden[$this->name] = FALSE;
        } else {
            $itemHidden = $session->itemHidden[$this->name];
        }
        $this->itemHidden = $itemHidden;

        foreach ($this->items as $item) {
            $item->check($menu, $this, $presenter);
        }
    }

}
