<?php
namespace Catpow\SimpleBrokenLinkFinder;

class SimpleBrokenLinkFinder{
	public static function search($root_dir,$target_dir=''){
		$results=[];
		$dir=rtrim($root_dir.'/'.trim($target_dir,'/'),'/');
		foreach(scandir($dir) as $fname){
			if($fname[0]==='.' || $fname[0]==='_'){continue;}
			$f=$dir.'/'.$fname;
			if(is_dir($f)){
				if(in_array($fname,['wp-content','wp-admin','node_modules'],true)){continue;}
				if($fname==='vendor' && file_exists($f.'/autoload.php')){continue;}
				$results=array_merge_recursive($results,self::search($root_dir,$target_dir.'/'.$fname));
				continue;
			}
			$ext=strrchr($fname,'.');
			if(preg_match('/^\.(html?|php)$/',$ext)){
				preg_match_all('/(src|srcset|href)\s*=\s*([\'"])(.+?)\2/m',file_get_contents($f),$matches,\PREG_SET_ORDER);
				foreach(array_column($matches,3) as $url){
					if($root_path=self::get_root_path($url,$target_dir)){
						if(!file_exists($root_dir.$root_path)){
							$results[$root_path][$target_dir.'/'.$fname]=true;
						}
					}
				}
			}
		}
		return $results;
	}
	public static function get_root_path($path,$base_dir){
		if(
			$path[0]==='#' ||
			preg_match('/^\w+:/',$path) ||
			strpos($path,'//')===0 ||
			strpos($path,'$')!==false || 
			strpos($path,'%s')!==false || 
			strpos($path,'{')!==false || 
			strpos($path,'[')!==false || 
			strpos($path,'<?')!==false || 
			strpos($path,'+')!==false || 
			strpos($path,'"')!==false || 
			strpos($path,"'")!==false
		){return false;}
		if(strpos($path,'?')!==false){$path=strstr($path,'?',true);}
		if(strpos($path,'#')!==false){$path=strstr($path,'#',true);}
		if(empty($path)){return false;}
		if($path[0]!=='/'){
			$path='/'.$base_dir.'/'.$path;
			$path=str_replace('//','/',$path);
			$path=str_replace('/./','/',$path);
			while(strpos($path,'/../')){
				$path=preg_replace('@/[^/]+/\.\./@m','/',$path);
			}
		}
		return rtrim($path,'/');
	}
}