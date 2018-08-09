<?php
namespace packages\userpanel;
use \packages\base\{image, options, IO\file, packages};

trait imageTrait{
	public function getImage(int $width, int $height, string $key = "image", bool $absolute = false){
		if($this->$key === null){
			$this->$key = options::get("packages.userpanel.default.avatar");
			return $this->getImage($height, $width, $key, $absolute);
		}
		static $package;
		if(!$package){
			$package = packages::package("userpanel");
		}
		if (preg_match('/([A-Za-z0-9\-]+)\\.(png|jpg|gif)$/', $this->$key, $matches)) {
			$name = $matches[1];
			$suffix = $matches[2];
			$path = "storage/public/resized/{$name}_{$height}x{$width}.{$suffix}";
			$resized = new file\local($package->getFilePath($path));
			if($resized->exists()){
				return  $package->url($path, $absolute);
			}
			$avatar = new file\local($package->getFilePath($this->$key));
			switch($suffix){
				case("jpg"):
					$image = new image\jpeg($avatar);
					break;
				case("gif"):
					$image = new image\gif($avatar);
					break;
				case("png"):
					$image = new image\png($avatar);
					break;
			}
			if(!$resized->getDirectory()->exists()){
				$resized->getDirectory()->make(true);
			}
			$image->resize($width, $height)->saveToFile($resized);
			return $package->url($path, $absolute);
		}
	}
}
