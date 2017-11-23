<?php
    /**
     * Created by PhpStorm.
     * User: feng
     * Date: 2017/3/7
     * Time: 15:23
     */

    namespace artecfeng\Utils\util;


    class Tool {
        public static $instance = null;

        public static function getInstance () {
            if (is_null( self::$instance )) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        private function __clone () {

        }

        /**
         * @return 生成唯一id
         * @explan 生成唯一id
         */
        public function generateNum () {
            //strtoupper转换成全大写的
            $charid = strtoupper( md5( uniqid( mt_rand(), true ) ) );
            $uuid = substr( $charid, 0, 8 ) . substr( $charid, 8, 4 ) . substr( $charid, 12, 4 ) . substr( $charid, 16, 4 ) . substr( $charid, 20, 12 );

            return $uuid;
        }

        /**
         * @param $filename
         *强制下载类
         *
         * @return 强制下载类
         */
        public function download ($filename) {
            if (( isset( $filename ) ) && ( file_exists( $filename ) )) {
                header( "Content-length: " . filesize( $filename ) );
                header( 'Content-Type: application/octet-stream' );
                header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
                readfile( "$filename" );
            } else {
                return false;
            }
        }

        /**
         * 字符串截取，支持中文和其他编码
         *
         * @param  [string] $str  [字符串]
         * @param integer $start   [起始位置]
         * @param integer $length  [截取长度]
         * @param string  $charset [字符串编码]
         * @param boolean $suffix  [是否有省略号]
         *
         * @return 字符串截取，支持中文和其他编码
         */
        public function msubstr ($str, $start = 0, $length = 15, $charset = "utf-8", $suffix = true) {
            if (function_exists( "mb_substr" )) {
                return mb_substr( $str, $start, $length, $charset );
            } elseif (function_exists( 'iconv_substr' )) {
                return iconv_substr( $str, $start, $length, $charset );
            }
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all( $re[$charset], $str, $match );
            $slice = join( "", array_slice( $match[0], $start, $length ) );
            if ($suffix) {
                return $slice . "…";
            }

            return $slice;
        }

        /**
         * @param $key
         * @param $string
         * @param $decrypt
         *
         * @return 加解密函数
         */
        public function encryptDecrypt ($key, $string, $decrypt) {
            if ($decrypt) {
                $reString = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $key ), base64_decode( $string ), MCRYPT_MODE_CBC, md5( md5( $key ) ) ), "12" );
            } else {
                $reString = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $key ), $string, MCRYPT_MODE_CBC, md5( md5( $key ) ) ) );
            }

            return $reString;
        }

        /**
         *
         * @param [type] $num [description]
         *
         * @return 数字转人民币
         */
        public function numtormb ($num) {
            $c1 = "零壹贰叁肆伍陆柒捌玖";
            $c2 = "分角元拾佰仟万拾佰仟亿";
            $num = round( $num, 2 );
            $num = $num * 100;
            if (strlen( $num ) > 10) {
                return false;
            }
            $i = 0;
            $c = "";
            while (1) {
                if ($i == 0) {
                    $n = substr( $num, strlen( $num ) - 1, 1 );
                } else {
                    $n = $num % 10;
                }
                $p1 = substr( $c1, 3 * $n, 3 );
                $p2 = substr( $c2, 3 * $i, 3 );
                if ($n != '0' || ( $n == '0' && ( $p2 == '亿' || $p2 == '万' || $p2 == '元' ) )) {
                    $c = $p1 . $p2 . $c;
                } else {
                    $c = $p1 . $c;
                }
                $i = $i + 1;
                $num = $num / 10;
                $num = (int)$num;
                if ($num == 0) {
                    break;
                }
            }
            $j = 0;
            $slen = strlen( $c );
            while ($j < $slen) {
                $m = substr( $c, $j, 6 );
                if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                    $left = substr( $c, 0, $j );
                    $right = substr( $c, $j + 3 );
                    $c = $left . $right;
                    $j = $j - 3;
                    $slen = $slen - 3;
                }
                $j = $j + 3;
            }
            if (substr( $c, strlen( $c ) - 3, 3 ) == '零') {
                $c = substr( $c, 0, strlen( $c ) - 3 );
            } // if there is a '0' on the end , chop it out
            return $c;
        }


    }