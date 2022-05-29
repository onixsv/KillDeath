<?php

namespace ojy\pvpdata\command;

use ojy\pvpdata\DataType;
use ojy\pvpdata\PvpData;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class KillRankCommand extends Command implements DataType{

	/**
	 * KillRankCommand constructor.
	 */
	public function __construct(){
		parent::__construct("킬순위", "킬순위를 확인합니다.", "/킬순위 [페이지]", ["killrank"]);
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
			arsort(PvpData::$db[self::KILL]);
			$killData = PvpData::$db[self::KILL];
			if(count($killData) > 0){
				$maxPage = ceil(count($killData) / 6);
				$page = floor($page);
				if($page > $maxPage)
					$page = $maxPage;
				$index1 = $page * 6 - 6;
				$index2 = $page * 6 - 1;
				$c = 0;
				$sender->sendMessage("§d<§f시스템§d> §f킬 순위를 표시합니다. ({$page}/{$maxPage})");
				foreach($killData as $playerName => $kill){
					if($c >= $index1 && $c <= $index2){
						$winningRate = round($kill / (PvpData::get($playerName, self::DEATH) + $kill), 4) * 100;
						$sender->sendMessage("§d<" . ($c + 1) . "§f위§d> §f{$playerName} §a> §f{$kill}킬, 승률: §d{$winningRate}%");
					}
					++$c;
					if($index2 < $c)
						break;
				}
			}else{
				$sender->sendMessage(self::PREFIX . "기록된 킬 데이터가 없습니다.");
			}
		}else{
			$sender->sendMessage(self::PREFIX . $this->getUsage());
		}
	}
}