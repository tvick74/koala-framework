<?php
class Kwf_Assets_Effects
{
    public static function home(Imagick $image)
    {
        $overlay = new Imagick();
        $overlay->readImage(KWF_PATH."/Kwf/Assets/Overlay/home.png");
        $image->compositeImage($overlay, Imagick::COMPOSITE_OVER, (16-12)/2, (16-11)/2);
    }

    public static function chained(Imagick $image)
    {
        $overlay = new Imagick();
        $overlay->readImage(KWF_PATH."/Kwf/Assets/Overlay/chain.png");
        $image->compositeImage($overlay, Imagick::COMPOSITE_OVER, 16-14, 16-7);
    }

    public static function invisible(Imagick $image)
    {
        $overlay = new Imagick();
        $overlay->readImage(KWF_PATH."/Kwf/Assets/Overlay/white80.png");
        $image->compositeImage($overlay, Imagick::COMPOSITE_OVER, 0, 0);
//         $image->setImageOpacity(0.2);
    }

    public static function forbidden(Imagick $image)
    {
        $overlay = new Imagick();
        $overlay->readImage(KWF_PATH."/Kwf/Assets/Overlay/forbidden.png");
        $image->compositeImage($overlay, Imagick::COMPOSITE_OVER, 16-10, 16-10);
    }

    public static function arrow(Imagick $image)
    {
        $overlay = new Imagick();
        $overlay->readImage(KWF_PATH."/Kwf/Assets/Overlay/bullet_go_small.png");
        $image->compositeImage($overlay, Imagick::COMPOSITE_OVER, 16-10, 16-10);
    }

    public static function rotate(Imagick $image, $params)
    {
        if (isset($params[0])) {
            $angle = $params[0];
            if ($angle > 0 && $angle < 360) {
                $image->rotateImage(new ImagickPixel('none'), $angle);
            }
        }
    }
}
