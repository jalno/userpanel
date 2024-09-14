<?php

namespace packages\userpanel;

use packages\base\Image;
use packages\base\IO\File;
use packages\base\Options;
use packages\base\Packages;

trait ImageTrait {
    public function getImage(int $width, int $height, string $key = 'image') {

        if ($this->$key === null) {
            $this->$key = Options::get('packages.userpanel.default.avatar');
            return $this->getImage($height, $width, $key);
        }
        
        $storage = Packages::package('userpanel')->getStorage("public");
        $image = $storage->file($this->$key);
        $name = substr($image->basename, 0, strrpos($image->basename, '.'));
        $suffix = $image->getExtension();

        $path = "resized/{$name}_{$height}x{$width}.{$suffix}";
        $resizedfile = $storage->file($path);

        if ($resizedfile->exists()) {
            return $storage->getURL($resizedfile);     
        } 
        $image = Image::fromFormat($image);
        if (!$resizedfile->getDirectory()->exists()) {
            $resizedfile->getDirectory()->make(true);
        }
        $image->resize($width, $height)->saveToFile($resizedfile);

        return $storage->getURL($resizedfile); 
    }
}
