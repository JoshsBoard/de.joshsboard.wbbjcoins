<?php
namespace wbb\system\event\listener;

use wbb\data\thread\Thread;
use wcf\data\jcoins\statement\UserJcoinsStatementAction;
use wcf\system\event\IEventListener;

/**
 * Handles jCoins on thread creation.
 * 
 * @author	Joshua RÃ¼sweg
 * @package	de.joshsboard.wbbjoins
 */
class JCoinsCreateThreadListener implements IEventListener {

	/**
	 * @see	IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!MODULE_JCOINS || JCOINS_RECEIVECOINS_CREATETHREAD == 0)
			return;

		$return = $eventObj->getReturnValues();
		$actionName = $eventObj->getActionName();

		switch ($actionName) {
			case 'create':
				$thread = $return['returnValues'];

				if (!$thread->isDisabled) {
					$this->create($thread->userID, 'wcf.jcoins.statement.threadadd.receive', JCOINS_RECEIVECOINS_CREATETHREAD);
				}
				break;
			
			case 'trash':
				$threadDatas = $return['returnValues']['threadData'];

				foreach ($threadDatas as $threadID => $data) {
					$thread = new Thread($threadID);

					$this->create($thread->userID, 'wcf.jcoins.statement.threadadd.delete', JCOINS_RECEIVECOINS_DELETETHREAD);
				}
				break; 
			
			case 'restore':
				$threadDatas = $return['returnValues']['threadData'];

				foreach ($threadDatas as $threadID => $data) {
					$thread = new Thread($threadID);

					$this->create($thread->userID, 'wcf.jcoins.statement.threadadd.restore', -JCOINS_RECEIVECOINS_DELETETHREAD);
				}
				break; 
			
			case 'enable':
				$threadDatas = $return['returnValues']['threadData'];

				foreach ($threadDatas as $threadID => $data) {
					$thread = new Thread($threadID);

					$this->create($thread->userID, 'wcf.jcoins.statement.threadadd.receive', JCOINS_RECEIVECOINS_CREATETHREAD);
				}
				break;
				
			case 'disable':
				
				$threadDatas = $return['returnValues']['threadData'];

				foreach ($threadDatas as $threadID => $data) {
					$thread = new Thread($threadID);

					$this->create($thread->userID, 'wcf.jcoins.statement.threadadd.revoke', -JCOINS_RECEIVECOINS_CREATETHREAD);
				}
				break;
		}
	}

	protected function create($userID, $reason, $sum) {
		$this->statementAction = new UserJcoinsStatementAction(array(), 'create', array(
		    'data' => array(
			'reason' => $reason,
			'sum' => $sum,
			'userID' => $userID
		    ),
		    'changeBalance' => 1
		));
		$this->statementAction->validateAction();
		$this->statementAction->executeAction();
	}
}