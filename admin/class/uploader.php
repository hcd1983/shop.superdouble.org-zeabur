<?php

class uploader{
	
	public $action='ktjno';
	public $file;
	public $keyValue;
	public $form;
	public $uploaddir = 'uploads/';
	public $inputname = "file";
	public $pathfixURL;
	public $pathfix;

	function uploadForm(){


		$output='<form id="uploadform" action="upload.php?action='.$this-> action .'" method="post" enctype="multipart/form-data">';

		//$output.='<h3>上傳檔案</h3>';
		$output.='<input type="file" name="file" id="file" required class="file"><br>';
		//$output.='<input type="submit" class="button" name="submit" value="送出">';
		$output.='</form>';


		return $output;

	}



	function path(){
		global $_FILES;
		$this-> pathfixURL = iconv("utf-8", "big5",$this-> uploaddir.urlencode($_FILES["file"]["name"]));
		$this-> pathfix = iconv("utf-8", "big5",$this-> uploaddir.$_FILES["file"]["name"]);
		move_uploaded_file($_FILES[$this->inputname]["tmp_name"],$this->pathfix);
		return '<a href="'.$this-> pathfixURL.'">'.'<h1>'.iconv("big5","utf-8",$this->pathfix).'</h1>'.'</a>';
	}




}


?>