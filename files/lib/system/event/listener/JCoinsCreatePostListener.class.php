<?php
namespace wbb\system\event\listener;

use wbb\data\post\Post;
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
     * @var StatementAction
     */
    public $statementAction = null;

    /**
     * @see	IEventListener::execute()
     */
    public function execute($eventObj, $className, $eventName) {
        if (!MODULE_JCOINS || JCOINS_RECEIVECOINS_CREATEPOST == 0) return;

        $return = $eventObj->getReturnValues();
        $actionName = $eventObj->getActionName();
        $parameters = $eventObj->getParameters();
        
        switch ($actionName) {
            case 'create':
                if (isset($parameters['isFirstPost'])) return;
                
                $post = $return['returnValues'];
                
                if (!$post->isDisabled) {
                    $this->create($post->userID, 'wcf.jcoins.statement.postadd.receive', JCOINS_RECEIVECOINS_CREATEPOST);
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
        }
    }

    protected function create($userID, $reason, $sum) {
        $this->statementAction = new StatementAction(array(), 'create', array(
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