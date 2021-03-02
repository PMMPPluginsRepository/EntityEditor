<?php

namespace skh6075\entityeditor\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use skh6075\entityeditor\EntityEditorLoader;

final class EntityCalculatePositionCommand extends Command{

    protected EntityEditorLoader $plugin;


    public function __construct(EntityEditorLoader $plugin) {
        parent::__construct("editor pos", "entity editor calculate position command.");
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
        $x = array_shift($args) ?? "";
        $y = array_shift($args) ?? "";
        $z = array_shift($args) ?? "";
        if (trim($entityId) === "" or !is_numeric($entityId) or trim($x) === "" or !is_numeric($x) or trim($y) === "" or !is_numeric($y) or trim($z) === "" or !is_numeric($z)) {
            $player->sendMessage(TextFormat::WHITE . "/" . $this->getName() . " [entityId:int] [x:float] [y:float] [z:float]");
            return false;
        }

        if (!($entity = Server::getInstance()->findEntity($entityId)) instanceof Entity) {
            $player->sendMessage(TextFormat::RED . "The entity could not be found.");
            return false;
        }

        $entity->teleport($entity->add($x, $y, $z));
        $player->sendMessage(TextFormat::GREEN . "The location of the entity has been moved by x: " . $x . ", y: " . $y . ", z: " . $z . ".");
        return true;
    }
}