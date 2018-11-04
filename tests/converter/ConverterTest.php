<?php

class ConverterTest extends \PHPUnit_Framework_TestCase
{


    public function testRandomIdsEqual()
    {
        $converter = new \utils\Converter();

        for ($i = 0; $i < 1000; $i++) {
            $id = mt_rand(0, 100000000);
            $link = $converter->dec2link($id);
            $dec = $converter->link2dec($link);
            $this->assertTrue($dec === $id);
        }
    }

    public function testWrongIds()
    {
        $converter = new \utils\Converter();
        $this->setExpectedException('\utils\ConverterException');
        $converter->dec2link(-1);
    }

    public function testWrongLinkChar()
    {
        $converter = new \utils\Converter();
        $this->setExpectedException('\utils\ConverterException');
        $converter->link2dec('G{P');
    }

    public function testLongLink()
    {
        $converter = new \utils\Converter();
        $this->setExpectedException('\utils\ConverterException');
        $converter->link2dec('FFFsjkkhka98wer9w9e8rohskdj');
    }


} 