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

namespace ChestAPI;

use ChestAPI\listeners\ChestCloseListener;
use ChestAPI\listeners\ChestTransactionListener;
use ChestAPI\event\MenuCloseEvent;
use ChestAPI\menu\ChestMenu;
use ChestAPI\menu\DoubleChestMenu;
use ChestAPI\menu\MenuManager;
use ChestAPI\menu\interface\IMenu;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;

class ChestAPI extends PluginBase{
    /** @var self */
    private static self $instance;

    public function onLoad() : void{
        self::$instance = $this;
    }

    /**
     * @return self
     */
    public static function getInstance() : self{
        return self::$instance;
    }

    /** @var MenuManager */
    private MenuManager $manager;

    public function onEnable() : void{
        $this->loadManager();
        $this->loadListeners();
    }

    private function loadManager() : void{
        $this->manager = new MenuManager();
    }

    private function loadListeners() : void{
        $listeners = [
            new ChestCloseListener(),
            new ChestTransactionListener()
        ];

        foreach($listeners as $listener){
            $this->getServer()->getPluginManager()->registerEvents(
                $listener,
                $this
            );
        }
    }

    /**
     * @param string $title
     * 
     * @return ChestMenu
     */
    public function createMenu(string $title) : ChestMenu{
        return new ChestMenu($title);
    }

    /**
     * @param string $title
     * 
     * @return DoubleChestMenu
     */
    public function createDoubleMenu(string $title) : DoubleChestMenu{
        return new DoubleChestMenu($title);
    }

    /**
     * @param Player $player
     * 
     * @return ?IMenu
     */
    public function getMenu(Player $player) : ?IMenu{
        return $this->manager->get($player);
    }

    /**
     * @param Player $player
     * @param IMenu $menu
     */
    public function sendMenu(
        Player $player,
        IMenu $menu
    ) : void{
        $this->closeMenu($player); // If other menu already opened

        $menu->onSend($player);
    }

    /**
     * @param Player $player
     * @param IMenu $menu
     */
    public function openMenu(
        Player $player,
        IMenu $menu
    ) : void{
        $this->manager->set(
            $player,
            $menu
        );

        $menu->onOpen($player);
    }

    /**
     * @param Player $player
     */
    public function closeMenu(Player $player) : void{
        $menu = $this->manager->get($player);
        if($menu === null){
            return;
        }

        $menu->onClose($player);

        $this->manager->remove($player);

        (new MenuCloseEvent(
            $player,
            $menu
        ))->call();
    }

    /**
     * @return MenuManager
     */
    public function getMenuManager() : MenuManager{
        return $this->manager;
    }
}
