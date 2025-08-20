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

namespace ChestAPI\data;

use pocketmine\nbt\tag\IntTag;
use pocketmine\item\Item;
use pocketmine\utils\Utils;
use pocketmine\Player;
use function spl_object_id;
use Closure;

class ClosureItemData{
    /** @var Item */
    private Item $item;
    /** @var ?Closure */
    private ?Closure $closure = null;

    /**
     * @param Item $item
     * @param ?Closure $closure
     */
    public function __construct(
        Item $item,
        ?Closure $closure = null
    ){
        if($closure !== null){
            Utils::validateCallableSignature(
                function(Player $player) : void{},
                $closure
            );
        }

        $this->item = $item;
        $this->closure = $closure;
    }

    /**
     * @return Item
     */
    public function serializeToItem() : Item{
        $item = clone $this->item;
        $item->setNamedTagEntry(new IntTag(
            Tags::TAG_CALLBACK_ID,
            $this->getId()
        ));

        return $item;
    }

    /**
     * @return int
     */
    public function getId() : int{
        return spl_object_id($this);
    }

    /**
     * @return Item
     */
    public function getItem() : Item{
        return $this->item;
    }

    /**
     * @return ?Closure
     */
    public function getClosure() : ?Closure{
        return $this->closure;
    }
}
