<?php

namespace NAttreid\Menu\Menu;

/**
 * Skupina linku
 *
 * @property-read boolean $hidden
 *
 * @author Attreid <attreid@gmail.com>
 */
class Group extends Item
{

	/**
	 * {@inheritdoc }
	 */
	public function setParent(IParent $parent)
	{
		parent::setParent($parent);
		$this->allowed = false;
	}

	/** @return boolean */
	public function isHidden()
	{
		$session = $this->getMenu()->getSessionSection();
		if (!isset($session->groupHidden[$this->name])) {
			return $session->groupHidden[$this->name] = false;
		} else {
			return $session->groupHidden[$this->name];
		}
	}

	/**
	 * {@inheritdoc }
	 */
	public function isGroup()
	{
		return true;
	}

}
