<?php

namespace App\Utils;

use App\Models\User;
use App\Services\Config;

class Tools
{

    /**
     * 根据流量值自动转换单位输出
     */
    static function flowAutoShow($value = 0)
    {
        $kb = 1024;
        $mb = 1048576;
        $gb = 1073741824;
        if ($value > $gb) {
            return round($value / $gb, 2) . "GB";
        } else if ($value > $mb) {
            return round($value / $mb, 2) . "MB";
        } else if ($value > $kb) {
            return round($value / $kb, 2) . "KB";
        } else {
            return round($value, 2);
        }
    }

    static function toMB($traffic){
        $mb = 1048576;
        return $traffic*$mb;
    }

    static function toGB($traffic){
        $gb = 1048576*1024;
        return $traffic*$gb;
    }

    //获取随机字符串
    static function genRandomChar( $length = 8 ) {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $char = '';
        for ( $i = 0; $i < $length; $i++ )
        {
            $char .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $char;
    }

    // Unix time to Date Time
    static function toDateTime($time){
        return date('Y-m-d H:i:s',$time);
    }

    // check html
    static function checkHtml($html) {
        $html = stripslashes($html);
        preg_match_all("/<([^<]+)>/is", $html, $ms);
        $searchs[] = '<';
        $replaces[] = '<';
        $searchs[] = '>';
        $replaces[] = '>';
        if($ms[1]) {
            $allowtags = 'img|a|font|div|table|tbody|caption|tr|td|th|br
						|p|b|strong|i|u|em|span|ol|ul|li|blockquote
						|object|param|embed';//允许的标签
            $ms[1] = array_unique($ms[1]);
            foreach ($ms[1] as $value) {
                $searchs[] = "<".$value.">";
                $value = shtmlspecialchars($value);
                $value = str_replace(array('/','/*'), array('.','/.'), $value);
                $skipkeys = array(
                    'onabort','onactivate','onafterprint','onafterupdate',
                    'onbeforeactivate','onbeforecopy','onbeforecut',
                    'onbeforedeactivate','onbeforeeditfocus','onbeforepaste',
                    'onbeforeprint','onbeforeunload','onbeforeupdate',
                    'onblur','onbounce','oncellchange','onchange',
                    'onclick','oncontextmenu','oncontrolselect',
                    'oncopy','oncut','ondataavailable',
                    'ondatasetchanged','ondatasetcomplete','ondblclick',
                    'ondeactivate','ondrag','ondragend',
                    'ondragenter','ondragleave','ondragover',
                    'ondragstart','ondrop','onerror','onerrorupdate',
                    'onfilterchange','onfinish','onfocus','onfocusin',
                    'onfocusout','onhelp','onkeydown','onkeypress',
                    'onkeyup','onlayoutcomplete','onload',
                    'onlosecapture','onmousedown','onmouseenter',
                    'onmouseleave','onmousemove','onmouseout',
                    'onmouseover','onmouseup','onmousewheel',
                    'onmove','onmoveend','onmovestart','onpaste',
                    'onpropertychange','onreadystatechange','onreset',
                    'onresize','onresizeend','onresizestart',
                    'onrowenter','onrowexit','onrowsdelete',
                    'onrowsinserted','onscroll','onselect',
                    'onselectionchange','onselectstart','onstart',
                    'onstop','onsubmit','onunload','javascript',
                    'script','eval','behaviour','expression',
                    'style','class'
                );
                $skipstr = implode('|', $skipkeys);
                $value = preg_replace(array("/($skipstr)/i"), '.', $value);
                if(!preg_match("/^[/|s]?($allowtags)(s+|$)/is", $value)) {
                    $value = '';
                }
                $replaces[] = empty($value)?'':"<".str_replace('"', '"', $value).">";
            }
        }
        $html = str_replace($searchs, $replaces, $html);
        $html = addslashes($html);
        return $html;
    }

    public static function genSID(){
        $unid =  uniqid(Config::get('key'));
        return Hash::sha256WithSalt($unid);
    }

    public static function getLastPort(){
        $user = User::orderBy('id', 'desc')->first();
        if ($user == null){
            return 1024; // @todo
        }
        return $user->port;
    }

    public static function getSurgeConf( $server, $port, $method, $password, $moduleUrl ) {
        $template = <<<EOF
[General]
skip-proxy = 192.168.0.0/16, 10.0.0.0/8, 172.0.0.0/8, localhost, *.local, e.crashlytics.com, *.jd.com
bypass-tun = 0.0.0.0/8, 1.0.0.0/9, 1.160.0.0/11, 1.192.0.0/11, 10.0.0.0/8, 14.0.0.0/11, 14.96.0.0/11, 14.128.0.0/11, 14.192.0.0/11, 27.0.0.0/10, 27.96.0.0/11, 27.128.0.0/9, 36.0.0.0/10, 36.96.0.0/11, 36.128.0.0/9, 39.0.0.0/11, 39.64.0.0/10, 39.128.0.0/10, 42.0.0.0/8, 43.224.0.0/11, 45.64.0.0/10, 47.64.0.0/10, 49.0.0.0/9, 49.128.0.0/11, 49.192.0.0/10, 54.192.0.0/11, 58.0.0.0/9, 58.128.0.0/11, 58.192.0.0/10, 59.32.0.0/11, 59.64.0.0/10, 59.128.0.0/9, 60.0.0.0/10, 60.160.0.0/11, 60.192.0.0/10, 61.0.0.0/10, 61.64.0.0/11, 61.128.0.0/10, 61.224.0.0/11, 100.64.0.0/10, 101.0.0.0/9, 101.128.0.0/11, 101.192.0.0/10, 103.0.0.0/10, 103.192.0.0/10, 106.0.0.0/9, 106.224.0.0/11, 110.0.0.0/7, 112.0.0.0/9, 112.128.0.0/11, 112.192.0.0/10, 113.0.0.0/9, 113.128.0.0/11, 113.192.0.0/10, 114.0.0.0/9, 114.128.0.0/11, 114.192.0.0/10, 115.0.0.0/8, 116.0.0.0/8, 117.0.0.0/9, 117.128.0.0/10, 118.0.0.0/11, 118.64.0.0/10, 118.128.0.0/9, 119.0.0.0/9, 119.128.0.0/10, 119.224.0.0/11, 120.0.0.0/10, 120.64.0.0/11, 120.128.0.0/11, 120.192.0.0/10, 121.0.0.0/9, 121.192.0.0/10, 122.0.0.0/7, 124.0.0.0/8, 125.0.0.0/9, 125.160.0.0/11, 125.192.0.0/10, 127.0.0.0/8, 139.0.0.0/11, 139.128.0.0/9, 140.64.0.0/11, 140.128.0.0/11, 140.192.0.0/10, 144.0.0.0/10, 144.96.0.0/11, 144.224.0.0/11, 150.0.0.0/11, 150.96.0.0/11, 150.128.0.0/11, 150.192.0.0/10, 152.96.0.0/11, 153.0.0.0/10, 153.96.0.0/11, 157.0.0.0/10, 157.96.0.0/11, 157.128.0.0/11, 157.224.0.0/11, 159.224.0.0/11, 161.192.0.0/11, 162.96.0.0/11, 163.0.0.0/10, 163.96.0.0/11, 163.128.0.0/10, 163.192.0.0/11, 166.96.0.0/11, 167.128.0.0/10, 167.192.0.0/11, 168.160.0.0/11, 169.254.0.0/16, 171.0.0.0/9, 171.192.0.0/11, 172.16.0.0/12, 175.0.0.0/9, 175.128.0.0/10, 180.64.0.0/10, 180.128.0.0/9, 182.0.0.0/8, 183.0.0.0/10, 183.64.0.0/11, 183.128.0.0/9, 192.0.0.0/24, 192.0.2.0/24, 192.88.99.0/24, 192.96.0.0/11, 192.160.0.0/11, 198.18.0.0/15, 198.51.100.0/24, 202.0.0.0/9, 202.128.0.0/10, 202.192.0.0/11, 203.0.0.0/9, 203.128.0.0/10, 203.192.0.0/11, 210.0.0.0/10, 210.64.0.0/11, 210.160.0.0/11, 210.192.0.0/11, 211.64.0.0/10, 211.128.0.0/10, 218.0.0.0/9, 218.160.0.0/11, 218.192.0.0/10, 219.64.0.0/11, 219.128.0.0/11, 219.192.0.0/10, 220.96.0.0/11, 220.128.0.0/9, 221.0.0.0/11, 221.96.0.0/11, 221.128.0.0/9, 222.0.0.0/8, 223.0.0.0/11, 223.64.0.0/10, 223.128.0.0/9
dns-server = 119.29.29.29,182.254.116.116,223.5.5.5,114.114.114.114
loglevel = notify

[Proxy]
Proxy = custom, $server, $port, $method, $password, $moduleUrl

[Rule]
# Proxy 10.28 URL
DOMAIN-KEYWORD,google,Proxy,force-remote-dns
DOMAIN-KEYWORD,gmail,Proxy,force-remote-dns
DOMAIN-KEYWORD,youtube,Proxy,force-remote-dns
DOMAIN-KEYWORD,blogspot,Proxy,force-remote-dns
DOMAIN-KEYWORD,twitter,Proxy,force-remote-dns
DOMAIN-KEYWORD,facebook,Proxy,force-remote-dns
DOMAIN-KEYWORD,instagram,Proxy,force-remote-dns
DOMAIN-SUFFIX,t.co,Proxy,force-remote-dns
DOMAIN-SUFFIX,twimg.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,kenengba.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,t66y.com,Proxy,force-remote-dns
DOMAIN-SUFFIX,amazonaws.com,Proxy
DOMAIN-SUFFIX,android.com,Proxy
DOMAIN-SUFFIX,angularjs.org,Proxy
DOMAIN-SUFFIX,appspot.com,Proxy
DOMAIN-SUFFIX,akamaihd.net,Proxy
DOMAIN-SUFFIX,amazon.com,Proxy
DOMAIN-SUFFIX,bit.ly,Proxy
DOMAIN-SUFFIX,bitbucket.org,Proxy
DOMAIN-SUFFIX,blog.com,Proxy
DOMAIN-SUFFIX,blogcdn.com,Proxy
DOMAIN-SUFFIX,blogger.com,Proxy
DOMAIN-SUFFIX,blogsmithmedia.com,Proxy
DOMAIN-SUFFIX,box.net,Proxy
DOMAIN-SUFFIX,bloomberg.com,Proxy
DOMAIN-SUFFIX,chromium.org,Proxy
DOMAIN-SUFFIX,cl.ly,Proxy
DOMAIN-SUFFIX,cloudfront.net,Proxy
DOMAIN-SUFFIX,cloudflare.com,Proxy
DOMAIN-SUFFIX,cocoapods.org,Proxy
DOMAIN-SUFFIX,crashlytics.com,Proxy
DOMAIN-SUFFIX,dmm.co.jp,Proxy
DOMAIN-SUFFIX,dribbble.com,Proxy
DOMAIN-SUFFIX,dropbox.com,Proxy
DOMAIN-SUFFIX,dropboxstatic.com,Proxy
DOMAIN-SUFFIX,dropboxusercontent.com,Proxy
DOMAIN-SUFFIX,docker.com,Proxy
DOMAIN-SUFFIX,duckduckgo.com,Proxy
DOMAIN-SUFFIX,digicert.com,Proxy
DOMAIN-SUFFIX,dnsimple.com,Proxy
DOMAIN-SUFFIX,edgecastcdn.net,Proxy
DOMAIN-SUFFIX,engadget.com,Proxy
DOMAIN-SUFFIX,eurekavpt.com,Proxy
DOMAIN-SUFFIX,fb.me,Proxy
DOMAIN-SUFFIX,fbcdn.net,Proxy
DOMAIN-SUFFIX,fc2.com,Proxy
DOMAIN-SUFFIX,feedburner.com,Proxy
DOMAIN-SUFFIX,fabric.io,Proxy
DOMAIN-SUFFIX,flickr.com,Proxy
DOMAIN-SUFFIX,fastly.net,Proxy
DOMAIN-SUFFIX,ggpht.com,Proxy
DOMAIN-SUFFIX,github.com,Proxy
DOMAIN-SUFFIX,github.io,Proxy
DOMAIN-SUFFIX,githubusercontent.com,Proxy
DOMAIN-SUFFIX,golang.org,Proxy
DOMAIN-SUFFIX,goo.gl,Proxy
DOMAIN-SUFFIX,gstatic.com,Proxy
DOMAIN-SUFFIX,godaddy.com,Proxy
DOMAIN-SUFFIX,gravatar.com,Proxy
DOMAIN-SUFFIX,imageshack.us,Proxy
DOMAIN-SUFFIX,imgur.com,Proxy
DOMAIN-SUFFIX,jshint.com,Proxy
DOMAIN-SUFFIX,ift.tt,Proxy
DOMAIN-SUFFIX,itunes.com,Proxy
DOMAIN-SUFFIX,j.mp,Proxy
DOMAIN-SUFFIX,kat.cr,Proxy
DOMAIN-SUFFIX,linode.com,Proxy
DOMAIN-SUFFIX,linkedin.com,Proxy
DOMAIN-SUFFIX,licdn.com,Proxy
DOMAIN-SUFFIX,lithium.com,Proxy
DOMAIN-SUFFIX,megaupload.com,Proxy
DOMAIN-SUFFIX,mobile01.com,Proxy
DOMAIN-SUFFIX,modmyi.com,Proxy
DOMAIN-SUFFIX,mzstatic.com,Proxy
DOMAIN-SUFFIX,nytimes.com,Proxy
DOMAIN-SUFFIX,name.com,Proxy
DOMAIN-SUFFIX,openvpn.net,Proxy
DOMAIN-SUFFIX,openwrt.org,Proxy
DOMAIN-SUFFIX,ow.ly,Proxy
DOMAIN-SUFFIX,pinboard.in,Proxy
DOMAIN-SUFFIX,ssl-images-amazon.com,Proxy
DOMAIN-SUFFIX,sstatic.net,Proxy
DOMAIN-SUFFIX,stackoverflow.com,Proxy
DOMAIN-SUFFIX,staticflickr.com,Proxy
DOMAIN-SUFFIX,squarespace.com,Proxy
DOMAIN-SUFFIX,symcd.com,Proxy
DOMAIN-SUFFIX,symcb.com,Proxy
DOMAIN-SUFFIX,symauth.com,Proxy
DOMAIN-SUFFIX,ubnt.com,Proxy
DOMAIN-SUFFIX,thepiratebay.org,Proxy
DOMAIN-SUFFIX,tumblr.com,Proxy
DOMAIN-SUFFIX,twitch.tv,Proxy
DOMAIN-SUFFIX,wikipedia.com,Proxy
DOMAIN-SUFFIX,wikipedia.org,Proxy
DOMAIN-SUFFIX,wikimedia.org,Proxy
DOMAIN-SUFFIX,wordpress.com,Proxy
DOMAIN-SUFFIX,wsj.com,Proxy
DOMAIN-SUFFIX,wsj.net,Proxy
DOMAIN-SUFFIX,wp.com,Proxy
DOMAIN-SUFFIX,vimeo.com,Proxy
DOMAIN-SUFFIX,youtu.be,Proxy
DOMAIN-SUFFIX,ytimg.com,Proxy

# Google
DOMAIN,csi.gstatic.com,REJECT
DOMAIN,static.googleadsserving.cn,REJECT
DOMAIN-SUFFIX,doubleclick.net,REJECT
DOMAIN-SUFFIX,googleadservices.com,REJECT
DOMAIN-SUFFIX,googlesyndication.com,REJECT
DOMAIN-SUFFIX,googletagservices.com,REJECT

# Apple
DOMAIN,iadsdk.apple.com,REJECT
DOMAIN-SUFFIX,adcdownload.apple.com,DIRECT
DOMAIN-SUFFIX,appldnld.apple.com,DIRECT
DOMAIN-SUFFIX,cdn-apple.com,DIRECT
DOMAIN-SUFFIX,itunes.com,DIRECT
DOMAIN-SUFFIX,lcdn-registration.apple.com,DIRECT
DOMAIN-SUFFIX,ls.apple.com,DIRECT
DOMAIN-SUFFIX,mzstatic.com,DIRECT
DOMAIN-SUFFIX,phobos.apple.com,DIRECT
DOMAIN-SUFFIX,swcdn.apple.com,DIRECT
DOMAIN-SUFFIX,icloud.com,Proxy
DOMAIN-SUFFIX,me.com,Proxy

# QQ
DOMAIN,lives.l.qq.com,REJECT
DOMAIN,monitor.uu.qq.com,REJECT
DOMAIN,pingjs.qq.com,REJECT
DOMAIN,pingma.qq.com,REJECT
DOMAIN,tajs.qq.com,REJECT
DOMAIN-SUFFIX,beacon.qq.com,REJECT
DOMAIN-SUFFIX,pingtcss.qq.com,REJECT
DOMAIN-SUFFIX,report.qq.com,REJECT
DOMAIN-SUFFIX,gtimg.com,DIRECT
DOMAIN-SUFFIX,qq.com,DIRECT

# 163
DOMAIN,dsp.youdao.com,REJECT
DOMAIN,g.163.com,REJECT
DOMAIN,temp.163.com,REJECT
DOMAIN-SUFFIX,stat.ws.126.net,REJECT
DOMAIN-SUFFIX,union.youdao.com,REJECT
DOMAIN-SUFFIX,126.net,DIRECT
DOMAIN-SUFFIX,163.com,DIRECT
DOMAIN-SUFFIX,netnease.com,DIRECT

# Baidu
DOMAIN,mtj.baidu.com,REJECT
DOMAIN,cbjs.baidu.com,REJECT
DOMAIN,cpro.baidu.com,REJECT
DOMAIN,mobads-logs.baidu.com,REJECT
DOMAIN,mobads.baidu.com,REJECT
DOMAIN-SUFFIX,baidustatic.com,REJECT
DOMAIN-SUFFIX,baidu.com,DIRECT

# Alibaba
DOMAIN,acjs.aliyun.com,REJECT
DOMAIN,adash.m.taobao.com,REJECT
DOMAIN-SUFFIX,simaba.taobao.com,REJECT
DOMAIN-KEYWORD,alipay,DIRECT
DOMAIN-SUFFIX,alicdn.com,DIRECT

# Sina
DOMAIN,sax.sina.cn,REJECT
DOMAIN-SUFFIX,beacon.sina.com.cn,REJECT

# Youku/Tudou
DOMAIN,ad.api.3g.tudou.com,REJECT
DOMAIN,ad.api.3g.youku.com,REJECT
DOMAIN-SUFFIX,atm.youku.com,REJECT

# Block privacy trackers
DOMAIN,bam.nr-data.net,REJECT
DOMAIN,counter.kingsoft.com,REJECT
DOMAIN,js-agent.newrelic.com,REJECT
DOMAIN,pixel.wp.com,REJECT
DOMAIN-KEYWORD,analytics,REJECT
DOMAIN-KEYWORD,trace,REJECT
DOMAIN-KEYWORD,track,REJECT
DOMAIN-KEYWORD,traffic,REJECT
DOMAIN-KEYWORD,usage,REJECT
DOMAIN-SUFFIX,51.la,REJECT
DOMAIN-SUFFIX,adjust.com,REJECT
DOMAIN-SUFFIX,cmcore.com,REJECT
DOMAIN-SUFFIX,coremetrics.com,REJECT
DOMAIN-SUFFIX,flurry.com,REJECT
DOMAIN-SUFFIX,irs01.com,REJECT
DOMAIN-SUFFIX,madmini.com,REJECT
DOMAIN-SUFFIX,mixpanel.com,REJECT
DOMAIN-SUFFIX,mmstat.com,REJECT
DOMAIN-SUFFIX,wrating.com,REJECT

# Block Ads servers
DOMAIN-KEYWORD,openx,REJECT
DOMAIN-SUFFIX,acs86.com,REJECT
DOMAIN-SUFFIX,adchina.com,REJECT
DOMAIN-SUFFIX,adcome.cn,REJECT
DOMAIN-SUFFIX,adinfuse.com,REJECT
DOMAIN-SUFFIX,admaster.com.cn,REJECT
DOMAIN-SUFFIX,admob.com,REJECT
DOMAIN-SUFFIX,adnxs.com,REJECT
DOMAIN-SUFFIX,ads.yahoo.com,REJECT
DOMAIN-SUFFIX,adsage.cn,REJECT
DOMAIN-SUFFIX,adsage.com,REJECT
DOMAIN-SUFFIX,adsmogo.org,REJECT
DOMAIN-SUFFIX,aduu.cn,REJECT
DOMAIN-SUFFIX,advertising.com,REJECT
DOMAIN-SUFFIX,adview.cn,REJECT
DOMAIN-SUFFIX,adwhirl.com,REJECT
DOMAIN-SUFFIX,adwo.com,REJECT
DOMAIN-SUFFIX,adxmi.com,REJECT
DOMAIN-SUFFIX,adzerk.net,REJECT
DOMAIN-SUFFIX,anquan.org,REJECT
DOMAIN-SUFFIX,appads.com,REJECT
DOMAIN-SUFFIX,appsflyer.com,REJECT
DOMAIN-SUFFIX,baifendian.com,REJECT
DOMAIN-SUFFIX,domob.cn,REJECT
DOMAIN-SUFFIX,domob.com.cn,REJECT
DOMAIN-SUFFIX,domob.org,REJECT
DOMAIN-SUFFIX,duomeng.cn,REJECT
DOMAIN-SUFFIX,duomeng.net,REJECT
DOMAIN-SUFFIX,duomeng.org,REJECT
DOMAIN-SUFFIX,guomob.com,REJECT
DOMAIN-SUFFIX,immob.cn,REJECT
DOMAIN-SUFFIX,inmobi.com,REJECT
DOMAIN-SUFFIX,localytics.com,REJECT
DOMAIN-SUFFIX,mob.com,REJECT
DOMAIN-SUFFIX,mobclix.com,REJECT
DOMAIN-SUFFIX,quantserve.com,REJECT
DOMAIN-SUFFIX,responsys.net,REJECT
DOMAIN-SUFFIX,scorecardresearch.com,REJECT
DOMAIN-SUFFIX,smartmad.com,REJECT
DOMAIN-SUFFIX,smartadserver.com,REJECT
DOMAIN-SUFFIX,tanx.com,REJECT
DOMAIN-SUFFIX,tapjoyads.com,REJECT
DOMAIN-SUFFIX,tiqcdn.com,REJECT
DOMAIN-SUFFIX,umeng.co,REJECT
DOMAIN-SUFFIX,umeng.com,REJECT
DOMAIN-SUFFIX,umeng.net,REJECT
DOMAIN-SUFFIX,unimhk.com,REJECT
DOMAIN-SUFFIX,uyunad.com,REJECT
DOMAIN-SUFFIX,waps.cn,REJECT
DOMAIN-SUFFIX,wiyun.com,REJECT
DOMAIN-SUFFIX,wooboo.com.cn,REJECT
DOMAIN-SUFFIX,wqmobile.com,REJECT
DOMAIN-SUFFIX,youmi.net,REJECT

# Streaming services, comment out if you don't need
DOMAIN-KEYWORD,qiyi,DIRECT
DOMAIN-KEYWORD,sohu,DIRECT
DOMAIN-SUFFIX,tudou.com,DIRECT
DOMAIN-SUFFIX,youku.com,DIRECT

// Telegram
IP-CIDR,91.108.56.0/22,Proxy,no-resolve
IP-CIDR,91.108.4.0/22,Proxy,no-resolve
IP-CIDR,109.239.140.0/24,Proxy,no-resolve
IP-CIDR,149.154.160.0/20,Proxy,no-resolve

// ALL CN DIRECT
DOMAIN-SUFFIX,cn,DIRECT

// PREVENT SNIFFER FROM A PUBLIC WIFI
DOMAIN,init.icloud-analysis.com,REJECT

# LAN, debugging rules should place above this line
IP-CIDR,192.168.0.0/16,DIRECT
IP-CIDR,10.0.0.0/8,DIRECT
IP-CIDR,172.16.0.0/12,DIRECT
IP-CIDR,127.0.0.0/8,DIRECT

// China Mobile
IP-CIDR,221.179.140.0/24,REJECT,no-resolve

# Detect local network
GEOIP,CN,DIRECT

# Use proxy for all others
FINAL,Proxy
EOF;
          return $template;
     }
}
