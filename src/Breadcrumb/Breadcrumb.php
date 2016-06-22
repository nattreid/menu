<?php

namespace NAttreid\Menu\Breadcrumb;

use Nette\Application\LinkGenerator,
    Nette\Utils\Strings;

/**
 * Drobeckova navigace
 * 
 * @author Attreid <attreid@gmail.com>
 */
class Breadcrumb extends \Nette\Application\UI\Control {

    /** @var LinkGenerator */
    private $linkGenerator;

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
    private $delimiter;

    /**
     * Zobrazi pokud obsahuje pouze jeden link
     * @var boolean
     */
    private $view = FALSE;

    public function __construct(LinkGenerator $linkGenerator) {
        $this->linkGenerator = $linkGenerator;
    }

    /**
     * Nastavi text pred navigaci
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
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
     * @param array|FALSE $args pokud je FALSE, link se nepreklada
     */
    public function addLink($name, $link = NULL, $args = []) {
        $plink = $this->checkLink($link, $args);
        if ($plink !== FALSE) {
            $obj = new \stdClass;
            $obj->name = $name;
            $obj->link = $plink;
            $this->links[] = $obj;
        }
    }

    /**
     * Existuje link v navigaci
     * @param string $link
     * @param string $args
     * @return string
     */
    private function checkLink($link, $args) {
        if ($link === NULL) {
            return NULL;
        }
        $link = Strings::replace($link, '/^:/');
        if ($args !== FALSE) {
            if (!empty($args)) {
                $plink = $this->linkGenerator->link($link, $args);
            } else {
                $plink = $this->linkGenerator->link($link);
            }
        } else {
            $plink = $link;
        }
        foreach ($this->links as $value) {
            if ($value->link == $plink) {
                return FALSE;
            }
        }
        return $plink;
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

interface IBreadcrumb {

    /**
     * @return Breadcrumb
     */
    public function create();
}
