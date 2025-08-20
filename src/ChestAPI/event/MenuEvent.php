<?php

/*
 *    ________              __  ___    ____  ____
 *   / ____/ /_  ___  _____/ /_/   |  / __ \/  _/
 *  / /   / __ \/ _ \/ ___/ __/ /| | / /_/ // /  
 * / /___/ / / /  __(__  ) /_/ ___ |/ ____// /   
 * \____/_/ /_/\___/____/\__/_/  |_/_/   /___/
 * 
 * @author Arelice
 * @link https://github.com/Arelicka
 */

declare(strict_types=1);

namespace ChestAPI\event;

use ChestAPI\ChestAPI;
use ChestAPI\menu\interface\IMenu;

use pocketmine\event\plugin\PluginEvent;

abstract class MenuEvent extends PluginEvent{
	/** @var IMenu */
	private IMenu $menu;

    /**
     * @param IMenu $menu
     */
	public function __construct(IMenu $menu){
	    parent::__construct(ChestAPI::getInstance());

		$this->menu = $menu;
	}

	/**
	 * @return IMenu
	 */
	public function getMenu() : IMenu{
		return $this->menu;
	}
}
