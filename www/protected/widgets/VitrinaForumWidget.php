<?php
class VitrinaForumWidget extends CWidget {

    public $template = 'forum';

    public function run() {
		parent::run();

        $m = Yii::app()->getModule('VForum');
        $discussionModel = new VForumDiscussion;
        $commentModel = new VForumDiscussionComment;
        $discussions = array();

        $sql = '
SELECT DISTINCT (d.id), d.title, lc.id as `cid`, lc.date
FROM (
SELECT MAX( c.date ) AS `date` , c.id AS `id` , c.forum_discussion_id AS forum_discussion_id
FROM `'.$commentModel->tableName().'` c
GROUP BY c.forum_discussion_id
) AS lc, `'.$discussionModel->tableName().'` d
WHERE lc.forum_discussion_id = d.id
ORDER BY lc.date DESC
LIMIT 5';

        $lastDiscussions = Yii::app()->db->commandBuilder->createSqlCommand($sql)->queryAll();

        $dIds = array();
        foreach ($lastDiscussions as $row)
        {
            $dIds[] = $row['id'];
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

		$this->render($this->template, array(
            'discussions' => $discussions,
		));
    }

}
