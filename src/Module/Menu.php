<?php

namespace NAttreid\Menu\Module;

use Nette\Utils\Strings,
    Nette\Security\User,
    NAttreid\Menu\Breadcrumb\IBreadcrumb,
    Nette\Caching\IStorage;

/**
 * Menu modulu
 * 
 * @author Attreid <attreid@gmail.com>
 */
class Menu extends \NAttreid\Menu\BaseMenu {

    /** @var \NAttreid\Menu\Menu\IMenuFactory */
    private $menuFactory;

    /** @var Item */
    private $items = [];

    public function __construct($namespace, User $user, IBreadcrumb $breadcrumbFactory, IStorage $cacheStorage, \NAttreid\Menu\Menu\IMenuFactory $menuFactory) {
        parent::__construct($namespace, $user, $breadcrumbFactory, $cacheStorage);
        $this->menuFactory = $menuFactory;
    }

    public function setMenu($menu) {
        $this->items = $this->getItems($this->namespace, function() use($menu) {
            $items = [];
            $parentModule = Strings::firstUpper($this->namespace);
            foreach ($menu[$this->namespace] as $name => $item) {
                $module = Strings::firstUpper($name);

                $link = ':' . $parentModule . ':' . $module . ':' . $item['link'];

                $items[] = new Item($this->namespace . '.' . $name, $link, $parentModule . ':' . $module);
            }
            return $items;
        });
    }

    private function checkItems() {
        foreach ($this->items as $item) {
            $item->check($this, $this->presenter);
        }
    }

    public function render($args = NULL) {
        $this->checkItems();

        $template = $this->template;
        $template->setFile(__DIR__ . '/menu.latte');
        $template->items = $this->items;
        parent::render($args);
    }

    /**
     * Je aktualni stranka povolena
     * @param string $link
     * @return boolean
     */
    public function isLinkAllowed($link) {
        $pos = strrpos($link, ':');
        $link = substr($link, 0, $pos);

        foreach ($this->items as $item) {
            $pos = strrpos($item->link, ':');
            $compareLink = substr($item->link, 0, $pos);

            $linkArr = explode(':', $link);
            $shortedLink = '';
            $counter = count($linkArr);
            foreach (explode(':', $compareLink) as $component) {
                --$counter;
                if ($counter < 0) {
                    break;
                }
                if (!empty($shortedLink)) {
                    $shortedLink = ':' . $shortedLink;
                }
                $shortedLink = $linkArr[$counter] . $shortedLink;
            }

            if ($compareLink == $shortedLink) {
                return $this->isAllowed($item->name);
            }
        }
        return FALSE;
    }

    /**
     * Vrati menu modulu
     * @param string $namespace
     * @return \NAttreid\Menu\Menu
     */
    public function createMenu($namespace) {
        $menu = $this->menuFactory->create($this->namespace . '.' . $namespace, $this);
        if ($this->translator !== NULL) {
            $menu->setTranslator($this->translator);
        }
        return $menu;
    }

}

interface IMenuFactory {

    /**
     * @param string $namespace
     * @return Menu
     */
    public function create($namespace);
}
