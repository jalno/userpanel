<?php

namespace packages\userpanel;

use packages\base\Image;
use packages\base\IO\File;
use packages\base\Options;
use packages\base\Packages;

trait ImageTrait
{
    public function getImage(int $width, int $height, string $key = 'image', bool $absolute = false)
    {
        if ($this->$key === null) {
            $this->$key = Options::get('packages.userpanel.default.avatar');

            return $this->getImage($height, $width, $key, $absolute);
        }
        static $package;
        if (!$package) {
            $package = Packages::package('userpanel');
        }
        if (preg_match('/([A-Za-z0-9\-]+)\\.(png|jpg|gif)$/', $this->$key, $matches)) {
            $name = $matches[1];
            $suffix = $matches[2];
            $path = "storage/public/resized/{$name}_{$height}x{$width}.{$suffix}";
            $resized = new File\Local($package->getFilePath($path));
            if ($resized->exists()) {
                return $package->url($path, $absolute);
            }
            $avatar = new File\Local($package->getFilePath($this->$key));
            switch ($suffix) {
                case 'jpg':
                    $image = new Image\JPEG($avatar);
                    break;
                case 'gif':
                    $image = new Image\GIF($avatar);
                    break;
                case 'png':
                    $image = new Image\PNG($avatar);
                    break;
            }
            if (!$resized->getDirectory()->exists()) {
                $resized->getDirectory()->make(true);
            }
            $image->resize($width, $height)->saveToFile($resized);

            return $package->url($path, $absolute);
        }
    }
}
