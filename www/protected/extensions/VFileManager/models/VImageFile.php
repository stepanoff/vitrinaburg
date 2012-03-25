<?php

Yii::import('ext.userfilesmanager.helpers.*');
Yii::import('ext.userfilesmanager.models.*');

class ImageFile extends UserFileBase
{
	private $_width;
	private $_height;

	const RESIZE_WIDTH_CODE = 'w';
	const RESIZE_HEIGHT_CODE = 'h';

	const RESIZE_ZERO_PRIORITY = 0;
	const RESIZE_LOW_PRIORITY = 50;
	const RESIZE_NORMAL_PRIORITY = 75;
	const RESIZE_HIGH_PRIORITY = 100;

	const RESIZE_BY_SMALLER_SIDE = 10;

	/**
	 * @return Image
	 */
	public function getARObject()
	{
		if ($this->__arObject === null)
			$this->__arObject = Image::model()->findByAttributes(array(
				'fileUid' => $this->getUID()
			));

		return $this->__arObject;
	}

	/**
	 * Ресайзит исходную картинку до новых размеров c соотношением сторон
	 *
	 * @param int $newWidth новая ширина
	 * @param int $newHeight новая высота
     * @param bool $addWatermark флаг наложения ватермарка
	 * @return string полный путь до ресайзнутого изображения
	 */
	public function resize($newWidth, $newHeight, $addWatermark = false)
	{
		$settings = new ezcImageConverterSettings(array(
		    new ezcImageHandlerSettings( 'IM', 'ezcImageImagemagickHandler', array() )
		));

		$converter = new ezcImageConverter( $settings );


        $filters = array(
            new ezcImageFilter(
                'scale',
                array(
                     'width' => (int)$newWidth,
                     'height' => (int)$newHeight,
                     'direction' => ezcImageGeometryFilters::SCALE_BOTH,
                )
            ),
        );
        
		$resizedFilePath = $this->getResizedExactFilePath($newWidth, $newHeight);

		$converter->createTransformation( 'resizing', $filters, array( $this->getMimeType() ));
		$converter->transform('resizing', $this->getFileOriginalPath(), $resizedFilePath);

		@chmod($resizedFilePath, 0777);
		
		if($addWatermark)
			$this->addWatermark($resizedFilePath, $newWidth, $newHeight);

		return $resizedFilePath;
    }

    /**
     * Кропает и заменяет оригинал
     *
     * @param int $x верхнего левого угла
     * @param int $y верхнего левого угла
     * @param int $width ширина области
     * @param int $height высота области
     * @return string полный путь до ресайзнутого изображения
     */

	public function crop($x, $y, $width, $height)
	{
		$settings = new ezcImageConverterSettings(array(
		    new ezcImageHandlerSettings( 'IM', 'ezcImageImagemagickHandler', array() )
		));

		$converter = new ezcImageConverter( $settings );

        $filters = array(
            new ezcImageFilter(
                'crop',
                array(
                     'x' => (int)$x,
                     'y' => (int)$y,
                     'width' => (int)$width,
                     'height' => (int)$height
                )
            )
        );

		$converter->createTransformation( 'crop', $filters, array( $this->getMimeType() ));
		$converter->transform('crop', $this->getFileRealPath(), $this->getFileRealPath());
		$converter->transform('crop', $this->getFileOriginalPath(), $this->getFileOriginalPath());

		@chmod($this->getFileOriginalPath(), 0777);

		return $this->getFileOriginalPath();
	}


	public function checkFileCorrect()
	{
		if (@filesize($this->getFileRealPath()))
        {
			$imagesize  = getimagesize($this->getFileRealPath());
			if (!$imagesize ||
					!is_array($imagesize) ||
					!$imagesize[0] ||
					!$imagesize[1])
				return false;

        }
        else
			return false;

        if (@filesize($this->getFileOriginalPath()))
        {
        	$imagesize  = getimagesize($this->getFileOriginalPath());
        	if (!$imagesize ||
                    !is_array($imagesize) ||
                    !$imagesize[0] ||
                    !$imagesize[1])
            	return false;;
        }
        else
        	return false;

        return true;
	}

	/**
	 * Ресайзит исходную картинку с соотношением сторон
	 *
	 * @param string $sideCode код стороны (ширина или высота), для которой задается новый размер. Другая сторона будет вычислена пропорционально
	 * @param string $newDimension новый размер для заданной стороны
	 * @return string путь до результирующего ресайзенного изображения
	 */
	public function resizeScaledBySide($sideCode = self::RESIZE_WIDTH_CODE, $newDimension, $addWatermark = false)
	{
		$settings = new ezcImageConverterSettings(array(
		    new ezcImageHandlerSettings( 'IM', 'ezcImageImagemagickHandler', array() )
		));

		$converter = new ezcImageConverter( $settings );

		list($newWidth, $newHeight) = $this->getNewDimensions($sideCode, $newDimension);

        $filters = array(
            new ezcImageFilter(
                'scaleExact',
                array(
                     'width' => $newWidth,
                     'height' => $newHeight,
                     'direction' => ezcImageGeometryFilters::SCALE_DOWN,
                )
            ),
        );


        $resizedFilePath = $this->getResizedScaledFilePath($sideCode, $newDimension);

		if ($this->getWidth() >= $newWidth && $this->getHeight() >= $newHeight) {
			$converter->createTransformation( 'resizing', $filters, array( $this->getMimeType() ));
			$converter->transform('resizing', $this->getFileOriginalPath(), $resizedFilePath);
		} else {
			copy($this->getFileOriginalPath(), $resizedFilePath);
		}


		@chmod($resizedFilePath, 0777);

		if($addWatermark)
			$this->addWatermark($resizedFilePath, $newWidth, $newHeight);

		return $resizedFilePath;
	}

	public function addWatermark($imageFilePath = false, $imageWidth = false, $imageHeight = false)
	{
		if(!$imageFilePath) {
			$imageFilePath = $this->getFileOriginalPath();
			$watermarkedFilePath = $this->getFileRealPath();
		}
		else {
			$watermarkedFilePath = $imageFilePath;
		}

		$watermarkFile = Yii::app()->params['staticDir2'].'/misc/watermark.png';
		if(!file_exists($watermarkFile))
			return $imageFilePath;

		// Если размеры картинки удовлетворяют параметрам по ширине и высоте
		$imageWidth = $imageWidth ? $imageWidth : $this->getWidth();
		$imageHeight = $imageHeight ? $imageHeight : $this->getHeight();

		if($imageWidth <= Yii::app()->params['minWidth4Watermark'] || $imageHeight <= Yii::app()->params['minHeight4Watermark'])
			return $imageFilePath;

		$settings = new ezcImageConverterSettings(array(
		    new ezcImageHandlerSettings( 'IM', 'ezcImageImagemagickHandler', array() )
		));

		$converter = new ezcImageConverter( $settings );

		list($wm_width, $wm_height)= getimagesize($watermarkFile);

		if ($imageHeight < $wm_height*3)
		{
			$newWmHeight = ceil($imageHeight/3);
			$newWmWidth = ceil( ($newWmHeight/$wm_height)*$wm_width );
			$filterType = 'watermarkPercent';
			$posX = (int) (100 - ($newWmWidth/$imageWidth)*100 - (10/$imageWidth)*100 ) ;
			$posY = (int) (100 - 30 - (10/$imageHeight)*100 ) ;
			$size = 30;
		}
		else
		{
			$filterType = 'watermarkAbsolute';
			$posX = - $wm_width - 10;
			$posY = - $wm_height - 10;
			$size = false;
		}

		$filterSettings = array (
			'posX' => $posX,
			'posY' => $posY,
			'image' => $watermarkFile,
		);
		if ($size)
			$filterSettings['size'] = $size;

		$filters = array(
			new ezcImageFilter(
			   $filterType,
				$filterSettings
			),);

		$converter->createTransformation( 'watermark', $filters, array( $this->getMimeType() ));
		$converter->transform('watermark', $imageFilePath, $watermarkedFilePath);

		@chmod($watermarkedFilePath, 0777);

		return $imageFilePath;
	}

	/**
	 * Получает url для ресайзеной картинки с новыми размерами
	 *
	 * @param int $width
	 * @param int $height
	 * @param array $params параметры <br>
	 * <ul>
	 * <li>forceConvert	 (boolean=false): принудительная конвертация (без постановки в очередь конвертации)
	 * <li>priority		 (int=RESIZE_LOW_PRIORITY): приоритет конвертации
	 * <li>overwrite	 (boolean = false): принудительно перезаписать
	 * <li>type			 (int=false): тип конвертации, по меньшей стороне (ImageFile::RESIZE_BY_SMALLER_SIDE) / вписать в прямоугольник
	 * <li>addWatermark	 (boolean=false): поместить на изображение watermark
	 * </ul>
	 * @return string URL для ресайзнутого изображения
	 */
	public function getUrlResized($width, $height, $params=array())
	{
        $defaultParams = array(
            'forceConvert'	=> false,
            'priority'		=> self::RESIZE_LOW_PRIORITY,
            'overwrite'		=> false,
            'type'			=> false,
            'addWatermark'	=> false
        );
        $params = array_merge($defaultParams, $params);

        // Проверяем лишние параметры
        if (count(array_diff_key( $params, $defaultParams )))
        	throw new Exception("Extra arguments to the method ".__METHOD__);

        if ($params['type'] == self::RESIZE_BY_SMALLER_SIDE )
		{
			$newHeightByWidth = intval($this->getHeight() * ($width / $this->getWidth()));
			$newWidthByHeight = intval($this->getWidth() * ($height / $this->getHeight()));

			$paramsScaled = array(
				'forceConvert'	=> $params['forceConvert'],
				'priority'		=> $params['priority'],
				'overwrite'		=> $params['overwrite'],
				'addWatermark'	=> $params['addWatermark']
			);
			if ($newWidthByHeight < $width)
				return $this->getUrlResizedScaledBySide(self::RESIZE_WIDTH_CODE, $width, $paramsScaled);
			elseif ($newHeightByWidth < $height)
				return $this->getUrlResizedScaledBySide(self::RESIZE_HEIGHT_CODE, $height, $paramsScaled);
		}

		$resizedImagePath = $this->getResizedExactFilePath($width, $height);
		if (!is_file($resizedImagePath) || $params['overwrite']) {
			if ($params['forceConvert']) {
				$resizedImagePath = $this->resize($width, $height, $params['addWatermark']);
				if (!$resizedImagePath) {
					$nginx_resize = array(
						'scale' => self::RESIZE_WIDTH_CODE.self::RESIZE_HEIGHT_CODE,
						'originalPath' => str_replace(Yii::app()->params['originalBasePath'], '', $this->getFileOriginalPath()),
						'dimension' => $width.'x'.$height,
					);

					return ImageFileConvertHelper::getNotAvailableImage('getUrlResized', array($width, $height, array('forceConvert' => true)), $nginx_resize);
				}
			}
			else {
				$this->convert('resize', array($width, $height, $params['addWatermark']), $params['priority']);
				$nginx_resize = array(
					'scale' => self::RESIZE_WIDTH_CODE.self::RESIZE_HEIGHT_CODE,
					'originalPath' => str_replace(Yii::app()->params['originalBasePath'], '', $this->getFileOriginalPath()),
					'dimension' => $width.'x'.$height,
				);
				return ImageFileConvertHelper::getNotAvailableImage('getUrlResized', array($width, $height, array('forceConvert'=>true)), $nginx_resize);
            }
		}
		$fileName = '';
		if (($pos=strrpos($resizedImagePath, DIRECTORY_SEPARATOR))!==false) {
			$fileName = substr($resizedImagePath, $pos+1);
		}
		$resizedFileUrl = Yii::app()->getComponent('userFilesManager')->getUrlByFileUid($this->getUID(), $this->getCustomPath());
		$resizedFileUrl = substr($resizedFileUrl, 0, strrpos($resizedFileUrl, '/')) . '/' . $fileName;
		return $resizedFileUrl;
	}

	/**
	 * Получает url для ресайзеной картинки с сохранением соотношения сторон
	 *
	 * @param string $sideCode код стороны (ширина или высота), для которой задается новый размер. Другая сторона будет вычислена пропорционально
	 * @param string $newDimension новый размер для заданной стороны
	 * @param array $params массив параметров<br>
	 * <ul>
	 * <li>forceConvert 	(boolean=false): принудительная конвертация (без постановки в очередь конвертации)
	 * <li>priority 		(int=RESIZE_LOW_PRIORITY): приоритет конвертации
	 * <li>overwrite 		(boolean = false): принудительно перезаписать
	 * <li>addWatermark 	(boolean=false): поместить на изображение watermark
	 * </ul>
	 * @return string URL для ресайзнутого изображения
	 */
	public function getUrlResizedScaledBySide($sideCode = self::RESIZE_WIDTH_CODE, $newDimension, $params=array())
	{
        $defaultParams = array(
            'forceConvert'	=> false,
            'priority'		=> self::RESIZE_LOW_PRIORITY,
            'overwrite'		=> false,
            'addWatermark'	=> false
        );
        $params = array_merge($defaultParams, $params);

        // Проверяем лишние параметры
        if (count(array_diff_key( $params, $defaultParams )))
        	throw new Exception("Extra arguments to the method ".__METHOD__);

		$resizedImagePath = $this->getResizedScaledFilePath($sideCode, $newDimension);

		if (!is_file($resizedImagePath) || $params['overwrite']) {

			if ($params['forceConvert']) {
				$resizedImagePath = $this->resizeScaledBySide($sideCode, $newDimension, $params['addWatermark']);
				if (!$resizedImagePath) {
					$nginx_resize = array(
						'scale' => $sideCode,
						'originalPath' => str_replace(Yii::app()->params['originalBasePath'], '', $this->getFileOriginalPath()),
						'dimension' => $newDimension,
					);

					return ImageFileConvertHelper::getNotAvailableImage('getUrlResizedScaledBySide', array($sideCode, $newDimension, array('forceConvert' => true)), $nginx_resize);
				}
			}
			else {
				$this->convert('resizeScaledBySide', array($sideCode, $newDimension, $params['addWatermark']), $params['priority']);
				$nginx_resize = array(
					'scale' => $sideCode,
					'originalPath' => str_replace(Yii::app()->params['originalBasePath'], '', $this->getFileOriginalPath()),
					'dimension' => $newDimension,
				);
				return ImageFileConvertHelper::getNotAvailableImage('getUrlResizedScaledBySide', array($sideCode, $newDimension, array('forceConvert'=>true)), $nginx_resize);
			}
		}
		$fileName = '';
		if (($pos=strrpos($resizedImagePath, DIRECTORY_SEPARATOR))!==false) {
			$fileName = substr($resizedImagePath, $pos+1);
		}
		$resizedFileUrl = Yii::app()->getComponent('userFilesManager')->getUrlByFileUid($this->getUID(), $this->getCustomPath());
		$resizedFileUrl = substr($resizedFileUrl, 0, strrpos($resizedFileUrl, '/')) . '/' . $fileName;
		return $resizedFileUrl;
	}
	
	public function getNewDimensions($sideCode, $newDimension) {
		switch ($sideCode) {
			case self::RESIZE_WIDTH_CODE:
				$newWidth = intval( $newDimension);
				$check = $this->getWidth(); if ($check == 0) { $check = 1; }
				$newHeight = intval( $this->getHeight() * $newDimension / $check );
				if(!$newHeight)
					$newHeight = 1;
				break;
			case self::RESIZE_HEIGHT_CODE:
				$newHeight = intval( $newDimension );
				$check = $this->getHeight(); if ($check == 0) { $check = 1; }
				$newWidth = intval( $this->getWidth() * $newDimension / $check );
				if(!$newWidth)
					$newWidth = 1;
				break;
			default:
				throw new CException('invalid sideCode: '.$sideCode);
		}

		return array($newWidth, $newHeight);
	}

	private function getResizedFilePath($width, $height, $postfix = '') {
		if (($pos=strrpos($this->getFileRealPath(), '.')) !== false &&
				strpos($this->getExtensionName(), '/') === false) {
			$resizedFilePath  = substr($this->getFileRealPath(), 0, $pos).'_resizedScaled_'.$width.'to'.$height.'.'.$this->getExtensionName();
		}
		else {
			$resizedFilePath = $this->getFileRealPath().'_resizedScaled_'.$width.'to'.$height;
		}

		return $resizedFilePath;
	}

	/**
	 * Получает путь в ФС для новой картинки, полученной из исходной путем ресайза с соотношением сторон
	 *
	 * @param string $sideCode код стороны (ширина или высота), для которой задается новый размер. Другая сторона будет вычислена пропорционально
	 * @param string $newDimension новый размер для заданной стороны
	 * @return string
	 */
	public function getResizedScaledFilePath($sideCode, $newDimension)
	{
		list($newWidth, $newHeight) = $this->getNewDimensions($sideCode, $newDimension);

		return $this->getResizedFilePath($newWidth, $newHeight, 'Scaled');
	}

	/**
	 * Получает путь в ФС для новой картинки, полученной из исходной путем ресайза
	 *
	 * @param int $newWidth новая ширина
	 * @param int $newHeight новая высота
	 * @return string
	 */
	public function getResizedExactFilePath($width, $height)
	{
		return $this->getResizedFilePath($width, $height);
	}

	/**
	 * @var ezcImageAnalyzer инстанс анализатора картинок
	 */
	private $_imageAnalyzer;

	/** getter method */
	private function getImageAnalyzer()
	{
		if (empty($this->_imageAnalyzer)) {
			$file = file_exists($this->getFileOriginalPath()) ? $this->getFileOriginalPath() : $this->getFileRealPath(); 
			$this->_imageAnalyzer = new ezcImageAnalyzer($file);
		}

		return $this->_imageAnalyzer;
	}

	/**
	 * Получает высоту картинки
	 * @return int
	 */
	public function getHeight()
	{
		if (empty($this->_height)) {
			$file = file_exists($this->getFileOriginalPath()) ? $this->getFileOriginalPath() : $this->getFileRealPath();
			$sizes = getimagesize($file);
			$this->_width = $sizes[0];
			$this->_height = $sizes[1];
		}

		return $this->_height;
	}

	/**
	 * Получает ширину картинки
	 * @return int
	 */
	public function getWidth()
	{
		if (empty($this->_width)) {
			$file = file_exists($this->getFileOriginalPath()) ? $this->getFileOriginalPath() : $this->getFileRealPath();
			$sizes = getimagesize($file);
			$this->_width = $sizes[0];
			$this->_height = $sizes[1];
		}

		return $this->_width;
	}

	/**
	 * автоматически поворачивает картинку на нужный угол, если нужно, при наличии в ней exif-Тега с ориентацией
	 */
	public function autoRotateImage()
	{
		$imageRealPath = $this->getFileRealPath();
		$imageOriginalPath = $this->getFileOriginalPath();
		system("convert $imageRealPath -auto-orient $imageRealPath");
		system("convert $imageOriginalPath -auto-orient $imageOriginalPath");
	}

    public function rotate($angle = 90)
    {
        $imagePath = $this->getFileRealPath();

        $convertExe = ezcBaseFeatures::getImageConvertExecutable();

        passthru("$convertExe $imagePath -rotate  ".intval($angle)."  $imagePath", $return);

        $derivedFiles = CFileHelper::findFiles(dirname($imagePath), array('exclude' => array($imagePath), 'level' => 1));
        foreach ($derivedFiles as $file) {
            @unlink($file);
        }
        
        $imagePath = $this->getFileOriginalPath();
        
        $convertExe = ezcBaseFeatures::getImageConvertExecutable();
        
        passthru("$convertExe $imagePath -rotate  ".intval($angle)."  $imagePath", $return);
        
        $derivedFiles = CFileHelper::findFiles(dirname($imagePath), array('exclude' => array($imagePath), 'level' => 1));
        foreach ($derivedFiles as $file) {
        	@unlink($file);
        }
        
    }

	/**
	 *
	 * @param  $method
	 * @param  $params
	 * @param int $priority
	 * @return bool
	 */
	public function convert($method, $params, $priority = self::RESIZE_LOW_PRIORITY)
	{
		if (empty($params)) $params = array();
		$params = serialize($params);
		$uid = $this->getUID();
		$file_custom_path = $this->getCustomPath();
		
		$attr = array(
			'file_uid'			=> $uid,
			'file_custom_path'	=> $file_custom_path,
			'resize_method'		=> $method,
			'params'			=> $params,
		);
		$convert = Imageconvert::model()->findByAttributes($attr);
		if (empty($convert)) {
			$convert = new Imageconvert();
			$convert->file_uid = $uid;
			$convert->resize_method = $method;
			$convert->params = $params;
			$convert->file_custom_path = $file_custom_path;
			$convert->priority = $priority;
			return $convert->save();
		}
		return true;
	}

	public function deleteARecords()
	{
		Imageconvert::model()->deleteAllByAttributes(array(
			'file_uid' => $this->getUID()
		));

		$image = $this->getARObject();
		if ($image)
			$image->delete();
        
	}

	public function deleteFiles() {
		$uid = $this->getUID();
		$file_custom_path = $this->getCustomPath();

		$convert = new Imageconvert();
		$convert->file_uid = $uid;
		$convert->resize_method = 'realDeleteFiles';
		$convert->params = serialize(array());
		$convert->file_custom_path = $file_custom_path;
		$convert->priority = self::RESIZE_ZERO_PRIORITY;
		$convert->save();
	}

	public function realDeleteFiles() {
		parent::deleteFiles();

		return true;
	}

	public function __convert()
	{
		$image = new Image();
		$image->fileUid  = $this->getUID();
		$image->width  = $this->getWidth();
		$image->height  = $this->getHeight();
		$image->fileSize  = filesize($this->getFileOriginalPath());
        $image->save();
	}
}
?>
