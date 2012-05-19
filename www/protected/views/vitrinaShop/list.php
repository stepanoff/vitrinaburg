        <div class="main-col">
            <?php
                $this->widget('application.widgets.VitrinaTopLogosWidget', array(
                    'max' => 6,
                    'alwaysMax' => true,
                ));
            ?>
            <div class="shopList">
          <?php
          if ($items)
          {
              ?>
              <table cellpadding="0" cellspacing="0" border="0" width="100%" class="objlist">
                      <?php
                      foreach ($items as $item)
                      {
                          $keys[mb_strtoupper(mb_substr($item->name, 0, 1, 'UTF8'), 'UTF8')]['rows'][] = $item;
                      }
                      $col_num = 3;
                      $rows_in_col = ceil(sizeof($items)/$col_num);
                      $col = 0;
                      $rows = array();
                      $cur_key = false;
                      $cur_count = 0;
                      foreach ($keys as $k=>$row)
                      {
                          $rows[$col][$k] = $row;
                          $cur_count += sizeof($row['rows']);
                          if ($cur_count>=$rows_in_col)
                          {
                              $col++;
                              $cur_count = 0;
                          }
                      }

              ?>
                  <tr>
              <?php
                      foreach ($rows as $i=>$_keys)
                      {
                      ?>
                      <td width="<?php echo floor(100/$col_num);?>%">
                      <?php
                      foreach ($_keys as $k=>$_row)
                      {
                          ?>
                          <div class="big"><?=$k?></div>
                          <ul class="col">
                          <?php
                          foreach ($_row['rows'] as $item )
                          {
                          ?>
                          <li>
                              <?php echo CHtml::link($item->name, array('/vitrinaShop/show', 'id'=>$item->id), array('class'=>'')); ?>
                          </li>
                          <?php
                          }
                          ?>
                          </ul>
                          <?php
                      }
                      ?>
                      </td>
                      <?php
                      }
                      ?>
                  </tr>
              </table>
              <?php
          }
          ?>
        </div>
            <div id="pageDescriptionFooter"></div>
        </div>
        <div class="left-col">
            <?php
                $this->widget('application.widgets.VitrinaSectionsTreeWidget', array(
                    'structure' => $mallsStructure,
                    'selectedSections' => $selectedMalls,
                    'route' => array('/vitrinaShop/index'),
                    'routeIndex' => 'mallId',
                    'counters' => $counters,
                ));
            ?>
          <div class="vk">
              <?php $this->renderPartial('application.views.blocks.social', array()); ?>
          </div>
        </div>
