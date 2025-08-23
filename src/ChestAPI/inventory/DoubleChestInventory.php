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

use pocketmine\item\Item;
use pocketmine\Player;
use function array_slice;
use function count;

class DoubleChestInventory extends ChestInventory{
	/** @var ChestInventory */
	private ChestInventory $left;
	/** @var ChestInventory */
	private ChestInventory $right;

	public function __construct(
	    ChestInventory $left,
	    ChestInventory $right
	){
		parent::__construct($right->getHolder());

		$this->left = $left;
		$this->right = $right;
	}

    /**
     * @return string
     */
	public function getName() : string{
		return "Double Chest";
	}

    /**
     * @return int
     */
	public function getDefaultSize() : int{
		return 54;
	}

    /**
     * @param Player $who
     */
	public function onOpen(Player $who) : void{
		if(count($this->getViewers()) === 1){
			$this->right->broadcastBlockEventPacket(true);
		}

		parent::onOpen($who);
	}

    /**
     * @param Player $who
     */
	public function onClose(Player $who) : void{
		if(count($this->getViewers()) === 1){
			$this->right->broadcastBlockEventPacket(false);
		}

		parent::onClose($who);
	}

    /**
     * @param int $index
     * 
     * @return Item
     */
	public function getItem(int $index) : Item{
		return $index < $this->left->getSize() ?
		    $this->left->getItem($index) :
		    $this->right->getItem($index - $this->left->getSize());
	}

    /**
     * @param int $index
     * @param Item $item
     * @param bool $send
     * 
     * @return bool
     */
	public function setItem(
	    int $index,
	    Item $item,
	    bool $send = true
	) : bool{
		$old = $this->getItem($index);

		if($index < $this->left->getSize() ?
		    $this->left->setItem(
		        $index,
		        $item,
		        $send
		    ) :
		    $this->right->setItem(
		        $index - $this->left->getSize(),
		        $item,
		        $send
		    )
		){
			$this->onSlotChange(
			    $index,
			    $old,
			    $send
		    );

			return true;
		}

		return false;
	}

    /**
     * @param bool $includeEmpty
     * 
     * @return Item[]
     */
    public function getContents(bool $includeEmpty = false) : array{
        $result = $this->getLeftSide()->getContents($includeEmpty);
        $leftSize = $this->getLeftSide()->getSize();

        foreach($this->getRightSide()->getContents($includeEmpty) as $i => $item){
            $result[$i + $leftSize] = $item;
        }

        return $result;
    }

	/**
	 * @param Item[] $items
	 * @param bool   $send
	 */
	public function setContents(
	    array $items,
	    bool $send = true
	) : void{
		$size = $this->getSize();
		if(count($items) > $size){
			$items = array_slice(
			    $items,
			    0,
			    $size,
			    true
			);
		}

		$leftSize = $this->left->getSize();
		for($i = 0; $i < $size; ++$i){
			if(!isset($items[$i])){
				if(
				    ($i < $leftSize and isset($this->left->slots[$i])) or
				    isset($this->right->slots[$i - $leftSize])
				){
					$this->clear(
					    $i,
					    false
					);
				}
			}elseif(!$this->setItem(
			    $i,
			    $items[$i],
			    false
			)){
				$this->clear(
				    $i,
				    false
			    );
			}
		}

		if($send){
			$this->sendContents($this->getViewers());
		}
	}

	/**
	 * @return ChestInventory
	 */
	public function getLeftSide() : ChestInventory{
		return $this->left;
	}

	/**
	 * @return ChestInventory
	 */
	public function getRightSide() : ChestInventory{
		return $this->right;
	}

    /**
     * @return self
     */
	public function getInventory() : self{
		return $this;
	}
}
