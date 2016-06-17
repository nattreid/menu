<?php

namespace NAttreid\Menu\Module;

/**
 * Polozka menu modulu
 * 
 * @author Attreid <attreid@gmail.com>
 */
class Item {

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
     * Nazev modulu
     * @var string 
     */
    private $module;

    public function __construct($name, $link, $module) {
        $this->module = $module;
        $this->name = $name;
        $this->link = $link;
    }

    /**
     * Kontrola
     * @param Menu $menu
     * @param \App\Presenters\Presenter $presenter
     */
    public function check(Menu $menu, $presenter) {
        $this->allowed = $menu->isAllowed($this->name);
        if ($this->allowed) {
            $this->isCurrent = $presenter->isModuleCurrent($this->module);
            if ($this->isCurrent) {
                $menu->setCurrentModule($this->name, $this->link);
            }
        }
    }

}
