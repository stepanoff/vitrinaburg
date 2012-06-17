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
                        <a name="message"></a>
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

<script type="text/javascript">
    $(document).ready(function(){
        $("#forum").vforum_comments({
            'userId' : <?php echo Yii::app()->user->id ? Yii::app()->user->id : 'false'; ?>,
            'onAuthorize' : function () {Vauth.launch(); return false;},
        });
    });
</script>