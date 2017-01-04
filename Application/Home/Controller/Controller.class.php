<?php
/***
 *
 *                        ,%%%%%%%%,
 *                      ,%%/\%%%%/\%%
 *                     ,%%%\c "" J/%%%
 *            %.       %%%%/ o  o \%%%
 *            `%%.     %%%%    _  |%%%
 *             `%%     `%%%%(__Y__)%%'
 *             //       ;%%%%`\-/%%%'
 *            ((       /  `%%%%%%%'
 *             \\    .'          |
 *              \\  /       \  | |
 *               \\/         ) | |
 *                \         /_ | |__
 *                (___________))))))) 攻城狮
 *
 * @author：gaoyuan
 * @modified_time：2016/11/19 10:47
 * When I wrote this, only God and I understood what I was doing
 * Now, God only knows
 */

namespace Home\Controller;


class Test extends  HomeController
{

    function execute_curl($url = '', $method = '', $param = ''){
        $this_header = array(
            "content-type: application/x-www-form-urlencoded;
        charset=UTF-8"
        );

        $opts = array(
            CURLOPT_TIMEOUT        => 6000,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL            => $url,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0',
            CURLOPT_FOLLOWLOCATION            => 1,
            CURLOPT_AUTOREFERER            => 1,
            CURLOPT_HTTPHEADER=>$this_header,
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_SSL_VERIFYHOST=>false
        );
        if($method === 'post'){
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $param;
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);

        $error = curl_error($ch);
        curl_close($ch);
        $data = json_decode($data,true);
        return $data;
    }
}