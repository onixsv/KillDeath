<?php

namespace ojy\pvpdata\command;

use ojy\pvpdata\DataType;
use ojy\pvpdata\PvpData;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class DeathRankCommand extends Command implements DataType{

	/**
	 * KillRankCommand constructor.
	 */
	public function __construct(){
		parent::__construct("데스순위", "데스순위를 확인합니다.", "/데스순위 [페이지]", ["deathrank"]);
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $commandLabel
	 * @param array         $args
	 *
	 * @return mixed|void
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args){
		$page = 1;
		if(count($args) === 1)
			$page = array_shift($args);
		if(is_numeric($page)){
			arsort(PvpData::$db[self::DEATH]);
			$deathData = PvpData::$db[self::DEATH];
			if(count($deathData) > 0){
				$maxPage = ceil(count($deathData) / 6);
				$page = floor($page);
				if($page > $maxPage)
					$page = $maxPage;
				$index1 = $page * 6 - 6;
				$index2 = $page * 6 - 1;
				$c = 0;
				$sender->sendMessage("§d<§f시스템§d> §f데스 순위를 표시합니다. ({$page}/{$maxPage})");
				foreach($deathData as $playerName => $death){
					if($c >= $index1 && $c <= $index2){
						$kill = PvpData::get($playerName, self::KILL);
						$winningRate = round(($kill / ($death + $kill)), 2);
						$sender->sendMessage("§d<" . ($c + 1) . "§f위§d> §f{$playerName} §a> §f{$death}데스, 승률: §d{$winningRate}%");
						++$c;
					}
				}
			}else{
				$sender->sendMessage(self::PREFIX . "기록된 데스 데이터가 없습니다.");
			}
		}else{
			$sender->sendMessage(self::PREFIX . $this->getUsage());
		}
	}
}