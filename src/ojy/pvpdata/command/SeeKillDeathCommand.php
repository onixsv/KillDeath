<?php

namespace ojy\pvpdata\command;

use ojy\pvpdata\DataType;
use ojy\pvpdata\PvpData;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class SeeKillDeathCommand extends Command implements DataType{

	public function __construct(){
		parent::__construct("킬뎃보기", "킬뎃을 확인합니다.", "/킬뎃보기 [닉네임]", []);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		$name = $args[0] ?? $sender->getName();
		$player = Server::getInstance()->getPlayerByPrefix($name);
		if($player !== null)
			$name = $player->getName();
		$kill = PvpData::get($name, self::KILL);
		$death = PvpData::get($name, self::DEATH);

		$per = $death + $kill;

		if($per <= 0){
			$winningRate = 0;
		}else{
			$winningRate = round($kill / ($death + $kill), 4) * 100;
		}

		$sender->sendMessage("§l§b[알림] §r§7{$name} 님의 킬뎃 정보");
		$sender->sendMessage("§l§b[알림] §r§7킬 수: {$kill}킬");
		$sender->sendMessage("§l§b[알림] §r§7데스 수: {$death}데스");
		$sender->sendMessage("§l§b[알림] §r§7승률: {$winningRate}%");
	}
}