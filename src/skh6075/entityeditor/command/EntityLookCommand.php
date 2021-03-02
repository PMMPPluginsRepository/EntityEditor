<?php

namespace skh6075\entityeditor\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use skh6075\entityeditor\EntityEditorLoader;

final class EntityLookCommand extends Command{

    protected EntityEditorLoader $plugin;


    public function __construct(EntityEditorLoader $plugin) {
        parent::__construct("editor look", "entity editor look command.");
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

        if (!$this->plugin->getEventListener()->isEditorQueue($player)) {
            $this->plugin->getEventListener()->addEditorQueue($player, "look");
            $player->sendMessage(TextFormat::GREEN . "Touch the entity you want to look.");
        } else {
            $this->plugin->getEventListener()->deleteEditorQueue($player);
            $player->sendMessage(TextFormat::GREEN . "The work in progress has been ended.");
        }
        return true;
    }
}