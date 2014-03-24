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
	$uploaddir = getcwd().'/uploads/in/';
    $downloaddir = getcwd().'/uploads/out/';

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
	    		// 'items'  => $pars_data->items['raw'],
			    'autors' => $pars_data->items['author'][$key],
			    'volume' => $pars_data->items['volume'][$key]?'Vol. '.$pars_data->items['volume'][$key]:'',
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
				$result_str = convertFio($value['autors']).' '.$value['title'].' '.$value['journal'].' '.$value['volume'].' '.$value['number'].' '.$value['publisher'].' '.$value['year'].' '.$value['pages'];
				echo $result_str.'<br>';
				fwrite($fp, $result_str."\n");  
				# code...
			}
		fclose($fp);

		echo "<h4>You can download file hear</h4><hr>";
		echo "<a href=uploads/out/".$_FILES['userfile']['name'].'.out'." target='_blank'>Download Link</a>";
	
	} else {
	    print "There some errors!";
	}

	echo "<br>___END___<br><hr>";

	function convertFio($string_fio)
    {
    	$result = '';
    	$items = explode('and',$string_fio);
    	foreach ($items as $key => $value) {
    		$parts = explode(" ", trim($value));
	        $result .= ' '.$parts[0].' ';
	        $result .= substr($parts[1], 0, 1) . '.';
	        if(isset($parts[2]))
	        	$result .= substr($parts[2], 0, 1) . '.';
	    }
	    return $result;
    }

?>
</body>
</html>

