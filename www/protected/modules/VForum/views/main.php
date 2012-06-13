    <div id="inner-page">
      <div class="base-width clearfix">
        <div class="main-col">
          <div class="forum">
            <h1>Общение на <?php echo Yii::app()->params['siteName']; ?></h1>
            <div class="f-top-bar">
              <?php $pager = VForumHtmlHelper::pager($total, $discussionOnPage, array('/VForum/VForum/index'), $page, 10, 'Всего: '.$total.', показано с '.($offset+1).' по '.($offset+count($discussions)) ); ?>
              <?php echo $pager; ?>
              <a href="<?php echo CHtml::normalizeUrl(array('/VForum/VForum/addDiscussion')); ?>" class="f-button gradient1">Создать тему</a>
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
          </div>
        </div>
        <div class="left-col">
          <div class="left-new clearfix">
            <h2>Новые коллекции</h2>
            <ul class="clearfix">
              <li>
                <a href="#">
                  <img src="images/must_be_deleted/left_new1.jpg" width="100" height="140" alt="">
                  <span>5 999 руб.</span>
                </a>
              </li>
              <li>
                <a href="#">
                  <img src="images/must_be_deleted/left_new2.jpg" width="100" height="140" alt="">
                  <span>5 999 руб.</span>
                </a>
              </li>
              <li>
                <a href="#">
                  <img src="images/must_be_deleted/left_new3.jpg" width="100" height="140" alt="">
                  <span>1 209 руб.</span>
                </a>
              </li>
              <li>
                <a href="#">
                  <img src="images/must_be_deleted/left_new4.jpg" width="100" height="140" alt="">
                  <span>4 500 руб.</span>
                </a>
              </li>
            </ul>
          </div>
          <div class="left-banner"><img src="images/must_be_deleted/right_banner.jpg" width="240" height="400" alt=""></div>
        </div>
      </div>
    </div>