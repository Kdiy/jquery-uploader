<?php
/*
 * @ 上传文件API接口
 * @ URL 
 * @ tips:
		文件上传接口配置信息
			单个图片		  maxSize 1MB	
			允许上传文件类型  jpg png gif
 * @return
		errcode 
			0 上传成功
				fiename		//原文件名
				filepath	//存储相对路径
				imgpath		//URL路径		
				config		//配置信息
					maxsize		//允许最大值
					allowtype	//允许格式
					filepath	//上传目录
			1 格式不允许
			2 大小超出限制
			3 服务器配置出错
			4 没有被上传
 * 	
 */ 


header("Content-type: text/html; charset=utf-8"); 
date_default_timezone_set('PRC');

$m=isset($_GET['m'])==''?'null':$_GET['m'];
if($m=='fileupload'){
	function makedir($file){
		$dirname=date("Ymd");
		$dir = iconv("UTF-8", "GBK", "$file/$dirname");
		if (!file_exists($dir)){
			mkdir ($dir,0777,true);
		}
		return $dir;
	}
	if ( !empty( $_FILES ) ) {
		
		/*
		 * 上传文件配置
		 */
	    
	    //文件大小 单位 MB
		$maxsize=10;   
		//限制上传文件类型 jpg png gif
		$allowtype=array('image/jpeg','image/jpg','image/png','image/gif','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		//文件上传目录   .表示当前目录
		$uploadPath=".";
		//创建子目录
		$filePath=makedir($uploadPath);
		//上传文件重命名
		$fileName = date('YmdHis').rand(10,99);   	
		//返回接口配置信息
		$arr_conf=array(
			'maxsize' =>$maxsize.'MB',
			'allowtype' =>json_encode($allowtype),
			'filepath' =>$filePath
		);
		
		$bit=$maxsize * 1024 * 1024;
		$array = explode('.',$_FILES['file']['name']);
		$filetype= $array[1];
		if(! in_array(($_FILES['file']['type']),$allowtype)){
			$arr=array(
				'errcode'  =>'1',
				'errortype' =>'不允许的文件类型',
				'config' =>$arr_conf
			);
			echo json_encode($arr);exit;
		}
		if($_FILES['file']['size']>=$bit){
			$arr=array(
				'errcode'  =>'2',
				'errortype' =>'超出文件限定大小',
				'config' =>$arr_conf
			);
			echo json_encode($arr);exit;
		}
		
		$tempPath = $_FILES[ 'file' ][ 'tmp_name' ];
		if(move_uploaded_file($tempPath,$filePath.'/'.$fileName.'.'.$filetype)){
		    $tmp=$filePath.'/'.$fileName.'.'.$filetype;
		    $tmp_=str_replace('./', '', $tmp);
			$arr=array(
				'errcode' =>'0',
				'msg'  =>'文件上传成功',
				'fiename' =>$_FILES['file']['name'],
				'filepath' =>$filePath.'/'.$fileName.'.'.$filetype,
			    'xlspath' =>__DIR__.'/'.$tmp_,
				'imgpath' => 'http://'.$_SERVER['HTTP_HOST'].str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'])).substr($filePath,1).'/'.$fileName.'.'.$filetype,
			    'realpath' => str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME'])).substr($filePath,1).'/'.$fileName.'.'.$filetype,
			    
			    'config' =>$arr_conf
			);
			echo json_encode($arr);exit;
		}else{
			$arr=array(
				'errcode' =>'3',
				'errortype' =>'文件移动失败,请检查文件权限',
				'config' =>$arr_conf
			);
			echo json_encode($arr);exit;
		}
		
		
	} else {
		$arr=array(
				'errcode'  =>'4',
				'errortype' =>'没有文件被上传',
				'config' =>$arr_conf
		);
		echo json_encode($arr);exit;
	}
}
?>