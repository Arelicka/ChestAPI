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
use ChestAPI\inventory\ChestInventory;
use ChestAPI\utils\FakeChestTrait;
use ChestAPI\data\ClosureItemData;

use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\tile\Spawnable;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\Item;
use pocketmine\Player;
use Closure;

class ChestMenu implements IMenu{
    use FakeChestTrait;

    /** @var ChestInventory */
    protected ChestInventory $inventory;
    /** @var string */
    protected string $title;
    /** @var ClosureItemData[] */
    protected array $data = [];

    /**
     * @param string $title
     */
    public function __construct(string $title){
        $this->inventory = new ChestInventory();
        $this->title = $title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title) : void{
        $this->title = $title;
    }

    /**
     * @param int $slot
     * @param Item $item
     * @param ?Closure $closure
     */
    public function setItem(
        int $slot,
        Item $item,
        ?Closure $closure = null
    ) : void{
        $data = new ClosureItemData(
            $item,
            $closure
        );

        $this->data[$slot] = $data;

        $this->inventory->setItem(
            $slot,
            $data->serializeToItem()
        );
    }

    /**
     * @param int $slot
     * 
     * @return ClosureItemData
     */
    public function getItem(int $slot) : ClosureItemData{
        return $this->data[$slot] ?? new ClosureItemData(ItemFactory::get(ItemIds::AIR));
    }

    /**
     * @param int $id
     * 
     * @return ?ClosureItemData
     */
    public function findDataById(int $id) : ?ClosureItemData{
        foreach($this->data as $closureData){
            if($closureData->getId() === $id){
                return $closureData;
            }
        }

        return null;
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

        $this->sendChest(
            $player,
            $this->title,
            $vector
        );

        $this->inventory->setHolder($vector);
    }

    /**
     * @param Player $player
     */
    public function onOpen(Player $player) : void{
        $player->addWindow($this->inventory);
    }

    /**
     * @param Player $player
     */
    public function onClose(Player $player) : void{
        $player->getLevel()?->sendBlocks(
            [$player],
            [$this->inventory->getHolder()],
            UpdateBlockPacket::FLAG_ALL
        );

        if(($tile = $player->getLevel()?->getTile($this->inventory->getHolder())) instanceof Spawnable){
            $tile->spawnTo($player);
        }
    }

    /**
     * @return ChestInventory
     */
    public function getInventory() : ChestInventory{
        return $this->inventory;
    }

    /**
     * @return string
     */
    public function getTitle() : string{
        return $this->title;
    }
}
