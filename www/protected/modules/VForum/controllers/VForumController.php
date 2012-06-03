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
        $dIds = array ();
        $limit = 50;
        $offset = 0;
        $comments = VForumDiscussionComment::model()->orderLast()->byLimit($limit)->byOffset($offset*$limit)->findAll();
        $maxDisc = 20;
        $disc = array();
        $lastCom = array ();
        $user = Yii::app()->user;

        while ($comments || count($dIds) < $maxDisc)
        {
            $lastComments = array ();
            $ids = array();

            foreach ($comments as $comment)
            {
                if (isset($lastCom[$comment->forum_discussion_id]))
                    continue;

                $lastCom[$comment->forum_discussion_id] = $comment;
                $lastComments[$comment->forum_discussion_id] = $comment;
                $ids[] = $comment->forum_discussion_id;
                $dIds[] = $comment->forum_discussion_id;
                if (count($dIds) >= $maxDisc)
                    break;
            }

            if ($ids)
            {
                $discussions = VForumDiscussion::model()->byIds($ids)->findAll();
                $tmp = array ();
                foreach ($discussions as $discussion)
                    $tmp[$discussion->id] = $discussion;

                foreach ($lastComments as $dId => $c)
                {
                    if (count($disc) >= $maxDisc)
                        break;
                    if (isset($tmp[$dId]))
                        $disc[] = $tmp[$dId];
                }
            }
            $offset++;
            $comments = VForumDiscussionComment::model()->orderLast()->byLimit($limit)->byOffset($offset*$limit)->findAll();
        }

        $view = $this->getModule()->getViewsAlias('main');
        $this->render($view, array (
            'comments' => $lastCom,
            'discussions' => $disc,
            'commentsOnPge' => self::COMMENTS_ON_PAGE,
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

        $comments = VForumDiscussionComment::model()->byObjectId('forum_discussion_id', $id)->byOffset(($page-1)*self::COMMENTS_ON_PAGE)->byLimit(self::COMMENTS_ON_PAGE)->orderDefault()->findAll();

        $view = $this->getModule()->getViewsAlias('discussion');
        $this->render($view, array (
            'comments' => $comments,
            'discussion' => $discussion,
            'commentsOnPge' => self::COMMENTS_ON_PAGE,
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