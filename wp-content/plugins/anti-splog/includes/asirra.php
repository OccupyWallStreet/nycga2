<?php
/*
Plugin Name: Asirra
Plugin URI: http://research.microsoft.com/asirra/
Description: Uses the Asirra web service (<a href="http://research.microsoft.com/asirra/">http://research.microsoft.com/asirra/</a>) to add a pleasant image-based HIP for comments
Author: Jon Howell
Version: 1.0
Author URI: http://research.microsoft.com/~howell/
*/
/*  Copyright 2007  Jon Howell  (contact email : asirra@microsoft.com)
**
**  This program is in the public domain.
*/

class AsirraValidator
{
	var $inResult = 0;
	var $passed = 0;
	
	function AsirraValidator($ticket)
	{
		global $g_this;	// Yuck. Is there a way to have callback methods see my $this without using a global in PHP?
		$g_this = $this;
		$g_this->dbg = "";

		$g_this->dbg .= "<br>ticket = ".$ticket;

		$AsirraServiceUrl = "http://challenge.asirra.com/cgi/Asirra";
	
		$url = $AsirraServiceUrl."?action=ValidateTicket&ticket=".$ticket;
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$resultXml = curl_exec($ch);
		curl_close($ch);

		$xml_parser = xml_parser_create();

  function startElement($parser, $name, $attrs)
		{
			global $g_this;
			$g_this->inResult = ($name=="RESULT");
			$g_this->dbg .= "<br>start ".$name." ir=".$g_this->inResult;
		}

		function endElement($name)
		{
			global $g_this;
			$g_this->inResult = 0;
			$g_this->dbg .= "<br>end ".$name;
		}

		function characterData($parer, $data)
		{
			global $g_this;
			$g_this->dbg .= "<br>cd ir ".$g_this->inResult." data=".$data;
			if ($g_this->inResult && $data == "Pass")
			{
				$g_this->dbg .= "<br>setting PASS";
				$g_this->passed = 1;
			}
		}

		xml_set_element_handler($xml_parser, startElement, endElement);
		xml_set_character_data_handler($xml_parser, characterData);
		xml_parse($xml_parser, $resultXml, 1);
		xml_parser_free($xml_parser);

		$g_this->dbg .= "<p><pre>XML: ".$resultXml."</pre>";

	}



}

?>