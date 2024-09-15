<?php

namespace packages\userpanel;

use packages\base\Image;
use packages\base\Packages;

trait ImageTrait {
    public function getImage(int $width, int $height, string $key = 'image') {
        $package = Packages::package('userpanel');
        $storage = $package->getStorage("public");
        $image = $this->$key;

        if ($image) {
            $image = $storage->file($image);
        } else {
            $image = $package->getHome()->file("resources/images/default-avatar.png");
        }

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
