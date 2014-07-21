<?php
/**
* @Copyright (c) 2012-, 新浪网运营部-网络应用开发部
* All rights reserved.
* Image基类 
*
* @author shuoshi <shuoshi@staff.sina.com.cn>
* @time   2012/1/2 11:10
* @version Id: 1.0
*/
class BaseModelImage {

    const NorthWest = 'MW_NorthWestGravity';
    const North = 'MW_NorthGravity'; 
    const NorthEast = 'MW_NorthEastGravity';
    const West = 'MW_WestGravity';
    const Center = 'MW_CenterGravity';
    const East = 'MW_EastGravity'; 
    const SouthWest = 'MW_SouthWestGravity'; 
    const South = 'MW_SouthGravity';
    const SourthEast = 'MW_SouthEastGravity'; 

    /**
     * magickwand资源描述符
     * @var resource
     */
    private static $resource;
    private static $resourcek;

    /**
     * 文件资源描述符
     */
    private static $fd;

    /**
     * 构造函数
     * @param mixed $image 图片路径/图片属性数组
        array(
            'width'=>200,
            'height'=>100,
            'backgrounColor'=>'white',
            'format'=>'jpg'
        )
     */
    public function __construct ($image) {
        self::$resource = NewMagickWand(); 
        $this->setImage($image);
    } 

    /**
     * 缩略（放大）图片
     * @param int $thumbWidth 缩放的最大宽度
     * @param int $thumbHeight 缩放的最大高度
     * @param bool $magnify 是否允许放大图片
     * @return bool 成功返回true，失败返回false
     */
    public function resize ($thumbWidth=0, $thumbHeight=0, $magnify = true) {
        $width = $this->getWidth();
        $height = $this->getHeight();
        do {
            //定宽缩放
            if ($thumbHeight === 0) {
                $thumbHeight = $thumbWidth/$width*$height;
                break;
            }
            //定高缩放
            if ($thumbWidth === 0) {
                $thumbWidth = $thumbHeight/$height*$width;
                break;
            }
            //等比例缩放
            if ($width/$thumbWidth > $height/$thumbHeight) {
                $ratio = $thumbWidth/$width;
            } else {
                $ratio = $thumbHeight/$height;
            }
            $thumbWidth = $width*$ratio;
            $thumbHeight = $height*$ratio;
        } while (0);
        
        //由于图片是等比率的，所以只需判断一条边
        if(!$magnify && ($thumbWidth >= $width)) {
            return true;
        }
        
        MagickSetImageCompressionQuality(self::$resource, 90); //1-100值越大，越清晰
        return MagickScaleImage(self::$resource, $thumbWidth, $thumbHeight);
    }

    /**
     * 旋转
     * @param int $degree 要旋转的角度
     * @return bool 成功返回true，失败返回false
     */
    public function rotate ($degree) {
        return MagickRotateImage(self::$resource, 'white', $degree);
    }

    /**
     * 透明度
     * @param int $opacity 透明度
     * @return bool 成功返回true，失败返回false
     */
    public function transparent ($opcity) {
        return MagickEvaluateImage(self::$resource, MW_MultiplyEvaluateOperator , $opcity, MW_OpacityChannel);
    }

    /**
     * 加水印
     * @param string $text 要添加水印的文字
     * @param int $size 字体大小
     * @param int $opcity 水印透明度
     * @param int $degree 要旋转的角度
     * @param const $position 要添加的位置
     * @param int $x 水平位移
     * @param int $y 垂直位移
     * @return bool 成功返回true，失败返回false
     */
    public function addWaterMark ($text, $size, $opacity=1, $degree=0, $position=self::Center, $x=0, $y=0) {
        $width = $this->getWidth();
        $height = $this->getHeight();
        $black = NewPixelWand('black'); 
        $none = NewPixelWand('none');
        $textWand = NewMagickWand();
        MagickNewImage($textWand, $width, $height, $none) ;
        $textDrawWand = NewDrawingWand(); 
        DrawSetTextEncoding($textDrawWand, 'UTF-8') ; 
        DrawSetFont($textDrawWand, '/Library/Fonts/Hei.ttf');
        DrawSetFontWeight($textDrawWand, 900);
        DrawSetFillColor($textDrawWand, $black);
        DrawSetFontSize($textDrawWand, $size);
	if ($x == 0 && $y == 0) {
            DrawSetGravity($textDrawWand, constant($position));
	}
        DrawSetFillAlpha($textDrawWand, $opacity);
        $degree = intval($degree);
        if ($degree !== 0) {
            DrawRotate($textDrawWand, $degree);
        }
        DrawAnnotation($textDrawWand, $x, $y, $text);
        MagickDrawImage($textWand, $textDrawWand);
        return MagickCompositeImage(self::$resource, $textWand, MW_OverCompositeOp, $x, $y);
    }

    /**
     *
     *
     */
    public function drawArc ($x, $y, $r, $sd, $ed, $property=array()) {
        $width = $this->getWidth();
        $height = $this->getHeight();
        $borderColor = NewPixelWand(isset($property['borderColor']) ?  $property['borderColor'] : 'black'); 
        $fillColor = NewPixelWand(isset($property['fillColor']) ? $property['fillColor'] : 'none');
        $none = NewPixelWand('none');
        $picWand = NewMagickWand();
        MagickNewImage($picWand, $width, $height, $none) ;
        $drawWand = NewDrawingWand(); 
        DrawSetStrokeWidth($drawWand, $property['borderWidth']);
        DrawSetStrokeColor($drawWand, $borderColor);
        DrawSetFillColor($drawWand, $fillColor);
        $x*=2; $y*=2; $r*=2;
        $sx = $x-$r; $sy = $y-$r; $ex = $x+$r; $ey = $y+$r;
        DrawArc($drawWand, $sx, $sy, $ex, $ey, $sd, $ed);
        MagickDrawImage($picWand, $drawWand);
        return MagickCompositeImage(self::$resource, $picWand, MW_OverCompositeOp, $sx, $sy);
    }

    /**
     * 画一条线
     * @param int $sx     起始坐标x
     * @param int $sy     起始坐标y
     * @param int $ex     终止坐标y
     * @param int $ey     终止坐标y
     * @param int $border 线条宽度
     */
    public function drawLine ($sx, $sy, $ex, $ey, $property=array()) {
        $width = $this->getWidth();
        $height = $this->getHeight();
        $color = NewPixelWand(isset($property['color']) ? $property['color'] : 'black'); 
        $fillColor = NewPixelWand(isset($property['fillColor']) ? $property['fillColor'] : 'none');
        $backgroundColor = NewPixelWand(isset($property['fillColor']) ? $property['backgroundColor'] : 'none');
        $picWand = NewMagickWand();
        MagickNewImage($picWand, $width, $height, $backgroundColor) ;
        $drawWand = NewDrawingWand(); 
        DrawSetFillColor($drawWand, $fillColor);
        DrawSetStrokeWidth($drawWand, isset($property['borderWidth']) ? $property['borderWidth'] : 1);
        DrawSetStrokeColor($drawWand, $color);
        if (isset($property['stroke'])) {
            DrawSetStrokeDashArray($drawWand, $property['stroke']);
        }
        DrawLine($drawWand, $sx, $sy, $ex, $ey);
        MagickDrawImage($picWand, $drawWand);
        return MagickCompositeImage(self::$resource, $picWand, MW_OverCompositeOp, $x, $y);
    }

    /**
     * 裁剪图片
     * @param int $width  要裁剪的宽度
     * @param int $height 要裁剪的高度
     * @param int $x      裁剪起始横坐标x
     * @param int $y      裁剪起始纵坐标y
     * @return bool       成功返回true，失败返回false
     */
    public function cropImage ($width, $height, $x, $y) {
        return MagickCropImage(self::$resource, $width, $height, $x, $y);
    }

    /**
     * 转换图片格式
     * @param string $format 要转换到的格式（jpg, jpeg, png等)
     * @return bool 成功返回true，失败返回false
     */
    public function setImageFormat($format) {
        return MagickSetImageFormat(self::$resource, $format);
    }

    /**
     * 将图片保存到指定位置
     * @param string $image 图片输出路径
     */
    public function write ($image) {
        MagickWriteImage(self::$resource, $image);
    }

    /**
     * 打印图片
     */
    public function dump () {
        MagickEchoImageBlob(self::$resource);
    }

    /**
     * 获取图片内容
     */
    public function getContent () {
        ob_start();
        MagickEchoImageBlob(self::$resource);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * 重置资源
     */
    public function reset() {
        self::$resource = self::$resourcek;
    }

    /**
     * 读取图片
     * @param string $image 图片路径
     */
    public function setImage ($image) {
        if (is_string($image)) {
            $opts = array (
                'http' => array (
                    'timeout' => 5
                )
            );
            $context = stream_context_create($opts);
            $times = 0;
            do {
                self::$fd = fopen($image, 'r', $include_path=false, $context);
                if (++$times >= 3) {
                    break;
                }
            } while (self::$fd === false);
            MagickReadImageFile(self::$resource, self::$fd); 
            self::$resourcek = CloneMagickWand(self::$resource);
        } else if (is_array($image)) {
            MagickNewImage(self::$resource, $image['width'], $image['height'], $image['backgroundColor']);
            MagickSetFormat(self::$resource, $image['format']);
        }
    }

    /**
     * 获取图片高度
     */
    public function getHeight () {
        return MagickGetImageHeight(self::$resource);
    }

    /**
     * 获取图片宽度
     */
    public function getWidth () {
        return MagickGetImageWidth(self::$resource);
    }

    public function __destruct () {
        if (is_resource(self::$resource)) {
            fclose(self::$fd);
            DestroyMagickWand(self::$resource);
        }
    }

    /**
     * 返回图片大小
     */
    public function getSize () {
        return MagickGetImageSize(self::$resource);
    }

    /**
     * 获取图片mime
     */
    public function getMimeType () {
        return MagickGetImageMimeType(self::$resource);
    }
}
