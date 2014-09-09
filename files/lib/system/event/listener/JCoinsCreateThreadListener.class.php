<?php
namespace wbb\system\event\listener;

use wbb\data\thread\Thread;
use wcf\data\user\jcoins\statement\UserJcoinsStatementAction;
use wcf\system\event\IEventListener;
use wcf\system\WCF; 

/**
 * Handles jCoins on thread creation.
 * 
 * @author	Joshua Rüsweg
 * @package	de.joshsboard.wbbjoins
 */
class JCoinsCreateThreadListener implements IEventListener {

	/**
	 * @see	IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!MODULE_JCOINS || WCF::getSession()->userID == 0)
			return;

		$return = $eventObj->getReturnValues();
		$actionName = $eventObj->getActionName();

		switch ($actionName) {
			case 'create':
				$thread = $return['returnValues'];
				$board = $thread->getBoard();
				
				$jcoins = ($board->customJCoins) ? $board->customJCoinsCreateThread : JCOINS_RECEIVECOINS_CREATETHREAD;
				
				if ($jcoins == 0) {
					return; // no jcoins :(
				}
				
				if (!$thread->isDisabled) {
					$this->create($thread->userID, 'wcf.jcoins.statement.threadadd.receive', $jcoins, $thread->getLink(), $thread->getTitle());
				}
				break;
			
			case 'trash':
				$threadDatas = $return['returnValues']['threadData'];

				foreach ($threadDatas as $threadID => $data) {
					$thread = new Thread($threadID);
					$board = $thread->getBoard(); 
					
					$jcoins = ($board->customJCoins) ? $board->customJCoinsTrashThread : JCOINS_RECEIVECOINS_DELETETHREAD;
				
					if ($jcoins == 0) {
						return; // no jcoins :(
					}
					
					$this->create($thread->userID, 'wcf.jcoins.statement.threadadd.delete', $jcoins, $thread->getLink(), $thread->getTitle());
				}
				break; 
			
			case 'restore':
				$threadDatas = $return['returnValues']['threadData'];

				foreach ($threadDatas as $threadID => $data) {
					$thread = new Thread($threadID);
					$board = $thread->getBoard();
					
					$jcoins = ($board->customJCoins) ? $board->customJCoinsTrashThread : JCOINS_RECEIVECOINS_DELETETHREAD;
				
					if ($jcoins == 0) {
						return; // no jcoins :(
					}
					
					$this->create($thread->userID, 'wcf.jcoins.statement.threadadd.restore', -$jcoins, $thread->getLink(), $thread->getTitle());
				}
				break; 
			
			case 'enable':
				$threadDatas = $return['returnValues']['threadData'];

				foreach ($threadDatas as $threadID => $data) {
					$thread = new Thread($threadID);
					$board = $thread->getBoard();
					
					$jcoins = ($board->customJCoins) ? $board->customJCoinsCreateThread : JCOINS_RECEIVECOINS_CREATETHREAD;
				
					if ($jcoins == 0) {
						return; // no jcoins :(
					}
					
					$this->create($thread->userID, 'wcf.jcoins.statement.threadadd.receive', $jcoins, $thread->getLink(), $thread->getTitle());
				}
				break;
				
			case 'disable':
				
				$threadDatas = $return['returnValues']['threadData'];

				foreach ($threadDatas as $threadID => $data) {
					$thread = new Thread($threadID);
					$board = $thread->getBoard();
					
					$jcoins = ($board->customJCoins) ? $board->customJCoinsCreateThread : JCOINS_RECEIVECOINS_CREATETHREAD;
				
					if ($jcoins == 0) {
						return; // no jcoins :(
					}
					
					$this->create($thread->userID, 'wcf.jcoins.statement.threadadd.revoke', -$jcoins, $thread->getLink(), $thread->getTitle());
				}
				break;
		}
	}

	protected function create($userID, $reason, $sum, $link = '', $title = '') {
		$this->statementAction = new UserJcoinsStatementAction(array(), 'create', array(
		    'data' => array(
			'reason' => $reason,
			'sum' => $sum,
			'userID' => $userID, 
			'additionalData' => array('title' => $title), 
			'link' => $link
		    ),
		    'changeBalance' => 1
		));
		$this->statementAction->validateAction();
		$this->statementAction->executeAction();
	}
}
