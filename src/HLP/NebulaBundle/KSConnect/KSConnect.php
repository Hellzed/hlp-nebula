<?php

/*
* Copyright 2014 HLP-Nebula authors, see NOTICE file

*
* Licensed under the EUPL, Version 1.1 or – as soon they
will be approved by the European Commission - subsequent
versions of the EUPL (the "Licence");
* You may not use this work except in compliance with the
Licence.
* You may obtain a copy of the Licence at:
*
*
http://ec.europa.eu/idabc/eupl

*
* Unless required by applicable law or agreed to in
writing, software distributed under the Licence is
distributed on an "AS IS" basis,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
express or implied.
* See the Licence for the specific language governing
permissions and limitations under the Licence.
*/

namespace HLP\NebulaBundle\KSConnect;

class KSConnect
{
  protected $sendURL;
  
  protected $retrieveURL;
  
  protected $converterURL;
  
  protected $wsURL;
  
  protected $APIkey;
  
  protected $ksconn;
  
  public function __construct($server, $APIkey, $secure)
  {
    $s = '';
    
    if($secure)
    {
      $s = 's';
    }
    
    $this->sendURL = 'http'.$s.'://'.$server.'/api/converter/request';
    $this->retrieveURL = 'http'.$s.'://'.$server.'/api/converter/retrieve';
    $this->converterURL = 'http'.$s.'://'.$server.'/static/converter.js';
    $this->wsURL = 'ws'.$s.'://'.$server.'/ws/converter';
    
    $this->APIkey = $APIkey;
    
    $this->ksconn = curl_init();
    curl_setopt($this->ksconn, CURLOPT_FAILONERROR, true);
    curl_setopt($this->ksconn, CURLOPT_RETURNTRANSFER, true);
  }
  
  public function getConverterURL()
  {
    return $this->converterURL;
  }
  
  public function getWsURL()
  {
    return $this->wsURL;
  }
  
  public function sendData($data, $webhook)
  {
    $fields = array(
			  'passwd'  => $this->APIkey, 
			  'data'    => $data,
			  'webhook' => $webhook
	  );
	  
	  $fields_string = '';
	  foreach($fields as $key=>$value)
    {
      $fields_string .= $key.'='.urlencode($value).'&';
    }
    $fields_string = rtrim($fields_string, '&');
	  
	  curl_setopt($this->ksconn, CURLOPT_URL, $this->sendURL);
    curl_setopt($this->ksconn, CURLOPT_POST, count($fields));
    curl_setopt($this->ksconn, CURLOPT_POSTFIELDS, $fields_string);
    
    return curl_exec($this->ksconn);
  }
  
  public function retrieveData($ticket, $token)
  {
    $fields = array(
			  'ticket' => $ticket, 
			  'token'  => $token,
	  );
	  
	  $fields_string = '';
	  foreach($fields as $key=>$value)
    {
      $fields_string .= $key.'='.urlencode($value).'&';
    }
    $fields_string = rtrim($fields_string, '&');
	  
	  curl_setopt($this->ksconn, CURLOPT_URL, $this->retrieveURL);
    curl_setopt($this->ksconn, CURLOPT_POST, count($fields));
    curl_setopt($this->ksconn, CURLOPT_POSTFIELDS, $fields_string);
    
    return curl_exec($this->ksconn);
  }
  
  public function __destruct()
  {
    curl_close($this->ksconn);
  }
}
