<?php

/**
 * BsSTYLEMEDIA
 * Represents the different values for the 'media' attribute of a <link />-tag.
 */
class BsSTYLEMEDIA {
	/**
	 * BsSTYLEMEDIA::ALL
	 * This style applies to all types of media.
	 */
	const ALL        = 255;

	/**
	 * BsSTYLEMEDIA::AURAL
	 * This style is used for audible output.
	 */
	const AURAL      =   1;

	/**
	 * BsSTYLEMEDIA::BRAILLE
	 * This style is used for embossed printing.
	 */
	const BRAILLE    =   2;

	/**
	 * BsSTYLEMEDIA::HANDHELD
	 * This style applies handheld comupters, mobild phones and similar devices.
	 */
	const HANDHELD   =   4;

	/**
	 * BsSTYLEMEDIA::PRINTER
	 * This style applies to all types of media.
	 */
	const PRINTER    =   8;

	/**
	 * BsSTYLEMEDIA::PROJECTION
	 * This style is used for projectors.
	 */
	const PROJECTION =  16;

	/**
	 * BsSTYLEMEDIA::SCREEN
	 * This style is used for computer screens. In most cases this is the
	 * setting of choice.
	 */
	const SCREEN     =  32;

	/**
	 * BsSTYLEMEDIA::TTY
	 * This style applies to teletypewriter .
	 */
	const TTY        =  64;

	/**
	 * BsSTYLEMEDIA::TV
	 * This style is used for television.
	 */
	const TV         = 128;
}
