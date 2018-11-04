<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 04.11.18
 * Time: 18:43
 */

namespace utils;


class Converter
{
    const MAX_SHORT_URL_LENGTH = 10;

    private $digits = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';


    /**
     * @param $id
     * @return string
     * @throws ConverterException
     */
    public function dec2link($id) {
        if ($id < 0) {
            throw new ConverterException('Id must contain unsigned value.');
        }
        $link = '';
        $count = count(str_split($this->digits));

        do {
            $dig = $id%$count;
            $link = $this->digits[$dig] . $link;
            $id = floor($id / $count);
        } while($id != 0);

        return $link;
    }

    /**
     * @param $link
     * @return int
     * @throws ConverterException
     */
    function link2dec($link) {
        if (strlen($link) > self::MAX_SHORT_URL_LENGTH) {
            throw new ConverterException('Too many characters in short url.');
        }
        $digits = array_flip(str_split($this->digits));
        $id = 0;

        for ($i = 0; $i < strlen($link); $i++) {
            if (strpos($this->digits, $link[(strlen($link) - $i - 1)]) === false) {
                throw new ConverterException('Unexpected char in short link.');
            }
            $id += $digits[$link[(strlen($link) - $i - 1)]] * pow(count($digits),$i);
        }

        return $id;
    }
}