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
			array('name, content', 'safe'),
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

}
?>
