<?php
namespace JambageCom\Mailbyzip\UserFunc;

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


use TYPO3\CMS\Core\Utility\GeneralUtility;

use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\SessionUtility;


/**
 * Class ZipReceiver
 */
class ZipReceiver
{
    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected $contentObject = null;

    /**
     * TypoScript settings as plain array
     *
     * @var array
     */
    protected $settings = [];

    /**
     * TypoScript Powermail settings as plain array
     *
     * @var array
     */
    protected $powerSettings = [];


    /**
     * @param Mail $mail
     * @param array $settings
     */
    public function __construct()
    {
        $this->settings = ObjectUtility::getTypoScriptFrontendController()->tmpl->setup['lib.']['mailbyzip.']['settings.'];
        $this->powerSettings = ObjectUtility::getTypoScriptFrontendController()->tmpl->setup['plugin.']['tx_powermail.']['settings.']['setup.'];
    }

    /**
     * UserFunc method to fetch email addresses according to a give zip
     *
     * Example:
     * # Convert 2015-12-31 into 1451516400
     * lib.zip {
     *        userFunc = In2code\Powermail\UserFunc\DateConverter->convert
     *        includeLibs = EXT:powermail/Classes/UserFunc/DateConverter.php
     *
     *        input = TEXT
     *        input.value = 2015-12-31
     *
     *        inputFormat = TEXT
     *        inputFormat.value = Y-m-d
     *
     *        outputFormat = TEXT
     *        outputFormat.value = U
     * }
     *
     * @param string $content normally empty in userFuncs
     * @param array $configuration TypoScript configuration from userFunc
     * @return string
     */
    /**
     * Return Receiver by FE User Uid
     *
     * @param    int            FE_Users Uid
     * @return    string        Matching Email address
     */
    public function fetchEmails($content, array $conf)
    {
        $receiverEmails = [];
        $conf = $this->settings;
        // config
        $cObj = ObjectUtility::getContentObject();
        $receivers = $conf['receiver.']; // receivers array
        $sendername = $conf['sendername']; // read sendername
        $sessionData = SessionUtility::getSessionValuesForPrefill($this->powerSettings);

        $types = ['angebotueber', 'kuredade'];
        foreach ($types as $type) {
            $newEmails =
                $this->getReceiverByOptions(
                    $type,
                    $sessionData[$type],
                    $conf['shortcut.']
                );
            if ($newEmails) {
                $receiverEmails = array_merge($receiverEmails, $newEmails);
            }
        }

        if (!$receiverEmails) {
            $zip = $sessionData['plz'];
            $newEmail = $this->getReceiverByZip($zip, $conf['shortcut.'], $receivers); // fill string with a fitting receiver
            if ($newEmail) {
                $receiverEmails[] = $newEmail;
            }
        }

        $receiverEmails[] = $conf['shortcut.'][$conf['receiver_static']];
        $receiverEmails = array_unique($receiverEmails);
        $result = [];
        foreach ($receiverEmails as $email) {
            if (GeneralUtility::validEmail($email)) {
                $result[] = $email;
            }
        }

        return implode(',', $result);
    }

    private function getReceiverByOptions($type, $optionCode, array $shortcuts) {
        $result = [];

        if (isset($this->settings['option.'][$type . '.'][$optionCode])) {
            $shurtcut = $this->settings['option.'][$type . '.'][$optionCode];
            if (isset($shortcuts[$shurtcut])) {
                $result = $shortcuts[$shurtcut];
            }
        }

        return $result;
    }

    private function zipComparison ($wildcardZip, $zip) 
    {
        $wildcardZipPrefix = str_replace('*', '', $wildcardZip);
        $originalZipPrefix = substr($zip, 0, strlen($wildcardZipPrefix)); // cut user given zip after same length as current zip definition length
        return strcmp($originalZipPrefix, $wildcardZipPrefix);
    }

    /**
     * Return Receiver by given ZIP code
     *
     * @param    string        ZIP code from user input
     * @param    array        Receivers Array
     * @return    string        Matching Email address
     */
    private function getReceiverByZip($zip, array $shortcuts, array $receivers)
    {
        $result = '';
        foreach ((array) $receivers as $receiver) { // one loop for every zip/email definition
            if (
                !isset($receiver['start']) ||
                !isset($receiver['email']) ||
                !isset($shortcuts[$receiver['email']])
            ) {
                continue;
            }
            $comparison = $this->zipComparison($receiver['start'], $zip);
            if ($comparison == 0 && !isset($receiver['end'])) {
                $result = $shortcuts[$receiver['email']];
            } else if ($comparison >= 0 && isset($receiver['end'])) {
                $comparison = $this->zipComparison($receiver['end'], $zip);
                if ($comparison <= 0) {
                    $result = $shortcuts[$receiver['email']];
                }
            }
        }

        return $result;
    }
}

