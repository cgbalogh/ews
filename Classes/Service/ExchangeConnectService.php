<?php
namespace CGB\Ews\Service;

use CGB\Ews\Client\ExchangeWebServices;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Christoph Balogh <cb@lustige-informatik.at>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can resedistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Various helper routines
 *
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU protected License, version 2
 */
class ExchangeConnectService  {

    /*
     * @var string
     */
    protected $host;
    
    /**
     * @var string
     */
    protected $authMethod;
    
    /**
     *
     * @var string 
     */
    protected $version;
    
    /**
     *
     * @var bool 
     */
    protected $canConnect = false;
    
    /**
     *
     * @var \CGB\Ews\Client\ExchangeWebServices 
     */
    protected $exchangeWebService;
    
    /**
     *
     * @var type 
     */
    protected $request;
    
	/**
     *
     * @var type 
     */
    protected $responseCode;
	  
	/**
     *
     * @var type 
     */
    protected $response;
	  
    /**
     *
     * @var type 
     */
    protected $success;
    
    /**
     *
     * @var string 
     */
    protected $username;
    
    /**
     *
     * @var string 
     */
    protected $password;
    
    /**
     * @param \CGB\Ews\Domain\Model\Credential $credential
     */
    public function __construct(\CGB\Ews\Domain\Model\Credential $credential) {
        
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $configurationManager = $objectManager->get(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::class);
        $typoScript = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        
        $this->host = $typoScript['plugin.']['tx_ews.']['host'];
        $this->authMethod = $typoScript['plugin.']['tx_ews.']['auth_method'];
        $this->version = $typoScript['plugin.']['tx_ews.']['version'];

        if ($credential->getExchangeUsername() && $credential->getExchangePassword()) {
            // username and password set, try to connect
            $this->exchangeWebService = new ExchangeWebServices(
                $this->host, 
                $credential->getExchangeUsername(), 
                $credential->getExchangePassword(), 
                $this->version, 
                $this->authMethod);

            $this->canConnect = true;

            try {
                $this->canConnect = $this->findFolders();
            }
            catch (Exception $e) {
                $this->canConnect = false;
            }
        } else {
            // 
            $this->canConnect = false;
        }
    }
    
    /**
     * findFolders
     * 
     * @return boolean
     */
    public function findFolders() {
		if (! $this->canConnect) {
            return false;
        }

		unset($this->request);

		// start building the find folder request
		$request = new EWSType\FindFolderType();
		$request->Traversal = EWSType\FolderQueryTraversalType::SHALLOW;
		$request->FolderShape = new EWSType\FolderResponseShapeType();
		$request->FolderShape->BaseShape = EWSType\DefaultShapeNamesType::ALL_PROPERTIES;

		// configure the view
		$request->IndexedPageFolderView = new EWSType\IndexedPageViewType();
		$request->IndexedPageFolderView->BasePoint = 'Beginning';
		$request->IndexedPageFolderView->Offset = 0;

		// set the starting folder as the inbox
		$request->ParentFolderIds = new EWSType\NonEmptyArrayOfBaseFolderIdsType();
		$request->ParentFolderIds->DistinguishedFolderId = new EWSType\DistinguishedFolderIdType();
		$request->ParentFolderIds->DistinguishedFolderId->Id = EWSType\DistinguishedFolderIdNameType::INBOX;

		// make the actual call
        $response = $this->exchangeWebService->FindFolder($request);
		return $this->evaluateResponse($response);
    }
    
	/**
	 * createAppointment
     * 
     * @param array $appointment
	 */
    public function createAppointment($appointment) {
		// create calendar item
		if (! $this->canConnect) {
            return false;
        }
		unset($this->request);
        
        $this->request = new EWSType\CreateItemType();
		$this->request->SendMeetingInvitations = $appointment['meetingInvitations'];
        
        $this->request->SavedItemFolderId = new EWSType\TargetFolderIdType();
        $this->request->SavedItemFolderId->DistinguishedFolderId = new EWSType\DistinguishedFolderIdType;
		$this->request->SavedItemFolderId->DistinguishedFolderId->Id = $appointment['folder'];
        
        $this->request->Items = new EWSType\ArrayOfRealItemsType;
        $this->request->Items->CalendarItem = new EWSType\CalendarItemType;
		$this->request->Items->CalendarItem->Subject = $appointment['subject'];
		$this->request->Items->CalendarItem->Start = date('c', $appointment['start']);
		$this->request->Items->CalendarItem->End = date('c',  $appointment['end'] );
		$this->request->Items->CalendarItem->Location = $appointment['location'];
		$this->request->Items->CalendarItem->IsAllDayEvent = $appointment['isAllDayEvent'];
		$this->request->Items->CalendarItem->LegacyFreeBusyStatus = $appointment['freeBusyStatus'];
        
        // add attendees
        if (is_array($appointment['attendees'])) {
            $this->request->Items->CalendarItem->RequiredAttendees = new EWSType\NonEmptyArrayOfAttendeesType();
            $this->request->Items->CalendarItem->RequiredAttendees->Attendee = $this->addAttendees($appointment['attendees']);
        }
        
        $this->request->Items->CalendarItem->Categories = new EWSType\ItemType;
		$this->request->Items->CalendarItem->Categories->String = $appointment['category'];
        
        $this->request->Items->CalendarItem->Body = new EWSType\BodyType();
		$this->request->Items->CalendarItem->Body->BodyType = $appointment['bodyType'];
		$this->request->Items->CalendarItem->Body->_ = $appointment['body'];
        $r = $this->evaluateResponse($this->exchangeWebService->CreateItem($this->request));
        // print_r($this->response);
		return $r;
	}
    
    /**
     * @param array $attendees
     */
    private function addAttendees ($attendees = []) {
        $ewsAttendees = [];
        foreach($attendees as $attendee) {
            $ewsAttendee = new EWSType\AttendeeType();
            $ewsAttendee->Mailbox = new EWSType\EmailAddressType();
            $ewsAttendee->Mailbox->EmailAddress = $attendee;
            $ewsAttendee->Mailbox->Name = $attendee;
            $ewsAttendees[] = $ewsAttendee;
        }
        // print_r($ewsAttendees);
        return $ewsAttendees;
    }
    
	/**
	 * updateAppointment
     * 
     * @param array $appointment
	 */
	public function updateAppointment($appointment) {
		// update calendar item there *MUST* be the element $appointment['msolid'] set
		if (! $this->canConnect || ! $appointment['msolid']) {
            return false;
        }
		$remoteAppointment = $this->findAppointment($appointment);
		
		$changeKey = $this->response->ResponseMessages->GetItemResponseMessage->Items->CalendarItem->ItemId->ChangeKey;
		if (! $changeKey) {
            return false;
        }
		unset($this->request);
		$this->request = new EWSType\UpdateItemType(); 
 
		$this->request->SendMeetingInvitationsOrCancellations = 'SendToNone'; 
		$this->request->MessageDisposition = 'SaveOnly'; 
		$this->request->ConflictResolution = 'AlwaysOverwrite'; 
		$this->request->ItemChanges = new EWSType\NonEmptyArrayOfItemChangesType(); 

        $this->request->ItemChanges->ItemChange = new EWSType\ItemChangeType();
        $itemId = new EWSType\ItemIdType();
		$itemId->Id = $appointment['msolid'];
		$this->request->ItemChanges->ItemChange->ItemId = $itemId;
		$this->request->ItemChanges->ItemChange->ItemId->ChangeKey = $changeKey; 
		$this->request->ItemChanges->ItemChange->Updates = new EWSType\NonEmptyArrayOfItemChangeDescriptionsType(); 

		$this->request->ItemChanges->ItemChange->Updates->SetItemField = []; 

        $this->request->ItemChanges->ItemChange->Updates->SetItemField[] = self::createChangeItem([
            'property' => 'Subject', 'item' => 'item:Subject','value' => $appointment['subject']]); 

        $this->request->ItemChanges->ItemChange->Updates->SetItemField[] = self::createChangeItem([
            'property' => 'Body', 'item' => 'item:Body','value' => $appointment['body']]); 
        
        $this->request->ItemChanges->ItemChange->Updates->SetItemField[] = self::createChangeItem([
            'property' => 'Location', 'item' => 'calendar:Location','value' => $appointment['location']]); 

        $this->request->ItemChanges->ItemChange->Updates->SetItemField[] = self::createChangeItem([
            'property' => 'Start', 'item' => 'calendar:Start','value' => date('c', $appointment['start'])]); 
        
        $this->request->ItemChanges->ItemChange->Updates->SetItemField[] = self::createChangeItem([
            'property' => 'End', 'item' => 'calendar:End','value' => date('c', $appointment['end'])]); 

        $response = $this->exchangeWebService->UpdateItem($this->request);
		return $this->evaluateResponse($response);
	}

	/**
     * deleteAppointment
     * 
	 * @param array $appointment
	 */
	public function deleteAppointment ($appointment) {
		if (! $this->canConnect || ! $appointment['msolid']) {
            return false;
        }
		unset($this->request);
		// Form the GetItem request
		$this->request = new EWSType\DeleteItemType();
		$this->request->SendMeetingCancellations = 'SendToNone';
		$this->request->ItemIds = new EWSType\NonEmptyArrayOfBaseItemIdsType();
		$this->request->ItemIds->ItemId = new EWSType\ItemIdType();
		$this->request->ItemIds->ItemId->Id = $appointment['msolid']; 
        
        $this->request->DeleteType = new EWSType\DisposalType();
        $this->request->DeleteType = EWSType\DisposalType::MOVE_TO_DELETED_ITEMS;

        $response = $this->exchangeWebService->DeleteItem($this->request);
		return $this->evaluateResponse($response);
	}
    
	/**
	 * findAppointment
     * 
     * @param array $appointment
     * @param type $defaultShape
     * @return boolean
     */
    public function findAppointment($appointment = [], $defaultShape = '') {
		if (! $this->canConnect || ! $appointment['msolid']) {
            return false;
        }

		// Form the GetItem request
		$request = new EWSType\GetItemType();

		// Define which item properties are returned in the response
		$itemProperties = new EWSType\ItemResponseShapeType();
		
		if (! $defaultShape) {
            $defaultShape = EWSType\DefaultShapeNamesType::ID_ONLY;
        }
		$itemProperties->BaseShape = $defaultShape;

		// Add properties shape to request
		$request->ItemShape = $itemProperties;

		// Set the itemID of the desired item to retrieve
		$itemId = new EWSType\ItemIdType();
		$itemId->Id = $appointment['msolid'];
        // $request->ItemIds = new EWSType\ArrayOfBaseItemIdsType();
        $request->ItemIds = new \stdClass();
		$request->ItemIds->ItemId = $itemId;
		return $this->evaluateResponse($this->exchangeWebService->GetItem($request));
	}
    
	/**
	 * evaluateResponse
     * 
	 * @param type $response 
	 */
	private function evaluateResponse($response) {
		if (gettype($response) == 'object') {
			$this->responseCode = 200;
			$this->success = true;
			$this->response = $response;
		} else {
			$this->responseCode = $response;
			$this->success = false;
			$this->response = null;
		}
		return $this->success;
	}
    
    
    /**
     * canConnect
     * 
     * @return bool
     */
    public function canConnect () {
        return $this->canConnect;
    }
    
    /**
     * getExchangeWebservice
     * 
     * @return \CGB\Ews\Client\ExchangeWebServices 
     */
    public function getExchangeWebservice() {
        return $this->exchangeWebService;
    }
    
    /**
     * getCredentials
     * 
     * @param string $username
     * @return mixed
     */
    static function getCredentials($username = '') {
        if(! $username && $GLOBALS['TSFE']->loginUser && !empty($GLOBALS['TSFE']->fe_user->user['uid'])) {
            $username = $GLOBALS['TSFE']->fe_user->user['username'];
        }
                
        if ($username) {
            $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
            $credentialRepository = $objectManager->get(\CGB\Ews\Domain\Repository\CredentialRepository::class);

            $credentials = $credentialRepository->findByUsername($username);
            if ($credentials->count() > 0) {
                return $credentials->current();
            }
        } 
        return null;
    }
    
    /**
     * getExchangeWebservice
     * 
     * @param string $username
     * @return \CGB\Ews\Service\ExchangeConnectService|null
     */
    static public function getConnector ($username = '') {
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $exchangeConnector = $objectManager->get(\CGB\Ews\Service\ExchangeConnectService::class, self::getCredentials($username));
        if ($exchangeConnector->canConnect()) {
            return $exchangeConnector;
        } else {
            return null;
        }
    }
    
	/**
     * 
     * @return type
     */
    public function getResponseCode() {
		return $this->responseCode;
	}

	/**
     * 
     * @return type
     */
    public function getResponse() {
		return $this->response;
	}
    
    /**
     * 
     * @param array $changeItemArray
     */
    static function createChangeItem ($changeItemArray = []) {
        $changeItem = new EWSType\SetItemFieldType();
		$changeItem->Path = "FieldURI"; 
        $changeItem->FieldURI = new EWSType\PathToUnindexedFieldType();
		$changeItem->FieldURI->FieldURI = $changeItemArray['item']; 
		$changeItem->CalendarItem = new EWSType\CalendarItemType(); 
        if ($changeItemArray['property'] == 'Body') {
            $changeItem->CalendarItem->Body = new EWSType\BodyType();
    		$changeItem->CalendarItem->Body->BodyType = 'Text';
        	$changeItem->CalendarItem->Body->_ = $changeItemArray['value'];
        } else {
            $changeItem->CalendarItem->{$changeItemArray['property']} = $changeItemArray['value'];
        }
        return $changeItem;
    }
    
}
