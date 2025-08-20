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

use ChestAPI\inventory\ChestInventory;
use ChestAPI\inventory\DoubleChestInventory;

use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\tile\Spawnable;
use pocketmine\math\Vector3;
use pocketmine\Player;

class DoubleChestMenu extends ChestMenu{
    /** @var DoubleChestInventory */
    protected ChestInventory $inventory;

    /**
     * @param string $title
     */
    public function __construct(string $title){
        $this->inventory = new DoubleChestInventory(
            new ChestInventory(),
            new ChestInventory()
        );

        $this->title = $title;
    }

    /**
     * @param Player $player
     */
    public function onSend(Player $player) : void{
        $vector = $player->floor()->add(
            0,
            -3,
            0
        );

        $pairVector = $vector->getSide(Vector3::SIDE_WEST);

        $this->sendChest(
            $player,
            $this->title,
            $vector,
            $pairVector
        );

        $this->sendChest(
            $player,
            $this->title,
            $pairVector,
            $vector
        );

        $this->inventory->getLeftSide()->setHolder($vector);
        $this->inventory->getRightSide()->setHolder($pairVector);
        $this->inventory->setHolder($pairVector);
    }

    /**
     * @param Player $player
     */
    public function onClose(Player $player) : void{
        $holders = [
            $this->inventory->getLeftSide()->getHolder(),
            $this->inventory->getRightSide()->getHolder()
        ];

        $player->getLevel()?->sendBlocks(
            [$player],
            $holders,
            UpdateBlockPacket::FLAG_ALL
        );

        foreach($holders as $holder){
            if(($tile = $player->getLevel()?->getTile($holder)) instanceof Spawnable){
                $tile->spawnTo($player);
            }
        }
    }
}
