<?php 

trait AmazonS3Actions{
	
	protected static function S3PublicFileUpload($local_file_path, $file_name){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->putObject(
					$s3->inputFile($local_file_path),
					RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'public_bucket_name'),
					$file_name,
					S3::ACL_PUBLIC_READ
			);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3PrivateFileUpload($local_file_path, $file_name){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->putObject(
					$s3->inputFile($local_file_path),
					RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'private_bucket_name'),
					$file_name,
					S3::ACL_PRIVATE
			);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3GetBucketList($return_object_type='Model'){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			$buckets = $s3->listBuckets();
				
//			pr($buckets);die;
			
			return $buckets;
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3GetBucket($bucket_name,$return_object_type='Model'){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->getBucket($bucket_name);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3GetPublicFile($uri){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->getObject(RuntimeInfo::instance()->getApplicationName().'-public', $uri);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3GetPrivateFile($uri){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->getObject(RuntimeInfo::instance()->getApplicationName().'-private', $uri);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3DeletePublicFile($uri){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->deleteObject(RuntimeInfo::instance()->getApplicationName().'-public', $uri);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3DeletePrivateFile($uri){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->deleteObject(RuntimeInfo::instance()->getApplicationName().'-private', $uri);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
}
