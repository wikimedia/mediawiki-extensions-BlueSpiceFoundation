<?php

namespace BlueSpice;

use MWTimestamp;
use User;
use RequestContext;

class Timestamp extends MWTimestamp {

	/**
	 *
	 * @param Timestamp|null $relativeTo
	 * @param User|null $user
	 * @param int $numberOfDisplayedUnits optional default 2 If 2 the output
	 * will be something like "1 year and 2 months ago". If 1, the output
	 * will be something like "1 year ago"
	 * @return string
	 */
	public function getAgeString(
		Timestamp $relativeTo = null, User $user = null, $numberOfDisplayedUnits = 2
	) {
		if ( !$relativeTo ) {
			$relativeTo = new static;
		}
		if ( !$user ) {
			$user = RequestContext::getMain()->getUser();
		}

		// Adjust for the user's timezone.
		$offsetThis = $this->offsetForUser( $user );
		$offsetRel = $relativeTo->offsetForUser( $user );

		$ts = $this->getAgeStringInternal( $this, $relativeTo, $user, $numberOfDisplayedUnits );

		$this->timestamp->sub( $offsetThis );
		$relativeTo->timestamp->sub( $offsetRel );

		return $ts;
	}

	/**
	 *
	 * @param Timestamp $ts
	 * @param Timestamp $relativeTo
	 * @param User $user
	 * @param int $numberOfDisplayedUnits
	 * @param string $ageString
	 * @return string
	 */
	protected function getAgeStringInternal(
		Timestamp $ts, Timestamp $relativeTo, User $user, $numberOfDisplayedUnits, $ageString = ''
	) {
		$diff = $ts->diff( $relativeTo );
		$params = [];
		foreach ( $this->getAvailableAgeStrings() as $key => $msgKey ) {
			if ( count( $params ) > 1 ) {
				break;
			}

			$value = 0;
			if ( $key === 'w' && $diff->d > 0 && ( $diff->d / 7 ) >= 1 ) {
				$value = (int)( $diff->d / 7 );
			} elseif ( $key === 'd' && $diff->d > 0 && ( $diff->d / 7 ) > 1 ) {
				$value = (int)( $diff->d / 7 );
			} elseif ( $key !== 'w' ) {
				$value = $diff->{$key};
			}

			if ( $value < 1 ) {
				if ( count( $params ) > 0 ) {
					break;
				}
				continue;
			}
			$params[] = \Message::newFromKey( $msgKey )
				->params( $value )
				->inLanguage( $user->getOption( 'language' ) )
				->text();
		}

		$paramCount = count( $params );
		if ( $paramCount === 0 ) {
			$ageString .= \Message::newFromKey( 'bs-now' )
				->inLanguage( $user->getOption( 'language' ) )
				->text();
			return $ageString;
		}

		// Fallback to displaying only one unit if number of displayed
		// units is not covered by the code.
		$ageMsgKey = 'bs-one-unit-ago';
		if ( $numberOfDisplayedUnits == 2 ) {
			$ageMsgKey = $paramCount > 1 ? 'bs-two-units-ago' : 'bs-one-unit-ago';
		}

		$ageString .= \Message::newFromKey( $ageMsgKey )
			->params( $params )
			->inLanguage( $user->getOption( 'language' ) )
			->text();

		return $ageString;
	}

	/**
	 *
	 * @return array
	 */
	protected function getAvailableAgeStrings() {
		return [
			'y' => 'bs-years-duration',
			'm' => 'bs-months-duration',
			'w' => 'bs-weeks-duration',
			'd' => 'bs-days-duration',
			'h' => 'bs-hours-duration',
			'i' => 'bs-mins-duration',
			's' => 'bs-secs-duration',
		];
	}
}
