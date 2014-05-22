<?php
class VForumController extends Controller
{
    const COMMENTS_ON_PAGE = 10;

    public function pageTitlePostfix () {
        return 'Общение на сайте '.Yii::app()->params['siteName'];
    }

    public function init ()
    {
        $this->crumbs[] = array('label' => 'Общение', 'link' => array('/VForum/VForum/index'));
        $this->pageTitle = $this->pageTitlePostfix ();
        $res = parent::init();
        $this->layout = $this->getModule()->getLayout();
        return $res;
    }

    public function actionIndex()
    {
        $discussionModel = new VForumDiscussion;
        $commentModel = new VForumDiscussionComment;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
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
        foreach ($lastDiscussions as $row)
        {
            $dIds[] = $row['id'];
            $lc = VForumDiscussionComment::model()->byObjectId('forum_discussion_id', $row['id'])->orderLast()->find();
            if ($lc && $lc->user)
                $comments[$row['id']] = $lc;
        }

        if ($dIds)
        {
            $discussionsModels = VForumDiscussion::model()->byIds($dIds)->findAll();
            $tmp = array ();
            foreach ($discussionsModels as $discussion)
            {
                if (!$discussion->user)
                    continue;
                $tmp[$discussion->id] = $discussion;
            }
            foreach ($dIds as $dId)
            {
                if (isset($tmp[$dId]))
                    $discussions[] = $tmp[$dId];
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
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $id = isset($_GET['id']) ? (int)$_GET['id'] : false;
        $discussion = VForumDiscussion::model()->findByPk($id);
        if (!$discussion)
            throw new CHttpException(404);
        if (!$discussion->user)
            throw new CHttpException(404);

        $this->crumbs[] = array('label' => $discussion->title, 'link' => array('/VForum/VForum/discussion', 'id'=>$id));
        $this->pageTitle = $discussion->title.' &mdash; '.$this->pageTitlePostfix ();

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
                        $errors[CHtml::activeName($commentForm, $attr)] = $_errors[0];
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
        $tmp = array ();
        foreach ($comments as $comment)
        {
            if (!$comment->user)
                continue;
            $tmp[] = $comment;
        }
        $comments = $tmp;

        $cs = Yii::app()->clientScript;
        $url = $this->getModule()->getAssetsUrl();
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
        $this->crumbs[] = array('label' => 'Новая тема', 'link' => array('/VForum/VForum/addDiscussion', 'id'=>$id));
        $this->pageTitle = 'Новая тема &mdash; '.$this->pageTitlePostfix ();

        $form = new VForumDiscussionForm;
        $form->forum_category_id = 1;
        if (isset($_POST['VForumDiscussionForm']))
        {
            $form->setAttributes($_POST['VForumDiscussionForm']);
            $error = true;
            $comment = new VForumDiscussionComment;
            if ($form->validate())
            {
                if (!Yii::app()->user->id)
                {
                    $form->addError('text', 'зайдите на сайт, чтобы создать тему');
                }
                else
                {
                    $discussion = new VForumDiscussion;
                    $discussion->scenario = 'type'.VForumCategory::TYPE_DEFAULT;
                    $discussion->title = $form->title;
                    $discussion->forum_category_id = $form->forum_category_id;
                    $discussion->date_created = time();
                    $discussion->user_id = Yii::app()->user->id;
                    if ($discussion->save())
                    {
                        $comment->user_id = Yii::app()->user->id;
                        $comment->date = time();
                        $comment->forum_discussion_id = $discussion->id;
                        $comment->content = $form->text;
                        if ($comment->save())
                            $error = false;
                    }
                }
            }

            if (Yii::app()->request->isAjaxRequest)
            {
                if ($error)
                {
                    $errors = array();
                    foreach ($form->getErrors() as $attr => $_errors)
                    {
                        $errors[CHtml::activeName($form, $attr)] = $_errors[0];
                    }

                    echo CJSON::encode(array(
                        'error' => true,
                        'errors' => $errors,
                    ));
                    die();
                }
                else
                {
                }
            }

            if (!$error)
            {
                $this->redirect(array('/VForum/VForum/discussion', 'id'=>$discussion->id));
            }
        }
        $view = $this->getModule()->getViewsAlias('addDiscussion');
        $this->render($view, array (
            'form' => $form,
        ));
    }

    public function actionAddComment()
    {
    }

    public function actionRemoveDiscussion()
    {
    }

    public function actionRemoveComment()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : false;
        if ($id && Yii::app()->user->checkRoles(array(VUser::ROLE_ADMIN, VUser::ROLE_MODER))) {
            $comment = VForumDiscussionComment::model()->findByPk($id);
            $res = $comment->delete();
            if (Yii::app()->request->isAjaxRequest) {
                if ($res) {
                    $result = array (
                        'success' => 1,
                    );
                }
                else {
                    $result = array (
                        'error' => 1,
                        'message' => 'Не удалось удалить комментарий'
                    );
                }
                echo CJSON::encode($result);
                die();
            }
            else
                $this->redirect(Yii::app()->user->returnUrl);
        }
        else
            throw new CHttpException(404, 'Страница не найдена');

    }


}