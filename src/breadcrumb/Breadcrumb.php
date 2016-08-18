<?php

namespace NAttreid\Menu;

use     Nette\Utils\Strings,
    Nette\Localization\ITranslator;

/**
 * Drobeckova navigace
 * 
 * @author Attreid <attreid@gmail.com>
 */
class Breadcrumb extends \Nette\Application\UI\Control {



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
    private $view = FALSE;



    /**
     * Nastavi translator
     * @param ITranslator $translator
     */
    public function setTranslator(ITranslator $translator) {
        $this->translator = $translator;
    }

    /**
     * Nastavi text pred navigaci
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $this->translator !== NULL ? $this->translator->translate($title) : $title;
    }

    /**
     * Nastavi oddelovac
     * @param string $delimiter
     */
    public function setDelimiter($delimiter) {
        $this->delimiter = $delimiter;
    }

    /**
     * Zobrazi i tehdy pokud ma jen jeden link
     */
    public function always() {
        $this->view = TRUE;
    }

    /**
     * Prida polozku do navigace
     * @param string $name
     * @param string $link
     * @param array $arguments
     */
    public function addLink($name, $link = NULL, $arguments = []) {
            $obj = new \stdClass;
            $obj->name = $this->translator !== NULL ? $this->translator->translate($name) : $name;
            $obj->link = $link;
            $obj->arguments = $arguments;
            $this->links[] = $obj;
    }

    public function render($args = NULL) {
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
