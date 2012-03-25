<?php
class VImageFile extends VFileBase
{
	private $_width;
	private $_height;

	/**
	 * Ресайзит исходную картинку до новых размеров c соотношением сторон
	 *
	 * @param int $newWidth новая ширина
	 * @param int $newHeight новая высота
     * @param bool $addWatermark флаг наложения ватермарка
	 * @return string полный путь до ресайзнутого изображения
	 */
	public function resize($newPath, $newWidth, $newHeight, $addWatermark = false)
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

        try {
            $resizedFilePath = $newPath;

            $converter->createTransformation( 'resizing', $filters, array( $this->getMimeType() ));
            $converter->transform('resizing', $this->getFileRealPath(), $resizedFilePath);

            @chmod($resizedFilePath, 0777);

            if($addWatermark)
                $this->addWatermark($resizedFilePath, $newWidth, $newHeight);
        }
        catch (Exception $e){
            $resizedFilePath = '';
        }


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

        return true;
	}

    public function getThumb ($width, $height, $type, $options = array())
    {
        $defaultParams = array(
            'force'	=> true,
            'addWatermark'	=> false,
            'create' => false,
        );
        $params = array_merge($defaultParams, $options);

        if (count(array_diff_key( $params, $defaultParams )))
        	throw new CException("Extra arguments to the method ".__METHOD__, 500);

        $filePath = $this->getFileRealPath();
		if (!file_exists($filePath))
			return '';
		$thumbName = $this->getThumbName ($width, $height, $type);
		$thumbNameFull = Yii::app()->fileManager->getFilePath($thumbName);

		if (file_exists($thumbNameFull) && !$params['create'])
			return VFileBase::createInstance($thumbName);

        $newWidth = false;
        $newHeight = false;
        // ресайз по ширине
        if (($width && !$height) || ($width && $type == VHtml::SCALE_WIDTH) )
        {
            $sizes = $this->getNewDimensions(VHtml::SCALE_WIDTH, $width);
            $newWidth = $sizes[0];
            $newHeight = $sizes[1];
        }
        // ресайз по высоте
        elseif (($height && !$width) || ($height && $type == VHtml::SCALE_HEIGHT) )
        {
            $sizes = $this->getNewDimensions(VHtml::SCALE_HEIGHT, $height);
            $newWidth = $sizes[0];
            $newHeight = $sizes[1];
        }
        // ресайз по двум сторонам
        elseif ($width && $height)
        {
            // ресайз по меньшей стороне
            if ($type == VHtml::SCALE_SMALLER_SIDE)
            {
                $newHeightByWidth = intval($this->getHeight() * ($width / $this->getWidth()));
                $newWidthByHeight = intval($this->getWidth() * ($height / $this->getHeight()));

                if ($newWidthByHeight < $width)
                {
                    $newWidth = $width;
                    $newHeight = intval( $this->getHeight() * $width / $this->getWidth() );
                }
                elseif ($newHeightByWidth < $height)
                {
                    $newHeight = intval( $height );
                    $newWidth = intval( $this->getWidth() * $height / $this->getHeight() );
                }
                else
                {
                    $newHeight = $height;
                    $newWidth = $width;
                }
            }
            // ресайз по большей стороне
            else
            {
                $byWidth = $this->getNewDimensions(VHtml::SCALE_WIDTH, $width);
                $newWidth = $byWidth[0];
                $newHeight = $byWidth[1];
                if ($newHeight > $height)
                {
                    $byHeight = $this->getNewDimensions(VHtml::SCALE_HEIGHT, $height);
                    $newWidth = $byHeight[0];
                    $newHeight = $byHeight[1];
                }
            }
        }

        // todo: возращать заглушку
        if (!$newHeight || !$newWidth)
            return '';


        if ($this->resize($thumbNameFull, $newWidth, $newHeight, $params['addWatermark']))
        {
            return VFileBase::createInstance($thumbName);
		}
        return '';
    }

    public function getThumbName ($width, $height, $type)
    {
        $postfix = '';
        if ($width && $height && $width==$height)
            $postfix = '_'.$width;
        else
        {	if ($width)
                $postfix .= '_w'.$width;
            if ($height)
                $postfix .= '_h'.$height;
        }

        if ($type == VHtml::SCALE_SMALLER_SIDE)
            $postfix .= '_smaller';

        $str = $this->getFileName();
        $info = pathinfo($str);
        return $info['dirname'].'/'.$info['filename'].$postfix.'.'.$info['extension'];
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

	public function getNewDimensions($sideCode, $newDimension) {
		switch ($sideCode) {
			case VHtml::SCALE_WIDTH:
				$newWidth = intval( $newDimension);
				$check = $this->getWidth(); if ($check == 0) { $check = 1; }
				$newHeight = intval( $this->getHeight() * $newDimension / $check );
				if(!$newHeight)
					$newHeight = 1;
				break;
			case VHtml::SCALE_HEIGHT:
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

	/**
	 * @var ezcImageAnalyzer инстанс анализатора картинок
	 */
	private $_imageAnalyzer;

	/** getter method */
	private function getImageAnalyzer()
	{
		if (empty($this->_imageAnalyzer)) {
			$file = $this->getFileRealPath();
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
			$file = $this->getFileRealPath();
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
			$file = $this->getFileRealPath();
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
		system("convert $imageRealPath -auto-orient $imageRealPath");
	}

	public function deleteFiles() {
	}

}
?>
