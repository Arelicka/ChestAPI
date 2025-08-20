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

namespace ChestAPI\inventory;

use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\inventory\ContainerInventory;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use function count;

class ChestInventory extends ContainerInventory{
    public const int DATA_CLOSED = 0;
    public const int DATA_OPENED = 1;

    /**
     * @param ?Vector3 $holder
     */
    public function __construct(?Vector3 $holder = null){
        parent::__construct($holder ?? new Vector3(
            0,
            0,
            0
        )); // temporary vector with null arguments for comfortable job
    }

    /**
     * @return int
     */
    public function getNetworkType() : int{
        return WindowTypes::CONTAINER;
    }

    /**
     * @return string
     */
    public function getName() : string{
        return "Chest";
    }

    /**
     * @return int
     */
    public function getDefaultSize() : int{
        return 27;
    }

    /**
     * @return Vector3
     */
    public function getHolder() : Vector3{
        return $this->holder;
    }

    /**
     * @param Vector3 $holder
     */
    public function setHolder(Vector3 $holder) : void{
        $this->holder = $holder;
    }

    /**
     * @return int
     */
    protected function getOpenSound() : int{
        return LevelSoundEventPacket::SOUND_CHEST_OPEN;
    }

    /**
     * @return int
     */
    protected function getCloseSound() : int{
        return LevelSoundEventPacket::SOUND_CHEST_CLOSED;
    }

    /**
     * @param Player $who
     */
    public function onOpen(Player $who) : void{
        parent::onOpen($who);

        if(count($this->getViewers()) === 1){
            //TODO: this crap really shouldn't be managed by the inventory
            $this->broadcastBlockEventPacket(true);
            $this->broadcastLevelSoundEventPacket(true);
        }
    }

    /**
     * @param Player $who
     */
    public function onClose(Player $who) : void{
        if(count($this->getViewers()) === 1){
            //TODO: this crap really shouldn't be managed by the inventory
            $this->broadcastBlockEventPacket(false);
            $this->broadcastLevelSoundEventPacket(false);
        }

        parent::onClose($who);
    }

    /**
     * @param bool $isOpen
     */
    protected function broadcastBlockEventPacket(bool $isOpen) : void{
        $holder = $this->getHolder();

        $packet = new BlockEventPacket();
        $packet->x = (int) $holder->x;
        $packet->y = (int) $holder->y;
        $packet->z = (int) $holder->z;
        $packet->eventType = 1; //it's always 1 for a chest

        $packet->eventData = $isOpen ? 
            self::DATA_OPENED :
            self::DATA_CLOSED;

        Server::getInstance()->broadcastPacket(
            $this->getViewers(),
            $packet
        );
    }

    /**
     * @param bool $isOpen
     */
    protected function broadcastLevelSoundEventPacket(bool $isOpen) : void{
		$packet = new LevelSoundEventPacket();

		$packet->sound = $isOpen ? 
            $this->getOpenSound() :
            $this->getCloseSound();

		$packet->position = $this->getHolder()->add(
		    0.5,
		    0.5,
		    0.5
		);

        Server::getInstance()->broadcastPacket(
            $this->getViewers(),
            $packet
        );
    }
}
