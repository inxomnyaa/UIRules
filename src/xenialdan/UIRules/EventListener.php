<?php

namespace xenialdan\UIRules;

use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\form\Form;
use pocketmine\form\ModalForm;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class EventListener implements Listener{
	/** @var Loader */
	public $owner;

	public function __construct(Plugin $plugin){
		$this->owner = $plugin;
	}

	public function onInteract(PlayerInteractEvent $event){
		if (($level = ($player = $event->getPlayer())->getLevel())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
		if (($item = $event->getItem())->getId() !== ItemIds::BOOK) return;
		$event->setCancelled();
		$rules = implode("\n", Loader::getRules());
		$player->sendForm(
			new class(TextFormat::DARK_RED . "Rules", TextFormat::DARK_RED . "These are the rules of the server. Violation leads into warn points and bans. Please read them carefully.\n" . $rules . "\n" . TextFormat::DARK_RED . "Do you accept the rules?", "Accept", "Don't accept") extends ModalForm{
				public function onSubmit(Player $player): ?Form{
					if ($this->getChoice() === true){
						$player->sendForm(new class("Welcome!", "Welcome on the server! Have fun playing!") extends ModalForm{
						}, true);
					} elseif (is_null($this->getChoice())){
						$player->sendForm($this);
					} else{
						$player->kick(TextFormat::RED . "You MUST accept the rules to play on this server", false);
					}
					return null;
				}
			}
			, true
		);
	}

	public function onJoin(PlayerJoinEvent $event){
		if (($level = ($player = $event->getPlayer())->getLevel())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
		$book = ItemFactory::get(ItemIds::BOOK)->setCustomName(TextFormat::DARK_RED. "Rules");
		if(!$player->getInventory()->contains($book))
		$player->getInventory()->addItem($book);
		if ($player->hasPlayedBefore()) return;
		/*$rules = implode("\n", Loader::getRules());
		$player->sendForm(
			new class(TextFormat::DARK_RED . "Rules", TextFormat::DARK_RED . "These are the rules of the server. Violation leads into warn points and bans. Please read them carefully.\n" . $rules . "\n" . TextFormat::DARK_RED . "Do you accept the rules?", "Accept", "Don't accept") extends ModalForm{
				public function onSubmit(Player $player): ?Form{
					if ($this->getChoice() === true){
						$player->sendForm(new class("Welcome!", "Welcome on the server! Have fun playing!") extends ModalForm{
						}, true);
					} elseif (is_null($this->getChoice())){
						$player->sendForm($this);
					} else{
						$player->kick(TextFormat::RED . "You MUST accept the rules to play on this server", false);
					}
					return null;
				}
			}
			, true
		);*/
	}

	public function onLevelChange(EntityLevelChangeEvent $event){
		/** @var Player $player */
		if(!($player = $event->getEntity()) instanceof Player) return;
		if (($level = $event->getTarget())->getId() !== Server::getInstance()->getDefaultLevel()->getId()) return;
		$book = ItemFactory::get(ItemIds::BOOK)->setCustomName(TextFormat::DARK_RED. "Rules");
		if(!$player->getInventory()->contains($book))
			$player->getInventory()->addItem($book);
	}
}