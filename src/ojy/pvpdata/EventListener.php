<?php

namespace ojy\pvpdata;

use onebone\economyapi\EconomyAPI;
use OnixUtils\OnixUtils;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class EventListener implements Listener, DataType{

	/**
	 * EventListener constructor.
	 */
	public function __construct(){
		Server::getInstance()->getPluginManager()->registerEvents($this, PvpData::$i);
	}

	/**
	 * @param ProjectileHitEntityEvent $event
	 *
	 * @handleCancelled
	 */
	public function onProjectileHit(ProjectileHitEntityEvent $event){
		$pj = $event->getEntity();
		if($pj instanceof Arrow){
			$player = $event->getEntityHit();
			if($player instanceof Player){
				$damager = $pj->getOwningEntity();
				if($damager instanceof Player){
					$damagerName = strtolower($damager->getName());
					if(!isset(PvpData::$db[self::ARROW_ATTACK_COUNT][$damagerName]))
						PvpData::$db[self::ARROW_ATTACK_COUNT][$damagerName] = 0;
					++PvpData::$db[self::ARROW_ATTACK_COUNT][$damagerName];
				}
			}
		}
	}

	/**
	 * @param EntityShootBowEvent $event
	 *
	 * @handleCancelled
	 */
	public function onShootArrow(EntityShootBowEvent $event){
		$player = $event->getEntity();
		if($player instanceof Player){
			$playerName = strtolower($player->getName());
			if(!isset(PvpData::$db[self::SHOOT_COUNT][$playerName]))
				PvpData::$db[self::SHOOT_COUNT][$playerName] = 0;
			++PvpData::$db[self::SHOOT_COUNT][$playerName];
		}
	}

	public static $continuousKill = [];

	/**
	 * @param PlayerDeathEvent $event
	 *
	 * @handleCancelled
	 */
	public function onDeath(PlayerDeathEvent $event){
		$player = $event->getPlayer();
		$cause = $player->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent){
			$event->setDeathMessage(false);
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$damagerName = strtolower($damager->getName());
				$playerName = strtolower($player->getName());
				EconomyAPI::getInstance()->addMoney($damager, 3000);
				Server::getInstance()->broadcastPopup("§d{$playerName}§f님이 §d{$damagerName}§f님에게 살해당했습니다.");
				if(!isset(self::$continuousKill[$damager->getName()]))
					self::$continuousKill[$damager->getName()] = 0;
				++self::$continuousKill[$damager->getName()];
				if(self::$continuousKill[$damager->getName()] >= 3){
					$continuousKill = self::$continuousKill[$damager->getName()];
					$killMoney = $continuousKill * 1500;
					OnixUtils::broadcast("§d{$damager->getName()}§f님이 §d{$continuousKill}§f연속 킬을 달성했습니다. (현상금: §d{$killMoney}§f원)");
				}
				if(isset(self::$continuousKill[$player->getName()])){
					if(self::$continuousKill[$player->getName()] >= 3){
						$continuousKill = self::$continuousKill[$player->getName()];
						$killMoney = $continuousKill * 1500;
						OnixUtils::broadcast("§d{$damager->getName()}§f님이 §d{$player->getName()}§f님을 살해하여 현상금 §d{$killMoney}§f원을 얻었습니다.");
						EconomyAPI::getInstance()->addMoney($damager, $killMoney);
					}
					self::$continuousKill[$player->getName()] = 0;
				}
				if(isset(self::$continuousKill[$player->getName()]))
					self::$continuousKill[$player->getName()] = 0;
				if(!isset(PvpData::$db[self::KILL][$damagerName]))
					PvpData::$db[self::KILL][$damagerName] = 0;
				$kill = ++PvpData::$db[self::KILL][$damagerName];
				if($kill % 100 === 0){
					$money = 30000 * $kill / 100;
					EconomyAPI::getInstance()->addMoney($damager, $money);
					OnixUtils::broadcast("§d{$damager->getName()}§f님이 §d{$kill}§f킬을 달성하여 §d{$money}§f원을 획득했습니다!");
				}
				if(!isset(PvpData::$db[self::DEATH][$playerName]))
					PvpData::$db[self::DEATH][$playerName] = 0;
				++PvpData::$db[self::DEATH][$playerName];
			}
		}
	}

	/**
	 * @param EntityDamageEvent $event
	 *
	 * @handleCancelled
	 */
	public function onHit(EntityDamageEvent $event){
		if($event instanceof EntityDamageByEntityEvent){
			$e = $event->getEntity();
			$d = $event->getDamager();
			if($e instanceof Player && $d instanceof Player){
				if(!$event->isCancelled()){
					$damagerName = strtolower($d->getName());
					if(!isset(PvpData::$db[self::DAMAGE][$damagerName]))
						PvpData::$db[self::DAMAGE][$damagerName] = 0;
					PvpData::$db[self::DAMAGE][$damagerName] += $event->getFinalDamage();
					if(!isset(PvpData::$db[self::ATTACK_COUNT][$damagerName]))
						PvpData::$db[self::ATTACK_COUNT][$damagerName] = 0;
					++PvpData::$db[self::ATTACK_COUNT][$damagerName];
				}
			}
		}
	}
}