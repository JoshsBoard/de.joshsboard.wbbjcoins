<?php
namespace wbb\system\event\listener;
use wcf\data\jCoins\statement\StatementAction;
use wcf\system\event\IEventListener;

/**
 * Handles jCoins on thread creation.
 * 
 * @author	Joshua RÃ¼sweg
 * @package	de.joshsboard.wbbjoins
 */
class JCoinsCreateThreadListener implements IEventListener {
	/**
	 * @see	wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!MODULE_JCOINS || JCOINS_RECEIVECOINS_CREATETHREAD == 0) return;
		if ($eventObj->getActionName() != 'create') return;
		
		$this->statementAction = new StatementAction(array(), 'create', array(
			'data' => array(
				'reason' => 'wcf.jcoins.statement.threadadd.receive',
				'sum' => JCOINS_RECEIVECOINS_CREATETHREAD,
			),
			'changeBalance' => 1
		));
		$this->statementAction->validateAction();
		$this->statementAction->executeAction();
	}
}