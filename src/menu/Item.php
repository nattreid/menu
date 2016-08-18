<?php

namespace NAttreid\Menu;

use Nette\Utils\Strings;

/**
 * Polozka menu
 * 
 * @property-read string $name
 * @property-read boolean $allowed
 * @property-read boolean $current
 * @property-read boolean $group
 * @property-read Item $parent
 * @property-read Item[] $items
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class Item implements IParent {

    use \Nette\SmartObject,
        ItemTrait;

    /** @var string  */
    private $name;

    /** @var IParent */
    private $parent;

    /** @var boolean */
    protected $allowed = FALSE;

    /** @var boolean */
    private $current = FALSE;

    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Vrati namespace
     * @return string
     */
    public function getNamespace() {
        return $this->parent->getNamespace() . '.' . $this->name;
    }

    /**
     * Vrati menu
     * @return Menu
     */
    protected function getMenu() {
        $parent = $this->parent;
        if ($parent instanceof Menu) {
            return $parent;
        } elseif ($parent instanceof Item) {
            return $parent->getMenu();
        }
    }

    /**
     * Prida skupinu
     * @param string $name
     * @param int $position
     * @return Group
     */
    public function addGroup($name, $position = NULL) {
        return $this->addItem(new Group($name), $position);
    }

    /**
     * Prida link
     * @param string $name
     * @param int $position
     * @return Link
     */
    public function addLink($name, $link, $position = NULL) {
        $item = $this->addItem(new Link($name, $link), $position);
        return $this->getMenu()->addLinkAddress($item);
    }

    /**
     * Nastavi rodice
     * @param IParent $parent
     * @throws \Nette\InvalidArgumentException
     */
    public function setParent(IParent $parent) {
        $this->parent = $parent;
    }

    /** @return string */
    public function getName() {
        $translator = $this->getMenu()->getTranslator();
        if ($translator !== NULL) {
            $message = 'menu.' . $this->getNamespace();
            if (count($this->items) > 0) {
                $message .= '.title';
            }
            return $translator->translate($message);
        } else {
            return $this->name;
        }
    }

    /**
     * Vrati link
     * @param string $link
     * @return string
     */
    protected function createLink($link) {
        $this->prepareLink($link);

        if ($this instanceof Link) {
            $link = Strings::firstUpper($this->name) . ':' . $link;
        }

        $parent = $this->parent;
        if ($parent instanceof Menu) {
            return ':' . Strings::firstUpper($parent->getNamespace()) . ':' . $link;
        } elseif ($parent instanceof Item) {
            return $parent->createLink($link);
        }
    }

    /** @var boolean */
    public function isAllowed() {
        return $this->allowed;
    }

    /** @var Item[] */
    public function getItems() {
        return $this->items;
    }

    /** @var boolean */
    public function isCurrent() {
        return $this->current;
    }

    /** @var boolean */
    public abstract function isGroup();

    /**
     * Linky (od aktualniho po vsechyn rodice)
     * @return Link[]
     */
    public function getActualLinks() {
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
    public function setCurrent() {
        $this->current = TRUE;
        $parent = $this->parent;
        if ($parent instanceof Item) {
            $parent->setCurrent();
        }
    }

}
