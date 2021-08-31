<?php
namespace JambageCom\Mailbyzip\Slots;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

use In2code\Powermail\Utility\ObjectUtility;


/**
 * Class for slots to signals for the determination of receiver emails
 */
class ReceiverMailReceiverSlots implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * TypoScript settings as plain array
     *
     * @var array
     */
    protected $settings = [];


    /**
     * @param Mail $mail
     * @param array $settings
     */
    public function __construct()
    {
        $conf = ObjectUtility::getTypoScriptFrontendController()->tmpl->setup['lib.']['mailbyzip.'];
        $this->settings['converter']  = $conf['converter'];
        $this->settings['converter.'] = $conf['converter.'];
    }

    public function getEmailsFromCsv (&$emailArray, $pObj)
    {
        $cObj = ObjectUtility::getContentObject();
        $newEmails =
            $cObj->cObjGetSingle(
                $this->settings['converter'], 
                $this->settings['converter.']
            );

        if ($newEmails) {
            $emailArray = array_merge($emailArray, explode(',', $newEmails));
            $emailArray = array_unique($emailArray);
        }
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
