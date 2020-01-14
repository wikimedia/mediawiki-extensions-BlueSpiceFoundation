<?php

namespace BlueSpice;

/**
 * DEPRECATED
 * Example request parameters of an ExtJS store
 *
 * _dc:1430126252980
 * filter:[
 * {
 * "type":"string",
 * "comparison":"ct",
 * "value":"some text ...",
 * "field":"someField"
 * }
 * ]
 * group:[
 * {
 * "property":"someOtherField",
 * "direction":"ASC"
 * }
 * ]
 * sort:[
 * {
 * "property":"someOtherField",
 * "direction":"ASC"
 * }
 * ]
 * page:1
 * start:0
 * limit:25
 * @deprecated since version 3.1 - Use \BlueSpice\Api\Store instead
 */
abstract class StoreApiBase extends \BlueSpice\Api\Store {
}
