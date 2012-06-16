    <div id="inner-page">
      <div class="base-width clearfix">
        <div class="main-col">
          <div class="forum" id="forum">
            <h1><?php echo $discussion->title; ?></h1>
                  <?php
                  $pages = VForumHtmlHelper::pager($discussion->commentsTotal, $commentsOnPge, array('/VForum/VForum/discussion', 'id'=>$discussion->id), $page, 10, 'Страницы' );
                  if ($pages)
                  {
                      echo '<div class="t-top-bar">';
                      echo $pages;
                      echo '</div>';
                  }
                  ?>
            <div class="theme">
                <div class="comments-container">
                <?php
                foreach ($comments as $comment)
                {
                    $view = $this->getModule()->getViewsAlias('blocks.comment');
                    $this->renderPartial($view, array('comment' => $comment));
                }
                ?>
                </div>

                    <div class="comment-form">
                        <?php $this->widget('application.extensions.VExtension.widgets.VFormBuilderWidget', array('model'=>$commentForm, 'elements'=>$commentForm->getFormElements())); ?>
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

<script type="text/javascript">
    $(document).ready(function(){
        $("#forum").vforum_comments({
            'userId' : <?php echo Yii::app()->user->id ? Yii::app()->user->id : 'false'; ?>,
            'onAuthorize' : function () {Vauth.launch(); return false;},
        });
    });
</script>