<?php

namespace BlueSpice\Tag;

/**
* The MarkerType defines the \StripState of the string data returned by a tag extension

* From documentaion in class \Parser:
*     [Tag]Hooks may return extended information by returning an array, of which the
*     first numbered element (index 0) must be the return string, and all other
*     entries are extracted into local variables within an internal function
*     in the Parser class.
*
*     This interface (introduced r61913) appears to be undocumented, but
*     'markerType' is used by some core tag hooks to override which strip
*     array their results are placed in. **Use great caution if attempting
*     this interface, as it is not documented and injudicious use could smash
*     private variables.**
*/
class MarkerType {
	const KEY = 'markerType';
}
