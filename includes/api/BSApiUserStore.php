<?php

use BlueSpice\Renderer\Params;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

/**
 * @deprecated since 4.2.2 - use CommonWebAPIs REST api instead
 * /mws/v1/user-query-store
 */
class BSApiUserStore extends BSApiExtJSStoreBase {

	/**
	 *
	 * @var array
	 */
	protected $aGroups = [];
	/**
	 *
	 * @var array
	 */
	protected $aBlocks = [];

	/**
	 *
	 * @param string $sQuery
	 * @return array
	 */
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
		$blocksRes = $dbr->select( 'block_target', '*', '', __METHOD__ );
		foreach ( $blocksRes as $row ) {
			$this->aBlocks[$row->bt_user] = $row->bt_user_text;
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
		return [
			'user_id' => (int)$row->user_id,
			'user_name' => $row->user_name,
			'user_real_name' => $row->user_real_name,
			'user_registration' => $row->user_registration,
			'user_editcount' => (int)$row->user_editcount,
			'groups' => isset( $this->aGroups[$row->user_id] ) ? $this->aGroups[$row->user_id] : [],
			'enabled' => isset( $this->aBlocks[$row->user_id] ) ? false : true,
			// legacy fields
			'display_name' => $row->user_real_name == null ? $row->user_name : $row->user_real_name,
		];
	}

	/**
	 *
	 * @param array $aTrimmedData
	 * @return array
	 */
	protected function addSecondaryFields( $aTrimmedData ) {
		foreach ( $aTrimmedData as &$dataSet ) {
			$oUserPageTitle = Title::makeTitle( NS_USER, $dataSet->user_name );
			$dataSet->page_link = $this->oLinkRenderer->makeLink(
				$oUserPageTitle,
				// The whitespace is to aviod automatic rewrite to user_real_name by BSF
				$dataSet->user_name . ' '
			);
			$dataSet->page_prefixed_text = $oUserPageTitle->getPrefixedText();

			$dataSet->user_image = $this->getUserImage( $dataSet->user_name );
		}
		return $aTrimmedData;
	}

	/**
	 * @param sting $userName
	 * @return string
	 */
	protected function getUserImage( $userName ) {
		$factory = $this->services->getService( 'BSRendererFactory' );
		$thumbParams = [ 'width' => '32', 'height' => '32' ];

		$user = $this->services->getUserFactory()->newFromName( $userName );
		if ( $user instanceof User === false ) {
			return '';
		}

		$image = $factory->get( 'userimage', new Params( [
				'user' => $user
			] + $thumbParams ) );

		return $image->render();
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
	 * @param \stdClass $aDataSet
	 * @return bool
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
	 * @return bool true if filter applies, false if not
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
