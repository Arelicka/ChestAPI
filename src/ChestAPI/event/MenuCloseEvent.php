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

use ChestAPI\menu\interface\IMenu;

use pocketmine\Player;

class MenuCloseEvent extends MenuEvent{
    /** @var Player */
    private Player $player;

    /**
     * @param Player $player
     * @param IMenu $menu
     */
    public function __construct(
        Player $player,
        IMenu $menu
    ){
        parent::__construct($menu);

        $this->player = $player;
    }

    /**
     * @return Player
     */
    public function getPlayer() : Player{
        return $this->player;
    }
}
