<?php

namespace skh6075\entityeditor;

use Closure;
use pocketmine\event\EventPriority;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use skh6075\entityeditor\command\EntityEditorCommand;

final class Loader extends PluginBase{
	use SingletonTrait;

	public static function getInstance() : Loader{
		return self::$instance;
	}

	/**
	 * @phpstan-var array<string, Closure>
	 * @var Closure[]
	 */
	public static array $queue = [];

	protected function onLoad() : void{
		self::$instance = $this;
	}

	protected function onEnable() : void{
		$this->getServer()->getCommandMap()->register(strtolower($this->getName()), new EntityEditorCommand($this));
		$this->getServer()->getPluginManager()->registerEvent(DataPacketReceiveEvent::class, function(DataPacketReceiveEvent $event): void{
			$packet = $event->getPacket();
			$player = $event->getOrigin()->getPlayer();
			if(
				$player !== null &&
				$packet instanceof InventoryTransactionPacket &&
				$packet->trData instanceof UseItemOnEntityTransactionData
			){
				$rawID = $player->getUniqueId()->getBytes();
				$entity = $player->getWorld()->getEntity($packet->trData->getActorRuntimeId());
				if(!isset(self::$queue[$rawID]) || $entity === null){
					return;
				}
				(self::$queue[$rawID])($player, $entity);
			}
		}, EventPriority::MONITOR, $this, false);
	}
}