<?php

namespace NAttreid\Menu;

use Nette\Security\User,
    Nette\Caching\Cache,
    NAttreid\Menu\Breadcrumb\IBreadcrumb,
    Nette\Localization\ITranslator;

/**
 * Zakladni trida pro menu
 * 
 * @author Attreid <attreid@gmail.com>
 */
abstract class BaseMenu extends \Nette\Application\UI\Control {

    /** @var string */
    protected $namespace = '';

    /** @var User */
    private $user;

    /** @var IBreadcrumb */
    private $breadcrumbFactory;

    /** @var ITranslator */
    private $translator;

    /** @var Cache */
    private $cache;

    /** @var \stdClass */
    private $moduleLink;

    /** @var \stdClass */
    private $presenterLink;

    /** @var string */
    private $baseUrl;

    /** @var boolean */
    private $view = TRUE;

    public function __construct($namespace, User $user, IBreadcrumb $breadcrumbFactory, \Nette\Caching\IStorage $cacheStorage, ITranslator $translator = NULL) {
        $this->namespace = $namespace;
        $this->user = $user;
        $this->breadcrumbFactory = $breadcrumbFactory;
        $this->cache = new Cache($cacheStorage, 'component/menu');
        $this->translator = $translator;
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

    protected function getItems($key, $callback) {
        $result = $this->cache->load($key);
        if ($result === NULL) {
            $result = $this->cache->save($key, $callback);
        }
        return $result;
    }

    /**
     * Vytvori resource, pokud neexistuje a zjisti zda ma k nemu uzivatel prava
     * @param string $resource
     * @return boolean
     */
    public function isAllowed($resource) {
        return $this->user->isAllowed($resource, 'view');
    }

    /**
     * Nastavi aktualni presenter
     * @param string $name
     * @param string $link
     */
    public function setCurrentPresenter($name, $link) {
        $this->presenterLink = new \stdClass;
        $this->presenterLink->link = $link;
        $this->presenterLink->name = $this->translator !== NULL ? $this->translator->translate('menu.' . $name) : $name;
    }

    /**
     * Nastavi aktualni modul
     * @param string $name
     * @param string $link
     */
    public function setCurrentModule($name, $link) {
        $this->moduleLink = new \stdClass;
        $this->moduleLink->link = $link;
        $this->moduleLink->name = $this->translator !== NULL ? $this->translator->translate('menu.' . $name . '.title') : $name;
    }

    /**
     * Nastavi korenovou url
     * @param string $name
     * @param string $link
     */
    public function setBaseUrl($name, $link) {
        $this->baseUrl = new \stdClass;
        $this->baseUrl->name = $name;
        $this->baseUrl->link = $link;
    }

    /**
     * Vytvori drobeckovou navidaci
     * @return Breadcrumb
     */
    public function createBreadcrumb() {
        $breadcrumb = $this->breadcrumbFactory->create();
        if ($this->baseUrl) {
            $breadcrumb->addLink($this->baseUrl->name, $this->baseUrl->link, FALSE);
        }
        if ($this->moduleLink) {
            $breadcrumb->addLink($this->moduleLink->name, $this->moduleLink->link);
        }
        if ($this->presenterLink) {
            $breadcrumb->addLink($this->presenterLink->name, $this->presenterLink->link);
        }
        return $breadcrumb;
    }

    public function render($args = NULL) {
        $template = $this->template;

        $template->view = $this->view;
        $template->args = $args;

        $template->render();
    }

}
