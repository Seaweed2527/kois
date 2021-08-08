<?php
//set waktu 
date_default_timezone_set("Asia/Jakarta");

// API access key from Google API's Console
define("API_ACCESS_KEY", "AAAAkoEgVfs:APA91bENm04ADdF6fQrev8MhdvHO-6i4LoQ0QEZVEtB-r0FkAmAd4f58EhXKVSIPLLqTNwYm8wkY_C3I-LhHRJz1ecmtBt8ZEF0v0S_FTg2ns9e_EzzwFEuf4cyleYaIi0ppqCJEOypx");
$registrationIds = array($_GET['id']);

include "inc/func.php";
$ponik = new hidroponik;
$link = $ponik->koneksi();

//alat mati atau tidak pandreas
$q = mysqli_query($link, "SELECT dgw, tgw from moni where sn ='2021070003' order by no desc ");
$q2 = mysqli_fetch_row($q);
$dgw = $q2[0];
$tgw = $q2[1];
echo "jam alat : $dgw || $tgw<br>";

$awal  = ("$dgw $tgw");
$akhir = date("Y-m-d H:i:s");
$selisih   = datediff($awal, $akhir);
// echo " selisih :$selisih menit<br>";
$delay  = $selisih['Secon_total'] / 3600;
$delay  = round($delay, 1);
echo " selisih notif:$delay jam<br>";

function datediff($awal, $akhir)
{
	$awal = strtotime($awal);
	$akhir = strtotime($akhir);
	$diff_secs = abs($awal - $akhir);
	return array("Secon_total" => floor($diff_secs));
}

$query = mysqli_query($link, "SELECT thd_ph1, thd_ph2, thd_tds1, thd_tds2, thd_ox1, thd_ox2, thd_sn1, thd_sn2 from plant_user where sn ='2021070003' order by no desc ");
$query2 = mysqli_fetch_row($query);
$thd_ph1 = $query2[0];
$thd_ph2 = $query2[1];
$thd_tds1 = $query2[2];
$thd_tds2 = $query2[3];
$thd_ox1 = $query2[4];
$thd_ox2 = $query2[5];
$thd_sn1 = $query2[6];
$thd_sn2 = $query2[7];

echo "threshold atas : $thd_ph1 || $thd_ph2 || $thd_tds1 || $thd_tds2 || $thd_ox1 || $thd_ox2 || $thd_sn1 || $thd_sn2 <br>";

//pH
$query3 = mysqli_query($link, "SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='pH' and moni.sn='2021070003' ORDER BY moni_detail.id DESC limit 1");
$q4 = mysqli_fetch_row($query3);
$pH = $q4[0];
echo "pH saat ini : $pH <br>";

//TDS
$query4 = mysqli_query($link, "SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='TDS' and moni.sn='2021070003' ORDER BY moni_detail.id DESC limit 1");
$q5 = mysqli_fetch_row($query4);
$TDS = $q5[0];
echo "TDS saat ini : $TDS<br>";

//Oxygen
//Water Level
$query5 = mysqli_query($link, "SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='oksigen' and moni.sn='2021070003' ORDER BY moni_detail.id DESC limit 1");
$q6 = mysqli_fetch_row($query5);
$DO = $q6[0];
echo "Kadar oksigen saat ini : $DO<br>";

//suhu nutrisi
$query6 = mysqli_query($link, "SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='reservoir_temp' and moni.sn='2021070003' ORDER BY moni_detail.id DESC limit 1");
$q7 = mysqli_fetch_row($query6);
$sunut = $q7[0];
echo "Suhu nutrisi saat ini : $sunut<br>";

$fieldsList = [];
//notif alat mati
if ($delay > 1) {

	mysqli_query($link, " insert into dami(id, selisih) values ('2021', '$delay')");
	// prep the bundle
	$msg = array(
		'message' 	=> 'Please check your device and your wireless connection !!!',
		'title'		=> 'Warning !!!',
		'vibrate'	=> 1,
		'sound'		=> 1,
		'largeIcon'	=> 'large_icon',
		'smallIcon'	=> 'small_icon'
	);
	$fields = array(
		'to'  => '/topics/systemkoi_pakroy',
		'data'	=> $msg
	);
	$fieldsList[] = $fields;
}

//notif pH lebih dari thd
if ($pH > $thd_ph2 || $pH < $thd_ph1) {
	mysqli_query($link, " insert into dami(id) values ('2021', '$pH')");
	// prep the bundle
	$msg = array(
		'message' 	=> 'Current pH : ' . $pH,
		'title'		=> 'Warning !!!',
		'vibrate'	=> 1,
		'sound'		=> 1,
		'largeIcon'	=> 'large_icon',
		'smallIcon'	=> 'small_icon'
	);
	$fields = array(
		'to'  => '/topics/pHkoi_pakroy',
		'data'	=> $msg
	);
	$fieldsList[] = $fields;
}


//notif TDS lebih dari thd
if ($TDS > $thd_tds2 || $TDS < $thd_tds1) {
	mysqli_query($link, " insert into dami(id) values ('2021', '$TDS')");
	// prep the bundle
	$msg = array(
		'message' 	=> 'Current TDS :' . $TDS,
		'title'		=> 'Warning !!!',
		'vibrate'	=> 1,
		'sound'		=> 1,
		'largeIcon'	=> 'large_icon',
		'smallIcon'	=> 'small_icon'
	);
	$fields = array(
		'to'  => '/topics/TDSkoi_pakroy',
		'data'	=> $msg
	);
	$fieldsList[] = $fields;
}


//notif Oksigen kurang dari thd
if ($DO > $thd_ox2 || $DO < $thd_ox1) {
	mysqli_query($link, " insert into dami(id) values ('2021', '$DO')");
	// prep the bundle
	$msg = array(
		'message' 	=> 'Current oxygen level :' . $DO,
		'title'		=> 'Warning !!!',
		'vibrate'	=> 1,
		'sound'		=> 1,
		'largeIcon'	=> 'large_icon',
		'smallIcon'	=> 'small_icon'
	);
	$fields = array(
		'to'  => '/topics/oksigen_pakroy',
		'data'	=> $msg
	);
	$fieldsList[] = $fields;
}

//notif Suhu Nutrisi lebih dari thd
if ($sunut > $thd_sn2 || $sunut < $thd_sn1) {
	mysqli_query($link, " insert into dami(id) values ('2021')");
	// prep the bundle
	$msg = array(
		'message' 	=> 'Current Water Temperature :' . $sunut,
		'title'		=> 'Warning !!!',
		'vibrate'	=> 1,
		'sound'		=> 1,
		'largeIcon'	=> 'large_icon',
		'smallIcon'	=> 'small_icon'
	);
	$fields = array(
		'to'  => '/topics/nutrisikoi_pakroy',
		'data'	=> $msg
	);
	$fieldsList[] = $fields;
}

for ($i = 0; $i < count($fieldsList); $i++) {

	$headers = array(
		'Authorization: key=' . API_ACCESS_KEY,
		'Content-Type: application/json'
	);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fieldsList[$i]));
	$result = curl_exec($ch);
	curl_close($ch);
	echo $result;
}
