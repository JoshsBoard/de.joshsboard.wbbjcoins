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
				$thread = new \wbb\data\thread\Thread($parameters['objectID']); 
				$board = $thread->getBoard(); 
				
			case 'create':
				if (isset($parameters['isFirstPost'])) return;
                                
				if (!isset($parameters['data']['isDisabled'])) {
					if (!isset($board)) {
						$post = $return['returnValues']; 
						$thread = $post->getThread();
						$board = $thread->getBoard(); 
					}
					
					if (isset($post)) {
						$link = $post->getLink(); 
						$title = $post->getTitle(); 
					} else {
						$link = $thread->getLink(); 
						$title = 'RE: '.$thread->getTitle(); 
					}
					
					$jcoins = ($board->customJCoins) ? $board->customJCoinsCreatePost : JCOINS_RECEIVECOINS_CREATEPOST;
					
					$this->create($parameters['data']['userID'], 'wcf.jcoins.statement.postadd.receive', $jcoins, $link, $title);
				}
				break;
				
			case 'enable':
				$postDatas = $return['returnValues']['postData'];

				foreach ($postDatas as $postID => $data) {
					$post = new Post($postID);
					$thread = $post->getThread();
					$board = $thread->getBoard(); 
					
					$jcoins = ($board->customJCoins) ? $board->customJCoinsCreatePost : JCOINS_RECEIVECOINS_CREATEPOST;
				
					if ($jcoins == 0) {
						return; // no jcoins :(
					}
					
					if ($post->postID != $thread->firstPostID) {
						$this->create($post->userID, 'wcf.jcoins.statement.postadd.receive', $jcoins, $post->getLink(), $post->getTitle());
					}
				}
				break;
				
			case 'disable':
				$postDatas = $return['returnValues']['postData'];

				foreach ($postDatas as $postID => $data) {
					$post = new Post($postID);
					$thread = $post->getThread();
					$board = $thread->getBoard(); 
					
					$jcoins = ($board->customJCoins) ? $board->customJCoinsCreatePost : JCOINS_RECEIVECOINS_CREATEPOST;
				
					if ($jcoins == 0) {
						return; // no jcoins :(
					}
					
					if ($post->postID != $thread->firstPostID) {
						$this->create($post->userID, 'wcf.jcoins.statement.postadd.revoke', -$jcoins, $post->getLink(), $post->getTitle());
					}
				}
				break;
				
			case 'trash':
				$postDatas = $return['returnValues']['postData'];

				foreach ($postDatas as $postID => $data) {
					$post = new Post($postID);
					$thread = $post->getThread();
					$board = $thread->getBoard(); 
					
					$jcoins = ($board->customJCoins) ? $board->customJCoinsTrashPost : JCOINS_RECEIVECOINS_DELETEPOST;
				
					if ($jcoins == 0) {
						return; // no jcoins :(
					}
					
					if ($post->postID != $thread->firstPostID) {
						$this->create($post->userID, 'wcf.jcoins.statement.postadd.delete', $jcoins, $post->getLink(), $post->getTitle());
					}
				}
				break; 
			
			case 'restore':
				$postDatas = $return['returnValues']['postData'];

				foreach ($postDatas as $postID => $data) {
					$post = new Post($postID);
					$thread = $post->getThread();
					$board = $thread->getBoard(); 
					
					$jcoins = ($board->customJCoins) ? $board->customJCoinsTrashPost : JCOINS_RECEIVECOINS_DELETEPOST;
				
					if ($jcoins == 0) {
						return; // no jcoins :(
					}
					
					if ($post->postID != $thread->firstPostID) {
						$this->create($post->userID, 'wcf.jcoins.statement.postadd.restore', $jcoins * -1, $post->getLink(), $post->getTitle());
					}
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
