<?php

class photoHandler {

	private $ah;
	private $targets = array();
	private $source = '';
	private $err = false;
	private $filename;

	
	function __construct( $ah ) {
		
		$this->ah = $ah;
		
	}	
	
	public function source( $source ) {
	
		$this->source = $source;
		
		$this->makeNewFileName();
		
		if( !$this->isJpg() ) $this->ah->err( 'The file does not appear to be a valid jpg image.' );
	
	}
	
	public function addTarget( $path, $width=false, $height=false) {
	
		if( $this->err ) return false;
	
		if( !empty( $path ) ) $this->targets[$path . $this->filename] = array( 'width' => $width, 'height' => $height );
		
	}
		
	public function validateSize( $width, $height, $fixed=false ) {
	
		if( $this->err ) return false;
		
		$tmp = getimagesize( $this->source );		
		
		if( $fixed ) {
		
			if( $tmp[0] != $width && $tmp[1] != $height ) {
				
				$this->ah->err( "The photo is the incorrect size, the photo needs to be EXACTLY $width pixels wide and $height pixels high." );
				$this->err = true;
				
			}
			
		}
		else{
		
			if( $tmp[0] < $width && $tmp[1] <  $height ) {
				
				$this->ah->err( "The photo is the incorrect size, the photo needs to be AT LEAST $width pixels wide and $height pixels high." );
				$this->err = true;
				
			}
		
		
		}
		
		
	}
	
	public function getFileName() {
		
		return $this->filename;
	
	}
	
	public function process() {
	
		if( $this->err ) return false;
				
		foreach( $this->targets AS $path => $dimensions ) {
			
			$this->doResize( $path, $dimensions['width'], $dimensions['height'] );
	
		}		
		
		unlink( $this->source );
		
		return true;
	
	}
	
	private function isJpg() {
	
		if( !imagecreatefromjpeg( $this->source ) ) return false;
		else return true;
	
	}
		
	private function makeNewFileName() {
	
		$this->filename =  md5( time() . $this->source . rand( 1, 999999999 ) ) . '.jpg';
	
	}
	
	private function doResize( $savePath, $width, $height ) {
		
		if( !$width && !$height ) copy( $this->source, $savePath );
		else{ 
			
			$tgt = imagecreatetruecolor( $width, $height );
			$src = imagecreatefromjpeg( $this->source );
			$tmp = getimagesize( $this->source );
			
			imagecopyresampled( $tgt, $src, 0, 0, 0, 0, $width, $height, $tmp[0], $tmp[1] );
			imagejpeg( $tgt, $savePath, 100 );
			
		}
	
	}	
	
}

?>