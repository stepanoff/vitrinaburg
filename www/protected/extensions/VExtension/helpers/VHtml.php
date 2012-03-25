<?php
class VHtml
{
    const SCALE_EXACT = 0;
	const SCALE_SMALLER_SIDE = 10;
    const SCALE_WIDTH = 20;
    const SCALE_HEIGHT = 30;

    public static function thumb ($src, $sizes = false, $scaleMethod = false, $htmlOptions = array())
    {

        $sizes = is_array($sizes) ? $sizes : array();
        $w = isset($sizes[0]) && $sizes[0] ? $sizes[0] : false;
        $h = isset($sizes[1]) && $sizes[1] ? $sizes[1] : false;
        $scaleMethod = $scaleMethod !== false ? $scaleMethod : self::SCALE_EXACT;

        $image = Yii::app()->fileManager->getImage($src);

        if (!$image)
            return '';

        $thumb = $image->getThumb($w, $h, $scaleMethod);

        if (!$thumb)
            return '';

        $htmlOptions = is_array($htmlOptions) ? $htmlOptions : array();
        $htmlOptions['width'] = $thumb->getWidth();
        $htmlOptions['height'] = $thumb->getHeight();

		return CHtml::image($thumb->getSiteUrl(),'',$htmlOptions);
    }
}
?>