<?php

class BSApiUserStore extends BSApiExtJSStoreBase {

	protected $aGroups = [];
	protected $aBlocks = [];

	protected function makeData( $sQuery = '' ) {
		$dbr = $this->getDB();

		$this->aGroups = [];
		$groupsRes = $dbr->select( 'user_groups', '*', '', __METHOD__ );
		foreach ( $groupsRes as $row ) {
			if ( !isset( $this->aGroups[$row->ug_user] ) ) {
				$this->aGroups[$row->ug_user] = [];
			}
			$this->aGroups[$row->ug_user][] = $row->ug_group;
		}

		$this->aBlocks = [];
		$blocksRes = $dbr->select( 'ipblocks', '*', '', __METHOD__ );
		foreach ( $blocksRes as $row ) {
			$this->aBlocks[$row->ipb_user] = $row->ipb_address;
		}

		// TODO: It would be very cool to have the permissions as a filterable
		// field. Unfortunately this requires some context information from the
		// client. I.e. The page/namespace for which the permissions should be
		// calculated. This would also be very expensive and a potential
		// security issue.

		$aData = [];
		$userRes = $dbr->select( 'user', '*', '', __METHOD__ );
		foreach ( $userRes as $aRow ) {
			$aResRow = $this->makeResultRow( $aRow );
			if ( !$aResRow ) {
				continue;
			}
			$aData[] = (object)$aResRow;
		}

		return $aData;
	}

	/**
	 * Builds a single result set
	 * @param stdClass $row
	 * @return array
	 */
	protected function makeResultRow( $row ) {
		$oUserPageTitle = Title::makeTitle( NS_USER, $row->user_name );
		return [
			'user_id' => (int)$row->user_id,
			'user_name' => $row->user_name,
			'user_real_name' => $row->user_real_name,
			'user_registration' => $row->user_registration,
			'user_editcount' => (int)$row->user_editcount,
			'groups' => isset( $this->aGroups[$row->user_id] ) ? $this->aGroups[$row->user_id] : [],
			'enabled' => isset( $this->aBlocks[$row->user_id] ) ? false : true,
			'page_link' => $this->oLinkRenderer->makeLink(
				$oUserPageTitle,
				// The whitespace is to aviod automatic rewrite to user_real_name by BSF
				$row->user_name . ' '
			),

			// legacy fields
			'display_name' => $row->user_real_name == null ? $row->user_name : $row->user_real_name,
			'page_prefixed_text' => $oUserPageTitle->getPrefixedText()
		];
	}

	/**
	 * Set "enabled" filter to true as a default. This needs to be done
	 * programmatically, because the filter json must be integrated with
	 * other filters.
	 *
	 * @param string $paramName Parameter name
	 * @param array|mixed $paramSettings Default value or an array of settings
	 *  using PARAM_* constants.
	 * @param bool $parseLimit Parse limit?
	 * @return mixed Parameter value
	 */
	protected function getParameterFromSettings( $paramName, $paramSettings, $parseLimit ) {
		$value = parent::getParameterFromSettings( $paramName, $paramSettings, $parseLimit );

		if ( $paramName === 'filter' ) {

			$bEnabledFilterIsSet = false;
			foreach ( $value as $filter ) {
				if ( isset( $filter->property ) && $filter->property == 'enabled' ) {
					$bEnabledFilterIsSet = true;
					break;
				}
				if ( isset( $filter->field ) && $filter->field == 'enabled' ) {
					$bEnabledFilterIsSet = true;
					break;
				}
			}
			if ( !$bEnabledFilterIsSet ) {
				$value[] = (object)[
					'type' => 'boolean',
					'value' => true,
					'field' => 'enabled'
				];
			}
		}

		return $value;
	}

	/**
	 * @param object $aDataSet
	 * @return boolean
	 */
	public function filterCallback( $aDataSet ) {
		$bFilterApplies = $this->filterUserName(
			$this->getParameter( 'query' ),
			$aDataSet
		);
		if ( !$bFilterApplies ) {
			return false;
		}

		return parent::filterCallback( $aDataSet );
	}

	/**
	 * Performs string filtering on the user name and real name based on given
	 * query parameter
	 * @param string $sQuery
	 * @param oject $aDataSet
	 * @return boolean true if filter applies, false if not
	 */
	public function filterUserName( $sQuery, $aDataSet ) {
		if ( empty( $sQuery ) || !is_string( $sQuery ) ) {
			return true;
		}

		return BsStringHelper::filter( 'ct', $aDataSet->user_name, $sQuery )
		|| BsStringHelper::filter(
			'ct',
			$aDataSet->user_real_name,
			$sQuery
		);
	}

	/**
	 *
	 * @param \stdClass $filter
	 * @param \stdClass $dataSet
	 * @return bool
	 */
	public function filterString( $filter, $dataSet ) {
		if ( $filter->field === 'groups' ) {
			if ( !isset( $dataSet->{$filter->field} ) ) {
				return false;
			}
			if ( !is_string( $filter->value ) ) {
				// TODO: Warning
				return true;
			}
			return BsStringHelper::filter(
				$filter->comparison,
				implode( '', $dataSet->{$filter->field} ),
				$filter->value
			);
		}
		return parent::filterString( $filter, $dataSet );
	}
}
