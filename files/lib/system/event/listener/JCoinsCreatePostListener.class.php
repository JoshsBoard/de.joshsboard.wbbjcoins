<?php
namespace wbb\system\event\listener;
use wcf\data\jCoins\statement\StatementAction;
use wcf\system\event\IEventListener;

/**
 * Handles jCoins on post creation.
 * 
 * @author	Joshua RÃ¼sweg
 * @package	de.joshsboard.wbbjoins
 */
class JCoinsCreatePostListener implements IEventListener {
	/**
	 * Statement action
	 * @var wcf\data\jCoins\statement\StatementAction
	 */
	public $statementAction = null;
	
	/**
	 * @see	wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!MODULE_JCOINS || JCOINS_RECEIVECOINS_CREATEPOST == 0) return;
		if ($eventObj->getActionName() !== 'triggerPublication') return;
		
		$this->statementAction = new StatementAction(array(), 'create', array(
			'data' => array(
				'reason' => 'wcf.jcoins.statement.postadd.receive',
				'sum' => JCOINS_RECEIVECOINS_CREATEPOST,
			),
			'changeBalance' => 1
		));
		$this->statementAction->validateAction();
		$this->statementAction->executeAction();
	}
}