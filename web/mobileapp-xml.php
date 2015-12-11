<?php
$st = microtime(true);
$data = file_get_contents('php://input');
/* MOBILE APP */

function return_state_by_abbreviation($abb){
	$states=array(
			"AL"=>"Alabama", 
			"AK"=>"Alaska", 
			"AZ"=>"Arizona", 
			"AR"=>"Arkansas", 
			"CA"=>"California", 
			"CO"=>"Colorado", 
			"CT"=>"Connecticut", 
			"DE"=>"Delaware", 
			"DC"=>"District Of Columbia", 
			"FL"=>"Florida", 
			"GA"=>"Georgia", 
			"HI"=>"Hawaii", 
			"ID"=>"Idaho", 
			"IL"=>"Illinois", 
			"IN"=>"Indiana", 
			"IA"=>"Iowa", 
			"KS"=>"Kansas", 
			"KY"=>"Kentucky", 
			"LA"=>"Louisiana", 
			"ME"=>"Maine", 
			"MD"=>"Maryland", 
			"MA"=>"Massachusetts", 
			"MI"=>"Michigan", 
			"MN"=>"Minnesota", 
			"MS"=>"Mississippi", 
			"MO"=>"Missouri", 
			"MT"=>"Montana", 
			"NE"=>"Nebraska", 
			"NV"=>"Nevada", 
			"NH"=>"New Hampshire", 
			"NJ"=>"New Jersey", 
			"NM"=>"New Mexico", 
			"NY"=>"New York", 
			"NC"=>"North Carolina", 
			"ND"=>"North Dakota", 
			"OH"=>"Ohio", 
			"OK"=>"Oklahoma", 
			"OR"=>"Oregon", 
			"PA"=>"Pennsylvania", 
			"RI"=>"Rhode Island", 
			"SC"=>"South Carolina", 
			"SD"=>"South Dakota", 
			"TN"=>"Tennessee", 
			"TX"=>"Texas", 
			"UT"=>"Utah", 
			"VT"=>"Vermont", 
			"VA"=>"Virginia", 
			"WA"=>"Washington", 
			"WV"=>"West Virginia", 
			"WI"=>"Wisconsin", 
			"WY"=>"Wyoming",
		);
	if (array_key_exists(strtoupper($abb), $states)) {
	    return $states[strtoupper($abb)];
	}else{
		return $abb;
	}
}

$data = json_decode($data, TRUE);
//if($data['intMaxRecords'] > 20)


if( $data['fltLatitude'] == '' || $data['fltLongitude'] == '' ){
	
	$data = $_GET;
}

if( $data['fltLatitude'] == '' || $data['fltLongitude'] == '' ){
	
	$data = $_POST;
}
function get($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}
$data['intMaxRecords'] = 100;
//fwrite($fpReq, print_r($data,1)."\n\n");
//fwrite($fpReq, "GET---".print_r($_GET,1)."\n\n");
//fwrite($fpReq, "POST---".print_r($_POST,1)."\n\n");
isset($data['fltLatitude'])?$fltLatitude = $data['fltLatitude']:$fltLatitude = '';
isset($data['fltLongitude'])?$fltLongitude = $data['fltLongitude']:$fltLongitude = '';
isset($data['intMaxRecords'])?$intMaxRecords = ' LIMIT '.$data['intMaxRecords']:$intMaxRecords = '';
isset($data['intMaxDistanceInMiles'])?$intMaxDistanceInMiles = $data['intMaxDistanceInMiles']:$intMaxDistanceInMiles = 25;
//$strCompanyIDs = $_REQUEST['strCompanyIDs'];
//print_r($_REQUEST);

if( $fltLatitude == '' || $fltLongitude == '' ){
	die('lat empty');
	return;
}

$st = microtime(true);
$url = "http://52.24.27.64/api/stores/search?latitude=".$data['fltLatitude']."&longitude=".$data['fltLongitude'];
$content =json_decode( file_get_contents($url));
$content = $content->message;
$et = microtime(true);

$create_xml = '<?xml version="1.0" encoding="utf-8"?> <ArrayOfCenter xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://tempuri.org/">';
foreach($content as $item){
	if($c['Status'] == 'Open'){
	$create_xml .='<Center>'.
		'<ShopID>'. $item->storeId .'</ShopID>'.
		'<ShopNumber>'. $item->storeId .'</ShopNumber>'.
		'<CompanyID>1</CompanyID>'.
		'<StreetAddress1>'. $item->streetAddress1 .'</StreetAddress1>'.
		'<StreetAddress2>'. $item->streetAddress2 .'</StreetAddress2>'.
		'<StreetAddress3 />'.
		'<City>'.  $item->locationCity .'</City>'.
		'<StateProvinceTerritory>'. $item->locationState .'</StateProvinceTerritory>'.
		'<FullStateProvinceTerritoryName>'. return_state_by_abbreviation($item->locationState) .'</FullStateProvinceTerritoryName>'.
		'<PostalCode>'. $item->locationPostalCode .'</PostalCode>'.
		'<LocationCountry>'. 'US' .'</LocationCountry>'.
		'<Latitude>'. $item->latitude .'</Latitude>'.
		'<Longitude>'. $item->longitude .'</Longitude>'.
		'<LocationDirections>'. $item->locationDirections .'</LocationDirections>'.
		'<LocationPhone1>'. $item->phone .'</LocationPhone1>'.
		'<LocationPhone2>'.$item->semCamPhone.'</LocationPhone2>'.
		'<DealerMessage>**We are a full service repair/maintenance facility located at '. $item->streetAddress1 .'. *******COME TO US FOR YOUR FACTORY SCHEDULED MAINTAINANCE. *OIL CHANGE $19.95 - FREE BRAKE INSPECTION ***'. $item->locationState .' STATE INSPECTION***EMISSIONS REPAIR. ** Call us for convenient appointment.</DealerMessage>'.
		'<HoursWeekdayOpen>'. $item->hoursWeekdayOpen .'</HoursWeekdayOpen>'.
		'<HoursWeekdayClose>'. $item->hoursWeekdayClose .'</HoursWeekdayClose>'.
		'<HoursSaturdayOpen>'. $item->hoursSaturdayOpen .'</HoursSaturdayOpen>'.
		'<HoursSaturdayClose>'. $item->hoursSaturdayClose .'</HoursSaturdayClose>'.
		'<HoursSundayOpen>'. $item->hoursSundayOpen .'</HoursSundayOpen>'.
		'<HoursSundayClose>'. $item->hoursSundayClose .'</HoursSundayClose>'.
		'<DistanceFromStartingPoint>'. $item->distance .'</DistanceFromStartingPoint>'.
		'<OnlineScheduling />'.
		'<KeyWord>brake-service</KeyWord>'.
		'<UrlDisplayText>In need of brake service? At Meineke, our trained technicians will do a &lt;b&gt;FREE*&lt;/b&gt; no-obligation brake inspection at the first hint of trouble. Our goal is to provide you with affordable options to get your vehicle safely on the road. Meineke offers complete brake service including machining of rotors, brake pad and shoe replacement, brake fluid flush, replacement of master cylinders, brake lines and hoses, and calipers. Call today to schedule an appointment!  &lt;br/&gt;&lt;b&gt;*&lt;/b&gt;&lt;i&gt;At participating Meineke locations.&lt;/i&gt;</UrlDisplayText>'.
		'</Center>';
	}
}
$create_xml .='</ArrayOfCenter>';
header("Content-type: text/xml; charset=utf-8");
echo $create_xml;
?>
