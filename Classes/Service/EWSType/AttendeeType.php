<?php
namespace CGB\Ews\Service\EWSType;
use CGB\Ews\Client\EWSType;

/**
 * Definition of the AttendeeType type
 * 
 * @author James I. Armes <http://www.jamesarmes.net>
 */

/**
 * Definition of the AttendeeType type
 * 
 * @author James I. Armes <http://www.jamesarmes.net>
 */
class AttendeeType extends EWSType {
	/**
	 * Mailbox property
	 * 
	 * @var EWSType_EmailAddressType
	 */
	public $Mailbox;

	/**
	 * ResponseType property
	 * 
	 * @var EWSType_ResponseTypeType
	 */
	public $ResponseType;

	/**
	 * LastResponseTime property
	 * 
	 * @var EWSType_dateTime
	 */
	public $LastResponseTime;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->schema = array(
			array(
				'name' => 'Mailbox',
				'required' => false,
				'type' => 'EmailAddressType',
			),
			array(
				'name' => 'ResponseType',
				'required' => false,
				'type' => 'ResponseTypeType',
			),
			array(
				'name' => 'LastResponseTime',
				'required' => false,
				'type' => 'dateTime',
			),
		); // end $this->schema
	} // end function __construct()
} // end class AttendeeType