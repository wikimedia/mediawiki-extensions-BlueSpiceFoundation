<?php

namespace BlueSpice;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;
use MediaWiki\Context\RequestContext;
use MediaWiki\MediaWikiServices;
use MediaWiki\Message\Message;
use MediaWiki\User\User;
use MediaWiki\Utils\MWTimestamp;

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
		?Timestamp $relativeTo = null, ?User $user = null, $numberOfDisplayedUnits = 2
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
		$userLanguageSetting = MediaWikiServices::getInstance()->getUserOptionsLookup()
			->getOption( $user, 'language' );
		foreach ( $this->getAvailableAgeStrings() as $key => $msgKey ) {
			if ( count( $params ) > 1 ) {
				break;
			}

			$value = 0;
			if ( $key === 'w' && $diff->d > 0 && ( $diff->d / 7 ) >= 1 ) {
				$weeks = (int)( $diff->d / 7 );

				$value = $weeks;
				$diff->d -= ( $weeks * 7 );
			} elseif ( $key === 'd' && $diff->d > 0 && ( $diff->d / 7 ) > 1 ) {
				$value = $diff->d % 7;
			} elseif ( $key !== 'w' ) {
				$value = $diff->{$key};
			}

			if ( $value < 1 ) {
				if ( count( $params ) > 0 ) {
					break;
				}
				continue;
			}
			$params[] = Message::newFromKey( $msgKey )
				->params( $value )
				->inLanguage( $userLanguageSetting )
				->text();
		}

		$paramCount = count( $params );
		if ( $paramCount === 0 ) {
			$ageString .= Message::newFromKey( 'bs-now' )
				->inLanguage( $userLanguageSetting )
				->text();
			return $ageString;
		}

		// Fallback to displaying only one unit if number of displayed
		// units is not covered by the code.
		$ageMsgKey = 'bs-one-unit-ago';
		if ( $numberOfDisplayedUnits == 2 ) {
			$ageMsgKey = $paramCount > 1 ? 'bs-two-units-ago' : 'bs-one-unit-ago';
		}

		$ageString .= Message::newFromKey( $ageMsgKey )
			->params( $params )
			->inLanguage( $userLanguageSetting )
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

	/**
	 * Force-remove the adjustment of the timestamp depending on the given user's
	 * preferences.
	 *
	 * @param User $user User to take preferences from
	 * @return DateInterval Offset that was removed from the timestamp
	 */
	public function unOffsetForUser( User $user ) {
		global $wgLocalTZoffset;

		$option = MediaWikiServices::getInstance()->getUserOptionsLookup()
			->getOption( $user, 'timecorrection' );
		$data = explode( '|', $option, 3 );

		// First handle the case of an actual timezone being specified.
		if ( $data[0] == 'ZoneInfo' ) {
			try {
				$tz = new DateTimeZone( $data[2] );
			} catch ( Exception $e ) {
				$tz = false;
			}

			if ( $tz ) {
				$this->timestamp = new DateTime( $this->timestamp->format( 'YmdHis' ), $tz );
				$this->timestamp->setTimezone( new DateTimeZone( 'UTC' ) );
				return new DateInterval( 'P0Y' );
			}

			$data[0] = 'Offset';
		}

		$diff = 0;
		// If $option is in fact a pipe-separated value, check the
		// first value.
		if ( $data[0] == 'System' ) {
			// First value is System, so use the system offset.
			if ( $wgLocalTZoffset !== null ) {
				$diff = $wgLocalTZoffset;
			}
		} elseif ( $data[0] == 'Offset' ) {
			// First value is Offset, so use the specified offset
			$diff = (int)$data[1];
		} else {
			// $option actually isn't a pipe separated value, but instead
			// a comma separated value. Isn't MediaWiki fun?
			$data = explode( ':', $option );
			if ( count( $data ) >= 2 ) {
				// Combination hours and minutes.
				$diff = abs( (int)$data[0] ) * 60 + (int)$data[1];
				if ( (int)$data[0] < 0 ) {
					$diff *= -1;
				}
			} else {
				// Just hours.
				$diff = (int)$data[0] * 60;
			}
		}

		$interval = new DateInterval( 'PT' . abs( $diff ) . 'M' );
		if ( $diff < 1 ) {
			$interval->invert = 1;
		}

		$this->timestamp->sub( $interval );
		return $interval;
	}
}
