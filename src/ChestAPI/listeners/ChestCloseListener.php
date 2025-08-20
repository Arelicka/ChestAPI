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

namespace ChestAPI\listeners;

use ChestAPI\ChestAPI;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\inventory\InventoryCloseEvent;

class ChestCloseListener implements Listener{

    /**
     * @param PlayerQuitEvent $event
     */
    public function onPlayerQuit(PlayerQuitEvent $event) : void{
        ChestAPI::getInstance()->closeMenu($event->getPlayer());
    }

    /**
     * @param InventoryCloseEvent $event
     */
    public function onInventoryClose(InventoryCloseEvent $event) : void{
        ChestAPI::getInstance()->closeMenu($event->getPlayer());
    }
}
