        <div class="c-main-col">
          <div class="cmc-pad">
            <?php
                if ($sections)
                {
                    echo '<ul class="mp-rubriks">';
                    foreach ($sections as $section)
                    {
                        echo '<li class="mpr-l1">';
                        echo CHtml::link($section['name'], array('/vitrinaCollection/section/', 'sectionId'=>$section['id']), array('class'=>'mpr-l1-a red'));

                        $children = '';
                        if (isset($section['children']) && $section['children'])
                        {
                            $children = '<ul>';
                            $tmp = array();
                            $num = sizeof($section['children']);
                            $i = 1;
                            foreach ($section['children'] as $child)
                            {
                                $_t = '<li>';
                                $_t .= CHtml::link($child['name'], array('/vitrinaCollection/section/', 'sectionId'=>$child['id']), array());
                                if (isset($photosInSections[$child['id']]) && $photosInSections[$child['id']])
                                    $_t .= '<small>+'.$photosInSections[$child['id']].'</small>';
                                if ($i != $num)
                                    $_t .= ',';
                                $_t .= '</li>';

                                $tmp[] = $_t;
                                $i++;
                            }
                            $children .= implode('', $tmp).'</ul>';
                        }
                        echo $children;
                        echo '</li>';
                    }
                    echo '</ul>';
                }
            ?>
            <div class="mp-section">
                <?php
                if ($photos)
                {
                    $header = '<h2>Новые коллекции';
                    if ($todayPhotos)
                        $header .= ' <small>Сегодня +'.$todayPhotos.'</small>';
                    $header .= '</h2>';
                    echo $header;

                    echo '<ul class="mp-collections">';
                    foreach ($photos as $photo)
                    {
                        echo '<li>';
                        $imageTag = VHtml::thumb($photo->src, array(110, 150), VHtml::SCALE_SMALLER_SIDE, array('title'=>$photo->name, 'alt'=>$photo->name));
                        echo CHtml::link($imageTag, array('/vitrinaCollection/show/', 'collectionId'=>$photo->collection->id,'photoId'=>$photo->id), array('class'=>'mp-collections-photo'));
                        echo CHtml::link($photo->collection->name, array('/vitrinaCollection/show/', 'collectionId'=>$photo->collection->id,'photoId'=>$photo->id), array());
                        echo '</li>';
                    }
                    echo '</ul>';

                }
                ?>
            </div>
            <div class="mp-section">
            <?php
                if (0 && $actions)
                {
                    $header = '<h2>Акции и скидки';
                    if ($todayActions)
                        $header .= ' <small>Сегодня +'.$todayActions.'</small>';
                    $header .= '</h2>';
                    echo $header;

                    echo '<ul class="mp-actions">';
                    foreach ($actions as $action)
                    {
                        echo '<li>';
                        echo '<div class="mpa-logo">';
                        $imageTag = VHtml::thumb($action->img, array(100, 50), VHtml::SCALE_EXACT, array('title'=>$action->title, 'alt'=>$action->title));
                        echo CHtml::link($imageTag, array('/vitrinaAction/action/', 'id'=>$action->id), array());
                        echo '</div>';
                        echo CHtml::link($action->title, array('/vitrinaAction/action/', 'id'=>$action->id), array());
                        echo '<small>'.DateUtils::_date($action->date).'</small>';
                        echo '</li>';
                    }
                    echo '</ul>';
                }
            ?>
            </div>
            <div class="mp-bi-section clearfix">
              <div class="mpbs-col-1">
              <?php echo CHtml::link('Создать свой образ', array('/vitrinaWidget/create/'), array('class'=>'fr red-button gradient1 mr15')); ?>
                <h2 class="mp-red">Новые образы</h2>
                <?php
                if ($sets)
                {
                    echo '<div class="mp-images clearfix">';
                    $i = 0;
                    foreach ($sets as $set)
                    {
                        $w = $i == 0 || $i == 5 ? 157 : 77;
                        $h = $i == 0 || $i == 5 ? 157 : 77;
                        $imgTag = VHtml::thumb($set->image, array($w, $h), VHtml::SCALE_EXACT, array('title'=>$set->name, 'alt'=>$set->name));
                        echo CHtml::link($imgTag, array('/vitrinaWidget/show/', 'id'=>$set->id), array());
                        $i++;
                    }
                    echo '</div>';
                }
                ?>
                <small>Все образы формируются пользователями из каталога товаров</small>
              </div>
              <div class="mpbs-col-2">
              <?php
              if ($discussions)
              {
                  echo '<h2 class="mp-red"><a href="'.CHtml::normalizeUrl(array('/VForum/VForum/index')).'">Последние ответы на форуме</a></h2>';
                  echo '<ul class="mp-last-forum">';
                  foreach ($discussions as $discussion)
                  {
                      echo '<li>';
                      echo CHtml::link($discussion->title, array('/VForum/VForum/discussion', 'id'=>$discussion->id));
                      $comment = isset($comments[$discussion->id]) ? $comments[$discussion->id] : false;
                      if ($comment)
                        echo '<small>последний комментарий: '.DateUtils::_date($comment->date).' от '.VHtml::userLink($comment->user, false, array('class'=>'red')).'</small>';
                      echo '</li>';
                  }
                  echo '</ul>';
              }
              ?>
              </div>
            </div>
          </div>
          <div id="pageDescriptionFooter"></div>
        </div>
        <div class="c-right-col">
          <div class="right-banner">
              <?php $this->renderPartial('application.views.blocks.banner_right', array()); ?>
          </div>
            <?php
            if ($articles)
            {
                echo '<h2 class="mp-red">Статьи</h2>';
                echo '<ul class="mp-articles">';
                foreach ($articles as $article)
                {
                    echo '<li class="clearfix">';
                    $imgTag = VHtml::thumb($article->img, array(100, false), VHtml::SCALE_WIDTH, array('title'=>$article->title, 'alt'=>$article->title));
                    echo CHtml::link($imgTag, array('/vitrinaArticle/show/', 'id'=>$article->id), array());
                    echo '<p>';
                    echo CHtml::link($article->title, array('/vitrinaArticle/show/', 'id'=>$article->id), array());
                    echo ' <small>'.DateUtils::_date($article->date).'</small>';
                    echo '</p>';
                    echo '</li>';
                }
                echo '</ul>';
            }
            ?>
          <div class="vk">
              <?php $this->renderPartial('application.views.blocks.social', array()); ?>
          </div>
        </div>
