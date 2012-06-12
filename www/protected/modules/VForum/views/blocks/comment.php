    <div id="inner-page">
      <div class="base-width clearfix">
        <div class="main-col">
          <div class="forum">
            <h1><?php echo $discussion->title; ?></h1>
                  <?php
                  $pages = VForumHtmlHelper::pager($discussion->commentsTotal, $commentsOnPge, array('/VForum/VForum/discussion', 'id'=>$discussion->id), false, 10, 'Страницы' );
                  if ($pages)
                  {
                      echo '<div class="t-top-bar">';
                      echo $pages;
                      echo '</div>';
                  }
                  ?>
            <div class="theme">

                <?php
                foreach ($comments as $comment)
                {
                    ?>
                    <div class="message clearfix">
                      <div class="author-">
                          <?php
                            $img = '<img src="'.$comment->user->getAvatar('medium').'" alt="'.$comment->user->username.'">';
                            echo CHtml::link($img, array('/vitrinaForum/user', 'id'=>$comment->user->id));
                          ?>
                        <?php echo CHtml::link($comment->user->username, array('/vitrinaForum/user', 'id'=>$comment->user->id)); ?>
                        <small><?php echo DateUtils::_date($comment->date); ?></small>
                      </div>
                      <div class="body-">
                        <?php echo $comment->getContent(); ?>
                        <div class="actions- clearfix">
                          <div class="links-"><a class="js-forum-complaint" href="#">пожаловаться модератору</a></div>
                          <div class="buttons-">
                            <a href="#" class="js-forum-answer gradient1">Ответить</a>
                            <a href="#" class="js-forum-quote gradient1">Цитировать</a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php
                }
                ?>

                    <div class="comment-form">
                        <form method="post" action="">
                            <div class="comment-form__title"></div>
                            <div class="form-row">
                                <label>Оставить комментарий</label>
                                <textarea name="<?php echo CHtml::activeName($commentForm, 'text'); ?>"></textarea>
                            </div>
                            <div class="form-row">
                                <input type="submit" name="send" value="Отправить"/>
                            </div>
                        </form>
                    </div>
            </div>

                <?php
                  if ($pages)
                  {
                      echo '<div class="t-bottom-bar">';
                      echo $pages;
                      echo '</div>';
                  }
                ?>
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
  </div>