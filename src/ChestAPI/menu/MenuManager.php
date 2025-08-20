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

namespace ChestAPI\menu;

use ChestAPI\menu\interface\IMenu;

use pocketmine\Player;

class MenuManager{
    /** @var IMenu[] */
    private array $menus = [];

    /**
     * @param Player $player
     * 
     * @return ?IMenu
     */
    public function get(Player $player) : ?IMenu{
        return $this->menus[$player->getLowerCaseName()] ?? null;
    }

    /**
     * @param Player $player
     * @param IMenu $menu
     */
    public function set(
        Player $player,
        IMenu $menu
    ) : void{
        $this->menus[$player->getLowerCaseName()] = $menu;
    }

    /**
     * @param Player $player
     */
    public function remove(Player $player) : void{
        unset($this->menus[$player->getLowerCaseName()]);
    }
}
