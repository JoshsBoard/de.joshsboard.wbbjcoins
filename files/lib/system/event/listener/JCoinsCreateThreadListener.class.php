<?php
namespace wbb\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\data\jCoins\statement\StatementAction;

/**
 * add jcoins on create a thread
 * 
 * @author	Joshua Rüsweg
 * @package	de.joshsboard.jcoins
 */
class JCoinsCreateThreadListener implements IEventListener {
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!MODULE_JCOINS || JCOINS_RECEIVECOINS_CREATETHREAD == 0) return;
		if ($eventObj->getActionName() != 'create') return; 
		
		$this->statementAction = new StatementAction(array(), 'create', array(
			'data' => array(
				'reason' => 'wcf.jcoins.statement.threadadd.recive',
				'sum' => JCOINS_RECEIVECOINS_CREATETHREAD, 
                        ), 
                        'changeBalance' => 1
		));
                $this->statementAction->validateAction();
		$this->statementAction->executeAction();
	}
}