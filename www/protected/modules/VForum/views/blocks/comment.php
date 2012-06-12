                    <div class="message clearfix" itemId="<?php echo $comment->id; ?>">
                      <a name="comment<?php echo $comment->id; ?>"></a>
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
                          </div>
                        </div>
                      </div>
                    </div>