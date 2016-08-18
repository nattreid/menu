<?php

namespace NAttreid\Menu;

use Nette\Security\User,
    Nette\Http\Session,
    Nette\Localization\ITranslator,
    Nette\Http\SessionSection;

/**
 * Menu
 *
 * @author Attreid <attreid@gmail.com>
 */
class Menu extends \Nette\Application\UI\Control implements IParent {

    use ItemTrait;

    /** @var string */
    private $namespace;

    /** @var User */
    private $user;

    /** @var Session */
    private $session;

    /** @var \stdClass */
    private $baseUrl;

    /** @var ITranslator */
    private $translator;

    /** @var boolean */
    private $view = TRUE;

    /** @var Link[] */
    private $links = [];

    /** @var Breadcrumb */
    private $breadcrumb;

    public function __construct($namespace, User $user, Session $session) {
        $this->namespace = $namespace;
        $this->user = $user;
        $this->session = $session;
    }

    /**
     * Nastavi menu z pole (treba z config.neon)
     * @param array $menu
     * @throws \Nette\InvalidArgumentException
     */
    public function setMenu(array $menu) {
        $this->items = [];
        foreach ($menu[$this->namespace] as $name => $item) {
            if (!isset($item ['link'])) {
                throw new \Nette\InvalidArgumentException('First level of Menu must have set link');
            } else {
                $link = $this->addLink($name, $item['link']);

                unset($item['link']);
                $this->addMenuItems($link, $item);
            }
        }
    }

    /**
     * Prida polozky do menu
     * @param Item $parent
     * @param array $menu
     */
    private function addMenuItems(Item $parent, array $menu) {
        foreach ($menu as $name => $item) {
            if (isset($item ['link'])) {
                $obj = $parent->addLink($name, $item ['link']);
                unset($item ['link']);
            } else {
                $obj = $parent->addGroup($name);
            }
            $this->addMenuItems($obj, $item);
        }
    }

    /**
     * Nastavi pocet (cislo za text linku)
     * @param string $link
     * @param int $count
     * @param string $type
     */
    public function setCount($link, $count, $type = Item::INFO) {
        $this->links[$link]->setCount($count, $type);
    }

    /**
     * Prida link
     * @param string $name
     * @param int $position
     * return Link
     */
    public function addLink($name, $link, $position = NULL) {
        $item = $this->addItem(new Link($name, $link), $position);
        return $this->addLinkAddress($item);
    }

    /**
     * Prida link do seznamu podle linku
     * @param \NAttreid\Menu\Link $link
     * @return Link
     * @throws \Nette\InvalidStateException
     */
    public function addLinkAddress(Link $link) {
        if (isset($this->links[$link->link])) {
//            throw new \Nette\InvalidStateException("Link '{$link->link}' already exists in Menu.");
        }
        return $this->links[$link->link] = $link;
    }

    /**
     * Nastavi translator
     * @param ITranslator $translator
     */
    public function setTranslator(ITranslator $translator) {
        $this->translator = $translator;
    }

    /**
     * Vrati translator
     * @return ITranslator
     */
    public function getTranslator() {
        return $this->translator;
    }

    /**
     * {@inheritdoc }
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Vrati session
     * @return SessionSection
     */
    public function getSessionSection() {
        return $this->session->getSection('nattreid/menu/' . $this->name);
    }

    /**
     * Vypne zobrazeni
     */
    public function disable() {
        $this->view = FALSE;
    }

    /**
     * Zapne zobrazeni (vychozi stav)
     */
    public function enable() {
        $this->view = TRUE;
    }

    /**
     * Nastavi korenovou url
     * @param string $name
     * @param string $link
     */
    public function setBaseUrl($name, $link) {
        $this->baseUrl = new \stdClass;
        $this->baseUrl->name = $this->translator !== NULL ? $this->translator->translate($name) : $name;
        $this->baseUrl->link = $link;
    }

    /**
     * Autorizace
     * @return boolean
     */
    public function isAllowed($resource) {
        return $this->user->isAllowed($resource, 'view');
    }

    /**
     * Je aktualni stranka povolena
     * @param string $link
     * @return boolean
     */
    public function isLinkAllowed($link) {
        $this->prepareLink($link);
        if (isset($this->links[$link])) {
            return $this->links[$link];
        } else {
            return FALSE;
        }
    }

    /**
     * Toggler scrolling
     * @param string $name
     */
    public function handleScrollGroup($name) {
        if ($this->presenter->isAjax()) {
            $session = $this->getSessionSection();
            $session->groupHidden[$name] = !$session->groupHidden[$name];
        }
        $this->presenter->terminate();
    }

    /**
     * Vrati drobeckovou navidaci
     * @return Breadcrumb
     */
    public function getBreadcrumb() {
        if ($this->breadcrumb === NULL) {
            $this->breadcrumb = new Breadcrumb;
            if ($this->baseUrl) {
                $this->breadcrumb->addLink($this->baseUrl->name, $this->baseUrl->link);
            }
        }
        return $this->breadcrumb;
    }

    public function render($args = NULL) {
        $template = $this->template;
        $template->setFile(__DIR__ . '/menu.latte');

        $template->view = $this->view;
        $template->args = $args;
        $template->items = $this->items;

        $action = $this->presenter->getAction(TRUE);
        $this->prepareLink($action);
        if (isset($this->links[$action])) {
            $item = $this->links[$action];
            $item->setCurrent();

            $breadcrumb = $this->getBreadcrumb();
            foreach ($item->getActualLinks() as $link) {
                $breadcrumb->addLink($link->name, $link->link, $link->arguments);
            }
        }

        $template->render();
    }

}

interface IMenuFactory {

    /** @return Menu */
    public function create();
}
