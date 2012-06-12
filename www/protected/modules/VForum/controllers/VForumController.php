<?php
class VForumController extends Controller
{
    const COMMENTS_ON_PAGE = 10;

    public function init ()
    {
        $res = parent::init();
        $this->layout = $this->getModule()->getLayout();
        return $res;
    }

    public function actionIndex()
    {
        $user = Yii::app()->user;
        $discussionModel = new VForumDiscussion;
        $commentModel = new VForumDiscussionComment;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 40;
        $offset = ($page-1)*$limit;

        $discussions = array();
        $comments = array();

        $sql = '
SELECT DISTINCT (d.id), d.title, lc.id as `cid`, lc.date
FROM (
SELECT MAX( c.date ) AS `date` , c.id AS `id` , c.forum_discussion_id AS forum_discussion_id
FROM `'.$commentModel->tableName().'` c
GROUP BY c.forum_discussion_id
) AS lc, `'.$discussionModel->tableName().'` d
WHERE lc.forum_discussion_id = d.id
ORDER BY lc.date DESC
LIMIT '.$offset.', '.$limit.'
        ';

        $lastDiscussions = Yii::app()->db->commandBuilder->createSqlCommand($sql)->queryAll();

        $sql = '
SELECT COUNT( DISTINCT d.id ) as `total`
FROM `'.$commentModel->tableName().'` c, `'.$discussionModel->tableName().'` d
WHERE c.forum_discussion_id = d.id
LIMIT 1
        ';

        $total = Yii::app()->db->commandBuilder->createSqlCommand($sql)->queryAll();
        if ($total)
            $total = $total[0]['total'];
        else
            $total = 0;

        $dIds = array();
        $cIds = array();
        foreach ($lastDiscussions as $row)
        {
            $dIds[] = $row['id'];
            $cIds[] = $row['cid'];
        }

        if ($dIds)
        {
            $discussionsModels = VForumDiscussion::model()->byIds($dIds)->findAll();
            $tmp = array ();
            foreach ($discussionsModels as $discussion)
                $tmp[$discussion->id] = $discussion;
            foreach ($dIds as $dId)
            {
                if (isset($tmp[$dId]))
                    $discussions[] = $tmp[$dId];
            }
        }
        if ($cIds)
        {
            $commentModels = VForumDiscussionComment::model()->byIds($cIds)->findAll();
            $tmp = array ();
            foreach ($commentModels as $commentModel)
                $tmp[$commentModel->id] = $commentModel;
            foreach ($cIds as $cId)
            {
                if (isset($tmp[$cId]))
                    $comments[$tmp[$cId]->forum_discussion_id] = $tmp[$cId];
            }
        }

        $view = $this->getModule()->getViewsAlias('main');
        $this->render($view, array (
            'comments' => $comments,
            'discussions' => $discussions,
            'commentsOnPge' => self::COMMENTS_ON_PAGE,
            'discussionOnPage' => $limit,
            'total' => $total,
            'offset' => $offset,
            'page' => $page,
        ));
    }

    public function actionDiscussion()
    {
        $user = Yii::app()->user;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $id = isset($_GET['id']) ? (int)$_GET['id'] : false;
        $discussion = VForumDiscussion::model()->findByPk($id);
        if (!$discussion)
            throw new CHttpException(404);

        $commentForm = new VDiscussionCommentForm;
        if (isset($_GET['replyTo']))
        {
            $parentId = (int)$_GET['replyTo'];
            $comment = VForumDiscussionComment::model()->findByPk($parentId);
            $error = true;
            if ($comment)
            {
                $error = false;
                $commentForm->text = $comment->getQuoteText ();
            }
            if (Yii::app()->request->isAjaxRequest)
            {
                echo CJSON::encode(array(
                    'success' => true,
                    'text' => $commentForm->text,
                ));
                die();
            }
        }
        else if (isset($_POST['VDiscussionCommentForm']))
        {
            $commentForm->setAttributes($_POST['VDiscussionCommentForm']);
            $error = true;
            $comment = new VForumDiscussionComment;
            if ($commentForm->validate())
            {
                if (!Yii::app()->user->id)
                {
                    $commentForm->addError('text', 'зайдите на сайт, чтобы оставлять комментарии');
                }
                else
                {
                    // todo: сохраняем комментарий
                    $comment->user_id = Yii::app()->user->id;
                    $comment->date = time();
                    $comment->forum_discussion_id = $discussion->id;
                    $comment->content = $commentForm->text;
                    // $comment->parentId = $commentForm->parentId;

                    if ($comment->save())
                        $error = false;
                    else
                    {
                        print_r($comment->getErrors());
                        die();
                    }

                }
            }

            if (Yii::app()->request->isAjaxRequest)
            {
                if ($error)
                {
                    $errors = array();
                    foreach ($commentForm->getErrors() as $attr => $_errors)
                    {
                        $errors[$attr] = $_errors[0];
                    }

                    echo CJSON::encode(array(
                        'error' => true,
                        'errors' => $errors,
                    ));
                    die();
                }
                else
                {
                    $view = $this->getModule()->getViewsAlias('blocks.comment');
                    $commentHtml = $this->renderPartial($view, array('comment' => $comment), true);
                    echo CJSON::encode(array(
                        'success' => true,
                        'commentId' => $comment->id,
                        'parentCommentId' => $commentForm->parentId,
                        'comment' => $commentHtml,
                    ));
                    die();
                }
            }

        }

        $comments = VForumDiscussionComment::model()->byObjectId('forum_discussion_id', $id)->byOffset(($page-1)*self::COMMENTS_ON_PAGE)->byLimit(self::COMMENTS_ON_PAGE)->orderDefault()->findAll();

        $cs = Yii::app()->clientScript;
        $assets_path = dirname(__FILE__). DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR.'assets';
        $url = Yii::app()->assetManager->publish($assets_path, false, -1, YII_DEBUG);
        $cs->registerScriptFile($url.'/js/jquery.form.js', CClientScript::POS_HEAD);
        $cs->registerScriptFile($url.'/js/jquery.vforum.js', CClientScript::POS_HEAD);


        $view = $this->getModule()->getViewsAlias('discussion');
        $this->render($view, array (
            'comments' => $comments,
            'discussion' => $discussion,
            'commentsOnPge' => self::COMMENTS_ON_PAGE,
            'page' => $page,
            'commentForm' => $commentForm,
        ));

    }

    public function actionCategory()
    {
    }

    public function actionAddDiscussion()
    {
    }

    public function actionAddComment()
    {
    }

    public function actionRemoveDiscussion()
    {
    }

    public function actionRemoveComment()
    {
    }


}