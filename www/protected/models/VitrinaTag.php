<?php
class VitrinaTag extends VitrinaSectionItemBase
{
	public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 'tag_tags';
    }
    
}