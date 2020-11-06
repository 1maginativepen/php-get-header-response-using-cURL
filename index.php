<?php 
    require_once(__DIR__.'/config.php'); 
    /*
        set second parameter 0 if checking from database (Used on CRON)
        set second parameter 1 if used as checker only. (Used outside)
    */
    function AllResponse($Content, $passCheck){
        $responseCode =""; $website = $Content;
        if(strpos($Content, '200') !== false){
            $responseCode = "200 (OK)";     $Content = "200";
        }elseif(strpos($Content, '201') !== false){
            $responseCode = "201 (Created)";    $Content = "201";
        }elseif(strpos($Content, '202') !== false){
            $responseCode = "202 (Accepted)";   $Content = "202";
        }elseif(strpos($Content, '204') !== false){
            $responseCode = "204 (No Content)!";    $Content = "204";
        }elseif(strpos($Content, '301') !== false){
            $responseCode = "301 (Moved Permanently)";  $Content = "301";
        }elseif(strpos($Content, '302') !== false){
            $responseCode = "302 (Found)";  $Content = "302";
        }elseif(strpos($Content, '303') !== false){
            $responseCode = "303 (See Other)";  $Content = "303";
        }elseif(strpos($Content, '304') !== false){
            $responseCode = "304 (Not Modified)";   $Content = "304";
        }elseif(strpos($Content, '307') !== false){
            $responseCode = "307 (Temporary Redirect)";     $Content = "307";
        }elseif(strpos($Content, '400') !== false){
            $responseCode = "400 (Bad Request)";    $Content = "400";
        }elseif(strpos($Content, '401') !== false){
            $responseCode = "401 (Unauthorized)";   $Content = "401";
        }elseif(strpos($Content, '403') !== false){
            $responseCode = "403 (Forbidden)";      $Content = "403"; 
        }elseif(strpos($Content, '404') !== false){
            $responseCode = "404 (Not Found)";  $Content = "404";
        }elseif(strpos($Content, '405') !== false){
            $responseCode = "405 (Method Not Allowed)";     $Content = "405";
        }elseif(strpos($Content, '406') !== false){
            $responseCode = "406 (Not Acceptable)";     $Content = "406";
        }elseif(strpos($Content, '412') !== false){
            $responseCode = "412 (Precondition Failed)";    $Content = "412";
        }elseif(strpos($Content, '415') !== false){
            $responseCode = "415 (Unsupported Media Type)";     $Content = "415";
        }elseif(strpos($Content, '500') !== false){
            $responseCode = "500 (Internal Server Error)";  $Content = "500";
        }elseif(strpos($Content, '501') !== false){
            $responseCode = "501 (Not Implemented)";    $Content = "501";
        } else{
            $responseCode = "Response Unknown!"; 
        }  
        if($passCheck == 0){
            return $responseCode;
        }else{
            return $Content;
        } 
    } 
    /*
        SQL Query to get Data from bsct_coindata
    */
    $sql = "
        SELECT
            --- records from db with website link to be checked
        FROM
            --- database table 
    ";  
    /*
        Use PHP PDO Process to fetch the Data
    */
    $statement = $dbh->query($sql);  
    $statement->setFetchMode(PDO::FETCH_ASSOC); 
    while ($row = $statement->fetch()):      
    /*
        Use cURL process for checking. 
    */
        $url = $row['websitelinkfromdatabastable'];
        $ch = curl_init($url);
        // We need to get all header returns of the website
        curl_setopt($ch, CURLOPT_HEADER, true); 
        // We dont need the body of the websites we checked. 
        curl_setopt($ch, CURLOPT_NOBODY, true); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
        // Use FollowLocation cUrl Option to trace the last return of the header
        // because when it returns 301, we need to get if redirects is available.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    /*
        cUrl returns 'HTTP 1.1 (StatusCode) (Info)
        - We only need the status code as of now, so we need to check what code is inside the string returned.
        - We need to Pass the $httpcode variable to Function 'AllResponse' to perform checking and to return the needed code.
    */
        echo 'HTTP code: ' .AllResponse( $httpcode , 0 ) . ' <br>';
    
?>