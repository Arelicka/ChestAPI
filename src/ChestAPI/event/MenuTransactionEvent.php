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

use pocketmine\event\Cancellable;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\Player;

class MenuTransactionEvent extends MenuEvent implements Cancellable{
    /** @var Player */
    private Player $player;
    /** @var int */
    private int $slot;
    /** @var Item */
    private Item $item;
    /** @var Inventory */
    private Inventory $inventory;

    /**
     * @param Player $player
     * @param int $slot
     * @param Item $item
     * @param Inventory $inventory
     * @param IMenu $menu
     */
    public function __construct(
        Player $player,
        int $slot,
        Item $item,
        Inventory $inventory,
        IMenu $menu
    ){
        parent::__construct($menu);

        $this->player = $player;
        $this->slot = $slot;
        $this->item = $item;
        $this->inventory = $inventory;
    }

    /**
     * @return Player
     */
    public function getPlayer() : Player{
        return $this->player;
    }

    /**
     * @return int
     */
    public function getSlot() : int{
        return $this->slot;
    }

    /**
     * @return Item
     */
    public function getItem() : Item{
        return $this->item;
    }

    /**
     * @return Inventory
     */
    public function getInventory() : Inventory{
        return $this->inventory;
    }
}
