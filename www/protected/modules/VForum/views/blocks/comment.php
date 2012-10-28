                    <div class="message clearfix" itemId="<?php echo $comment->id; ?>">
                      <a name="comment<?php echo $comment->id; ?>"></a>
                      <div class="author-">
                          <?php
                            $img = '<img src="'.$comment->user->getAvatar('medium').'" alt="'.$comment->user->username.'">';
                            echo VHtml::userLink($comment->user, $img);
                          ?>
                        <?php echo VHtml::userLink($comment->user); ?>
                        <small><?php echo DateUtils::_date($comment->date); ?></small>
                      </div>
                      <div class="body-">
                        <?php echo $comment->getContent(); ?>
                        <div class="actions- clearfix">
                          <div class="links-"><!--a class="js-forum-complaint" href="#">пожаловаться модератору</a--></div>
                          <div class="buttons-">
                            <a href="#" class="js-forum-answer gradient1">Ответить</a>
                          </div>
                        </div>
                      </div>
                      <?php
                        if (Yii::app()->user->checkRoles(array(VUser::ROLE_ADMIN, VUser::ROLE_MODER))) {
                            echo '<div class="admin-actions">';
                            echo CHtml::link('удалить', array('/VForum/VForum/removeComment', 'id' => $comment->id), array('action' => 'removeComment'));
                            echo '</div>';
                        }
                      ?>
                    </div>