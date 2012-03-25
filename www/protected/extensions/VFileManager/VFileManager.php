<?php
class VFileManager extends CApplicationComponent {
	
	public $filesPath = null;

	protected function getCommand($command) {
		$commands = $this->getCommands();
		if (!isset($commands[$command]))
			throw new CronException(Yii::t('auth_backend', 'Undefined command name: {command}', array('{command}' => $command)), 500);
		return $commands[$command];
	}

    public function getImage($string, $compare)
    {
        if ( $string === '*' )
        {
            return true;
        }

        if ( strpos($string, ',') )
        {
            $string = explode(',', $string);
            foreach ( $string as $element )
            {
                if ( $this->parseTimeArgument($element, $compare) )
                {
                    return true;
                }
            }
            return false;
        }
        else
        {
            if ( strpos($string, '-') )
            {
                list($min, $max) = explode('-', $string);
                return ($compare >= $min) && ($compare <= $max);
            }
            elseif ( substr($string, 0, 1) == '/' )
            {
                return !($compare % substr($string, 1));
            }
            else
            {
                return $compare == $string;
            }
        }
    }

}

class CronException extends CException {}