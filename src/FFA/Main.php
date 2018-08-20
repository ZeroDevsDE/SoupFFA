<?php

namespace FFA;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {
	
	
	public $playercfg;
	
	public $Prefix = TextFormat::AQUA . "SoupFFA" . TextFormat::GRAY . "|";
	
	public function onLoad() {
		
		$this->getServer()->getLogger()->info($this->Prefix . TextFormat::GOLD . "SoupFFA faehrt hoch...");
		
	}
	
	public function onEnable() {
		
		@mkdir($this->getDataFolder());

		$cfg = new Config($this->getDataFolder() . "Einstellungen.yml", Config::YAML);
		
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getLogger()->info($this->Prefix . TextFormat::GREEN . "SoupFFA erfolgreich hochgefahren!");
		$this->getServer()->getLogger()->info($this->Prefix . TextFormat::AQUA . "Codet by Fosro");
		$this->getServer()->getLogger()->info($this->Prefix . TextFormat::GOLD . "github.com/PMExperts");
		
	}
	
	public function setItems(Player $player) {
		
		$player->getInventory()->clearAll();
		
		$player->getArmorInventory()->setHelmet(Item::get(306));
		$player->getArmorInventory()->setChestplate(Item::get(307));
		$player->getArmorInventory()->setLeggings(Item::get(308));
		$player->getArmorInventory()->setBoots(Item::get(309));
		//Irone Sword
		$player->getInventory()->setItem(0, Item::get(267));
		//Soups 
		$player->getInventory()->setItem(1, Item::get(282));
		$player->getInventory()->setItem(2, Item::get(282));
		$player->getInventory()->setItem(3, Item::get(282));
		$player->getInventory()->setItem(4, Item::get(282));
		$player->getInventory()->setItem(5, Item::get(282));
		$player->getInventory()->setItem(6, Item::get(282));
		$player->getInventory()->setItem(7, Item::get(282));
		$player->getInventory()->setItem(8, Item::get(282));
	
	}
	
	public function onJoin(PlayerJoinEvent $event) {
		
		$player = $event->getPlayer();
		$name = $player->getName();
		$ip = $player->getAddress();
		$cid = $player->getClientId();
		
		@mkdir($this->getDataFolder() . "/Spieler/");
		
		$playercfg = new Config($this->getDataFolder() . "/Spieler/". strtolower($name) . ".yml", Config::YAML);
		
		$playercfg->set("Cid", $cid);
		$playercfg->set("Ip", $ip);
		$playercfg->set("Online", "Ja");
		
		if(empty($playercfg->get("Cid"))) {
			
			$playercfg->set("Cid", $cid);
			
		}
		
		if(empty($playercfg->get("Ip"))) {
			
			$playercfg->set("Ip", $Ip);
			
		}
		
		if(empty($playercfg->get("Online"))) {
			
			$playercfg->set("Online", "Unbekannt");
			
		}
		
		if(empty($playercfg->get("Kills"))) {
			
			$playercfg->set("Kills", "Soon!");
			
		}
		
		if(empty($playercfg->get("Coins"))) {
			
			$playercfg->set("Coins", 0);
			
		}
		
		$playercfg->save();
		
		$player->teleport($this->getServer()->getDefaultLevel()->getSpawnLocation());
		
		$event->setJoinMessage("$this->Prefix [+] $player");
		$player->sendMessage("Willkommen in " . $this->Prefix);
		
		foreach($this->getServer()->getOnlinePlayers() as $OnPly) {
			
			$OnPly->sendPopup(TextFormat::GRAY . $name . TextFormat::DARK_GREEN . " will kaempfen!");
			
		}
		
		$this->setItems($player);
		
	}
	
	public function onRespawn(PlayerRespawnEvent $event) {
		
		$player = $event->getPlayer();
		
		$this->setItems($player);
		
	}
	
	public function onDeath(PlayerDeathEvent $event) {
		
		$player = $event->getPlayer();
		$name = $player->getName();
		$entity = $event->getEntity();
		
		if($entity instanceof Player) {
			
			$event->setDrops([]);
			
		}

		$ursache = $player->getLastDamageCause();
		
		if($ursache instanceof EntityDamageByEntityEvent){
			
			$killer = $ursache->getDamager();
			
			if($killer instanceof Player){
						
				$killer->sendPopup("+ 1");
				
				$event->setDeathMessage($this->Prefix . "" . $player->getName() . " wurde von " . $killer->getName() . " getoetet");
				
			}
			
		}
		
	}
		
	
	public function onInteract(PlayerInteractEvent $event) {
		
		$player = $event->getPlayer();
		$name = $player->getName();
		$item = $event->getItem();
		
		if($item->getId() == 282) {
			
			$event->setCancelled(true);
			$player->getInventory()->removeItem($item);
			$player->setHealth(20);
			$player->setFood(20);
			
		}
		
	}
	
	public function onHurt(EntityDamageEvent $event) {
		
		$entity = $event->getEntity();
		
		$v = new Vector3($entity->getLevel()->getSpawnLocation()->getX(),$entity->getPosition()->getY(),$entity->getLevel()->getSpawnLocation()->getZ());
		$r = $this->getServer()->getSpawnRadius();
		
		if(($entity instanceof Player) && ($entity->getPosition()->distance($v) <= $r)) {
			
			$event->setCancelled(true);
			
		}
		
	}
	
	public function onPickUp(InventoryPickupItemEvent $event) {
		
		$event->setCancelled(true);
		
	}
	
	public function onPlayerMove(PlayerMoveEvent $event) {
		
		$player = $event->getPlayer();
		
		$v = new Vector3($player->getLevel()->getSpawnLocation()->getX(),$player->getPosition()->getY(),$player->getLevel()->getSpawnLocation()->getZ());
		$r = $this->getServer()->getSpawnRadius();
		
		if(($player instanceof Player) && ($player->getPosition()->distance($v) <= $r)) {
			
			$player->sendTip(TextFormat::GREEN . "$this->Prefix Du bist sicher!");
			
		}
		
		else{
			
			$player->sendTip(TextFormat::RED . "$this->Prefix Du bist verwundbar!");
			
		}
		
	}
	
	public function onBreak(BlockBreakEvent $event) {
		
		$player = $event->getPlayer();
		$name = $player->getName();
		
		$event->setCancelled(true);
		
		if($player->isOp()) {
			
			$event->setCancelled(false);
			
		}
		
	}
	
	public function onTransaction(InventoryTransactionEvent $event) {
			
		$event->setCancelled(true);
		
	}
	
	public function onDrop(PlayerDropItemEvent $event) {
		
		$player = $event->getPlayer();
		$name = $player->getName();
		
		$event->setCancelled(true);
		
	}
	
	public function onPlace(BlockPlaceEvent $event) {
		
		$event->setCancelled(true);
		
	}
	
	
	
}
