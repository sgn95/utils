<?php
    /**
     * Created by PhpStorm.
     * User: feng
     * Date: 2017/11/22
     * Time: 17:42
     */

    namespace artecfeng\Utils;

    use artecfeng\Utils\util\CCEncode;
    use artecfeng\Utils\util\CheckIdCard;
    use artecfeng\Utils\util\IntToChr;
    use artecfeng\Utils\util\JianFan;
    use artecfeng\Utils\util\PinYin;
    use artecfeng\Utils\util\Tool;
    use artecfeng\Utils\wechat\WxApi;
    use artecfeng\Utils\wechat\WechatShare;

    class UtilsFactory {
        public static function create ($className) {
            try {
                return $className::getInstance();
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }