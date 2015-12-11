<?php
microtime(true);
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

if( $data['fltLatitude'] == '' || $data['fltLongitude'] == '' ){
	
	$data = $_GET;
}

if( $data['fltLatitude'] == '' || $data['fltLongitude'] == '' ){
	
	$data = $_POST;
}
isset($data['fltLatitude'])?$fltLatitude = $data['fltLatitude']:$fltLatitude = '';
isset($data['fltLongitude'])?$fltLongitude = $data['fltLongitude']:$fltLongitude = '';
if( $fltLatitude == '' || $fltLongitude == '' ){
	die('lat empty');
	return false;
}

microtime(true);
$url = "http://52.24.27.64/api/stores/search?latitude=".$data['fltLatitude']."&longitude=".$data['fltLongitude'];
$content =json_decode( file_get_contents($url));
$content = $content->message;
microtime(true);

$d = array();
foreach($content as $item){
	$c = array();
	$c['__type'] = 'DrivenBrands.CenterInformation.Center';
	$c['ShopID'] = (int) $item->storeId;
	$c['Status'] = $item->openStatus;
	$c['ShopNumber'] = $item->storeId;
	$c['CompanyID'] = 1;
	$c['StreetAddress1'] = $item->streetAddress1;
	$c['StreetAddress2'] = $item->streetAddress2;
	$c['StreetAddress3'] = '';
	$c['City'] = $item->locationCity;
	$c['StateProvinceTerritory'] = $item->locationState;
	$c['FullStateProvinceTerritoryName'] = return_state_by_abbreviation($item->locationState);
	$c['PostalCode'] = $item->locationPostalCode;
	$c['LocationCountry'] = "US";
	$c['Latitude'] =(float) $item->latitude;
	$c['Longitude'] =(float) $item->longitude;
	$c['LocationDirections'] = $item->locationDirections;
	$c['LocationPhone1'] = str_replace(array('(', ') '), array('', '/'), $item->phone);
	$c['LocationPhone2'] = str_replace(array('(', ') '), array('', '/'), $item->semCamPhone);
	$c['DealerMessage'] = '**We are a full service repair/maintenance facility located at '. rtrim($item->streetAddress1,'.') .'. *******COME TO US FOR YOUR FACTORY SCHEDULED MAINTENANCE. *OIL CHANGE $19.95 - FREE BRAKE INSPECTION ***'. $item->locationState .' STATE INSPECTION***EMISSIONS REPAIR. ** Call us for convenient appointment.';
	$c['HoursWeekdayOpen'] = $item->hoursWeekdayOpen;
	$c['HoursWeekdayClose'] = $item->hoursWeekdayClose;
	$c['HoursSaturdayOpen'] = $item->hoursSaturdayOpen;
	$c['HoursSaturdayClose'] = $item->hoursSaturdayClose;
	$c['HoursSundayOpen'] = $item->hoursSundayOpen;
	$c['HoursSundayClose'] = $item->hoursSundayClose;
	$c['DistanceFromStartingPoint'] = $item->distance;
	$c['OnlineScheduling'] = $item->storeId;
	$c['KeyWord'] = 'brake-service';
	$c['UrlDisplayText'] = 'In need of brake service? At Meineke, our trained technicians will do a &lt;b&gt;FREE*&lt;/b&gt; no-obligation brake inspection at the first hint of trouble. Our goal is to provide you with affordable options to get your vehicle safely on the road. Meineke offers complete brake service including machining of rotors, brake pad and shoe replacement, brake fluid flush, replacement of master cylinders, brake lines and hoses, and calipers. Call today to schedule an appointment!  &lt;br/&gt;&lt;b&gt;*&lt;/b&gt;&lt;i&gt;At participating Meineke locations.&lt;/i&gt;';
	if($c['Status'] == 'Open'){
		$d[] = $c;
	}
}
header("Content-type: application/json; charset=utf-8");
echo json_encode(array('d' => $d));