<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/


/*
 * Untested code. I just wrote this wrapper for imagine,
 * because it's the interface i'd like for editing images (minus the layer merging from one file to another).
 * This needs better error handling for sure. There should be try catches that behave based on debug settings
 */

class ImageAbstraction {
	
	const FLIP_HORIZONAL='1';
	const FLIP_VERTICAL='2';
	const FLIP_BOTH='3';
	
	private $_imagine_instance;
	private $_image_instance; // once opened or created, there's an image instance that needs to be referenced.
	private $_debug;
	private $_interface; // GD or ImageMagick or GMagick
	private $_default_format; // png, or jpg, or something else 
	
	public function __construct($default_format, $interface, $debug=true){
		
		if(!in(strtolower($default_format), array('png','jpg','gif'))){
			die('Unsupported default output format. Choose png, jpg, or gif.');
		}
		
		if(!in(ucfirst($interface), array('Gd','Imagick','Gmagick'))){
			die('Unsupported interface type. Choose Gd, Imagick, or Gmagick.');
		}
		
		$this->_default_format = strtolower($default_format);
		$this->_interface = ucfirst($interface);
		$this->_debug = $debug;
	}
	
	public function getImagineObject(){
		return $this->_imagine_instance;
	}
	
	public function getImageObject(){
		return $this->_image_instance;
	}
	
	public function create($width, $height, $background_color=0, $background_alpha=100){
		
// 		$total_size = 0;
		
// 		// I'm confused why this memory management doesnt work because the script only needs 25165824 bytes, 
// 		// but wouldnt work without allocating ~ 43349391 bytes. 
// 		/*
// 		 * 
// this is the current memory usage
// 2359296
// this is the total memory about to be allocated
// 43349391.36
// this is the total memory usage after the canvas has been drawn
// 25165824
// this is the total memory about to be allocated after calculating the size of the images to be merged
// 61279432
// this is the total memory usage after the images have been merged
// 48234496
// 		 * 
// 		 */
		
// 		$total_size = ($width * $height * 4); // height x width x rgba
// 		pr('this is the total memory that needs to be allocated to just the canvas');
// 		pr($total_size);
		
// 		$total_size = ($width * $height * 4) * 2.28; 
// 		pr('this is the amount of memory the canvas thinks it needs');
// 		pr($total_size);
		
// 		pr('this is the current memory usage');
// 		pr(memory_get_usage(true));
		
// 		$total_size += memory_get_usage(true);
		
// 		pr('this is the total memory about to be allocated');
// 		pr($total_size);
		
// 		ini_set('memory_limit', $total_size);
		
		switch($this->_interface){
			case 'Gd':
				$this->_imagine_instance = new Imagine\Gd\Imagine();
				break;
			case 'Imagick':
				$this->_imagine_instance = new Imagine\Imagick\Imagine();
				break;
			case 'Gmagick':
				$this->_imagine_instance = new Imagine\Gmagick\Imagine();
				break;
		}
		
		if(empty($background_color)){
			$this->_image_instance = $this->_imagine_instance->create( new Imagine\Image\Box($width, $height) );
		} else {
			$this->_image_instance = $this->_imagine_instance->create(
				new Imagine\Image\Box($width, $height),
				new Imagine\Image\Color($background_color, $background_alpha)
			);
		}
		
// 		pr('this is the total memory usage after the canvas has been drawn');
// 		pr(memory_get_usage(true));
		
		return $this;
	}
	
	public function layerImagesFromBinary($images = array(), $starting_positions = array()){
		
		if(!($this->_image_instance instanceof Imagine\Image\ImageInterface)){ die('Base image must be created to use layering. Call create method first.'); }
		
		$image_objects = array();
		
		foreach($images as $pos => $binary){
			$image_objects[$pos] = $this->_imagine_instance->load($binary);
			if(isset($starting_positions[$pos])){
				if(isset($starting_positions[$pos]['x'])){
					$starting_positions[$pos]['x'] = 0;
				}
				if(isset($starting_positions[$pos]['y'])){
					$starting_positions[$pos]['y'] = 0;
				}
			} else {
				$starting_positions[$pos] = array('x'=>0,'y'=>0);
			}
		}
		
		foreach($image_objects as $pos => $Image){
			$this->_image_instance->paste($Image, new Imagine\Image\Point($starting_positions[$pos]['x'], $starting_positions[$pos]['y']));
		}
		
		return $this;
	}
	
	public function layerImagesFromPaths($paths = array(), $starting_positions = array()){
		
		if(!($this->_image_instance instanceof Imagine\Image\ImageInterface)){ die('Base image must be created to use layering. Call create method first.'); }
		
		$image_objects = array();
		
// 		$total_size = 0;
// 		$size = array();
// 		foreach($paths as $pos => $path){
// 			$size = getimagesize($path);
// 			$total_size += ($size[0] * $size[1] * 4);
// 		}
		
// 		$ImageArea = $this->_image_instance->getSize();
// 		$total_size += ($ImageArea->getHeight() * $ImageArea->getWidth() * 4);
		
// 		$total_size += memory_get_usage(true);
		
// 		pr('this is the total memory about to be allocated after calculating the size of the images to be merged');
// 		pr($total_size);
		
// 		ini_set('memory_limit', $total_size);
		
		foreach($paths as $pos => $path){
			$image_objects[$pos] = $this->_imagine_instance->open($path);
			if(isset($starting_positions[$pos])){
				if(isset($starting_positions[$pos]['x'])){
					$starting_positions[$pos]['x'] = 0;
				}
				if(isset($starting_positions[$pos]['y'])){
					$starting_positions[$pos]['y'] = 0;
				}
			} else {
				$starting_positions[$pos] = array('x'=>0,'y'=>0);
			}
		}
		
		foreach($image_objects as $pos => $Image){
			$this->_image_instance->paste($Image, new Imagine\Image\Point($starting_positions[$pos]['x'], $starting_positions[$pos]['y']));
		}
		
// 		pr('this is the total memory usage after the images have been merged');
// 		pr(memory_get_usage(true));
				
		return $this;
	}
	
	public function merge($top_image_path, $bottom_image_path, $top_x=0, $top_y=0, $bottom_x=0, $bottom_y=0){
		
		if(!($this->_image_instance instanceof Imagine\Image\ImageInterface)){ die('Base image must be created to use layering. Call create method first.'); }
		
		$top_image = $this->_imagine_instance->open($top_image_path);
		$bottom_image = $this->_imagine_instance->open($bottom_image_path);
		
		$this->_image_instance
			->paste($bottom_image, new Imagine\Image\Point($bottom_x, $bottom_y))
			->paste($bottom_image, new Imagine\Image\Point($top_x, $top_y));
		
		return $this;
	}
	
	public function load($path){
		switch($this->_interface){
			case 'Gd':
				$this->_imagine_instance = new Imagine\Gd\Imagine();
				break;
			case 'Imagick':
				$this->_imagine_instance = new Imagine\Imagick\Imagine();
				break;
			case 'Gmagick':
				$this->_imagine_instance = new Imagine\Gmagick\Imagine();
				break;
		}
		
		$this->_image_instance = $this->_imagine_instance->open($path);
		
		return $this;
	}
	
	public function rotate($angle, $background_color=0, $background_alpha=100){
		if(empty($background_color)){
			$this->_image_instance->rotate($angle);
		} else {
			$this->_image_instance->rotate(
				$angle, 
				new Imagine\Image\Color($background_color, $background_alpha)
			);
		}
		return $this;
	}
	
	public function flip($type){
		switch($type){
			case self::FLIP_HORIZONAL:
				$this->_image_instance->flipHorizontally();
				break;
			case self::FLIP_VERTICAL:
				$this->_image_instance->flipVertically();
				break;
			case self::FLIP_BOTH:
				$this->_image_instance->flipHorizontally();
				$this->_image_instance->flipVertically();
				break;
		}
		return $this;
	}
	
	public function resize($new_width, $new_height, $crop_during_resize=true){
		if($crop_during_resize){
			$mode = Imagine\Image\ImageInterface::THUMBNAIL_INSET; // don't crop during resize
		} else {
			$mode = Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND; // do crop during resize to fill area
		}

		$this->_image_instance->thumbnail(new Imagine\Image\Box($new_width, $new_height), $mode);
		
		return $this;
	}
	
	public function crop($from_x, $from_y, $new_width, $new_height){
		$this->_image_instance->crop(
			new Imagine\Image\Point($from_x,$from_y), 
			new Imagine\Image\Box($new_width,$new_height)
		);
		return $this;
	}
	
	public function save($save_path, $quality=null, $destroy=true){
		$options = array();
		
		$path = str_replace('\\', '/', $save_path);
		$parts = explode('/',$path);
		if(count($parts) > 1){ $file_name = array_pop($parts); }
		else { $file_name = $path; }
		$parts = explode('.',$file_name);
		if(count($parts) > 1){ $file_type = array_pop($parts); }
		else { $file_type = $path; }
		
		switch($file_type){
			case 'jpg':
			case 'jpeg':
				$options['format'] = 'jpg';
				break;
			case 'png':
				$options['format'] = 'png';
				break;
			case 'gif':
				$options['format'] = 'gif';
				break;
			default:
				$options['format'] = $this->_default_format;
				break;
		}
		
		if(!is_null($quality)){ $options['quality'] = $quality; }
		
		$this->_image_instance->save($save_path, $options);
		if($destroy){ unset($this->_image_instance); }
		return $this;
	}
	
	public function show($format=null, $quality=null, $destroy=true){
		$options = array();
		$options['format'] = !is_null($format)?$format:$this->_default_format;
		if(!is_null($quality)){ $options['quality'] = $quality; }
		
		$this->_image_instance->show($options['format'], $options);
		if($destroy){ unset($this->_image_instance); }
		return $this;
	}
			
	// what?
// 	public function restore(){
// 		return $this;
// 	}
	
	public function destroy(){
		unset($this->_image_instance);
		return $this;
	}
	
}
