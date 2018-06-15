<?php
namespace CGB\Ews\Controller;

/***
 *
 * This file is part of the "ews" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017
 *
 ***/

/**
 * CredentialController
 */
class CredentialController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * credentialRepository
     *
     * @var \CGB\Ews\Domain\Repository\CredentialRepository
     * @inject
     */
    protected $credentialRepository = null;

    /**
     * action list
     *
     * @param CGB\Ews\Domain\Model\Credential
     * @return void
     */
    public function listAction()
    {
        $credentials = $this->credentialRepository->findAll();
        $this->view->assign('credentials', $credentials);
    }

    /**
     * action new
     *
     * @param CGB\Ews\Domain\Model\Credential
     * @return void
     */
    public function newAction()
    {

    }

    /**
     * action create
     *
     * @param CGB\Ews\Domain\Model\Credential
     * @return void
     */
    public function createAction(\CGB\Ews\Domain\Model\Credential $newCredential)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->credentialRepository->add($newCredential);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param CGB\Ews\Domain\Model\Credential $credential
     * @param bool $ignoreError
     * @ignorevalidation $credential
     * @return void
     */
    public function editAction(\CGB\Ews\Domain\Model\Credential $credential=null, $ignoreError = false)
    {
        if (is_null($credential)) {
            if($GLOBALS['TSFE']->loginUser && !empty($GLOBALS['TSFE']->fe_user->user['uid'])) {
                $username = $GLOBALS['TSFE']->fe_user->user['username'];
                
                if ($username) {
                    $credentials = $this->credentialRepository->findByUsername($username);
                    if ($credentials->count() == 0) {
                        $credential = $this->objectManager->get(\CGB\Ews\Domain\Model\Credential::class);
                        $credential->setUsername($username);
                        $this->credentialRepository->add($credential);
                        $persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
                        $persistenceManager->persistAll();
                    } else {
                        $credential = $credentials->current();
                    }
                } 
            }
        }
        
        $exchangeConnector = $this->objectManager->get(\CGB\Ews\Service\ExchangeConnectService::class, $credential);
        
        if (! $ignoreError) {
            if ($exchangeConnector->canConnect()) {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_ews_valid', strtolower($this->extensionName)), 
                    '', 
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::INFO
                );
            } else {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_ews_invalid', strtolower($this->extensionName)), 
                    '', 
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING
                );
            }
        }
        
        $this->view->assign('credential', $credential);
    }

    /**
     * action update
     *
     * @param CGB\Ews\Domain\Model\Credential
     * @return void
     */
    public function updateAction(\CGB\Ews\Domain\Model\Credential $credential)
    {
        $exchangeConnector = $this->objectManager->get(\CGB\Ews\Service\ExchangeConnectService::class, $credential);

        if ($exchangeConnector->canConnect()) {
            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_ews_can_connect', strtolower($this->extensionName)), 
                '', 
                \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
            );
        } else {
            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_ews_cannot_connect', strtolower($this->extensionName)), 
                '', 
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        }
        
        $this->credentialRepository->update($credential);
        $this->redirect('edit', null, null, ['ignoreError' => true]);
    }

    /**
     * action delete
     *
     * @param CGB\Ews\Domain\Model\Credential
     * @return void
     */
    public function deleteAction(\CGB\Ews\Domain\Model\Credential $credential)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/typo3cms/extensions/extension_builder/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->credentialRepository->remove($credential);
        $this->redirect('list');
    }
}

