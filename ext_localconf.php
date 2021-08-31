<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(function () {

    if (TYPO3_MODE == 'FE') {
        /** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
        $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
        $signalSlotDispatcher->connect(
            \In2code\Powermail\Domain\Service\Mail\ReceiverMailReceiverPropertiesService::class,     // Signal class name
            'setReceiverEmails',                                           // Signal name
            \JambageCom\Mailbyzip\Slots\ReceiverMailReceiverSlots::class, // Slot class name
            'getEmailsFromCsv'                                          // Slot name
        );
    }
});

