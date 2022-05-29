<?php

namespace ojy\pvpdata;

use ojy\pvpdata\command\DeathRankCommand;
use ojy\pvpdata\command\KillRankCommand;
use ojy\pvpdata\command\SeeKillDeathCommand;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\Config;

class PvpData extends PluginBase implements DataType{

	/** @var self */
	public static PvpData $i;

	/** @var Config */
	public static Config $data;

	/** @var array */
	public static array $db;

	protected function onLoad() : void{
		self::$i = $this;
	}

	protected function onEnable() : void{
		///// CONFIG /////
		self::$data = new Config($this->getDataFolder() . "Data.yml", Config::YAML, [
			"kill" => [],
			"death" => [],
			"damage" => [],
			"attack-count" => [],
			"shoot-count" => [],
			"arrow-attack-count" => []
		]);
		self::$db = self::$data->getAll();

		///// COMMAND /////
		foreach([
			KillRankCommand::class,
			DeathRankCommand::class,
			SeeKillDeathCommand::class
		] as $c)
			Server::getInstance()->getCommandMap()->register("PvpData", new $c);

		///// SAVE TASK /////
		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{
			self::save();
		}), 20 * 60 * 10);
	}

	public static function get(string $playerName, string $type) : int{
		return self::$db[$type][strtolower($playerName)] ?? 0;
	}

	public static function getByPlayer(Player $player, string $type) : int{
		return self::$db[$type][strtolower($player->getName())] ?? 0;
	}

	public static function save() : void{
		self::$data->setAll(self::$db);
		self::$data->save();
	}

	protected function onDisable() : void{
		self::save();
	}

}