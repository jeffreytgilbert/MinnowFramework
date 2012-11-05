<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ScaffoldPagesPage extends TemplatedPageRequest{
	
	protected function loadIncludedFiles(){
	}
	
	protected function handleRequest(){
		$RuntimeInfo = RuntimeInfo::instance();
		
		if(isset($_POST['list']) && trim($_POST['list']) != ''){
			
			$simple_sitemap = '<h1>Partial Sitemap</h1><ul>';
			$controller = file_get_contents(os_path(dirname(__FILE__).'/../../../Framework/Scaffold/Controller.txt'));
			$view = file_get_contents(os_path(dirname(__FILE__).'/../../../Framework/Scaffold/View.htm'));
			$view_php = file_get_contents(os_path(dirname(__FILE__).'/../../../Framework/Scaffold/View.txt'));
			$js = file_get_contents(os_path(dirname(__FILE__).'/../../../Framework/Scaffold/pagescript.js'));
			$css = file_get_contents(os_path(dirname(__FILE__).'/../../../Framework/Scaffold/pagestyle.css'));
			$paths = explode("\n",$_POST['list']."\n");
			
			// incase these empties got deleted
			if(!is_dir(os_path(dirname(__FILE__).'/../../../../js/pages/'))){
				echo "Creating /js/pages/\n<br>";
				mkdir(os_path(dirname(__FILE__).'/../../../../js/pages/', 0755, true));
				chmod(os_path(dirname(__FILE__).'/../../../../js/pages/', 0755));
			}

			// incase these empties got deleted
			if(!is_dir(os_path(dirname(__FILE__).'/../../../../css/pages/'))){
				echo "Creating /css/pages/\n<br>";
				mkdir(os_path(dirname(__FILE__).'/../../../../css/pages/', 0755, true));
				chmod(os_path(dirname(__FILE__).'/../../../../css/pages/', 0755));
			}
			
			// run through the list of pages
			foreach($paths as $path){
				// if this is a foldered path, create the folder heirarchy
				if(strstr($path,'/')){
					
					// Identify relevant structures
					$path_parts = explode('/',$path);
					$file_name = trim($path_parts[count($path_parts)-1]);
					$folder = trim($path_parts[0]);
					$simple_sitemap .= '<li><a href="/'.$folder."/".$file_name.'">/'.$folder."/".$file_name.'</a></li>';
					
					// Write out Controller structures
					if(!is_dir(os_path(dirname(__FILE__).'/'.$folder))){
						echo "Creating folder /Application/".$RuntimeInfo->getApplicationName()."/Controllers/".$folder."/"."\n<br>";
						mkdir(os_path(dirname(__FILE__).'/'.$folder), 0755, true);
						chmod(os_path(dirname(__FILE__).'/'.$folder), 0755);
					} else {
						echo "Folder exists. Skipping /Application/".$RuntimeInfo->getApplicationName()."/Controllers/".$folder."/"."\n<br>";
					}
					
					$page_controller = str_replace('PageName', $file_name, $controller);
					$page_controller = str_replace('FolderPagePath', $folder."/".$file_name, $page_controller);
					
					if(!file_exists(os_path(dirname(__FILE__).'/'.$folder."/".$file_name.'Page.php'))){
						echo "Creating /Application/".$RuntimeInfo->getApplicationName()."/Controllers/".$folder."/".$file_name.'Page.php'."\n<br>";
						file_put_contents(os_path(dirname(__FILE__).'/'.$folder."/".$file_name.'Page.php'), $page_controller);
						chmod(os_path(dirname(__FILE__).'/'.$folder."/".$file_name.'Page.php'), 0755);
					} else {
						echo "Already exists. Skipping /Application/".$RuntimeInfo->getApplicationName()."/Controllers/".$folder."/".$file_name.'Page.php'."\n<br>";
					}
					
					// Write out View structures
					if(!is_dir(os_path(dirname(__FILE__).'/../Views/pages/'.$folder.'/'.$file_name))){
						echo "Creating folder /Application/".$RuntimeInfo->getApplicationName()."/Views/pages/".$folder.'/'.$file_name."/"."\n<br>";
						mkdir(os_path(dirname(__FILE__).'/../Views/pages/'.$folder.'/'.$file_name), 0755, true);
						chmod(os_path(dirname(__FILE__).'/../Views/pages/'.$folder.'/'.$file_name), 0755);
					} else {
						echo "Folder exists. Skipping /Application/".$RuntimeInfo->getApplicationName()."/Views/pages/".$folder.'/'.$file_name."/"."\n<br>";
					}
					
					$page_view = str_replace('PageName', $file_name, $view);
					if(!file_exists(os_path(dirname(__FILE__).'/../Views/pages/'.$folder."/".$file_name.'/layout.htm'))){
						echo "Creating /Application/".$RuntimeInfo->getApplicationName()."/Views/pages/".$folder."/".$file_name.'/layout.htm'."\n<br>";
						file_put_contents(os_path(dirname(__FILE__).'/../Views/pages/'.$folder."/".$file_name.'/layout.htm'), $page_view);
						chmod(os_path(dirname(__FILE__).'/../Views/pages/'.$folder."/".$file_name.'/layout.htm'), 0755);
					} else {
						echo "Already exists. Skipping /Application/".$RuntimeInfo->getApplicationName()."/Views/pages/".$folder."/".$file_name.'/layout.htm'."\n<br>";
					}
					
					$page_view_php = str_replace('PageName', $file_name, $view_php);
					if(!file_exists(os_path(dirname(__FILE__).'/../Views/pages/'.$folder."/".$file_name.'/layout.php'))){
						echo "Creating /Application/".$RuntimeInfo->getApplicationName()."/Views/pages/".$folder."/".$file_name.'/layout.php'."\n<br>";
						file_put_contents(os_path(dirname(__FILE__).'/../Views/pages/'.$folder."/".$file_name.'/layout.php'), $page_view_php);
						chmod(os_path(dirname(__FILE__).'/../Views/pages/'.$folder."/".$file_name.'/layout.php'), 0755);
					} else {
						echo "Already exists. Skipping /Application/".$RuntimeInfo->getApplicationName()."/Views/pages/".$folder."/".$file_name.'/layout.php'."\n<br>";
					}
					
					// Write out media files to support good practices
					
					if(!is_dir(os_path(dirname(__FILE__).'/../../../../css/pages/'.$folder))){
						echo "Creating folder /css/pages/".$folder.'/'."\n<br>";
						mkdir(os_path(dirname(__FILE__).'/../../../../css/pages/'.$folder), 0755, true);
						chmod(os_path(dirname(__FILE__).'/../../../../css/pages/'.$folder), 0755);
					} else {
						echo "Folder exists. Skipping /css/pages/".$folder.'/'."\n<br>";
					}
					
					$page_css = str_replace('PageName', $file_name, $css);
					if(!file_exists(os_path(dirname(__FILE__).'/../../../../css/pages/'.$folder."/".$file_name.'.css'))){
						echo "Creating /css/pages/".$folder."/".$file_name.'.css'."\n<br>";
						file_put_contents(os_path(dirname(__FILE__).'/../../../../css/pages/'.$folder."/".$file_name.'.css'), $page_css);
						chmod(os_path(dirname(__FILE__).'/../../../../css/pages/'.$folder."/".$file_name.'.css'), 0755);
					} else {
						echo "Already exists. Skipping /css/pages/".$folder."/".$file_name.'.css'."\n<br>";
					}
					
					if(!is_dir(os_path(dirname(__FILE__).'/../../../../js/pages/'.$folder))){
						echo "Creating folder /js/pages/".$folder.'/'."\n<br>";
						mkdir(os_path(dirname(__FILE__).'/../../../../js/pages/'.$folder), 0755, true);
						chmod(os_path(dirname(__FILE__).'/../../../../js/pages/'.$folder), 0755);
					} else {
						echo "Folder exists. Skipping /js/pages/".$folder.'/'."\n<br>";
					}
					
					$page_js = str_replace('PageName', $file_name, $js);
					if(!file_exists(os_path(dirname(__FILE__).'/../../../../js/pages/'.$folder."/".$file_name.'.js'))){
						echo "Creating /js/pages/".$folder."/".$file_name.'.js'."\n<br>";
						file_put_contents(os_path(dirname(__FILE__).'/../../../../js/pages/'.$folder."/".$file_name.'.js'), $page_js);
						chmod(os_path(dirname(__FILE__).'/../../../../js/pages/'.$folder."/".$file_name.'.js'), 0755);
					} else {
						echo "Already exists. Skipping /js/pages/".$folder."/".$file_name.'.js'."\n<br>";
					}
					
				} else if(trim($path) != '') { // otherwise, skip a ton of checks and jump right to the files
					
					// Identify relevant structures
					$file_name = trim($path);
					$page_controller = str_replace('PageName', $file_name, $controller);
					$page_controller = str_replace('FolderPagePath', $file_name, $page_controller);
					
					$simple_sitemap .= '<li><a href="/'.$file_name.'">/'.$file_name.'</a></li>';
					
					// Write out Controller structures
					if(!file_exists(os_path(dirname(__FILE__).'/'.$file_name.'Page.php'))){
						echo "Creating /Application/".$RuntimeInfo->getApplicationName()."/Controllers/".$file_name.'Page.php'."\n<br>";
						file_put_contents(os_path(dirname(__FILE__).'/'.$file_name.'Page.php'), $page_controller);
						chmod(os_path(dirname(__FILE__).'/'.$file_name.'Page.php'), 0755);
					} else {
						echo "Already exists. Skipping /Application/".$RuntimeInfo->getApplicationName()."/Controllers/".$file_name.'Page.php'."\n<br>";
					}
					
					// Write out View structures
					if(!is_dir(os_path(dirname(__FILE__).'/../Views/pages/'.$file_name))){
						echo "Creating folder /Application/".$RuntimeInfo->getApplicationName()."/Views/pages/".$file_name."/"."\n<br>";
						mkdir(os_path(dirname(__FILE__).'/../Views/pages/'.$file_name), 0755, true);
						chmod(os_path(dirname(__FILE__).'/../Views/pages/'.$file_name), 0755);
					} else {
						echo "Folder exists. Skipping /Application/".$RuntimeInfo->getApplicationName()."/Views/pages/".$file_name."/"."\n<br>";
					}
					
					$page_view = str_replace('PageName', $file_name, $view);
					if(!file_exists(os_path(dirname(__FILE__).'/../Views/pages/'.$file_name.'/layout.htm'))){
						echo "Creating /Application/".$RuntimeInfo->getApplicationName()."/Views/pages/".$file_name.'/layout.htm'."\n<br>";
						file_put_contents(os_path(dirname(__FILE__).'/../Views/pages/'.$file_name.'/layout.htm'), $page_view);
						chmod(os_path(dirname(__FILE__).'/../Views/pages/'.$file_name.'/layout.htm'), 0755);
					} else {
						echo "Already exists. Skipping /Application/".$RuntimeInfo->getApplicationName()."/Views/pages/".$file_name.'/layout.htm'."\n<br>";
					}
					
					$page_view_php = str_replace('PageName', $file_name, $view_php);
					if(!file_exists(os_path(dirname(__FILE__).'/../Views/pages/'.$file_name.'/layout.php'))){
						echo "Creating /Application/".$RuntimeInfo->getApplicationName()."/Views/pages/".$file_name.'/layout.php'."\n<br>";
						file_put_contents(os_path(dirname(__FILE__).'/../Views/pages/'.$file_name.'/layout.php'), $page_view_php);
						chmod(os_path(dirname(__FILE__).'/../Views/pages/'.$file_name.'/layout.php'), 0755);
					} else {
						echo "Already exists. Skipping /Application/".$RuntimeInfo->getApplicationName()."/Views/pages/".$file_name.'/layout.php'."\n<br>";
					}
					
					// Write out media files to support good practices
					
					$page_css = str_replace('PageName', $file_name, $css);
					if(!file_exists(os_path(dirname(__FILE__).'/../../../../css/pages/'.$file_name.'.css'))){
						echo "Creating /css/pages/".$file_name.'.css'."\n<br>";
						file_put_contents(os_path(dirname(__FILE__).'/../../../../css/pages/'.$file_name.'.css'), $page_css);
						chmod(os_path(dirname(__FILE__).'/../../../../css/pages/'.$file_name.'.css'), 0755);
					} else {
						echo "Already exists. Skipping /css/pages/".$file_name.'.css'."\n<br>";
					}
					
					$page_js = str_replace('PageName', $file_name, $js);
					if(!file_exists(os_path(dirname(__FILE__).'/../../../../js/pages/'.$file_name.'.js'))){
						echo "Creating /js/pages/".$file_name.'.js'."\n<br>";
						file_put_contents(os_path(dirname(__FILE__).'/../../../../js/pages/'.$file_name.'.js'), $page_js);
						chmod(os_path(dirname(__FILE__).'/../../../../js/pages/'.$file_name.'.js'), 0755);
					} else {
						echo "Already exists. Skipping /js/pages/".$file_name.'.js'."\n<br>";
					}
					
				}
			} // endforeach
			
			echo $simple_sitemap.'</ul>';
			
		} // endif
	}
	
	public function renderPage(){
		die(file_get_contents(os_path(dirname(__FILE__).'/../../../Framework/Scaffold/simple_setup_form.htm')));
	}
}

