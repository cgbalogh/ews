<?php
namespace CGB\Ews\Service\EWSType;
use CGB\Ews\Client\EWSType;

/**
 * Definition of the ArrayOfBaseItemIdsType type
 * 
 * @author James I. Armes <http://www.jamesarmes.net>
 */

/**
 * Definition of the ArrayOfBaseItemIdsType type
 * 
 * @author James I. Armes <http://www.jamesarmes.net>
 */
class ArrayOfBaseItemIdsType extends EWSType {
	/**
	 * ItemId property
	 * 
	 * @var EWSType_ItemIdType
	 */
	public $ItemId;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->schema = array(
			array(
				'name' => 'ItemId',
				'required' => false,
				'type' => 'ItemIdType',
			),
		); // end $this->schema
	} // end function __construct()
} // end class ArrayOfBaseItemIdsType