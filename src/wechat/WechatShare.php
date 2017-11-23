<?php
    //$url = $_SERVER['HTTP_REFERER'];
    //    $appInfo = array (
    //        'appId'     => "wx8d442",
    //        'appSecret' => "b9a5c05f3216ce"
    //    );
    //    $jssdkObj = new JSSDK( $appInfo );

    namespace artecfeng\Utils\wechat;

    /**
     * Class WechatShare
     * @package wechat
     *微信分享类
     */
    class WechatShare {
        private $appId;
        private $appSecret;

        public function __construct ($appInfo) {
            $this->appId = $appInfo['appId'];
            $this->appSecret = $appInfo['appSecret'];
        }

        public function share ($url) {
            date_default_timezone_set( 'PRC' );//设置默认时区
            $signPackage = $this->getSignPackage( $url );
            if (!$signPackage) {
                echo json_encode( array (
                    'data' => '',
                    'type' => 'error'
                ) );
                exit;
            }
            if (isset( $_GET['callback'] )) {
                echo $_GET['callback'], '(';
            }
            echo json_encode( array (
                'data' => $signPackage,
                'type' => 'success'
            ) );
            if (isset( $_GET['callback'] )) {
                echo ')';
            }
        }

        public function getSignPackage ($url = false) {
            $jsapiTicket = $this->getJsApiTicket();
            $protocol = ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) ? "https://" : "http://";
            if (!$url) {
                $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            }
            //echo $url;
            $timestamp = time();
            $nonceStr = $this->createNonceStr();

            // 这里参数的顺序要按照 key 值 ASCII 码升序排序
            $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

            $signature = sha1( $string );

            $signPackage = array (
                "appId"     => $this->appId,
                "nonceStr"  => $nonceStr,
                "timestamp" => $timestamp,
                "url"       => $url,
                "signature" => $signature,
                "rawString" => $string
            );

            return $signPackage;
        }

        private function createNonceStr ($length = 16) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $str = "";
            for ($i = 0; $i < $length; $i++) {
                $str .= substr( $chars, mt_rand( 0, strlen( $chars ) - 1 ), 1 );
            }

            return $str;
        }

        private function getJsApiTicket () {
            // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
            // $data = json_decode( file_get_contents( "http://k-ad.cn/bg/share/jsapi_ticket.json" ) );
            $data = json_decode( file_get_contents( "./jsapi_ticket.json" ) );
            //print_r($data->expire_time.'|||||'.time())
            if ($data->expire_time < time()) {
                $accessToken = $this->getAccessToken();
                // 如果是企业号用以下 URL 获取 ticket
                //$url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=".$accessToken;
                $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=" . $accessToken;
                $res = json_decode( $this->httpGet( $url ) );
                $ticket = $res->ticket;
                if ($ticket) {
                    $data->expire_time = time() + 7000;
                    $data->jsapi_ticket = $ticket;
                    file_put_contents( './jsapi_ticket.json', json_encode( $data ) );
                }
            } else {
                $ticket = $data->jsapi_ticket;
            }

            return $ticket;
        }

        private function getAccessToken () {
            // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
            //$data = json_decode( file_get_contents( "http://k-ad.cn/bg/share/access_token.json" ) );
            $data = json_decode( file_get_contents( "./access_token.json" ) );
            if ($data->expire_time < time()) {
                // 如果是企业号用以下URL获取access_token
                //$url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$this->appId."&corpsecret=".$this->appSecret;
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appId . "&secret=" . $this->appSecret;
                $res = json_decode( $this->httpGet( $url ) );
                $access_token = $res->access_token;
                if ($access_token) {
                    $data->expire_time = time() + 7000;
                    $data->access_token = $access_token;
                    file_put_contents( './access_token.json', json_encode( $data ) );
                }
            } else {
                $access_token = $data->access_token;
            }

            return $access_token;
        }

        private function httpGet ($url) {
            $curl = curl_init();
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $curl, CURLOPT_TIMEOUT, 500 );
            curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
            curl_setopt( $curl, CURLOPT_URL, $url );

            $res = curl_exec( $curl );
            curl_close( $curl );

            return $res;
        }
    }