<?php

namespace BlueSpice\Data;

interface IStore {

	/**
	 * @return IWriter
	 */
	public function getWriter();

	/**
	 * @return IReader
	 */
	public function getReader();
}
