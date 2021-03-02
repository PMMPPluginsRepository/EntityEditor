<?php

namespace skh6075\entityeditor\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use skh6075\entityeditor\EntityEditorLoader;

final class EntityScaleCommand extends Command{

    protected EntityEditorLoader $plugin;


    public function __construct(EntityEditorLoader $plugin) {
        parent::__construct("editor scale", "entity editor set Entity Scale command.");
        $this->setPermission("entity.editor.permission");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $player, string $label, array $args): bool{
        if (!$player instanceof Player) {
            $player->sendMessage(TextFormat::RED . "Can only be used in-game.");
            return false;
        }

        if (!$this->testPermission($player)) {
            $player->sendMessage(TextFormat::RED . "You do not have permission to use this command.");
            return false;
        }

        $entityId = array_shift($args) ?? "";
        $scale = array_shift($args) ?? "";
        if (trim($entityId) === "" or !is_numeric($entityId) or trim($scale) === "" or !is_numeric($scale)) {
            $player->sendMessage(TextFormat::WHITE . "/" . $this->getName() . " [entityId:int] [scale:float]");
            return false;
        }

        if (!($entity = Server::getInstance()->findEntity($entityId)) instanceof Entity) {
            $player->sendMessage(TextFormat::RED . "The entity could not be found.");
            return false;
        }

        $entity->getDataPropertyManager()->setFloat(Entity::DATA_SCALE, $scale);
        $entity->setScale($scale);
        $player->sendMessage(TextFormat::GREEN . "The scale of the entity is set to " . $scale . ".");
        return true;
    }
}