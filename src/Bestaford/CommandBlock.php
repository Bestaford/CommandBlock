<?php

namespace Bestaford;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\utils\Config;

class CommandBlock extends PluginBase implements Listener {
	
	public $config;
	public $commands;

	public function onEnable() {
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
		$this->commands = $this->config->get("commands");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onPlayerCommandPreprocess(PlayerCommandPreprocessEvent $event) {
		$player = $event->getPlayer();
		if(!$player->hasPermission("commandblocker.bypass")) {
			$args = explode(" ", $event->getMessage());
 			$cmd = array_shift($args);
			if(strpos($cmd, "/") !== false) {
				$cmd = str_replace("/", "", $cmd);
				$command = $this->getServer()->getCommandMap()->getCommand($cmd);
				if($command !== null) {
					$name = $command->getName();
					if(in_array($name, $this->commands)) {
						$event->setCancelled(true);
						$player->sendMessage($this->config->get("command_blocked"));
					}
				}
			}
		}
	}
}