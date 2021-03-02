<?php

namespace skh6075\entityeditor\listener;

use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use skh6075\entityeditor\EntityEditorLoader;

final class EventListener implements Listener{

    protected EntityEditorLoader $plugin;

    private array $queue = [];


    public function __construct(EntityEditorLoader $plugin) {
        $this->plugin = $plugin;
    }

    public function isEditorQueue(Player $player): bool{
        return isset($this->queue[$player->getLowerCaseName()]);
    }

    public function addEditorQueue(Player $player, string $type): bool{
        if (!$this->isEditorQueue($player)) {
            $this->queue[$player->getLowerCaseName()] = $type;
            return true;
        }
        return false;
    }

    public function deleteEditorQueue(Player $player): bool{
        if ($this->isEditorQueue($player)) {
            unset($this->queue[$player->getLowerCaseName()]);
            return true;
        }
        return false;
    }

    /** @see Living*/
    private function lookAt(Player $player, Entity $entity) : void{
        $horizontal = sqrt(($player->x - $entity->x) ** 2 + ($player->z - $entity->z) ** 2);
        $vertical = $player->y - $entity->y;
        $pitch = -atan2($vertical, $horizontal) / M_PI * 180;

        $xDist = $player->x - $entity->x;
        $zDist = $player->z - $entity->z;
        $yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;
        if($yaw < 0){
            $yaw += 360.0;
        }

        $entity->yaw = $yaw;
        $entity->pitch = $pitch;
    }


    /** @priority HIGHEST */
    public function onDataPacketReceive(DataPacketReceiveEvent $event): void{
        $player = $event->getPlayer();
        $packet = $event->getPacket();

        /** @var InventoryTransactionPacket $packet */
        if (!$packet instanceof InventoryTransactionPacket)
            return;

        if ($packet->transactionType !== InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY)
            return;

        if (!in_array($packet->trData->actionType, [InventoryTransactionPacket::USE_ITEM_ON_ENTITY_ACTION_ATTACK, InventoryTransactionPacket::USE_ITEM_ON_ENTITY_ACTION_INTERACT]))
            return;

        $entityId = $packet->trData->entityRuntimeId;
        if (!($entity = Server::getInstance()->findEntity($entityId)) instanceof Entity)
            return;

        if (!$this->isEditorQueue($player))
            return;

        switch ($this->queue[$player->getLowerCaseName()]) {
            case "id":
                $player->sendMessage(TextFormat::AQUA . "EntityId: " . TextFormat::WHITE . $entity->getId());
                break;
            case "look":
                $this->lookAt($player, $entity);
                break;
            default:
                break;
        }
    }
}