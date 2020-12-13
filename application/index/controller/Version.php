<?php
namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');

class Version 
{
    function compare(){
                
        
        //{"os_type":"android","version":"1.0.4","imei":"1da124eb4bc0eacf"}
        //file_put_contents(__DIR__.'/version.txt',json_encode($_REQUEST)."\n");
        $os_type=$_REQUEST['os_type'];
        $version=$_REQUEST['version'];
        $curr_version=file_get_contents("./version/app.version");
        $curr_app_desc=file_get_contents("./version/app.desc");
        $curr_app_wgt_desc=file_get_contents("./version/app.wgt.desc");
        if($curr_version==$version){
            return json([
                'update'=>false,
                'type'=>'',
                'desc'=>'版本相同 无需更新',
                'url'=>''
            ]);
        }
        
        list ($client_v1, $client_v2) = explode('.', $version, 2);
        list ($curr_v1, $curr_v2) = explode('.', $curr_version, 2);
        $client_v1=intval($client_v1);
        $curr_v1=intval($curr_v1);
        $client_v2=$client_v2*1;
        $curr_v2=$curr_v2*1;
        //file_put_contents(__DIR__.'/version.txt',json_encode([$client_v1,$curr_v1,$client_v2,$curr_v2])."\n");
        if($curr_v1>$client_v1){//大版本更新
            if($os_type=='android'){
                 return json([
                    'update'=>true,
                    'type'=>'bg',
                    'desc'=>$curr_app_desc,
                    'url'=>'http://'.$_SERVER['HTTP_HOST'].'/version/app.apk'
                ]);
            }else{
                //返回苹果下载地址 
            }
        }
        if($curr_v1==$client_v1 && $curr_v2>$client_v2){
            
            file_put_contents(__DIR__.'/version.txt',date("Y-m-d")."小版本更新\n",FILE_APPEND);
            return json([
                'update'=>true,
                'type'=>'sm',
                'desc'=>$curr_app_wgt_desc,
                'url'=>'http://'.$_SERVER['HTTP_HOST'].'/version/app.wgt'
            ]);
        }
        return json([
            'update'=>false,
            'type'=>'',
            'desc'=>'',
            'url'=>''
        ]);
    }
    
}
