<?php 
require_once 'masters/Cltvo_FileUpload.php';

class FileUpload_Test extends Cltvo_FileUpload
{
	public static function metaboxDisplayRule(){
		return true;
	}
}