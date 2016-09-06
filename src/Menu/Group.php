<?php

namespace NAttreid\Menu;

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
		$this->allowed = FALSE;
	}

	/** @return boolean */
	public function isHidden()
	{
		$session = $this->getMenu()->getSessionSection();
		if (!isset($session->groupHidden[$this->name])) {
			return $session->groupHidden[$this->name] = FALSE;
		} else {
			return $session->groupHidden[$this->name];
		}
	}

	/**
	 * {@inheritdoc }
	 */
	public function isGroup()
	{
		return TRUE;
	}

}
