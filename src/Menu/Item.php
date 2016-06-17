<?php

namespace NAttreid\Menu\Menu;

/**
 * Polozka menu
 *
 * @author Attreid <attreid@gmail.com>
 */
class Item {

    const
            INFO = 'info',
            WARNING = 'warning';

    /**
     * Nazev
     * @var string
     */
    public $name;

    /**
     * Url
     * @var string
     */
    public $link;

    /**
     * Argumenty
     * @var array
     */
    public $arguments;

    /**
     * Je povolena pro uzivatele
     * @var boolean
     */
    public $allowed;

    /**
     * Je aktualni
     * @var boolean
     */
    public $isCurrent;

    /**
     * Pocet polozek
     * @var int
     */
    public $count = 0;

    /**
     * Typ polozky
     * @var string 
     */
    public $type;

    /**
     * Otevrit v novem okne
     * @var boolean 
     */
    public $toBlank = FALSE;

    public function __construct($name, $link, array $arguments = []) {
        $this->name = $name;
        $this->link = $link;
        $this->arguments = $arguments;
    }

    /**
     * Nastavi link do noveho okna
     */
    public function toBlank() {
        $this->toBlank = TRUE;
    }

    /**
     * Nastavi pocet (cislo za text linku)
     * @param int $count
     * @param string $type
     */
    public function setCount($count, $type = self::INFO) {
        if ($count > 0) {
            $this->count = $count;
            $this->type = $type;
        }
    }

    /**
     * Kontrola
     * @param Menu $menu
     * @param Group $group
     * @param \Nette\Application\UI\Presenter $presenter
     */
    public function check(Menu $menu, Group $group, $presenter) {
        $this->isCurrent = $presenter->isLinkCurrent($this->link, $this->arguments);
        if ($this->isCurrent) {
            $menu->setCurrentPresenter($this->name, $this->link);
        }
        $group->allowed |= ($this->allowed = $menu->isAllowed($this->name));
    }

}
