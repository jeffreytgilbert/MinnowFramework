<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class SitemapBuilderPage extends PageController implements HTMLCapable, JSONCapable{

	public 
		$forms,
		$dummy_forms;
	
	public function handleRequest(){
		
		// If you'd like to delete all the pages in the database, uncomment this line, and refresh the page, and they'll all be regenerated with blank records
//		SitemapActions::deleteAll();
		
		// ----- Page builder & Sitemap -----
		
		// Grab all controller files into an array
		$controller_files = File::relativePathsFromFilesInAppIncludingSubFoldersAsArray(Path::toControllers().'Pages/',Path::toControllers().'Pages', false, 8);
		
		// Check the db to see if they exist in the sitemap
		$SitemapCollection = SitemapActions::selectList();
		
		// Insert the ones that dont exist already
		foreach($controller_files as $url => $file_name){
			$SitemapPage = $SitemapCollection->getObjectByFieldValue('url', $url);
			
			if( !($SitemapPage instanceof Sitemap) ){
				SitemapActions::insertSitemap(new Sitemap(array(
					'url' => $url
				)));
			}
		}
		unset($SitemapCollection);
		
		// Requery the database for the ones that exist
		$SitemapCollection = SitemapActions::selectList();
		
		// Put them in a data field so they can be used on the view
		$this->getDataObject()->set('SitemapCollection',$SitemapCollection);
		
		$PageBuilderForm = $this->getForm('PageBuilder');
		
		// Check for form login from local page request
		// If data exists in expected form, check it as a login against the db
		if($PageBuilderForm->hasBeenSubmitted()){
			$PageBuilder = new DataObject(array(
				'url'=>$PageBuilderForm->getFieldData('url'),
				'title'=>$PageBuilderForm->getFieldData('title'),
				'description'=>$PageBuilderForm->getFieldData('description'),
				'ignore_in_sitemap'=>$PageBuilderForm->getFieldData('ignore_in_sitemap'),
				'HTML'=>$PageBuilderForm->getFieldData('HTML'),
				'JSON'=>$PageBuilderForm->getFieldData('JSON'),
				'XML'=>$PageBuilderForm->getFieldData('XML'),
				'HTMLBody'=>$PageBuilderForm->getFieldData('HTMLBody')
			));
			
			// Handle errors one at a time (if you want to handle them all at the same time, throw each one in a try catch block of its own
			$errors = array();
			
			try{
				
				$PageBuilderForm->checkString('url')->required()->maxLength(250);
				$PageBuilderForm->checkWords('title')->required()->allowUTF8WordsAndNumbersAndPunctuation(false)->maxLength(2000);
				$PageBuilderForm->checkWords('description')->required()->allowUTF8WordsAndNumbersAndPunctuation(false)->maxLength(160);
				$MinnowRequest = new MinnowRequest($PageBuilder->getString('url'));
				if(!$MinnowRequest->isValidURLFormat()) {
 					$errors['url'] = array(ValidURL::INVALID_URL_FORMAT=>'');
				}
				
				// temporarily disabled so builder can be tested
				SitemapActions::insertSitemap(new Sitemap(array(
					'url'=>$MinnowRequest->getRequestUrl(),
					'title'=>$PageBuilder->getString('title'),
					'description'=>$PageBuilder->getString('description'),
					'ignore_in_sitemap'=>$PageBuilder->getBoolean('ignore_in_sitemap')
				)));
				
				$this->buildPage($PageBuilder, $MinnowRequest);
				$this->flashConfirmation('Success', $MinnowRequest->getControllerName().' has been created.');
//				$this->redirect('/DeveloperTools/SitemapBuilder');
				
			} catch(Exception $e){
				$errors = $PageBuilderForm->getCurrentErrors();
				
				foreach($errors as $field => $error){
					
					switch(key($error)){
						case ValidString::INVALID_VALUE_IS_REQUIRED:
							$message = 'It is a required field.';
							break;
						case ValidString::INVALID_STRING_MAX_LENGTH:
							$message = 'The length of the field is limited to '.current($error).' characters.';
							break;
						case ValidWords::INVALID_WORD_FORMAT:
							$message = 'The data entered used characters that are not allowed.';
							break;
						case ValidURL::INVALID_URL_FORMAT:
							$message = 'The URL entered was not in a valid format.';
							break;
					}
					
					$this->flashError($field,'Page Builder had an error with the '.$field.' field. '.$message);
				}
			}
			
		} else {
			$PageBuilder = new DataObject(array(
				'url'=>'',
				'title'=>'',
				'description'=>'',
				'ignore_in_sitemap'=>false,
				'HTML'=>true,
				'JSON'=>true,
				'XML'=>true,
				'HTMLBody'=>true
			));
		}
		$this->getDataObject()->set('PageBuilder',$PageBuilder);
		
		$SitemapForm = $this->getForm('Sitemap');
		if($SitemapForm->hasBeenSubmitted()){
			
			// Store input in the collection for printing back to the screen
			$input_array = $SitemapForm->getFormDataAsDataObject()->toArray();
			foreach($input_array['title'] as $key => $title){
				$SitemapPage = $SitemapCollection->getSitemapByFieldValue('link_id', $key);
				// Ah the glory of object pointers. Update once and it updates the collection.
				$SitemapPage->set('title',$title);
				if(isset($input_array['description'][$key])) $SitemapPage->set('description',$input_array['description'][$key]);
				if(isset($input_array['ignore_in_sitemap'][$key])) $SitemapPage->set('ignore_in_sitemap',$input_array['ignore_in_sitemap'][$key]);
			}
			
			// Handle errors one at a time (if you want to handle them all at the same time, throw each one in a try catch block of its own
			$errors = array();
			
			try{

				$SitemapForm->checkArray('title', 'ValidString', function(ValidString $Validator){
					$Validator->maxLength(2000);
				});
				
				$SitemapForm->checkArray('description', 'ValidWords', function(ValidWords $Validator){
					$Validator->maxLength(160)->allowUTF8WordsAndNumbersAndPunctuation(false);
				});
				
				foreach($SitemapCollection as $SitemapPage){
					SitemapActions::updateSitemap($SitemapPage);
				}
				$this->flashConfirmation('Success', 'Your projects sitemap has been successfully updated!');
				
			} catch(Exception $e){
				$errors = $SitemapForm->getCurrentErrors();
				
				foreach($errors as $field => $error){
					if(key($error) != ''){
						$SitemapPage = $SitemapCollection->getSitemapByFieldValue('link_id', key($error));
						
						switch(key($error[key($error)])){
							case ValidString::INVALID_VALUE_IS_REQUIRED:
								$message = 'It is a required field.';
								break;
							case ValidString::INVALID_STRING_MAX_LENGTH:
								$message = 'The length of the field is limited to '.current($error).' characters.';
								break;
							case ValidWords::INVALID_WORD_FORMAT:
								$message = 'The data entered used characters that are not allowed.';
								break;
						}
						
						$this->flashError($field,$SitemapPage->getStringAsHTMLEntities('url').' had an error with the '.$field.' field. '.$message);
					}
				}
			}
		}
	}
	
	public function renderJSON(){ return $this->output = parent::renderJSON(); }
	public function renderHTML(){ return $this->_page_body = parent::renderHTML(); }

	private function buildPage($PageBuilder, MinnowRequest $MinnowRequest){
		
		$app_name = RuntimeInfo::instance()->getApplicationName();
		$controller = file_get_contents(File::osPath(Path::toFramework().'Scaffolding/Controller.txt'));
		$view = file_get_contents(File::osPath(Path::toFramework().'Scaffolding/View.txt'));
		$js = file_get_contents(File::osPath(Path::toFramework().'Scaffolding/pagescript.js'));
		$css = file_get_contents(File::osPath(Path::toFramework().'Scaffolding/pagestyle.css'));
		
		// Make sure base folders exist for css/js
		if(!File::exists(Path::toCSS().'Pages/')){
			$this->log()->info('Creating /www/css/Pages/');
			mkdir(File::osPath(Path::toCSS().'Pages/', 0755, true));
			chmod(File::osPath(Path::toCSS().'Pages/', 0755));
		}
		
		if(!File::exists(Path::toJS().'Pages/')){
			$this->log()->info('Creating /www/js/Pages/');
			mkdir(File::osPath(Path::toJS().'Pages/', 0755, true));
			chmod(File::osPath(Path::toJS().'Pages/', 0755));
		}
			
		// Write out Controller structures
		if(!File::exists(Path::toControllers().'Pages/'.$MinnowRequest->getControllerPath())){
			$this->log()->info('Creating folder /Application/'.$app_name.'/Controllers/Pages/'.$MinnowRequest->getControllerPath());
			mkdir(File::osPath(Path::toControllers().'Pages/'.$MinnowRequest->getControllerPath()), 0755, true);
			chmod(File::osPath(Path::toControllers().'Pages/'.$MinnowRequest->getControllerPath()), 0755);
		} else {
			$this->log()->info('Folder exists. Skipping /Application/'.$app_name.'/Controllers/Pages/'.$MinnowRequest->getControllerPath());
		}
		
		if($PageBuilder->getBoolean('HTML') || $PageBuilder->getBoolean('HTMLBody')){
			// Create CSS folders
			if(!File::exists(Path::toCSS().'Pages/'.$MinnowRequest->getControllerPath())){
				$this->log()->info('Creating folder /www/css/Pages/'.$MinnowRequest->getControllerPath());
				mkdir(File::osPath(Path::toCSS().'Pages/'.$MinnowRequest->getControllerPath()), 0755, true);
				chmod(File::osPath(Path::toCSS().'Pages/'.$MinnowRequest->getControllerPath()), 0755);
			} else {
				$this->log()->info('Folder exists. Skipping /www/css/Pages/'.$MinnowRequest->getControllerPath());
			}
			
			// Create JS folders
			if(!File::exists(Path::toJS().'Pages/'.$MinnowRequest->getControllerPath())){
				$this->log()->info('Creating folder /www/js/Pages/'.$MinnowRequest->getControllerPath());
				mkdir(File::osPath(Path::toJS().'Pages/'.$MinnowRequest->getControllerPath()), 0755, true);
				chmod(File::osPath(Path::toJS().'Pages/'.$MinnowRequest->getControllerPath()), 0755);
			} else {
				$this->log()->info('Folder exists. Skipping /www/js/Pages/'.$MinnowRequest->getControllerPath());
			}
			
			// Create view folders
			if(!File::exists(Path::toViews().'Pages/'.$MinnowRequest->getRequestUrl())){
				$this->log()->info('Creating folder /Application/'.$app_name.'/Views/Pages/'.$MinnowRequest->getRequestUrl());
				mkdir(File::osPath(Path::toViews().'Pages/'.$MinnowRequest->getRequestUrl()), 0755, true);
				chmod(File::osPath(Path::toViews().'Pages/'.$MinnowRequest->getRequestUrl()), 0755);
			} else {
				$this->log()->info('Folder exists. Skipping /Application/'.$app_name.'/Views/Pages/'.$MinnowRequest->getRequestUrl());
			}
			
			// Create view template
			$page_view = str_replace('PageName', $MinnowRequest->getControllerName(), $view);
			
			if(!File::exists(Path::toViews().'Pages/'.$MinnowRequest->getRequestUrl().'/layout.php')){
				$this->log()->info('Creating /Application/'.$app_name.'/Views/Pages/'.$MinnowRequest->getRequestUrl().'/layout.php');
				file_put_contents(File::osPath(Path::toViews().'Pages/'.$MinnowRequest->getRequestUrl().'/layout.php'), $page_view);
				chmod(File::osPath(Path::toViews().'Pages/'.$MinnowRequest->getRequestUrl().'/layout.php'), 0755);
			} else {
				$this->log()->info('Already exists. Skipping /Application/'.$app_name.'/Views/Pages/'.$MinnowRequest->getRequestUrl().'/layout.php');
			}
			
			// Write out media files to support good practices
			
			$page_css = str_replace('PageName', $MinnowRequest->getControllerName(), $css);
			if(!File::exists(Path::toCSS().'Pages/'.$MinnowRequest->getRequestUrl().'.css')){
				$this->log()->info('Creating /www/css/Pages/'.$MinnowRequest->getRequestUrl().'.css');
				file_put_contents(File::osPath(Path::toCSS().'Pages/'.$MinnowRequest->getRequestUrl().'.css'), $page_css);
				chmod(File::osPath(Path::toCSS().'Pages/'.$MinnowRequest->getRequestUrl().'.css'), 0755);
			} else {
				$this->log()->info('Already exists. Skipping /www/css/Pages/'.$MinnowRequest->getRequestUrl().'.css');
			}
			
			
			$page_js = str_replace('PageName', $MinnowRequest->getControllerName(), $js);
			if(!File::exists(Path::toJS().'Pages/'.$MinnowRequest->getRequestUrl().'.js')){
				$this->log()->info('Creating /www/js/Pages/'.$MinnowRequest->getRequestUrl().'.js');
				file_put_contents(File::osPath(Path::toJS().'Pages/'.$MinnowRequest->getRequestUrl().'.js'), $page_js);
				chmod(File::osPath(Path::toJS().'Pages/'.$MinnowRequest->getRequestUrl().'.js'), 0755);
			} else {
				$this->log()->info('Already exists. Skipping /www/js/Pages/'.$MinnowRequest->getRequestUrl().'.js');
			}
			
		}
			
		// Create controller file
		$page_controller = str_replace('PageName', $MinnowRequest->getControllerName(), $controller);
		
		$exported_formats = array();
		
		if($PageBuilder->getBoolean('HTML')){ 
			$exported_formats[] = 'HTMLCapable';
			$render_methods[] = "
	public function renderHTML(){ return parent::renderHTML(); }
	";
		}
		
		if($PageBuilder->getBoolean('JSON')){ 
			$exported_formats[] = 'JSONCapable'; 
			$render_methods[] = "
	public function renderJSON(){ return parent::renderJSON(); }
	";
		}
		
		if($PageBuilder->getBoolean('XML')){ 
			$exported_formats[] = 'XMLCapable';
			$render_methods[] = "
	public function renderXML(){ return parent::renderXML(); }
	";
		}
		
		if($PageBuilder->getBoolean('HTMLBody')){ 
			$exported_formats[] = 'HTMLBodyCapable';
			$render_methods[] = "
	public function renderHTMLBody(){ return parent::renderHTMLBody(); }
	";
		}
		
		if(count($exported_formats) > 0){
			$implemented_formats = implode(', ',$exported_formats);
			$page_controller = str_replace('/* exported formats */', 'implements '.$implemented_formats, $page_controller);
			$rendered_formats = implode('',$render_methods);
			$page_controller = str_replace('/* rendering methods */', $rendered_formats, $page_controller);
		}
		
		$page_controller = str_replace('PageName', $MinnowRequest->getControllerName(), $page_controller);
		
		if(!File::exists(Path::toControllers().'Pages/'.$MinnowRequest->getRequestUrl().'Page.php')){
			$this->log()->info('Creating /Application/'.$app_name.'/Controllers/Pages/'.$MinnowRequest->getRequestUrl().'Page.php');
			file_put_contents(File::osPath(Path::toControllers().'Pages/'.$MinnowRequest->getRequestUrl().'Page.php'), $page_controller);
			chmod(File::osPath(Path::toControllers().'Pages/'.$MinnowRequest->getRequestUrl().'Page.php'), 0755);
		} else {
			$this->log()->info('Already exists. Skipping /Application/'.$app_name.'/Controllers/Pages/'.$MinnowRequest->getRequestUrl().'Page.php');
		}
		
	}
	
	
}
