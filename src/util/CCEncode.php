<?php

    namespace artecfeng\Utils\util;

    /**
     * Class CCEncode
     * @package Util
     */
    class CCEncode {

        public static $instance = null;

        public static function getInstance () {
            if (is_null( self::$instance )) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        private function __clone () {

        }

        protected function getByte ($data) {
            $length = strlen( $data );
            for ($i = 0; $i < $length; $i++) {
                $tmpList[] = ord( $data{$i} );
            }

            return $tmpList;
        }

        protected function getChar ($data, $string = '') {
            $length = count( $data );
            foreach ($data as $value) {
                $string .= chr( $value );
            }

            return $string;
        }

        /**
         * @param $data
         * @param $key
         *
         * @return 编码
         */
        public function encrypt ($data, $key) {
            $dataArr = $this->getByte( $data );
            $keyArr = $this->getByte( $key );
            $lengthA = count( $dataArr );
            $lengthB = count( $keyArr );
            for ($i = 0; $i < $lengthA; $i++) {
                $tmpList[] = ( 0xFF & $dataArr[$i] ) + ( 0xFF & $keyArr[$i % $lengthB] );
            }

            return implode( '@', $tmpList );
        }

        /**
         * @param $data
         * @param $key
         *
         * @return 解码
         */
        public function decrypt ($data, $key) {
            $dataArr = explode( '@', $data );
            $keyArr = $this->getByte( $key );
            $lengthA = count( $dataArr );
            $lengthB = count( $keyArr );
            for ($i = 0; $i < $lengthA; $i++) {
                $tmpList[] = $dataArr[$i] - ( 0xFF & $keyArr[$i % $lengthB] );
            }

            return $this->getChar( $tmpList );
        }

    }