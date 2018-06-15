<?php
namespace CGB\Ews\Service\EWSType;
use CGB\Ews\Client\EWSType;

/**
 * Definition of the FolderResponseShapeType type
 * 
 * @author James I. Armes <http://www.jamesarmes.net>
 */

/**
 * Definition of the FolderResponseShapeType type
 * 
 * @author James I. Armes <http://www.jamesarmes.net>
 */
class FolderResponseShapeType extends EWSType {
	/**
	 * BaseShape property
	 * 
	 * @var EWSType_DefaultShapeNamesType
	 */
	public $BaseShape;

	/**
	 * AdditionalProperties property
	 * 
	 * @var EWSType_NonEmptyArrayOfPathsToElementType
	 */
	public $AdditionalProperties;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->schema = array(
			array(
				'name' => 'BaseShape',
				'required' => false,
				'type' => 'DefaultShapeNamesType',
			),
			array(
				'name' => 'AdditionalProperties',
				'required' => false,
				'type' => 'NonEmptyArrayOfPathsToElementType',
			),
		); // end $this->schema
	} // end function __construct()
} // end class FolderResponseShapeType