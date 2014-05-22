<?php
/**
 * Файл с классом модели InfoMessage
 *
 * @author stepanoff
 * @since 17.12.2009
 */

/**
 * Модель контент-блоков
 *
 * @author stepanoff
 * @version 1.0
 */
class VContentBlock extends CActiveRecord
{
    const NAME_DEFAULT = 'default';

    public $nList = array();

    public $defaultValue;

    protected $defaultCb = null;
    protected $namespaceList = null;
    /**
	 * Returns the static model of the specified AR class.
	 * The model returned is a static instance of the AR class.
	 * It is provided for invoking class-level methods (something similar to static class methods.)
     * @param string active record class name.
	 * @return CActiveRecord active record model instance.
	 */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Имя таблицы, к которой привязан данный класс
     * @return string Имя таблицы в базе данных
     */
    public function tableName()
    {
        return 'contentblocks';
    }

	public function rules()
	{
		return array(
            array('name', 'required', 'message' => 'Укажите название'),
            array('namespace', 'required', 'message' => 'Укажите область'),
			array('name, namespace, description, content, nList', 'safe'),
		);
	}

    public function scopes()
    {
        $res = parent::scopes();
        return array_merge($res, array(
        ));
    }

    public function relations()
    {
        $res = parent::relations();
        return array_merge($res, array(
        ));
    }

    public function byName($name)
    {
        $alias = $this->getTableAlias();
        $this->getDbCriteria()->mergeWith(array(
            'condition' => $alias.'.name = :name',
            'params' => array(':name' => $name),
        ));
        return $this;
    }

    public function getNamespaceList ()
    {
        if ($this->namespaceList === null) {
            $this->namespaceList = array();
            $criteria = new CDbCriteria(array(
                'select' => 'id, name, description',
            ));
            $list = VContentBlock::model()->byNameSpace($this->namespace)->findAll($criteria);
            foreach ($list as $item) {
                $this->namespaceList[$item->id] = $item->attributes;
            }
        }
        return $this->namespaceList;
    }

    public function getDefaultCb ()
    {
        if ($this->defaultCb === null) {
            if ($this->name != self::NAME_DEFAULT) {
                $this->defaultCb = VContentBlock::model()->byNameSpace($this->namespace)->byName(self::NAME_DEFAULT)->find();
            } else {
                $this->defaultCb = false;
            }
        }
        return $this->defaultCb;
    }

    public function byNamespace($name)
    {
        $alias = $this->getTableAlias();
        $this->getDbCriteria()->mergeWith(array(
            'condition' => $alias.'.namespace = :namespace',
            'params' => array(':namespace' => $name),
        ));
        return $this;
    }

    public function attributeLabels()
    {
        $res = parent::attributeLabels();
        return array_merge($res, array(
            'name' => 'Название контент-блока',
            'content' => 'Содержание',
        ));
    }

    protected function beforeValidate()
    {
        return parent::beforeValidate();
    }

    protected function beforeSave()
    {
        return parent::beforeSave();
    }

    protected function afterFind()
    {
        return parent::afterFind();
    }

    protected function afterSave()
    {
        return parent::afterSave();
    }

    protected function afterDelete()
    {
        return parent::afterDelete();
    }

    public function getFormElements ()
    {
        /*
        $list = $this->getNamespaceList();
        $data = array();
        foreach ($list as $id => $item) {
            $data[$id] = $item['description'];
        }
        */

        $res = array (
            'elements' => array (
                'id' => array (
                    'type' => 'hidden',
                ),
                /*
                $this->namespace,
                'nList' => array (
                    'type' => 'dropdownlist',
                    'items' => $data,
                ),
                */
            ),
            'buttons' => array (
                'send'		 => array(
                    'type' => 'submit',
                    'label'=> 'Сохранить',
                ),
            ),
        );

        $defaultCb = $this->getDefaultCb();
        if ($defaultCb) {
            $res['elements'][] = '<h5>Значение по умолчанию</h5>';
            $res['elements'][] = '<p>'.CHtml::encode($defaultCb->content).'</p>';
            $res['elements'][] = '<p>'.$defaultCb->content.'</p>';
            $res['elements'][] = '<hr>';
        }
        $res['elements']['content'] = array('type' => 'textarea');
        $res['elements'][] = '<p>'.$this->content.'</p>';

        return $res;
    }

}
?>
