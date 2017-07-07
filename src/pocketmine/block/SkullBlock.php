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

use pocketmine\math\AxisAlignedBB;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Skull as SkullTile;

use pocketmine\tile\Tile;

class SkullBlock extends Flowable {
	
	protected $id = self::SKULL_BLOCK;
	
	public function __construct($meta = 0) {
		$this->meta = $meta;
	}
	
	public function getHardness() {
		return 1;
	}
	
	public function getName() : bool{
		return "Mob Head";
	}
	
	protected function recalculateBoundingBox() {
		$x1 = 0;
		$x2 = 0;
		$z1 = 0;
		$z2 = 0;
		if ($this->meta === 0 || $this->meta === 1) {
			return new AxisAlignedBB(
				$this->x + 0.25,
				$this->y,
				$this->z + 0.25,
				$this->x + 0.75,
				$this->y + 0.5,
				$this->z + 0.75
			);
		} elseif ($this->meta === 2) {
			$x1 = 0.25;
			$x2 = 0.75;
			$z1 = 0;
			$z2 = 0.5;
		} elseif ($this->meta === 3) {
			$x1 = 0.5;
			$x2 = 1;
			$z1 = 0.25;
			$z2 = 0.75;
		} elseif ($this->meta === 4) {
			$x1 = 0.25;
			$x2 = 0.75;
			$z1 = 0.5;
			$z2 = 1;
		} elseif ($this->meta === 5) {
			$x1 = 0;
			$x2 = 0.5;
			$z1 = 0.25;
			$z2 = 0.75;
		}
		return new AxisAlignedBB(
			$this->x + $x1,
			$this->y + 0.25,
			$this->z + $z1,
			$this->x + $x2,
			$this->y + 0.75,
			$this->z + $z2
		);
	}
	
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null) {
		if ($face !== 0) {
			$this->meta = $face;
			if ($face === 1) {
				$rot = floor(($player->yaw * 16 / 360) + 0.5) & 0x0F;
			} else {
				$rot = 0;
			}
			$this->getLevel()->setBlock($block, $this, true);
			$moveMouth = false;
			if($item->getDamage() === SkullTile::TYPE_DRAGON){
				if(in_array($target->getId(), [Block::REDSTONE_TORCH, Block::REDSTONE_BLOCK])) $moveMouth = true; //Temp-hacking Dragon Head Mouth Move
			}
			$nbt = new CompoundTag("", [
				new StringTag("id", Tile::SKULL),
				new ByteTag("SkullType", $item->getDamage()),
				new ByteTag("Rot", $rot),
				new ByteTag("MouthMoving", (bool)$moveMouth),
				new IntTag("x", (int)$this->x),
				new IntTag("y", (int)$this->y),
				new IntTag("z", (int)$this->z)
			]);
			if ($item->hasCustomName()) {
				$nbt->CustomName = new StringTag("CustomName", $item->getCustomName());
			}
			Tile::createTile("Skull", $this->getLevel(), $nbt);
			return true;
		}
		return false;
	}
	
	public function getDrops(Item $item) : array{
		$tile = $this->level->getTile($this);
		if($tile instanceof SkullTile){
			return [
				[Item::MOB_HEAD, $tile->getType(), 1]
			];
		}
		return [];
	}
}