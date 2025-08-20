# ChestAPI

**ChestAPI** is a plugin API for creating easy-to-use Chest UIs on [MiSoft](https://github.com/MultiMCPE/MiSoft) (a PMMP fork).  
It supports **any version of MiSoft**.

## Features

- Simple creation of chest menus  
- Add items to specific slots  
- Click handling with callbacks  
- Full menu management through API  

## Installation

1. Download the latest release from [Releases](https://github.com/Arelicka/ChestAPI/releases).  
2. Put the unzipped plugin folder into your server’s `plugins/` folder. 
3. Enable option `folder-plugin-loader` in `MiSoft.yml` config.
4. Restart the server.  

## Usage

Example of creating a menu:

```php
$instance = \ChestAPI\ChestAPI::getInstance();
$menu = $instance->createMenu("test");

$menu->setItem(
    1,
    \pocketmine\item\ItemFactory::get(2, 0, 3),
    function(Player $player) : void {
        $player->sendMessage("hello!");
    }
);

$menu->setItem(
    2,
    \pocketmine\item\ItemFactory::get(2, 0, 4),
    function(Player $player) : void {
        $player->sendMessage("hello 2!");
    }
);

$menu->setItem(
    4,
    \pocketmine\item\ItemFactory::get(2, 0, 10)
);

$instance->sendMenu($player, $menu);

$this->getScheduler()->scheduleDelayedTask(new \pocketmine\scheduler\ClosureTask(
    function(int $currentTick) use ($player, $menu, $instance) : void {
        $instance->openMenu($player, $menu);
    }
), 10);
```

## API

- ChestAPI::createMenu(string $title) — create a new menu

- Menu::setItem(int $slot, Item $item, ?Closure $closure = null) — set an item in a slot (with optional click handler)

- ChestAPI::sendMenu(Player $player, IMenu $menu) — send the chest block and tile to a player

- ChestAPI::openMenu(Player $player, IMenu $menu) — open chest inventory window (for good performance without window auto-close recommended to use delayed ClosureTask after 10 ticks)


## Requirements

- MiSoft server (any version)
- PHP 8.3+
