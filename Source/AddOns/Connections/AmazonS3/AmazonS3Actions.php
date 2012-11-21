<?php 

trait AmazonS3Actions{
	
	protected static function AmazonS3PublicFileUpload($local_file_path, $file_name, $connection_name='default'){
		$s3 = RuntimeInfo::instance()->connections($connection_name)->AmazonS3();;
		$settings = RuntimeInfo::instance()->connections($connection_name)->config('Connections/AmazonS3/',$connection_name);

		try{
			return $s3->putObject(
				$s3->inputFile($local_file_path),
				$settings->get('bucket_name'),
				$file_name,
				S3::ACL_PUBLIC_READ
			);
		} catch(S3Exception $e){
			if($settings->get('debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function AmazonS3PrivateFileUpload($local_file_path, $file_name, $connection_name='default'){
		$s3 = RuntimeInfo::instance()->connections($connection_name)->AmazonS3();
		$settings = RuntimeInfo::instance()->connections($connection_name)->config('Connections/AmazonS3/',$connection_name);

		try{
			return $s3->putObject(
				$s3->inputFile($local_file_path),
				$settings->get('bucket_name'),
				$file_name,
				S3::ACL_PRIVATE
			);
		} catch(S3Exception $e){
			if($settings->get('debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function AmazonS3GetBucketList($return_object_type='Model', $connection_name='default'){
		$s3 = RuntimeInfo::instance()->connections($connection_name)->AmazonS3();
		$settings = RuntimeInfo::instance()->connections($connection_name)->config('Connections/AmazonS3/',$connection_name);

		try{
			$s3 = RuntimeInfo::instance()->connections($connection_name)->AmazonS3();
			$buckets = $s3->listBuckets();
			
			return $buckets;
		} catch(S3Exception $e){
			if($settings->get('debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function AmazonS3GetBucket($bucket_name, $return_object_type='Model', $connection_name='default'){
		$s3 = RuntimeInfo::instance()->connections($connection_name)->AmazonS3();
		$settings = RuntimeInfo::instance()->connections($connection_name)->config('Connections/AmazonS3/',$connection_name);

		try{
			$s3 = RuntimeInfo::instance()->connections($connection_name)->AmazonS3();
			
			$files = $s3->getBucket($bucket_name);
			$FileCollection = new DataCollection();
			foreach($files as $file){
				$FileCollection->addObject(new DataObject($file));
			}
			
			return $FileCollection;
		} catch(S3Exception $e){
			if($settings->get('debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function AmazonS3GetFile($uri, $connection_name='default'){
		$s3 = RuntimeInfo::instance()->connections($connection_name)->AmazonS3();
		$settings = RuntimeInfo::instance()->connections($connection_name)->config('Connections/AmazonS3/',$connection_name);

		try{
			$s3 = RuntimeInfo::instance()->connections($connection_name)->AmazonS3();
			
			$FileObject = $s3->getObject($settings->get('bucket_name'), $uri);
			return new DataObject(array(
				'error' => $FileObject->error,
				'name' => $uri,
				'code' => $FileObject->code,
				'time' => $FileObject->headers['time'],
				'hash' => $FileObject->headers['hash'],
				'type' => $FileObject->headers['type'],
				'size' => $FileObject->headers['size'],
				'body' => $FileObject->body,
			));
		} catch(S3Exception $e){
			if($settings->get('debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function AmazonS3DeleteFile($uri, $connection_name='default'){
		$s3 = RuntimeInfo::instance()->connections($connection_name)->AmazonS3();
		$settings = RuntimeInfo::instance()->connections($connection_name)->config('Connections/AmazonS3/',$connection_name);

		try{
			$s3 = RuntimeInfo::instance()->connections($connection_name)->AmazonS3();
			
			return $s3->deleteObject($settings->get('bucket_name'), $uri);
		} catch(S3Exception $e){
			if($settings->get('debug')){ pr($e); }
			return false;
		}
	}
	
}