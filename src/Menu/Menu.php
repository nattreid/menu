<?php

namespace NAttreid\Menu\Menu;

use Nette\Http\Session,
    Nette\Utils\Strings,
    Nette\Security\User,
    NAttreid\Menu\Breadcrumb\IBreadcrumb,
    Nette\Caching\IStorage,
    Kdyby\Translation\Translator;

/**
 * Menu
 * 
 * @author Attreid <attreid@gmail.com>
 */
class Menu extends \NAttreid\Menu\BaseMenu {

    /** @var Session */
    public $session;

    /** @var Group */
    private $groups = [];

    /** @var BaseMenu */
    private $parent;

    public function __construct($namespace, User $user, IBreadcrumb $breadcrumbFactory, IStorage $cacheStorage, Translator $translator, \NAttreid\Menu\Module\Menu $parent, Session $session) {
        parent::__construct($namespace, $user, $breadcrumbFactory, $cacheStorage, $translator);
        $this->parent = $parent;
        $this->session = $session;
    }

    public function setMenu($menu) {
        $this->groups = $this->getItems($this->namespace, function() use($menu) {
            $parents = explode('.', $this->namespace);
            $gArr = $menu;
            $link = ':';

            foreach ($parents as $parent) {
                if (isset($gArr[$parent])) {
                    $link .= Strings::firstUpper($parent) . ':';
                    $gArr = $gArr[$parent];
                } else {
                    $gArr = NULL;
                }
            }

            if ($gArr !== NULL) {
                unset($gArr['link']);

                foreach ($gArr as $groupName => $items) {
                    $group = $this->addGroup($groupName);
                    foreach ($items as $itemName => $values) {
                        $item = $group->addItem($itemName, $link . $values['link'], isset($values['arguments']) ? $values['arguments'] : []);
                        if (!empty($values['toBlank'])) {
                            $item->toBlank();
                        }
                    }
                }
            }
            return $this->groups;
        });
    }

    /**
     * Nastavi pocet (cislo za text linku)
     * @param string $name
     * @param int $count
     * @param string $type
     */
    public function setCount($name, $count, $type = Item::INFO) {
        list($group, $item) = explode('.', $name);
        $this->groups[$group]->items[$item]->setCount($count, $type);
    }

    /**
     * {@inheritdoc }
     */
    public function setCurrentPresenter($name, $link) {
        if ($this->parent !== NULL) {
            $this->parent->setCurrentPresenter($name, $link);
        } else {
            parent::setCurrentPresenter($name, $link);
        }
    }

    /**
     * Je aktualni stranka povolena
     * @param string $link
     * @return boolean
     */
    public function isLinkAllowed($link) {
        $pos = strrpos($link, ':');
        $link = substr($link, 0, $pos);

        foreach ($this->groups as $group) {
            foreach ($group->items as $item) {
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
        }
        return FALSE;
    }

    /**
     * Prida skupinu do menu
     * @param string $name
     * @return Group
     */
    public function addGroup($name) {
        $group = new Group($this->namespace . '.' . $name);
        $this->groups[$name] = $group;
        return $group;
    }

    /**
     * Toggler scrolling
     * @param string $name
     */
    public function handleScrollGroup($name) {
        if ($this->presenter->isAjax()) {
            $session = $this->session->getSection('menu/groups');
            $session->itemHidden[$name] = !$session->itemHidden[$name];
        }
        $this->presenter->terminate();
    }

    private function checkGroups() {
        foreach ($this->groups as $group) {
            $group->check($this, $this->presenter);
        }
    }

    public function render($args = NULL) {
        $this->checkGroups();

        $template = $this->template;
        $template->setFile(__DIR__ . '/menu.latte');
        $this->template->groups = $this->groups;
        parent::render($args);
    }

}

interface IMenuFactory {

    /**
     * @param string $namespace
     * @param \NAttreid\Menu\Module\Menu $parent
     * @return Menu
     */
    public function create($namespace, \NAttreid\Menu\Module\Menu $parent = NULL);
}
