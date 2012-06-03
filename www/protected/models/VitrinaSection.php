<?php
class VitrinaSection extends VTreeItemBase
{
	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'tag_rubrics';
    }
    
}