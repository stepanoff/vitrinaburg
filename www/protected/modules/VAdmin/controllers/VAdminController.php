<?phpclass VAdminController extends Controller{	public $onPageCount = 20;		public $model;		public $title; 		public $_sessionKey = null;		public $enableAjaxValidation = false;		public $_session = null;		public $_layoutFilters;		private $__session = null;    private $_adminModule = null;		public $formInputLayout = '<div class="row">{label}<div class="value">{input}{hint}</div><div class="error">{error}</div></div>';	public function filters()	{		return array( 			'accessControl'		);	}    public static function getAdminRoles ()    {        return array(            'admin' => 'admin',            'admin_master' => 'admin_master',        );    }    public function getAdminModule () {        if ($this->_adminModule === null) {            $this->_adminModule = Yii::app()->getModule('VAdmin');        }        return $this->_adminModule;    }		public function accessRules()	{        $c = new CAccessControlFilter;		return array(			array('allow',				'roles'=>array_keys(self::getAdminRoles()),			),			array('deny',				'users' => array('*'),			),		);	}		public function init()	{        $this->layout = $this->getAdminModule()->getViewsAlias('layouts.base');		// Инициируем сессию		$this->_sessionKey = $this->getAdminModule()->getId().'_'.$this->id;		$this->__session = Yii::app()->getSession();				$this->_session = Yii::app()->getSession()->itemAt($this->_sessionKey);				// Берем все по фильтрам		if (isset($_REQUEST['filter']['reset']))		{			$this->removeLayoutFilter();			$this->redirect(array('list'));		}		else		{			if (isset($this->_session['filter']) && is_array($this->_session['filter']))				$this->addLayoutFilter($this->_session['filter']);							if (!Yii::app()->request->isAjaxRequest)				$this->addLayoutFilter(array('page' => isset($_REQUEST['page'])?$_REQUEST['page']:null));							if (isset($_REQUEST['filter']) && is_array($_REQUEST['filter']))				$this->addLayoutFilter($_REQUEST['filter']);						}        $this->registerAssets();				}		public function __destruct()	{		$this->_session['filter'] = $this->_layoutFilters;		$this->__session[$this->_sessionKey] = $this->_session;	}		public function templates()	{		$res = array(            'list' => $this->getAdminModule()->getViewsAlias('admin.list'),            'edit' => $this->getAdminModule()->getViewsAlias('admin.edit'),        );        return $res;	}		public function addLayoutFilter($filters)	{		foreach ($filters as $_k => $_v)			$this->_layoutFilters[$_k] = $_v;	}		public function removeLayoutFilter($filters = array())	{		if (empty($filters))		{			$_SESSION[$this->_sessionKey]['filter'] = array();			$this->_layoutFilters = array();		}		else		{			foreach ($filters as $_k => $_v)			{				if (isset($this->_layoutFilters[$_k]))					unset($this->_layoutFilters[$_k]);				if (isset($_SESSION[$this->_sessionKey]['filter'][$_k]))					unset($_SESSION[$this->_sessionKey]['filter'][$_k]);			}		}	}		public function appendLayoutFilters($model)	{		return $model;	}			public function layoutsFilter()	{		return array();	}		protected function editFormElements ($model)	{		return array();	}			public function actionList()	{        $model = $this->appendLayoutFilters(CActiveRecord::model($this->model));        $dataProvider = new CActiveDataProvider($this->model, array(            'criteria' => $model->getDbCriteria()->toArray(),            'pagination'=>array(                'pageSize' => $this->onPageCount,            ),        ));        $columns = $this->getListColumns();		$tplData = array('dataProvider' => $dataProvider, 'columns' => $columns)+$this->additionalListData($dataProvider);        $templates = $this->templates();		if (Yii::app()->request->isAjaxRequest)		{            $listPart = $this->renderPartial($templates['list'], $tplData, true);			$this->setAjaxData('list', $listPart);		}		else            $this->render($templates['list'], $tplData);	}		public function actionIndex ()	{		return $this->actionList();	}		public function additionalListData($list = array())	{		return array();	}		protected function buildModel ($id = false)	{		if ($id)			return CActiveRecord::model($this->model)->findByPk($_GET['id']);		else			return new $this->model;	}		public function actionEdit($render = true)	{		if (isset($_GET['id']))		{			if (!$model = $this->buildModel($_GET['id']))				$this->redirect(array('list'));			$isNewRecord = false;		}		else		{			$model = $this->buildModel();			$isNewRecord = true;		}				$model->setScenario('admin');				$model = $this->beforeEdit ($model, $isNewRecord);				if (isset($_POST[$this->model]))		{			$model->setAttributes($_POST[$this->model]);			if ($model->validate())			{				if ($this->enableAjaxValidation && Yii::app()->request->isAjaxRequest)				{					$this->setAjaxData('success', true);				}				else 				{					if ($model = $this->save($model))					{						if ($isNewRecord)							$this->afterAdd($model);						else							$this->afterEdit($model);					}											if ($render)						$this->redirect(array('list'));					else						return $model;				}			}			elseif ($this->enableAjaxValidation && Yii::app()->request->isAjaxRequest)			{				$result = array();				foreach($model->getErrors() as $attribute=>$errors)		            $result[CHtml::activeId($model,$attribute)]=$errors;				$elements = $this->editFormElements($model);				$this->setAjaxData('errors', $result);			}			elseif (!$render)				return $model;		}					if ($render && !($this->enableAjaxValidation && Yii::app()->request->isAjaxRequest) )		{			$additionalEditData = $this->getAdditionalEditData($model, $isNewRecord);						// использовать построитель форм			$elements = $this->editFormElements($model);			            $templates = $this->templates();			if (count($elements))			{				$form = new CForm ($elements);				$form->model = $model;				$this->render($templates['edit'], array_merge(array('form' => $form, 'model'=>$model), $additionalEditData));			}			else				$this->render($templates['edit'], array_merge(array('model' => $model), $additionalEditData));		}		else 			return $model;	}	public function getAdditionalEditData($model, $isNewRecord)	{		return array();	}		public function actionDelete()	{		if (isset($_GET['id']) && $model = CActiveRecord::model($this->model)->findByPk($_GET['id']))		{			if ($this->beforeDelete($model))			{				if (isset($_REQUEST['confirm']))				{										if ($model = $this->delete($model))					{						$this->afterDelete($model);					}											$this->actionList();				}				else				{					Yii::app()->informer->confirm(						array('title' => 'Удалить объект?'),						CHtml::normalizeUrl(array('delete', 'id' => $_GET['id']))					);				}			}		}		else			$this->actionList();	}    public function actionShow () {        if (isset($_GET['id']) && $model = CActiveRecord::model($this->model)->findByPk($_GET['id']))        {            $model->setScenario('admin');            $model->visibleOn();            $this->actionList();        }        else            $this->actionList();    }	    public function actionHide () {        if (isset($_GET['id']) && $model = CActiveRecord::model($this->model)->findByPk($_GET['id']))        {            $model->setScenario('admin');            $model->visibleOff();            $this->actionList();        }        else            $this->actionList();    }	protected function save ($model)	{		$model->save();		return $model;	}		protected function delete ($model)	{		$model->delete();		return $model;	}		public function afterAdd($model) { }			public function afterEdit($model) { }		public function afterDelete($model) { }		public function beforeDelete($model) { return true; }		public function beforeEdit ($model, $isNewRecord)	{		return $model;	}    protected function registerAssets () {        $cs = Yii::app()->clientScript;        $url = $this->getAdminModule()->getAssetsUrl();		$cs->registerCssFile($url.'/css/bootstrap.css');        $cs->registerCssFile($url.'/css/bootstrap-responsive.css');        $cs->registerCssFile($url.'/css/docs.css');        $cs->registerScriptFile($url.'/js/bootstrap.min.js', CClientScript::POS_HEAD);    }    public function getListColumns() {        return array(            'name',            array(                'class'=>'VAdminButtonWidget',            ),        );    }}