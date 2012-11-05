<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/


/*
 * Untested code. I just wrote this wrapper for imagine,
 * because it's the interface i'd like for editing images (minus the layer merging from one file to another).
 * This needs better error handling for sure. There should be try catches that behave based on debug settings
 */

class VideoAbstraction {
	
	private $_video_instance; // once opened or created, there's an image instance that needs to be referenced.
	
	private $_path;
	private $_width;
	private $_height;
	private $_duration;
	private $_audio_bit_rate;
	private $_video_bit_rate;
	private $_sample_rate;
	private $_fps;
	
	public function getFFMPEGInstance(){
		return $this->_video_instance;
	}
	
	public function getPath(){ return $this->_path; }
	public function getWidth(){ return $this->_width; }
	public function getHeight(){ return $this->_height; }
	public function getDuration(){ return $this->_duration; }
	public function getAudioBitrate(){ return $this->_audio_bit_rate; }
	public function getVideoBitrate(){ return $this->_video_bit_rate; }
	public function getSampleRate(){ return $this->_sample_rate; }
	public function getFPS(){ return $this->_fps; }
	
	private $_debug;
	
	public function __construct($debug=true){
		
		$this->_debug = $debug;
	}
	
	public function load($path){
		
		if(!file_exists($path)){ throw new Exception('Video file could not be loaded at path: '.$path); }
		
		$this->_path = $path;
		
		$this->_video_instance = new ffmpeg_movie($this->_path);
		$this->_video_instance->getDuration(); // Gets the duration in secs.
		$this->_video_instance->getVideoCodec(); // What type of compression/codec used
		
		// movie width/height
		$this->_width = $this->_video_instance->getFrameWidth();
		$this->_height = $this->_video_instance->getFrameHeight();
		// correction calculations
		$this->_width = gettype($this->_width/2) == "integer"?$this->_width:($this->_width-1);
		$this->_height = gettype($this->_height/2) == "integer"?$this->_height:($this->_height-1);
		
		$this->_duration = $this->_video_instance->getDuration();
	
		$this->_fps = $this->_video_instance->getFrameRate();
		$this->_audio_bit_rate = intval($this->_video_instance->getAudioBitRate()/1000);
		$this->_video_bit_rate = $this->_video_instance->getBitRate();
		$this->_sample_rate = $this->_video_instance->getAudioSampleRate();
			
		return $this;
	}
	
	public function saveAsH264($save_path){
		shell_exec('/usr/bin/ffmpeg -i '.$this->_path.' -acodec libfaac -ab '.$this->_audio_bit_rate.'k -vcodec libx264 -vpre slow -b '.$this->_video_bit_rate.' -r '.$this->_fps.' -threads 0 '.$save_path.' 2>&1');
		return $this;
	}
	
	public function saveThumbnail($save_path, $quality=null){
		shell_exec('ffmpeg -i '.$this->_path.' -deinterlace -an -ss 1 -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg '.$save_path.' 2>&1');
		return $this;
	}
	
	// @todo need time to put this together correctly.
// 	public function saveAsAnimatedGif($save_path, $frame_rate, $loop_count){
// 		$AnimatedGif = new FFmpegMovie();
// 		$AnimatedGif = new FFmpegAnimatedGif($save_path, $this->_width, $this->_height, $frame_rate, $loop_count);
// 	}
	
	public function clear(){
		
		$this->_path = null;
		$this->_width = null;
		$this->_height = null;
		$this->_duration = null;
		$this->_audio_bit_rate = null;
		$this->_video_bit_rate = null;
		$this->_sample_rate = null;
		$this->_fps = null;
		
		return $this;
	}
	
}
