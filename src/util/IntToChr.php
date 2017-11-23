<?php
    /**
     * Created by PhpStorm.
     * User: feng
     * Date: 2017/3/7
     * Time: 11:47
     */

    namespace artecfeng\Utils\util;


    class IntToChr {

        public static $instance = null;

        private function __clone () {

        }

        public static function getInstance () {
            if (is_null( self::$instance )) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        /**
         * 数字转字母 （类似于Excel列标）
         *
         * @param Int $index 索引值
         * @param Int $start 字母起始值
         *
         * @return 数字转字母
         * @author feng
         * @date
         */
        public function itoc ($index, $start = 65) {

            $str = '';
            if (floor( $index / 26 ) > 0) {
                $str .= IntToChr( floor( $index / 26 ) - 1 );
            }

            return $str . chr( $index % 26 + $start );
        }

        /**
         * @param $char
         *
         * @explain 字母转数字
         *
         * @return 字母转数字
         */
        public function ctoi ($char) {

            $array = array (
                'a',
                'b',
                'c',
                'd',
                'e',
                'f',
                'g',
                'h',
                'i',
                'j',
                'k',
                'l',
                'm',
                'n',
                'o',
                'p',
                'q',
                'r',
                's',
                't',
                'u',
                'v',
                'w',
                'x',
                'y',
                'z'
            );
            $len = strlen( $char );
            $sum = '';
            for ($i = 0; $i < $len; $i++) {
                $index = array_search( $char[$i], $array );
                $sum += ( $index + 1 ) * pow( 26, $len - $i - 1 );
            }

            return $sum;
        }
    }