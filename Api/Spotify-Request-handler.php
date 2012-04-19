<?php

	require_once(dirname( __FILE__ ) . '/../../../../wp-config.php');		

    if ( is_user_logged_in() ) 
    {
    	$searchtype = $_POST["searchtype"];
    	$searchquery = $_POST["searchquery"];
        
        if ( isset($searchtype) && isset($searchquery) )
        {
        	// get the response
			$url = "http://ws.spotify.com/search/1/".$searchtype.".json?q=".$searchquery;	            
            if( function_exists('curl_version') == "Enabled" )
            {
    			$curl;
    			$curl = curl_init();
    	 
    			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
    			curl_setopt($curl, CURLOPT_HEADER, false);
    			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    			curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    			curl_setopt($curl, CURLOPT_URL, $url);
    	 
    			$response = json_decode(curl_exec($curl));
    			curl_close($curl);	
            }else{
                $res = file_get_contents($url);
                $response = json_decode($res);
            }
            // write it as json
			//header('HTTP/1.1 404 Page Not Found');
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-type: application/json');              
	    }
	    else
	    {
	        $response = array('error' => true, 'message' => 'id or message error');
            header('HTTP/1.1 500 Internal Server Error');
	    }

    }
    else
    {
        $response = array('error' => true, 'message' => 'please login');
        header('HTTP/1.1 500 Internal Server Error');
    }   
    
    echo json_encode($response);   
?>