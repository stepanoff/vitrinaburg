            <h1>Общение на <?php echo Yii::app()->params['siteName']; ?></h1>
            <div class="f-top-bar">
              <?php $pager = VForumHtmlHelper::pager($total, $discussionOnPage, array('/VForum/VForum/index'), $page, 10, 'Всего: '.$total.', показано с '.($offset+1).' по '.($offset+count($discussions)) ); ?>
              <?php echo $pager; ?>
                <?php
                $onclick = false;
                if (!Yii::app()->user->id)
                    $onclick = 'onclick="Vauth.launch(); return false;"';
                ?>
              <a href="<?php echo CHtml::normalizeUrl(array('/VForum/VForum/addDiscussion')); ?>" <?php echo $onclick; ?> class="f-button gradient1">Создать тему</a>
            </div>

            <?php
            if ($discussions)
            {
                ?>
                <div class="f-list-head clearfix">
                  <div class="cell-theme">Название</div>
                  <div class="cell-author">Автор</div>
                  <div class="cell-length">Сообщений</div>
                </div>
                <ul class="f-list">
                <?php
                foreach ($discussions as $discussion)
                {
                    ?>
                    <li>
                      <div class="cell-theme">
                        <?php echo CHtml::link($discussion->title, array('/VForum/VForum/discussion', 'id'=>$discussion->id)); ?>
                        <?php echo VForumHtmlHelper::pager($discussion->commentsTotal, $commentsOnPge, array('/VForum/VForum/discussion', 'id'=>$discussion->id), false, 3 ); ?>
                        <?php
                          if ($comments[$discussion->id])
                          {
                              $comment = $comments[$discussion->id];
                              echo '<small>последний комментарий: '.DateUtils::_date($comment->date).' от '.VHtml::userLink($comment->user).'</small>';
                          }
                        ?>
                      </div>
                      <div class="cell-author"><?php echo VHtml::userLink($discussion->user); ?></div>
                      <div class="cell-length"><?php echo $discussion->commentsTotal; ?></div>
                    </li>
                    <?php
                }
                ?>
                </ul>
                <?php
            }
            ?>

            <div class="f-bottom-bar">
                <?php echo $pager; ?>
            </div>
