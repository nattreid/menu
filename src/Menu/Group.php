<?php

declare(strict_types=1);

namespace NAttreid\Menu\Menu;

/**
 * Skupina linku
 *
 * @property-read bool $hidden
 *
 * @author Attreid <attreid@gmail.com>
 */
class Group extends Item
{

	/**
	 * {@inheritdoc }
	 */
	public function setParent(IParent $parent): void
	{
		parent::setParent($parent);
		$this->allowed = false;
	}

	/** @return bool */
	public function isHidden(): bool
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
	public function isGroup(): bool
	{
		return true;
	}

}
