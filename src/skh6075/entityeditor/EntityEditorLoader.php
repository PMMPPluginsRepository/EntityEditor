<?php

namespace skh6075\entityeditor;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use skh6075\entityeditor\command\EntityCalculatePositionCommand;
use skh6075\entityeditor\command\EntityIdViewerCommand;
use skh6075\entityeditor\command\EntityLookCommand;
use skh6075\entityeditor\command\EntityScaleCommand;
use skh6075\entityeditor\listener\EventListener;

final class EntityEditorLoader extends PluginBase{
    use SingletonTrait;

    protected EventListener $event;


    public function onLoad(): void{
        self::setInstance($this);
    }

    public function onEnable(): void{
        $this->getServer()->getCommandMap()->registerAll(strtolower($this->getName()), [
            new EntityLookCommand($this),
            new EntityIdViewerCommand($this),
            new EntityScaleCommand($this),
            new EntityCalculatePositionCommand($this)
        ]);

        $this->getServer()->getPluginManager()->registerEvents($this->event = new EventListener($this), $this);
    }

    public function getEventListener(): EventListener{
        return $this->event;
    }
}