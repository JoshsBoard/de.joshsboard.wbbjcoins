<?php
namespace wbb\system\event\listener;

use wbb\data\post\Post;
use wcf\data\user\jcoins\statement\UserJcoinsStatementAction;
use wcf\system\event\IEventListener;
use wcf\system\WCF; 

/**
 * Handles jCoins on post creation.
 * 
 * @author	Joshua Rüsweg
 * @package	de.joshsboard.wbbjoins
 */
class JCoinsCreatePostListener implements IEventListener {

	/**
	 * Statement action
	 * @var StatementAction
	 */
	public $statementAction = null;

	/**
	 * @see	IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (!MODULE_JCOINS || JCOINS_RECEIVECOINS_CREATEPOST == 0 || WCF::getSession()->userID == 0)
			return;

		$return = $eventObj->getReturnValues();
		$actionName = $eventObj->getActionName();
		$parameters = $eventObj->getParameters();

		switch ($actionName) {
			case 'quickReply':
			case 'create':
				if (isset($parameters['isFirstPost'])) return;

                                
				if (!isset($parameters['data']['isDisabled'])) {
					$this->create($parameters['data']['userID'], 'wcf.jcoins.statement.postadd.receive', JCOINS_RECEIVECOINS_CREATEPOST);
				}
				break;
				
			case 'enable':
				$postDatas = $return['returnValues']['postData'];

				foreach ($postDatas as $postID => $data) {
					$post = new Post($postID);
					$thread = $post->getThread();

					if ($post->postID != $thread->firstPostID) {
						$this->create($post->userID, 'wcf.jcoins.statement.postadd.receive', JCOINS_RECEIVECOINS_CREATEPOST);
					}
				}
				break;
				
			case 'disable':
				$postDatas = $return['returnValues']['postData'];

				foreach ($postDatas as $postID => $data) {
					$post = new Post($postID);
					$thread = $post->getThread();

					if ($post->postID != $thread->firstPostID) {
						$this->create($post->userID, 'wcf.jcoins.statement.postadd.revoke', -JCOINS_RECEIVECOINS_CREATEPOST);
					}
				}
				break;
				
			case 'trash':
				$postDatas = $return['returnValues']['postData'];

				foreach ($postDatas as $postID => $data) {
					$post = new Post($postID);
					$thread = $post->getThread();

					if ($post->postID != $thread->firstPostID) {
						$this->create($post->userID, 'wcf.jcoins.statement.postadd.delete', JCOINS_RECEIVECOINS_DELETEPOST);
					}
				}
				break; 
			
			case 'restore':
				$postDatas = $return['returnValues']['postData'];

				foreach ($postDatas as $postID => $data) {
					$post = new Post($postID);
					$thread = $post->getThread();

					if ($post->postID != $thread->firstPostID) {
						$this->create($post->userID, 'wcf.jcoins.statement.postadd.restore', JCOINS_RECEIVECOINS_DELETEPOST * -1);
					}
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
