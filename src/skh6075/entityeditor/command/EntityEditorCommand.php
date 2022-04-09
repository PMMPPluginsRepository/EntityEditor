<?php

declare(strict_types=1);

namespace skh6075\entityeditor\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\utils\TextFormat;
use skh6075\entityeditor\Loader;

final class EntityEditorCommand extends Command implements PluginOwned{
	use PluginOwnedTrait;

	public function __construct(Loader $loader){
		parent::__construct('editor', 'entity editor command');
		$this->setPermission("entity.editor.permission");
		$this->owningPlugin = $loader;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
		if(!$sender instanceof Player || !$this->testPermission($sender)){
			return false;
		}
		switch(array_shift($args) ?? ''){
			case 'id':
				$func = static function(Player $player, Entity $entity): void{
					$player->sendMessage(TextFormat::AQUA  . "EntityID: {$entity->getId()}");
				};
				Loader::$queue[$sender->getUniqueId()->getBytes()] = $func;
				$sender->sendMessage(TextFormat::YELLOW . "Click on the entity whose id you want to check");
				break;
			case 'look':
				$func = static function(Player $player, Entity $entity): void{
					$entity->setRotation($player->getLocation()->yaw, $player->getLocation()->pitch);
				};
				Loader::$queue[$sender->getUniqueId()->getBytes()] = $func;
				$sender->sendMessage(TextFormat::YELLOW . "Click on the entity you want to look at");
				break;
			case 'pos':
				$x = array_shift($args) ?? '';
				$y = array_shift($args) ?? '';
				$z = array_shift($args) ?? '';
				if(!is_numeric($x) || !is_numeric($y) || !is_numeric($z)){
					$sender->sendMessage(TextFormat::RED . "/" . $this->getName() . " pos [x:float] [y:float] [z:float]");
					return false;
				}
				$offset = array_map('floatval', [$x, $y, $z]);
				$func = static function(Player $player, Entity $entity) use ($offset): void{
					$entity->teleport($entity->getPosition()->add(...$offset));
				};
				Loader::$queue[$sender->getUniqueId()->getBytes()] = $func;
				$sender->sendMessage(TextFormat::YELLOW . "Click on the entity you want to reposition");
				break;
			case 'scale':
				$scale = array_shift($args) ?? '';
				if(!is_numeric($scale)){
					$sender->sendMessage(TextFormat::RED . "/" . $this->getName() . " scale [scale:float]");
					return false;
				}
				$scale = (float)$scale;
				$func = static function(Player $player, Entity $entity) use ($scale): void{
					$entity->setScale(min(30, max(0.1, $scale)));
				};
				Loader::$queue[$sender->getUniqueId()->getBytes()] = $func;
				$sender->sendMessage(TextFormat::YELLOW . "Click the entity whose scale you want to change");
				break;
			default:
				foreach([
					["id", "check entity id"],
					["look", "entity look at me"],
					["pos", "Changed entity position"],
					["scale", "Changed entity scale"]
				] as $value){
					$sender->sendMessage(TextFormat::YELLOW . "/" . $this->getName() . " " . $value[0] . " - " . $value[1]);
				}
				break;
		}
		return true;
	}
}