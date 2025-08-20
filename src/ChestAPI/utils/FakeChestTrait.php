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

namespace ChestAPI\utils;

use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\block\BlockIds;
use pocketmine\tile\Chest;
use pocketmine\tile\Nameable;
use pocketmine\math\Vector3;
use pocketmine\Player;
use function is_string;
use RuntimeException;

trait FakeChestTrait{

    /**
     * @param Player $player
     * @param string $title
     * @param Vector3 $vector
     * @param ?Vector3 $pairVector
     */
    public function sendChest(
        Player $player,
        string $title,
        Vector3 $vector,
        ?Vector3 $pairVector = null
    ) : void{
        $nbt = Chest::createNBT($vector);

        $nbt->setString(
            Nameable::TAG_CUSTOM_NAME,
            $title
        );

        if($pairVector !== null){
            $nbt->setInt(
                Chest::TAG_PAIRX,
                (int) $pairVector->x
            );

            $nbt->setInt(
                Chest::TAG_PAIRZ,
                (int) $pairVector->z
            );
        }

        $blockPacket = new UpdateBlockPacket();
        $blockPacket->x = (int) $vector->x;
        $blockPacket->y = (int) $vector->y;
        $blockPacket->z = (int) $vector->z;
        $blockPacket->blockId = BlockIds::CHEST;
        $blockPacket->blockMeta = 0;
        $blockPacket->flags = UpdateBlockPacket::FLAG_ALL;

        $writed = (new NetworkLittleEndianNBTStream())->write($nbt);
        if(!is_string($writed)){
            throw new RuntimeException("Failed to write NBT"); // For phpstan xd
        }

        $tilePacket = new BlockActorDataPacket();
        $tilePacket->x = (int) $vector->x;
        $tilePacket->y = (int) $vector->y;
        $tilePacket->z = (int) $vector->z;
        $tilePacket->namedtag = $writed;

        $player->sendDataPacket($blockPacket);
        $player->sendDataPacket($tilePacket);
    }
}
