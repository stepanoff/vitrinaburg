<?php
/**
 * $Id$
 *
 * @author vv
 * @since  12.08.2009
 */

abstract class UserFileBase
{
	private $_fileRealPath;
	/**
	 * @var string корневая папка в хранилице, отосительно которой хранится файл
	 */
	private $_customPath;
	private $_uid;
	protected $__arObject = null;
	protected $__convertObject = null;
	private $_size;
	private $_mimeType;
	private $_charset;
	private $_extensionName;
	private $_fileOriginalPath;
	
	/**
	 * 
	 * @var array маппинг типов файлов на конкретный класс-наследник этого абстрактного класса,
	 * необходим для фабрики конкретных инстансов для файлов разных типов
	 */
	private static $_types2Class = array(
		'image/jpeg'        => 'ImageFile',
		'image/gif'         => 'ImageFile',
		'image/png'         => 'ImageFile',

        'audio/mpeg'        => 'AudioFile', //mp3
		'audio/x-wav'       => 'AudioFile', //wav
        'audio/x-flac'      => 'AudioFile', //flac
        'audio/x-ogg'      => 'AudioFile', //ogg
        'audio/ogg'      => 'AudioFile', //ogg
        'application/ogg'      => 'AudioFile', //ogg
        'audio/x-mp4'      => 'AudioFile', //mp4
        'audio/x-m4a'      => 'AudioFile', //m4a
        'audio/m4a'      => 'AudioFile', //m4a
        'audio/mp4'        => 'AudioFile', //m4a

        'audio/x-aac'      => 'AudioFile', //aac
        'audio/x-ms-wma'      => 'AudioFile', //wma

        'video/x-msvideo'   => 'VideoFile', // avi
        'video/x-flv'       => 'VideoFile', // flv
        'video/x-fli'       => 'VideoFile', // flv, fli
        'video/quicktime'   => 'VideoFile', // mov, qt
        'video/mpeg'        => 'VideoFile',  // mpeg
		'video/mp4'			=> 'VideoFile', // mp4
		'video/3gpp'			=> 'VideoFile', // 3gp
		'application/pdf'	=> 'PdfFile',
		'application/msword' => 'DocFile', // doc
		'video/x-ms-wmv'	=> 'VideoFile',
	
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
				
			case 'AudioFile':
				$icon = Yii::app()->params['interfaceResourcesUrl2'] . '/img/documents_icons/icon_audio.png';
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
	
	public function getARObject()
	{
		return null;
	}
	
	public function getConvertObject()
	{
		return null;
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

	/**
	 * создает экземпляр объекта класса-потомка UserFileBase, соответствующего типу заданного файла
	 *
	 * @param string $filePath путь до файла
	 * @param string $uid уникальный uid-идентификатор файла в хранилище
	 * @param string $customPath особый строковой классификатор хранимого файла (в реализации это корневая папка в хранилице, отосительно которой хранится нужный файл, например 'photos' или 'user')
	 * @param string $mimeType MIME-тип файла
	 * @param string $ext расширение файла
	 * @param bool|string $checkFile проводить проверку файла
	 * @param $fileOriginalPath
	 * @internal param string $originalFilePath путь до оригинального файла
	 *
	 * @return UserFileBase|null|false UserFileBase, если создал Instance, null - если файл ошибочный, false - внутреняя ошибка
	 */
	public static function createInstance($filePath, $uid, $customPath='', $mimeType='', $ext='', $checkFile=true, $fileOriginalPath)
	{
		if (!file_exists($filePath))
			return false;
		
		if (empty($mimeType)) {
			$mimeType = UserFilesManager::getMimeType($filePath);
		}

		$charset = UserFilesManager::getCharset($filePath);
		if( (empty($ext)) && ($pos=strrpos($filePath,'.'))!==false ) {
			$ext = (string)substr($filePath,$pos+1);
		}

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
			$targetClass = 'BaseFile';
		}
		/** @var $resultFile UserFileBase */
		$resultFile = null;
		if (isset($targetClass))
		{
			$resultFile = new $targetClass();
			$resultFile->setUID($uid);
			$resultFile->setFileRealPath($filePath);
			$resultFile->setMimeType($mimeType);
			$resultFile->setCharset($charset);
			$resultFile->setSize(filesize($filePath));
			$resultFile->setExtensionName($ext);
			$resultFile->setCustomPath($customPath);
			$resultFile->setFileOriginalPath($fileOriginalPath);

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
		return $fileUrl = Yii::app()->getComponent('userFilesManager')->getUrlByFileUid($this->getUID(), $this->getCustomPath());
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

	/**
	 * Удаляет связанные Active Record модели
	 */
	public function deleteARecords()
	{

	}

	public function delete() {
		// Порядок важен! для imagefile
		$this->deleteARecords(); // здесь удаляются записи на конвертирование для будущих запусков imageconvert
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
	 * Получает uid-идентификатор файла
	 * 
	 * @return string
	 */
	public function getUID()
	{
		return $this->_uid;
	}
	
	/**
	 * Задает uid-идентификатор файла
	 * 
	 * @param string $uid
	 */
	public function setUID($uid)
	{
		$this->_uid = $uid;
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
	* Получает полный путь в ФС до оригинально файла
	*
	* @return string
	*/
	public function getFileOriginalPath()
	{
		return $this->_fileOriginalPath;
	}
	
	/**
	 * Задает полный путь в ФС до оригинального файла
	 *
	 * @param string $fileOriginalPath
	 */
	public function setFileOriginalPath($fileOriginalPath)
	{
		if(!empty($fileOriginalPath) && file_exists($fileOriginalPath))
			$this->_fileOriginalPath = $fileOriginalPath;
		else $this->_fileOriginalPath = $this->getFileRealPath();
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
	 * Получает Charset файла
	 *
	 * @return string
	 */
	public function getCharset()
	{
		return $this->_charset;
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

	/**
	 * Задает charset файла
	 *
	 * @param string $charset
	 */
	public function setCharset($charset)
	{
		$this->_charset = $charset;
	}
	
	/**
	 * Получает кастомный путь для файла в хранилище - особый строковой классификатор хранимого файла
	 * (в реализации это корневая папка в хранилице, отосительно которой хранится нужный файл, например 'photos' или 'user')
	 * @return string
	 */
	public function getCustomPath()
	{
		return $this->_customPath;
	}
	
	/**
	 * Задает кастомный путь для файла в хранилище
	 * @param string $customPath
	 */
	public function setCustomPath($customPath)
	{
		$this->_customPath = $customPath;
	}
	
	/**
	 * Добавляет элемент в очередь на конвртацию файла в необходимый формат
	 */
	public function __convert()
	{
		
	}

}
?>
