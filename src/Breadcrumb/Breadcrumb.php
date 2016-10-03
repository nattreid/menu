<?php

namespace NAttreid\Menu\Breadcrumb;

use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;

/**
 * Drobeckova navigace
 *
 * @author Attreid <attreid@gmail.com>
 */
class Breadcrumb extends Control
{

	/**
	 * Text pred navigaci
	 * @var string
	 */
	private $title;

	/**
	 * Linky v navigaci
	 * @var array
	 */
	private $links = [];

	/**
	 * Oddelovac
	 * @var string
	 */
	private $delimiter = '»';

	/** @var ITranslator */
	private $translator;

	/**
	 * Zobrazi pokud obsahuje pouze jeden link
	 * @var boolean
	 */
	private $view = false;

	/**
	 * Nastavi translator
	 * @param ITranslator $translator
	 */
	public function setTranslator(ITranslator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * Nastavi text pred navigaci
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $this->translator !== null ? $this->translator->translate($title) : $title;
	}

	/**
	 * Nastavi oddelovac
	 * @param string $delimiter
	 */
	public function setDelimiter($delimiter)
	{
		$this->delimiter = $delimiter;
	}

	/**
	 * Zobrazi i tehdy pokud ma jen jeden link
	 */
	public function always()
	{
		$this->view = true;
	}

	/**
	 * Prida polozku do navigace
	 * @param string $name
	 * @param string $link
	 * @param array $arguments
	 * @return Link
	 */
	public function addLink($name, $link = null, $arguments = [])
	{
		$name = $this->translator !== null ? $this->translator->translate($name) : $name;
		return $this->addLinkUntranslated($name, $link, $arguments);
	}

	/**
	 * Prida polozku do navigace (bez prekladu jmena)
	 * @param string $name
	 * @param string $link
	 * @param array $arguments
	 * @return Link
	 */
	public function addLinkUntranslated($name, $link = null, $arguments = [])
	{
		return $this->links[] = new Link($name, $link, $arguments);
	}

	public function render($args = null)
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/breadcrumb.latte');
		$template->title = $this->title;
		$template->links = $this->links;
		$template->delimiter = $this->delimiter;
		$template->view = (!empty($this->title) || count($this->links) > 1 || $this->view) && count($this->links) > 0;
		$template->args = $args;
		$template->render();
	}

}
