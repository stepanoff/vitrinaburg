<?php
class VitrinaHtmlHelper
{
	public static function formatAddress($addressObj)
	{
        $res = '';
        if ($addressObj->address)
            $res = '<span class="showMap" addr="'.$addressObj->address.'">'.$addressObj->address.'</span>';
        if ($addressObj->mallObj)
        {
            $res = '<span class="showMap" addr="'.$addressObj->mallObj->address.'">'.$addressObj->mallObj->address.'</span>, ('.CHtml::link($addressObj->mallObj->name, array('/vitrinaMall/show', 'id'=>$addressObj->mallObj->id)).')';
        }
		return $res;
	}

    public static function mapLink($addressObj)
    {
        $addr = false;
        if ($addressObj->address)
            $addr = $addressObj->address;
        if ($addressObj->mallObj)
        {
            $addr = $addressObj->mallObj->address;
        }
        if ($addr)
            return CHtml::link('на карте', array('#'), array('addr'=>$addr, 'class'=>'showMap'));
        return '';
    }

}
?>