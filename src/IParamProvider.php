<?php

namespace BlueSpice;

use BlueSpice\ParamProcessor\ParamDefinition;

interface IParamProvider {
	/**
	 * @return ParamDefinition[]
	 */
	public function getArgsDefinitions();
}
