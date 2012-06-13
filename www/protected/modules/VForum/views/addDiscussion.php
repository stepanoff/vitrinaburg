    <link rel="stylesheet" href="<?php echo Yii::app()->request->staticUrl; ?>/css/forum.css">
    <div id="inner-page">
      <div class="base-width clearfix">
        <div class="main-col">
          <div class="forum" id="forum">
            <h1>Создать новую тему</h1>
                    <div class="comment-form">
                        <form method="post" action="">

                            <?php echo CHtml::activeHiddenField($form, 'forum_category_id'); ?>
                            <div class="form-row<?php if ($form->getError('title')) {echo ' error';} ?>">
                                <label>Заголовок темы</label>
                                <div class="error-message"><?php echo $form->getError('title'); ?></div>
                                <?php echo CHtml::activeTextField($form, 'title'); ?>
                            </div>

                            <div class="form-row<?php if ($form->getError('text')) {echo ' error';} ?>">
                                <label>Комментарий</label>
                                <div class="error-message"><?php echo $form->getError('text'); ?></div>
                                <?php echo CHtml::activeTextarea($form, 'text'); ?>
                            </div>

                            <div class="form-row">
                                <input type="submit" class="input-submit gradient1" name="send" value="Создать"/>
                            </div>
                        </form>
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
  </div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#forum").vforum_comments({
            'userId' : <?php echo Yii::app()->user->id ? Yii::app()->user->id : 'false'; ?>,
            'onAuthorize' : function () {Vauth.launch(); return false;}
        });
    });
</script>