<?php

namespace BlueSpice;

interface IListPositionProvider {

	/**
	 * A numerical value to sort this item in a list. Should be something like `10`, `20`, ...
	 * @return int
	 */
	public function getPosition();
}
