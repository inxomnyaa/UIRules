<?php

namespace xenialdan\UIRules;

use pocketmine\level\Location;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;


class Loader extends PluginBase{
	/** @var Loader */
	private static $instance = null;
	/** @var Config */
	private $rules;

	/**
	 * @param string $rule
	 * @return bool
	 */
	public static function addRule(string $rule): bool{
		self::getInstance()->rules->set(count(self::getInstance()->rules->getAll()), $rule);
		self::getInstance()->rules->save(true);
		return true;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public static function removeRule($name){
		if (self::getInstance()->rules->get($name) === false) return false;
		self::getInstance()->rules->remove($name);
		self::getInstance()->rules->save(true);
		return true;
	}

	/**
	 * @return array
	 */
	public static function getRules(){
		return self::getInstance()->rules->getAll();
	}

	public function onLoad(){
		self::$instance = $this;
		$this->rules = new Config($this->getDataFolder() . "rules.yml");
	}

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getCommandMap()->register(Commands::class, new Commands($this));
	}

	/**
	 * Returns an instance of the plugin
	 * @return Loader
	 */
	public static function getInstance(){
		return self::$instance;
	}
}