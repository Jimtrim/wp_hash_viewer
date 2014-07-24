<?php

class HashViewerTest extends PHPUnit_Framework_TestCase
{
	private $viewer;
	public function before() {
		require_once('../classes/InstagramHashViewer.class.php');
		$this->viewer = InstagramHashViewer::get_instance(); 
	}

	public function testFilterHashtags()
	{
		$this->assertEquals($this->viewer("mittsteinkjer"), '"mittsteinkjer"');
		$this->assertEquals($this->viewer("#mittsteinkjer"), '"mittsteinkjer"');
		$this->assertEquals($this->viewer("mittsteinkjer ,ukm"), '"mittsteinkjer,ukm"');
		$this->assertEquals($this->viewer("mittsteinkjer, ukm"), '"mittsteinkjer,ukm"');
	}
}
?>