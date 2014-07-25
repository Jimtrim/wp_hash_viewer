<?php

class HashViewerTest extends PHPUnit_Framework_TestCase
{
	private $viewer;
	public function before() {
	}	

	public function testFilterHashtags()
	{
		require_once('classes/HashViewer.class.php');
		require_once('hash-viewer.php');
		$this->viewer = HashViewer::get_instance(); 
		$this->assertEquals($this->viewer->filter_hashtags("mittsteinkjer"), '"mittsteinkjer"');
		$this->assertEquals($this->viewer->filter_hashtags("	 mittsteinkjer	 "), '"mittsteinkjer"');
		$this->assertEquals($this->viewer->filter_hashtags("#mittsteinkjer"), '"mittsteinkjer"');
		$this->assertEquals($this->viewer->filter_hashtags("mittsteinkjer ,ukm"), '"mittsteinkjer"');
		$this->assertEquals($this->viewer->filter_hashtags("mittsteinkjer, ukm"), '"mittsteinkjer"');
	}
}
?>