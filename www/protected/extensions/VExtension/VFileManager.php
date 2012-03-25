<?php
class VFileManager extends CApplicationComponent {
	
	public $filesPath = null;
    public $filesUrl = null;

    public function getBasePath ()
    {
        return $this->filesPath;
    }

    public function getImage($path)
    {
       $file = $this->getFile($path);
       if (is_object($file) && get_class($file) == 'VImageFile')
           return $file;
       return null;
    }

    public function getFile($path)
    {
        return VFileBase::createInstance($path);
    }

    public function getFilePath($path)
    {
        $path = str_replace($this->filesPath, '', $path);
        $path = str_replace($this->filesUrl, '', $path);
        return $this->filesPath.$path;
    }

    public function getSiteUrl ($path)
    {
        $path = str_replace($this->filesPath, '', $path);
        $path = str_replace($this->filesUrl, '', $path);
        return $this->filesUrl.$path;
    }

	public static function getMimeType($file)
	{
		$mimetype = '';
		if(function_exists('finfo_open') && defined('FILEINFO_MIME_TYPE'))
		{
			if(($info=finfo_open(FILEINFO_MIME_TYPE)) && ($result=finfo_file($info,$file))!==false)
				$mimetype = $result;
		} else {
			$mimetype = CFileHelper::getMimeType($file);
			$mimetype = explode(';', $mimetype);
			$mimetype = $mimetype[0];
		}

		if($mimetype=='application/zip') {
			$zip = new ZipArchive();
		    if ($zip->open($file)) {
				// В случае успеха ищем в архиве файл с данными
				if (($index = $zip->locateName('word/document.xml')) !== false) {
					$mimetype = self::$additionalMimeTypes['docx'];
				}
				$zip->close();
			}
		}
		return $mimetype;
	}

}
