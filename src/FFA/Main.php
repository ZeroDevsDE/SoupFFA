<?php

# ╔═══╗╔═╗╔═╗╔═══╗╔═╗╔═╗╔═══╗╔═══╗╔═══╗╔════╗╔═══╗
# ║╔═╗║║║╚╝║║║╔══╝╚╗╚╝╔╝║╔═╗║║╔══╝║╔═╗║║╔╗╔╗║║╔═╗║
# ║╚═╝║║╔╗╔╗║║╚══╗─╚╗╔╝─║╚═╝║║╚══╗║╚═╝║╚╝║║╚╝║╚══╗
# ║╔══╝║║║║║║║╔══╝─╔╝╚╗─║╔══╝║╔══╝║╔╗╔╝──║║──╚══╗║
# ║║───║║║║║║║╚══╗╔╝╔╗╚╗║║───║╚══╗║║║╚╗──║║──║╚═╝║
# ╚╝───╚╝╚╝╚╝╚═══╝╚═╝╚═╝╚╝───╚═══╝╚╝╚═╝──╚╝──╚═══╝

namespace FFA;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as SF;
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

class Main extends PluginBase implements Listener
{
    public $prefix = SF::GRAY . "» " . SF::AQUA . "SoupFFA" . SF::GRAY . " » ";

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->Banner();
        $this->getLogger()->info(SF::GREEN . "SoupFFA was activated!");
    }

    public function onDisable()
    {
        $this->Banner();
        $this->getLogger()->info(SF::GREEN . "SoupFFA was stopped!");
    }

    private function Banner()
    {
        $banner = strval(
            "\n" .
            "╔═══╗╔═╗╔═╗╔═══╗╔═╗╔═╗╔═══╗╔═══╗╔═══╗╔════╗╔═══╗\n" .
            "║╔═╗║║║╚╝║║║╔══╝╚╗╚╝╔╝║╔═╗║║╔══╝║╔═╗║║╔╗╔╗║║╔═╗║\n" .
            "║╚═╝║║╔╗╔╗║║╚══╗─╚╗╔╝─║╚═╝║║╚══╗║╚═╝║╚╝║║╚╝║╚══╗\n" .
            "║╔══╝║║║║║║║╔══╝─╔╝╚╗─║╔══╝║╔══╝║╔╗╔╝──║║──╚══╗║\n" .
            "║║───║║║║║║║╚══╗╔╝╔╗╚╗║║───║╚══╗║║║╚╗──║║──║╚═╝║\n" .
            "╚╝───╚╝╚╝╚╝╚═══╝╚═╝╚═╝╚╝───╚═══╝╚╝╚═╝──╚╝──╚═══╝"
        );
        $this->getLogger()->info($banner);
    }

    public function setItems(Player $player)
    {
        //clear
        $player->getInventory()->clearAll();
        //Armor
        $player->getArmorInventory()->setHelmet(Item::get(306));
        $player->getArmorInventory()->setChestplate(Item::get(307));
        $player->getArmorInventory()->setLeggings(Item::get(308));
        $player->getArmorInventory()->setBoots(Item::get(309));
        //Sword
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

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getName();
        $event->setJoinMessage($this->prefix . SF::GREEN . "[+] " . $player->getName());
        $player->sendMessage(SF::GRAY . "Willkommen in " . SF::GREEN . "SoupFFA" . SF::GRAY . "!");
        foreach ($this->getServer()->getOnlinePlayers() as $OnPly) {
            $OnPly->sendPopup(SF::GRAY . $name . SF::DARK_GREEN . " will kämpfen!");
        }
        $this->setItems($player);
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $player = $event->getPlayer();
        $this->setItems($player);
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $event->setDrops([]);
        }

        $killer = $player->getLastDamageCause();
        if ($killer instanceof EntityDamageByEntityEvent) {
            $killer = $killer->getDamager();
            if ($killer instanceof Player) {
                $killer->sendPopup("+ 1");
                $event->setDeathMessage($this->prefix . SF::GREEN . $player->getName() . SF::GRAY . " wurde von " . SF::GREEN . $killer->getName() . SF::GRAY . " getötet!");
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($item->getId() == 282) {
            $event->setCancelled(true);
            $player->getInventory()->removeItem($item);
            $player->setHealth(20);
            $player->setFood(20);
        }
    }

    public function onHurt(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        $v = new Vector3($entity->getLevel()->getSpawnLocation()->getX(), $entity->getPosition()->getY(), $entity->getLevel()->getSpawnLocation()->getZ());
        $r = $this->getServer()->getSpawnRadius();
        if (($entity instanceof Player) && ($entity->getPosition()->distance($v) <= $r)) {
            $event->setCancelled(true);
        }
    }

    public function onPickUp(InventoryPickupItemEvent $event)
    {
        $event->setCancelled(true);
    }

    public function onPlayerMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $v = new Vector3($player->getLevel()->getSpawnLocation()->getX(), $player->getPosition()->getY(), $player->getLevel()->getSpawnLocation()->getZ());
        $r = $this->getServer()->getSpawnRadius();
        if (($player instanceof Player) && ($player->getPosition()->distance($v) <= $r)) {
            $player->sendTip($this->prefix . SF::GREEN . "Hier bist du sicher!");
        } else {
            $player->sendTip($this->prefix . SF::RED . SF::BOLD . "ACHTUNG!" . SF::RESET . SF::GREEN . " Hier bist du verwundbar!");
        }
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $event->setCancelled(true);
        if ($player->isOp()) {
            $event->setCancelled(false);
        }
    }

    public function onTransaction(InventoryTransactionEvent $event)
    {
        $event->setCancelled(true);
    }

    public function onDrop(PlayerDropItemEvent $event)
    {
        $player = $event->getPlayer();
        $event->setCancelled(true);
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $event->setCancelled(true);
    }
}