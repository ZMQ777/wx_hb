<?php
class Wxapi {
    private $app_id = ''; //appid
    private $app_mchid = '';//商户号
    private $parinerkey ='';//商户支付密匙
    function __construct(){
    //do sth here....
    }
    /**
     * 微信支付
     *
     * @param string $openid 用户openid
     */
    public function pay($re_openid, $money, $wishing)
    {
        include_once('WxHongBaoHelper.php');
        $wxHongBaoHelper = new WxHongBaoHelper($this->parinerkey);

        $wxHongBaoHelper->setParameter("nonce_str", $this->great_rand());//随机字符串，丌长于 32 位
        $wxHongBaoHelper->setParameter("mch_billno", $this->app_mchid.date('YmdHis').rand(1000, 9999));//订单号
        $wxHongBaoHelper->setParameter("mch_id", $this->app_mchid);//商户号
        $wxHongBaoHelper->setParameter("wxappid", $this->app_id);
        $wxHongBaoHelper->setParameter("nick_name", 'XXX');//提供方名称
        $wxHongBaoHelper->setParameter("send_name", 'XXXX');//红包发送者名称
        $wxHongBaoHelper->setParameter("re_openid", $re_openid);//相对于医脉互通的openid
        $wxHongBaoHelper->setParameter("total_amount", $money);//付款金额，单位分
        $wxHongBaoHelper->setParameter("min_value", $money);//最小红包金额，单位分
        $wxHongBaoHelper->setParameter("max_value", $money);//最大红包金额，单位分
        $wxHongBaoHelper->setParameter("total_num", 1);//红包収放总人数
        $wxHongBaoHelper->setParameter("wishing", $wishing);//红包祝福诧
        $wxHongBaoHelper->setParameter("client_ip", '127.0.0.1');//调用接口的机器 Ip 地址
        $wxHongBaoHelper->setParameter("act_name", '红包');//活劢名称
        $wxHongBaoHelper->setParameter("remark", '快来抢！');//备注信息
        $postXml = $wxHongBaoHelper->create_hongbao_xml();

        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $responseXml = $wxHongBaoHelper->curl_post_ssl($url, $postXml);
		$responseObj = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
		return $responseObj;

    }


    /**
     * Http方法
     *
     */
    public function http($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $output = curl_exec($ch);//输出内容
        curl_close($ch);
        return array($output);
    }

    /**
     * 生成随机数
     *
     */
    public function great_rand(){
        $str = '1234567890abcdefghijklmnopqrstuvwxyz';
        $t1 = '';
        for($i=0;$i<30;$i++){
            $j=rand(0,35);
            $t1 .= $str[$j];
        }
        return $t1;
    }
}
?>
