<?php

namespace ojy\pvpdata;

interface DataType{

	public const PREFIX = "§d<§f시스템§d> §f";

	/** @var string */
	public const KILL = "kill";

	/** @var string */
	public const DEATH = "death";

	/** @var string */
	public const DAMAGE = "damage";

	/** @var string */
	public const ATTACK_COUNT = "attack-count";

	/** @var string */
	public const SHOOT_COUNT = "shoot-count";

	/** @var string */
	public const ARROW_ATTACK_COUNT = "arrow-attack-count";
}