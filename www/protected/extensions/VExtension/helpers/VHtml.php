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
        $alt = isset($htmlOptions['alt']) ? $htmlOptions['alt'] : '';

		return CHtml::image($thumb->getSiteUrl(),$alt,$htmlOptions);
    }

    public static function thumbSrc ($src, $sizes = false, $scaleMethod = false)
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

        return $thumb->getSiteUrl();
    }

	public static function sumInterval ($min_entity, $max_entity = null, $currency = null, $format=array(
        'from'  => 'от ',
        'to'    => 'до ',
        'sum_prefix' => '<span style="white-space: nowrap;">',
        'sum_postfix' => '</span>',
        'prefix' => '<span>',
        'postfix' => '</span>',
        'middle' => ' &mdash; ',
        'currency_words' => array ('i' => array('рубль', 'рубля', 'рублей'),  'r' => array('рубля', 'рублей', 'рублей')),
    )){
		$types =$format['currency_words']['r'];
		$types_i = $format['currency_words']['i'];

		$t_cur_min = '';
		$t_cur_min_i = '';
		$t_cur_max = '';
		$t_cur_max_i = '';
		if ($currency && $min_entity)
		{
			$t = $types[$currency];
			$t_cur_min = ' '.self::plural ($min_entity, $t);
			$t_i = $types_i[$currency];
			$t_cur_min_i = ' '.self::plural ($min_entity, $t_i);
		}
		if ($currency && $max_entity)
		{
			$t = $types[$currency];
			$t_cur_max = ' '.self::plural ($max_entity, $t);
			$t_i = $types_i[$currency];
			$t_cur_max_i = ' '.self::plural ($max_entity, $t_i);
		}

		if ($min_entity==$max_entity && $min_entity && $max_entity)
		{
			$res = $format['sum_prefix'].number_format($min_entity,0,',',' ').$format['sum_postfix'].' '.$t_cur_min_i;
		}
		elseif ($max_entity && $min_entity)
		{
			$res = $format['from'].$format['sum_prefix'].number_format($min_entity,0,',',' ').$format['sum_postfix'].$format['middle'].$format['to'].$format['sum_prefix'].number_format($max_entity,0,',',' ').$format['sum_postfix'].$t_cur_max;
		}
		elseif ($max_entity)
		{
			$res = $format['to'].$format['sum_prefix'].number_format($max_entity,0,',',' ').$format['sum_postfix'].$t_cur_max;
		}
		elseif ($min_entity)
		{
			$res = $format['from'].$format['sum_prefix'].number_format($min_entity,0,',',' ').$format['sum_postfix'].$t_cur_min;
		} else {
		    return '';
        }

        return $format['prefix'] . $res . $format['postfix'];
	}

	public static function sum ($sum = null, $currency = null, $format = array()){
        $default=array(
            'sum_prefix' => '<span style="white-space: nowrap;">',
            'sum_postfix' => '</span>',
            'prefix' => '<span>',
            'postfix' => '</span>',
            'currency_format' => array('рубль', 'рубля', 'рублей')
        );
        $format = array_merge($default, $format);

		$types = $format['currency_format'];

        if (!$sum)
            return '';

        $t_cur = '';
		if ($currency)
		{
			$t_cur = ' '.self::plural ($sum, $types);
		}
		$res = $format['sum_prefix'].number_format($sum,0,',',' ').$format['sum_postfix'].$t_cur;

        return $format['prefix'] . $res . $format['postfix'];
	}

    public static function formattedSum ($sum = null, $currency = null){
        $format = array(
        'sum_prefix' => '',
        'sum_postfix' => '',
        'prefix' => '',
        'postfix' => '',
        'currency_format' => array('рубль', 'рубля', 'рублей')
        );

        return self::sum($sum, $currency, $format);
    }

    public static function plural($n, $format)
    {
        $c1 = $format[0];
        $c2 = $format[1];
        if(!isset($format[2]))
            $c3 = $c2;
        else
            $c3 = $format[2];

        return $n % 10 == 1 && $n % 100 !=11 ? $c1 : ($n % 10 >= 2 && $n % 10 <=4 && ($n % 100 < 10 || $n % 100 >= 20) ? $c2 : $c3);
    }

    public function userLink ($user, $content = false, $options = array(), $defaultClass='user-link')
    {
        $originService = Yii::app()->vauth->originService;
        $href = $user->getLink();
        if (!$content && $defaultClass)
            $options['class'] = isset($options['class']) ? $options['class'].' '.$defaultClass : $defaultClass;
        $content = $content === false ? $user->username : $content;
        if ($user->service != $originService)
        {
            $options['target'] = '_blank';
            $options['class'] = isset($options['class']) ? $options['class'].' '.$user->service : $user->service;
        }
        return CHtml::link($content, $href, $options);
    }

    static public function shrink($text, $length, $tail = '…')
    {
        if( mb_strlen($text) > $length )
        {
            $whiteSpacePosition = mb_strpos($text, ' ', $length) - 1;

            if( $whiteSpacePosition > 0 )
            {
                $chars = count_chars(mb_substr($text, 0, ($whiteSpacePosition + 1)), 1);
                if ( isset($chars[ord('<')]) && isset($chars[ord('>')]) && ($chars[ord('<')] > $chars[ord('>')]) )
                {
                    $whiteSpacePosition = mb_strpos($text, '>', $whiteSpacePosition) - 1;
                }
                $text = mb_substr($text, 0, ($whiteSpacePosition + 1));
            }

            // close unclosed html tags
            if( preg_match_all('|<([a-zA-Z]+)|', $text, $aBuffer) )
            {
                if( !empty($aBuffer[1]) )
                {
                    preg_match_all('|</([a-zA-Z]+)>|', $text, $aBuffer2);

                    if( count($aBuffer[1]) != count($aBuffer2[1]) )
                    {
                        foreach( $aBuffer[1] as $index => $tag )
                        {
                            if( empty($aBuffer2[1][$index]) || $aBuffer2[1][$index] != $tag)
                            {
                                $text .= '</'.$tag.'>';
                            }
                        }
                    }
                }
            }

            $text .= $tail;
        }

        return $text;
    }



}
?>