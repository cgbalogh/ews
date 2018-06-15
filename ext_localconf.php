<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'CGB.Ews',
            'Ews',
            [
                'Credential' => 'edit, update'
            ],
            // non-cacheable actions
            [
                'Credential' => 'edit, update'
            ]
        );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    ews {
                        icon = ' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ews') . 'Resources/Public/Icons/user_plugin_ews.svg
                        title = LLL:EXT:ews/Resources/Private/Language/locallang_db.xlf:tx_ews_domain_model_ews
                        description = LLL:EXT:ews/Resources/Private/Language/locallang_db.xlf:tx_ews_domain_model_ews.description
                        tt_content_defValues {
                            CType = list
                            list_type = ews_ews
                        }
                    }
                }
                show = *
            }
       }'
    );
    }
);
## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
  'ews-connector',
  \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
  ['source' => 'EXT:ews/Resources/Public/Images/ews-connector.png']
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    'mod {
        wizards.newContentElement.wizardItems.plugins {
            elements {
                ews {
                    icon >
                    iconIdentifier = ews-connector
                    title = LLL:EXT:ews/Resources/Private/Language/locallang_db.xlf:tx_ews_domain_model_ews
                    description = LLL:EXT:ews/Resources/Private/Language/locallang_db.xlf:tx_ews_domain_model_ews.description
                    tt_content_defValues {
                        CType = list
                        list_type = ews_ews
                    }
                }
            }
            show = *
        }
   }'
);
