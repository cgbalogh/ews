<?php
namespace CGB\Ews\Service\EWSType;
use CGB\Ews\Client\EWSType;

/**
 * Definition of the NonEmptyArrayOfBaseItemIdsType type
 * 
 * @author James I. Armes <http://www.jamesarmes.net>
 */

/**
 * Definition of the NonEmptyArrayOfBaseItemIdsType type
 * 
 * @author James I. Armes <http://www.jamesarmes.net>
 */
class NonEmptyArrayOfBaseItemIdsType extends EWSType {
	/**
	 * ItemId property
	 * 
	 * @var EWSType_ItemIdType
	 */
	public $ItemId;

	/**
	 * OccurrenceItemId property
	 * 
	 * @var EWSType_OccurrenceItemIdType
	 */
	public $OccurrenceItemId;

	/**
	 * RecurringMasterItemId property
	 * 
	 * @var EWSType_RecurringMasterItemIdType
	 */
	public $RecurringMasterItemId;

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
			array(
				'name' => 'OccurrenceItemId',
				'required' => false,
				'type' => 'OccurrenceItemIdType',
			),
			array(
				'name' => 'RecurringMasterItemId',
				'required' => false,
				'type' => 'RecurringMasterItemIdType',
			),
		); // end $this->schema
	} // end function __construct()
} // end class NonEmptyArrayOfBaseItemIdsType