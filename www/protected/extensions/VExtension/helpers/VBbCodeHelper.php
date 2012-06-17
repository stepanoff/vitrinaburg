<?php
class VBbCodeHelper
{
	public static $regexp = '%\[([^]=\s]+?)(?:(=|\s)([^]]+?))?\](.*?)\[/\1\]%siu';
	public static $regexpSmile = '%\[([^]=\s]+?)\]%iu';

	public static $_smiles = null;

	const INNER_TEXT_LENGTH = 32;

	public static function parse($text, $plaintext = false)
	{
		while(preg_match_all(self::$regexp, $text, $matches)) {
			foreach ($matches[0] as $key => $match) {
				list($tag, $pre_param, $param, $innertext) = array($matches[1][$key], $matches[2][$key], $matches[3][$key], $matches[4][$key]);

				$replacement = '';

				switch ($tag) {
					case 'b':
						$replacement = "<strong>" . $innertext . "</strong>";
						break;
					case 'i':
						$replacement = "<em>" . $innertext . "</em>";
						break;
					case 's':
						$replacement = "<s>" . $innertext . "</s>";
						break;
					case 'quote':
						$replacement = "<blockquote>" . $innertext . "</blockquote>" . ($param ? "<cite>" . $param . "</cite>" : '');
						break;
					case 'url':
						$href = ($param ? $param : $innertext);
						if (!$plaintext)
							$href = VLinkHelper::redirectParser($href);
						$innertext = VHtml::shrink($innertext, self::INNER_TEXT_LENGTH);
						$replacement = '<a href="' . $href . '" target="_blank">' . $innertext . '</a>';
						break;
                    /*
					case 'img':
						$attr = '';
						preg_match_all('/(width|height)=(?:\'|")?([0-9]+)/i', $param, $_matches);
						foreach ($_matches[0] as $_key => $_match) {
							list($dimension, $size) = array($_matches[1][$_key], $_matches[2][$_key]);
							$attr.= ' ' . $dimension . '="' . $size . '"';
						}
//						$innertext = preg_replace('/^\s*javascript/iu', '', $innertext);

						$replacement = '<img src="' . $innertext . '"' . $attr . ' />';
						break;
					case 'left':
						$replacement = '<div style="text-align:left">' . $innertext . '</div>';
						break;
					case 'right':
						$replacement = '<div style="text-align:right">' . $innertext . '</div>';
						break;
					case 'center':
						$replacement = '<div style="text-align:center">' . $innertext . '</div>';
						break;

					case 'user':
						if (!$plaintext) {
							$divId = "comment-object-" . md5(microtime() . rand(0, 9999999));
							$replacement = '<div class="' . $divId . '" style="display:inline;"></div><script src="http://' . Yii::app()->params['outerHostName'] . '/new66_comments_gate.php?object_type=' . $tag . '&object_id=' . $innertext . '&div_id=' . $divId . '"></script>';
						} else {
							$replacement = '<strong>'.$innertext.'</strong>';
						}
						break;
					case 'photo':
						if (!$plaintext) {
							$divId = "comment-object-" . md5(microtime() . rand(0, 9999999));
							$replacement = '<div class="' . $divId . '"></div><script src="http://' . Yii::app()->params['outerHostName'] . '/new66_comments_gate.php?object_type=' . $tag . '&object_id=' . $innertext . '&div_id=' . $divId . '"></script>';
						}
						break;
					case 'photoalbum':
						if (!$plaintext) {
							$divId = "comment-object-" . md5(microtime() . rand(0, 9999999));
							$replacement = '<div class="' . $divId . '"></div><script src="http://' . Yii::app()->params['outerHostName'] . '/new66_comments_gate.php?object_type=' . $tag . '&object_id=' . $innertext . '&div_id=' . $divId . '"></script>';
						}
						break;
					case 'video':
						if (!$plaintext) {
							$divId = "comment-object-" . md5(microtime() . rand(0, 9999999));
							$replacement = '<div class="' . $divId . '"></div><script src="http://' . Yii::app()->params['outerHostName'] . '/new66_comments_gate.php?object_type=' . $tag . '&object_id=' . $innertext . '&div_id=' . $divId . '"></script>';
						}
						break;
					case 'audio':
						if (!$plaintext) {
							$divId = "comment-object-" . md5(microtime() . rand(0, 9999999));
							$replacement = '<div class="' . $divId . '"></div><script src="http://' . Yii::app()->params['outerHostName'] . '/new66_comments_gate.php?object_type=' . $tag . '&object_id=' . $innertext . '&div_id=' . $divId . '"></script>';
						}
						break;
                    */
					default:
						$replacement = '[' . $tag . $pre_param . $param . ']' . $innertext . '[/ ' . $tag . ']';
				}

				$text = str_replace($match, $replacement, $text);
			}

		}

        /*
		$smiles = self::getSmiles();
		while(preg_match_all(self::$regexpSmile, $text, $matches)) {
			foreach ($matches[0] as $key => $match) {
				list($tag) = array($matches[1][$key]);
				$tagFull = '['.$tag.']';
				if (isset($smiles[$tagFull]))
				{
					$replacement = '<img src="'.$smiles[$tagFull]->getImageUrl().'" alt="'.$tag.'" title="'.$tag.'" />';
					$text = str_replace($match, $replacement, $text);
				}
				else
					$replacement = '';
				$text = str_replace($match, $replacement, $text);
			}
		}
        */
		return $text;
	}

	public static function parseForEmail($text)
	{
		return self::parse($text, true);
	}


    public static function parseLinksToBB($string)
	{
		// сохраняем bb коды, чтобы не модифицировались при автоматической проставлении ссылок
		$savedBBcodesRegex = '%\[(url|img)[^]]*\].*?\[/\1\]%';
		preg_match_all($savedBBcodesRegex, $string, $result, PREG_PATTERN_ORDER);
		$savedBBcodes = array();
		foreach($result[0] as $bbcode) {
			$savedBBcodes[md5($bbcode)] = $bbcode;
			$string = str_replace($bbcode, '[['.md5($bbcode).']]', $string);
		}

		$result = VLinkHelper::detectUrl($string);

		if (isset($result[0]) && sizeof($result[0])){
			foreach($result[0] as $itemReplace){

                // обрезаем url до коротенького вида
                $cutUrl = strlen($itemReplace) <= 127 ? $itemReplace : substr( $itemReplace, 0, 127 )."...";

				$bbcode = '[url='.$itemReplace.']'.$cutUrl.'[/url]';
				$savedBBcodes[md5($bbcode)] = $bbcode;
				$string = self::str_replace_once($itemReplace, '[['.md5($bbcode).']]', $string);
			}
		}

		// возвращаем сохраненные bb коды
		foreach ($savedBBcodes as $md5 => $savedBBcode) {
			$string = str_replace('[['.$md5.']]', $savedBBcode, $string);
		}

		return $string;
    }

    public static function str_replace_once($needle , $replace , $haystack){
        // Looks for the first occurence of $needle in $haystack
        // and replaces it with $replace.
        $pos = strpos($haystack, $needle);
        if ($pos === false) {
            // Nothing found
        return $haystack;
        }
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }

    /*
	public static function getSmiles()
	{
		if (self::$_smiles === null)
		{
			self::$_smiles = array ();
			$tmp = Adminsmiles::model()->onSite()->findAll();
			if ($tmp)
			{
				foreach ($tmp as $smile)
					self::$_smiles[$smile->code] = $smile;
			}
		}
		return self::$_smiles;
	}
    */
}
?>