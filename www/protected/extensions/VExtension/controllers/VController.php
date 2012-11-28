<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
// todo: брать путь до экстеншена из модуля
if (!Yii::app()->getComponent('informer'))
{
	Yii::app()->setComponents(array(
		'informer'=>array(
			'class'=>'application.extensions.VExtension.VInformer',
		),
	));
}
class VController extends CController
{
    protected $_ajaxData = array();

    protected $_iframeAjax = false; // посылает данные ajax'ом в виде переменной json, заключенной в тег <script>. Нужен для запросов, посылаемых из iframe
    protected $_sendAjaxData = true; // отправлять или нет данные ajax'ом
    protected $_sendMessages = true;

    public function setAjaxData ($key, $data)
    {
        $this->_ajaxData[$key] = $data;
    }

    protected function afterAction($action)
    {
        if (Yii::app()->request->isAjaxRequest && $this->_sendAjaxData)
        {
            if ($this->_sendMessages)
            {
                $c = Yii::app()->getComponent('VExtension');
                $items = Yii::app()->informer->getAlerts();
                $items2 = Yii::app()->informer->getUnread();
                if (is_array($items2) && sizeof($items2))
                    $items = array_merge($items, $items2);
                if (is_array($items) && sizeof($items))
                {
                    $widget=$this->createWidget('VMessagesWidget',array(
                        'items'=>$items
                    ));
                    $res = $widget->runInternal();
                    if (Yii::app()->request->isAjaxRequest)
                        $this->setAjaxData('messages', $res);
                }
            }

            if (count($this->_ajaxData))
            {
                if ($this->_iframeAjax)
                    echo '<body><script type="text/javascript">parent.window["iframeResponse"] = '.json_encode ($this->_ajaxData).';</script></body>' ;
                else
                    echo json_encode ($this->_ajaxData);
            }
        }
        return parent::afterAction($action);
    }


}