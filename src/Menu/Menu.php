<?php

namespace NAttreid\Menu;

use Nette\Application\UI\Control;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\InvalidArgumentException;
use Nette\InvalidStateException;
use Nette\Localization\ITranslator;
use Nette\Security\User;

/**
 * Menu
 *
 * @author Attreid <attreid@gmail.com>
 */
class Menu extends Control implements IParent
{

	use ItemTrait;

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

	public function __construct(User $user, Session $session)
	{
		parent::__construct();
		$this->user = $user;
		$this->session = $session;
	}

	/**
	 * Nastavi Menu z pole (treba z config.neon)
	 * @param array $menu
	 * @throws InvalidArgumentException
	 */
	public function setMenu(array $menu)
	{
		$this->items = [];
		foreach ($menu as $namespace => $modules) {
			if (!empty($modules)) {
				$this->addMenu($modules, $namespace);
			}
		}
	}

	/**
	 * Prida do Menu polozky
	 * @param array $menu
	 * @param string $namespace
	 * @param int $position
	 */
	public function addMenu(array $menu, $namespace = NULL, $position = NULL)
	{
		foreach ($menu as $name => $item) {
			if (!isset($item['link'])) {
				throw new InvalidArgumentException('First level of Menu must have set link');
			} else {
				/* @var $link Link */
				$link = $this->addItem(new Link($name, $item['link'], isset($item['arguments']) ? $item['arguments'] : []), $position);
				$link->setNamespace($namespace);
				$this->addLinkAddress($link);

				if (!empty($item['toBlank'])) {
					$link->toBlank();
				}

				unset($item['link'], $item['arguments'], $item['toBlank']);
				$this->addMenuItems($link, $item);
			}
		}
	}

	/**
	 * Prida polozky do Menu
	 * @param Item $parent
	 * @param array $menu
	 */
	private function addMenuItems(Item $parent, array $menu)
	{
		foreach ($menu as $name => $item) {
			if (isset($item ['link'])) {
				$obj = $parent->addLink($name, $item['link'], isset($item['arguments']) ? $item['arguments'] : []);
				if (!empty($item['toBlank'])) {
					$obj->toBlank();
				}
				unset($item['link'], $item['arguments'], $item['toBlank']);
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
	public function setCount($link, $count, $type = Link::INFO)
	{
		$this->links[$link]->setCount($count, $type);
	}

	/**
	 * Prida link
	 * @param string $name
	 * @param string $link
	 * @param array $arguments
	 * @param int $position
	 * @return Link
	 */
	public function addLink($name, $link, array $arguments = [], $position = NULL)
	{
		/* @var $item Link */
		$item = $this->addItem(new Link($name, $link, $arguments), $position);
		return $this->addLinkAddress($item);
	}

	/**
	 * Prida link do seznamu podle linku
	 * @internal
	 * @param Link $link
	 * @return Link
	 * @throws InvalidStateException
	 */
	public function addLinkAddress(Link $link)
	{
		if (isset($this->links[$link->link])) {
			throw new InvalidStateException("Link '{$link->link}' already exists in Menu.");
		}
		return $this->links[$link->link] = $link;
	}

	/**
	 * Nastavi translator
	 * @param ITranslator $translator
	 */
	public function setTranslator(ITranslator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * Vrati translator
	 * @return ITranslator
	 */
	public function getTranslator()
	{
		return $this->translator;
	}

	/**
	 * Vrati session
	 * @return SessionSection
	 */
	public function getSessionSection()
	{
		return $this->session->getSection('nattreid/Menu/' . $this->name);
	}

	/**
	 * Vypne zobrazeni
	 */
	public function disable()
	{
		$this->view = FALSE;
	}

	/**
	 * Zapne zobrazeni (vychozi stav)
	 */
	public function enable()
	{
		$this->view = TRUE;
	}

	/**
	 * Nastavi korenovou url
	 * @param string $name
	 * @param string $link
	 */
	public function setBaseUrl($name, $link)
	{
		$this->baseUrl = new \stdClass;
		$this->baseUrl->name = $name;
		$this->baseUrl->link = $link;
	}

	/**
	 * Autorizace
	 * @param $resource
	 * @return bool
	 */
	public function isAllowed($resource)
	{
		return $this->user->isAllowed($resource, 'view');
	}

	/**
	 * Je aktualni stranka povolena
	 * @param string $link
	 * @return boolean
	 */
	public function isLinkAllowed($link)
	{
		$this->prepareLink($link);
		return isset($this->links[$link]);
	}

	/**
	 * Toggler scrolling
	 * @param string $name
	 */
	public function handleScrollGroup($name)
	{
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
	public function getBreadcrumb()
	{
		if ($this->breadcrumb === NULL) {
			$this->breadcrumb = new Breadcrumb;
			$this->breadcrumb->setTranslator($this->translator);
			if ($this->baseUrl) {
				$this->breadcrumb->addLink($this->baseUrl->name, $this->baseUrl->link);
			}
		}
		return $this->breadcrumb;
	}

	public function render($args = NULL)
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/menu.latte');

		$template->view = $this->view;
		$template->args = $args;
		ksort($this->items);
		$template->items = $this->items;

		$action = $this->presenter->getAction(TRUE);
		$this->prepareLink($action);
		if (isset($this->links[$action])) {
			$item = $this->links[$action];
			$item->setCurrent();

			$breadcrumb = $this->getBreadcrumb();
			$links = $item->getActualLinks();

			foreach (array_reverse($links) as $link) {
				/* @var $link Link */
				$breadcrumb->addLink($link->getName(FALSE), $link->link, $link->arguments);
			}
		}

		$template->render();
	}

}

interface IMenuFactory
{

	/** @return Menu */
	public function create();
}
