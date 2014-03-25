<!DOCTYPE html>
<html>
<head>
	<title>Sun francisco)</title>
	
</head>
<body>
<form enctype="multipart/form-data" action="#" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="30000">
	Send this file: <input name="userfile" type="file">
	<input type="submit" value="Send File">
</form>
<hr>

<?php
	if (!file_exists(getcwd().'/uploads/')) {
		mkdir(getcwd().'/uploads/', 0777,true);
	}
	
	$uploaddir = getcwd().'/uploads/';
    $downloaddir = getcwd().'/uploads/';

	if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploaddir.$_FILES['userfile']['name'])) 
	{
	    print "File is valid, and was successfully uploaded.<br>";
	    
	    include "bibtext.php";
	    $pars_data = new BibTeX_Parser($uploaddir.$_FILES['userfile']['name']);
	    // $items  = $pars_data->items['raw'];
	    $autors = $pars_data->items['author'];
	    $volume = $pars_data->items['volume'];
	    
	    foreach ($autors as $key => $value) {
	    	$arr[$key] = array(
	    		'type'  => $pars_data->types[$key],
			    'autors' => $pars_data->items['author'][$key],
			    'volume' => $pars_data->items['volume'][$key],
			    'year'   => $pars_data->items['year'][$key],
			    'title'  => $pars_data->items['title'][$key],
			    'pages'  => $pars_data->items['pages'][$key],
			    'publisher'  => $pars_data->items['publisher'][$key]?'-- '.$pars_data->items['publisher'][$key].', ':'',
			    'number'  => $pars_data->items['number'][$key]?$pars_data->items['number'][$key].', ':'',
			    'journal'  => $pars_data->items['journal'][$key]?'// '.$pars_data->items['journal'][$key]:'',
	    		);
	    	# code...
	    }
	    asort($arr);
	    array_multisort($arr,$volume,SORT_DESC);
	    echo "<pre>";
	    // print_r($arr);
	    echo "</pre>";
	    
	    //create file
	    $fp = fopen($downloaddir.$_FILES['userfile']['name'].'.out', 'a');
	    chmod($downloaddir.$_FILES['userfile']['name'].'.out', 0777);
			foreach ($arr as $key => $value) {
				if ($value['type'] =='articl') {
					$result_str = "[$key] ".convertFio($value['autors']).' '.$value['title'].' '.$value['journal'].' '.$value['volume'].' '.$value['number'].' -- '.$value['year'].' -- pp. '.$value['pages'];
				}else
					$result_str = "[$key] ".convertFio($value['autors']).' '.$value['title'].', Vol. '.$value['volume'].' '.$value['number'].' '.$value['publisher'].' '.$value['year'].' '.$value['pages'];
				echo $result_str.'<br>';
				fwrite($fp, $result_str."\n");  
				# code...
			}
		fclose($fp);

		echo "<h4>You can download file hear</h4><hr>";
		echo "<a href=uploads/".$_FILES['userfile']['name'].'.out'." target='_blank'>Download Link</a>";
	
	} else {
	    print "There some errors!";
	}

	echo "<br>___END___<br><hr>";

	function convertFio($string_fio)
    {
    	$result = '';
    	$i = 0;
    	$items = explode('and',$string_fio);
    	foreach ($items as $key => $value) {
    		$parts = explode(" ", trim($value));
    		$result .= ($i>0) ? ', ' : '' ;
	        $result .= ' '.$parts[0].' ';
	        $result .= substr($parts[1], 0, 1) . '.';
	        if(isset($parts[2]))
	        	$result .= substr($parts[2], 0, 1) . '.';
	        $i++;
	    }
	    return $result;
    }

?>
</body>
</html>

