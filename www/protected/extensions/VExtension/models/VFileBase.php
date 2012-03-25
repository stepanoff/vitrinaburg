<?php
class VFileBase
{
	private $_fileRealPath;
    private $_fileName;

	private $_size;
	private $_mimeType;
	private $_extensionName;
	private $_fileOriginalPath;
	
	private static $_types2Class = array(
		'image/jpeg'        => 'VImageFile',
		'image/gif'         => 'VImageFile',
		'image/png'         => 'VImageFile',

        'video/x-msvideo'   => 'VVideoFile', // avi
        'video/x-flv'       => 'VVideoFile', // flv
        'video/x-fli'       => 'VVideoFile', // flv, fli
        'video/quicktime'   => 'VVideoFile', // mov, qt
        'video/mpeg'        => 'VVideoFile',  // mpeg
		'video/mp4'			=> 'VVideoFile', // mp4
		'video/3gpp'			=> 'VVideoFile', // 3gp
		'application/pdf'	=> 'VPdfFile',
		'application/msword' => 'VDocFile', // doc
		'video/x-ms-wmv'	=> 'VVideoFile',
	
	);
	
	public function getIcon ($width = false, $height = false)
	{
		$className = get_class($this);
		
		switch ($className)
		{
			case 'ImageFile':
				$icon = Yii::app()->params['interfaceResourcesUrl2'] . '/img/documents_icons/icon_video.png';
				break;
				
			case 'VideoFile':
				$icon = Yii::app()->params['interfaceResourcesUrl2'] . '/img/documents_icons/icon_video.png';
				break;
				
			case 'DocFile':
				$icon = Yii::app()->params['interfaceResourcesUrl2'] . '/img/documents_icons/icon_doc.png';
				break;
				
			case 'PdfFile':
				$icon = Yii::app()->params['interfaceResourcesUrl2'] . '/img/documents_icons/icon_pdf.png';
				break;
				
			case 'XlsFile':
				$icon = Yii::app()->params['interfaceResourcesUrl2'] . '/img/documents_icons/icon_xls.png';
				break;
				
			default :
				$icon = Yii::app()->params['interfaceResourcesUrl2'] . '/img/documents_icons/icon_unknown.png';
				break;
		}
		return $icon;
	}

	public function classname() {
		return get_class($this);
	}
	
	public function moveTo($destDir)
	{

	}

	public function copyTo($destDir)
	{
		
	}
	
	public function checkFileCorrect()
	{
		return true;
	}

    public function getSiteUrl ()
    {
        return Yii::app()->fileManager->getSiteUrl ($this->getFileName());
    }

    public function getFileName ()
    {
        return $this->_fileName;
    }

    public function setFileName ($fileName)
    {
        $this->_fileName = $fileName;
    }

	/**
	 * создает экземпляр объекта класса-потомка UserFileBase, соответствующего типу заданного файла
	 *
	 * @param string $filePath путь до файла
	 * @param bool|string $checkFile проводить проверку файла
	 *
	 * @return UserFileBase|null|false UserFileBase, если создал Instance, null - если файл ошибочный, false - внутреняя ошибка
	 */
	public static function createInstance($fileName, $checkFile=true)
	{
        $filePath = Yii::app()->fileManager->getFilePath($fileName);

		if (!file_exists($filePath))
			return false;

		if (empty($mimeType)) {
			$mimeType = VFileManager::getMimeType($filePath);
		}

        $info = pathinfo($filePath);
        $ext = $info['extension'];

		if (array_key_exists($mimeType, self::$_types2Class)) {
			$targetClass = self::$_types2Class[$mimeType];
		}
		elseif ($ext == 'doc' || $ext == 'docx')
		{
			$targetClass = 'DocFile';
		}
		elseif ($ext == 'xls' || $ext == 'xlsx')
		{
			$targetClass = 'XlsFile';
		}
		else
		{
			$targetClass = 'VFileBase';
		}
		/** @var $resultFile UserFileBase */
		$resultFile = null;
		if (isset($targetClass))
		{
			$resultFile = new $targetClass();
			$resultFile->setFileRealPath($filePath);
            $resultFile->setFileName($fileName);
			$resultFile->setMimeType($mimeType);
			$resultFile->setSize(filesize($filePath));
			$resultFile->setExtensionName($ext);

			if ($checkFile)
			{
				if (!$resultFile->checkFileCorrect() )
				{
					$resultFile = null;
				}
			}
		}
		
		return $resultFile;
	}

	public function getUrl()
	{
	}
	
	/**
	 * удаляет файл, и другие связанные с ним данные в хранилище 
	 */
	public function deleteFiles()
	{
		$fileDirs = array(substr($this->_fileRealPath, 0, strrpos($this->_fileRealPath, $this->_uid)), substr($this->_fileOriginalPath, 0, strrpos($this->_fileOriginalPath, $this->_uid)));
		foreach ($fileDirs as $fileDir) {
			try {
				if (!empty($fileDir))
					ezcFile::removeRecursive($fileDir);
			}
			catch (ezcBaseFileException $e) {
				// do nothing if older file or dir is not present
			}
		}
	}

	public function delete() {
		//$this->deleteARecords(); // здесь удаляются записи на конвертирование для будущих запусков imageconvert
		$this->deleteFiles(); // здесь добавляется запись в imageconvert на удаление файлов
	}

	/**
	 * получает полное содержимое файла
	 * 
	 * @return string
	 */
	public function getContents()
	{
		return file_get_contents($this->getFileRealPath());
	}

	/**
	 * Получает полный путь в ФС до основного файла
	 * 
	 * @return string
	 */
	public function getFileRealPath()
	{
		return $this->_fileRealPath;
	}
	
	/**
	 * Задает полный путь в ФС до основного файла
	 * 
	 * @param string $fileRealPath
	 */
	public function setFileRealPath($fileRealPath)
	{
		$this->_fileRealPath = $fileRealPath; 
	}
	
	/**
	 * Получает размер файла в байтах
	 * 
	 * @return int
	 */
	public function getSize()
	{
		if (empty($this->_size)) {
			$fileStat = stat($this->getFileRealPath());
			$this->_size = $fileStat['size'];
		}
		return $this->_size;
	}

	/**
	 * Получает MIME-тип файла
	 * 
	 * @return string
	 */
	public function getMimeType()
	{
		return $this->_mimeType;
	}

	/**
	 * Получает расширение файла
	 * 
	 * @return string
	 */
	public function getExtensionName()
	{
		return $this->_extensionName;
	}
	
	/**
	 * Задает расширение файла
	 * 
	 * @param string $extensionName
	 */
	public function setExtensionName($extensionName)
	{
		$this->_extensionName = $extensionName;
	}
	
	/**
	 * Задает размер файла
	 * 
	 * @param $size
	 * @return unknown_type
	 */
	public function setSize($size)
	{
		$this->_size = $size;
	}

	/**
	 * Задает MIME-тип файла
	 *  
	 * @param string $mimeType
	 */
	public function setMimeType($mimeType)
	{
		$this->_mimeType = $mimeType;
	}

}
?>
