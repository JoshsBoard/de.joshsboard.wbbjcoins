<?php
namespace wbb\system\event\listener;

use wcf\system\event\IEventListener;

/**
 * Handles jCoins on post creation.
 * 
 * @author	Joshua Rüsweg
 * @copyright	2013-2014 Joshua Rüsweg
 * @license	Creative Commons Attribution-ShareAlike 4.0 <https://creativecommons.org/licenses/by-sa/4.0/legalcode>
 * @package	de.joshsboard.wbbjoins
 */
class JCoinsBoardAddEditListener implements IEventListener {

	/**
	 * @see	IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!MODULE_JCOINS) return; 
		
		switch ($eventName) {
			case 'readFormParameters': 
				if (isset($_POST['customJCoins'])) $eventObj->customJCoins = 1; // for true :) 
				if (isset($_POST['customJCoinsCreateThread'])) $eventObj->customJCoinsCreateThread = intval($_POST['customJCoinsCreateThread']); 
				if (isset($_POST['customJCoinsCreatePost'])) $eventObj->customJCoinsCreatePost = intval($_POST['customJCoinsCreatePost']); 
				if (isset($_POST['customJCoinsTrashThread'])) $eventObj->customJCoinsTrashThread = intval($_POST['customJCoinsTrashThread']); 
				if (isset($_POST['customJCoinsTrashPost'])) $eventObj->customJCoinsTrashPost = intval($_POST['customJCoinsTrashPost']); 
				break; 
			
			case 'readParameters':
				$eventObj->customJCoins = 0; 
				$eventObj->customJCoinsCreateThread = 0; 
				$eventObj->customJCoinsCreatePost = 0; 
				$eventObj->customJCoinsTrashThread = 0; 
				$eventObj->customJCoinsTrashPost = 0;
				break; 
				
			case 'readData': 
				if ($eventObj instanceof \wbb\acp\form\BoardEditForm) {
					$eventObj->customJCoins = $eventObj->board->customJCoins; 
					$eventObj->customJCoinsCreateThread = $eventObj->board->customJCoinsCreateThread; 
					$eventObj->customJCoinsCreatePost = $eventObj->board->customJCoinsCreatePost; 
					$eventObj->customJCoinsTrashThread = $eventObj->board->customJCoinsTrashThread; 
					$eventObj->customJCoinsTrashPost = $eventObj->board->customJCoinsTrashPost; 
				}
				break; 
				
			case 'save':
				$eventObj->additionalFields['customJCoins'] = $eventObj->customJCoins; 
				$eventObj->additionalFields['customJCoinsCreateThread'] = $eventObj->customJCoinsCreateThread; 
				$eventObj->additionalFields['customJCoinsCreatePost'] = $eventObj->customJCoinsCreatePost; 
				$eventObj->additionalFields['customJCoinsTrashThread'] = $eventObj->customJCoinsTrashThread; 
				$eventObj->additionalFields['customJCoinsTrashPost'] = $eventObj->customJCoinsTrashPost; 
				break; 
			
			case 'assignVariables': 
				\wcf\system\WCF::getTPL()->assign(array(
					'customJCoins' => $eventObj->customJCoins, 
					'customJCoinsCreateThread' => $eventObj->customJCoinsCreateThread, 
					'customJCoinsCreatePost' => $eventObj->customJCoinsCreatePost, 
					'customJCoinsTrashThread' => $eventObj->customJCoinsTrashThread, 
					'customJCoinsTrashPost' => $eventObj->customJCoinsTrashPost
				)); 
				break; 
		}
	}
}
