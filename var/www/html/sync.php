<?php
while(1==1){
  //echo dirname(__FILE__); die();
  $url_prefix = "http://192.168.123.45/DCIM/102CANON/";
  $url = $url_prefix;
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);

  $http_resp = curl_exec($curl);
  $resp = explode("\n", $http_resp);
  $files_sd_name = array();
  $files_sd_size = array();

  // enumerate .JPG files on SD card

  foreach ($resp as $line){
    if (strpos($line, "wlansd.push({") > -1){
      $start = strpos($line, "\"fname\":\"") + 9;
      $stop = strpos($line, '"', $start+1);
      $filename = substr($line, $start, $stop-$start);
      $start = strpos($line, "\"fsize\":") + 8;
      $stop = strpos($line, '"', $start+1) - 1;
      $filesize = substr($line, $start, $stop-$start);
      if (strpos($filename, ".JPG") > -1){
        array_push($files_sd_name, $filename);
      }
    }
  }

  $files_to_sync_from_sd_name = $files_sd_name;
  //$files_to_sync_from_sd_size

  $files_local = scandir(dirname(__FILE__)."/pics/");
  array_shift($files_local);
  array_shift($files_local);

  logme("\nfiles in /pics/:\n");
//  echo "\nfiles in /pics/:\n";
  logme(print_r($files_local, true));

  foreach ($files_local as $filename_local){
    if (array_search($filename_local, $files_to_sync_from_sd_name)>-1){
      array_splice($files_to_sync_from_sd_name, array_search($filename_local, $files_to_sync_from_sd_name), 1);
    }
  }

  logme("\nfiles on SD card:\n");
  logme(print_r($files_sd_name, true));
  logme("files to sync:\n");
  logme(print_r($files_to_sync_from_sd_name, true));

  $counter = 0;

  foreach ($files_to_sync_from_sd_name as $filename_sd){
    logme($counter++ . "/" . sizeof($files_to_sync_from_sd_name) . ": $filename_sd...");
    $url = $url_prefix.$filename_sd;
    logme("\n$url...\n");
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
    $http_resp = curl_exec($curl);
    curl_close($curl);
    file_put_contents(dirname(__FILE__)."/pics/$filename_sd", $http_resp);
  }

  // enumerate files that are not present in the source, any more

  $files_local = scandir(dirname(__FILE__)."/pics/");
  array_shift($files_local);
  array_shift($files_local);

  logme("\nfiles in /pics/:\n");
  logme(print_r($files_local, true));

  logme("\nfiles on SD card:\n");
  logme(print_r($files_sd_name, true));

  if(sizeof($files_sd_name)>0){
    foreach ($files_local as $filename_local){
    //  echo "FILE: $filename_local\n";
    //logme(print_r($filename_local, true));
    logme("$filename_local\n");
    //  if (!array_search($filename_local, $files_sd_name)){
    // if (array_search($filename_local, $files_sd_name)==-1){
      if (!in_array($filename_local, $files_sd_name)){
        logme("DELETE pics/$filename_local\n");
        unlink(dirname(__FILE__)."/pics/$filename_local");
    //    array_splice($files_local_delete, array_search($filename_local_delete, $files_sd_name), 1);
      }
    }
  } else logme("\nsource (SD card) empty\n");
  sleep(5);
}

function logme($text){
  if (1==1){
    echo $text;
  }
}

?>
