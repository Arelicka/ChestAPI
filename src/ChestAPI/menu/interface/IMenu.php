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

namespace ChestAPI\menu\interface;

use ChestAPI\data\ClosureItemData;

use pocketmine\Player;

interface IMenu{

    /**
     * @param Player $player
     */
    public function onSend(Player $player) : void;

    /**
     * @param Player $player
     */
    public function onOpen(Player $player) : void;

    /**
     * @param Player $player
     */
    public function onClose(Player $player) : void;

    /**
     * @param int $slot
     * 
     * @return ClosureItemData
     */
    public function getItem(int $slot) : ClosureItemData;

    /**
     * @param int $id
     * 
     * @return ?ClosureItemData
     */
    public function findDataById(int $id) : ?ClosureItemData;
}
