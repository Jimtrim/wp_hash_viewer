<?php

class HashViewerTest extends PHPUnit_Framework_TestCase
{
    public function testFilterHashtags()
    {
        $this->assertEquals(self::filterHashtags("#mittsteinkjer"), '["mittsteinkjer"]');
    }

    private function filterHashtags($tag) {
    	return "";
    }
}
?>