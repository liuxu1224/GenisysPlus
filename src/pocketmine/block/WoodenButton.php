<?php

 /*
 *  _______                                     ______  _
 * /  ____ \                                   |  __  \| \
 * | |    \_|              _                   | |__| || |
 * | |   ___  ___  _  ___ (_) ___  __    _ ___ |  ____/| | _   _  ___
 * | |  |_  |/(_)\| '/_  || |/___\(_)\  ///___\| |     | || | | |/___\
 * | \___|| | |___| |  | || |_\_\   \ \// _\_\ | |     | || | | |_\_\
 * \______/_|\___/|_|  |_||_|\___/   \ /  \___/|_|     |_||__/,_|\___/
 *                                   //
 *                                  (_)                Power by:
 *                                                           Tesseract
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @由Tessetact团队创建，GenisysPlus项目组修改
 * @链接 https://github.com/TesseractTeam
 * @链接 https://github.com/Tcanw/GenisysPlus
 *
 */

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\sound\ButtonClickSound;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\LevelEventPacket;
use pocketmine\Player;

class WoodenButton extends Transparent{
	protected $id = self::WOODEN_BUTTON;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}
	public function isSolid(){
    	return false;
	}
	public function onUpdate($type){
		if($type == Level::BLOCK_UPDATE_SCHEDULED){
			if($this->isActivated()) {
				$this->meta ^= 0x08;
				$this->getLevel()->setBlock($this, $this, true, false);
				$this->getLevel()->broadcastLevelEvent($this, LevelEventPacket::EVENT_REDSTONE_TRIGGER);
				$this->deactivate();
			}
			return Level::BLOCK_UPDATE_SCHEDULED;
		}
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$side = $this->getDamage();
			if($this->isActivated()) $side ^= 0x08;
			$faces = [
				0 => 1,
				1 => 0,
				2 => 3,
				3 => 2,
				4 => 5,
				5 => 4,
			];

			if($this->getSide($faces[$side]) instanceof Transparent){
				$this->getLevel()->useBreakOn($this);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}
		return false;
	}

	public function deactivate(array $ignore = []){
		parent::deactivate($ignore = []);
		$faces = [
			0 => 1,
			1 => 0,
			2 => 3,
			3 => 2,
			4 => 5,
			5 => 4,
		];
		$side = $this->meta;
		if($this->isActivated()) $side ^= 0x08;

		$block = $this->getSide($faces[$side])->getSide(Vector3::SIDE_UP);

		if($side != 1){
		}

	}

	public function activate(array $ignore = []){
		parent::activate($ignore = []);
		$faces = [
				0 => 1,
				1 => 0,
				2 => 3,
				3 => 2,
				4 => 5,
				5 => 4,
		];
		
		$side = $this->meta;
		if($this->isActivated()) $side ^= 0x08;

		$block = $this->getSide($faces[$side])->getSide(Vector3::SIDE_UP);

		if($side != 1){
			$block = $this->getSide($faces[$side], 2);
		}

	}

	public function getName() : string{
		return "Wooden Button";
	}

	public function getHardness() {
		return 0.5;
	}

	public function onBreak(Item $item){
		if($this->isActivated()){
			$this->meta ^= 0x08;
			$this->getLevel()->setBlock($this, $this, true, false);
			$this->deactivate();
		}
		$this->getLevel()->setBlock($this, new Air(), true, false);
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($target->isTransparent() === false){
			$this->meta = $face;
			$this->getLevel()->setBlock($block, $this, true, false);
			return true;
		}
		return false;
	}

	public function canBeActivated() : bool {
		return true;
	}

	public function isActivated(Block $from = null){
		return (($this->meta & 0x08) === 0x08);
	}

	public function onActivate(Item $item, Player $player = null){
		if(!$this->isActivated()){
			$this->meta ^= 0x08;
			$this->getLevel()->setBlock($this, $this, true, false);
			$this->getLevel()->broadcastLevelEvent($this, LevelEventPacket::EVENT_REDSTONE_TRIGGER);
			$this->activate();
			$this->getLevel()->scheduleUpdate($this, 30);
		}
		return true;
	}
}