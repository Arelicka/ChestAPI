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
use ChestAPI\menu\interface\IMenu;
use ChestAPI\event\MenuTransactionEvent;
use ChestAPI\data\Tags;

use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\PETransaction\TransactionQueue;
use pocketmine\inventory\PETransaction\Transaction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\inventory\Inventory;
use pocketmine\nbt\tag\IntTag;
use pocketmine\item\Item;
use pocketmine\Player;

class ChestTransactionListener implements Listener{

    /**
     * @param InventoryTransactionEvent $event
     */
    public function onInventoryTransaction(InventoryTransactionEvent $event) : void{
        $transaction = $event->getTransaction();
        if(!(($player = $transaction->getSource()) instanceof Player)){
            return;
        }

        if(($menu = ChestAPI::getInstance()->getMenuManager()->get($player)) === null){
            return;
        }

        $event->setCancelled();
        if($transaction instanceof TransactionQueue){
            // I hate this stupid pw10 transactions... I made next checks for phpstan
            foreach(($transaction->getTransactions() ?? []) as $action){
                if(!($action instanceof Transaction)){
                    continue;
                }

                if(!(($inventory = $action->getInventory())) instanceof Inventory){
                    continue;
                }

                $this->handleItem(
                    $player,
                    $action->getSlot(),
                    $action->getTargetItem(),
                    $inventory,
                    $menu
                );
            }
        }elseif($transaction instanceof InventoryTransaction){
            foreach($transaction->getActions() as $action){
                if($action instanceof SlotChangeAction){
                    $this->handleItem(
                        $player,
                        $slot = $action->getSlot(),
                        $action->getInventory()->getItem($slot),
                        $action->getInventory(),
                        $menu
                    );
                }
            }
        }
    }

    /**
     * @param Player $player
     * @param int $slot
     * @param Item $item
     * @param Inventory $inventory
     * @param IMenu $menu
     */
    private function handleItem(
        Player $player,
        int $slot,
        Item $item,
        Inventory $inventory,
        IMenu $menu
    ) : void{
        ($event = new MenuTransactionEvent(
            $player,
            $slot,
            $item,
            $inventory,
            $menu
        ))->call();

        if($event->isCancelled()){
            return;
        }

        if(!(($id = $item->getNamedTagEntry(Tags::TAG_CALLBACK_ID)) instanceof IntTag)){
            return;
        }

        $value = $id->getValue();
        $callbackData = $menu->findDataById($value);

        if($callbackData === null){
            return;
        }

        if(($callback = $callbackData->getClosure()) === null){
            return;
        }

        $callback($player);
    }
}
