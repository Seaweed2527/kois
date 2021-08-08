<?php
//set waktu 
date_default_timezone_set("Asia/Jakarta");

// API access key from Google API's Console
define("API_ACCESS_KEY", "AAAAkoEgVfs:APA91bENm04ADdF6fQrev8MhdvHO-6i4LoQ0QEZVEtB-r0FkAmAd4f58EhXKVSIPLLqTNwYm8wkY_C3I-LhHRJz1ecmtBt8ZEF0v0S_FTg2ns9e_EzzwFEuf4cyleYaIi0ppqCJEOypx");
$registrationIds = array($_GET['id']);

include "inc/func.php";
$ponik = new hidroponik;
$link = $ponik->koneksi();

//alat mati atau tidak
$q = mysqli_query($link, "SELECT dgw, tgw from moni where sn ='2021040001' order by no desc ");
$q2 = mysqli_fetch_row($q);
$dgw = $q2[0];
$tgw = $q2[1];
echo "jam alat : $dgw || $tgw<br>";


function datediff($awal, $akhir)
{
	$awal = strtotime($awal);
	$akhir = strtotime($akhir);
	$diff_secs = abs($awal - $akhir);
	return array("Secon_total" => floor($diff_secs));
}

$awal  = ("$dgw $tgw");
$akhir = date("Y-m-d H:i:s");
$selisih   = datediff($awal, $akhir);
$delay  = $selisih['Secon_total'] / 3600;
$delay  = round($delay, 1);
echo " selisih notif:$delay jam<br>";


$query = mysqli_query($link, "SELECT thd_ph1, thd_ph2, thd_tds1, thd_tds2, thd_ox1, thd_ox2, thd_sn1, thd_sn2 from plant_user where sn ='2021040001' order by no desc ");
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
$query3 = mysqli_query($link, "SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='pH' and moni.sn='2021040001' ORDER BY moni_detail.id DESC limit 1");
$q4 = mysqli_fetch_row($query3);
$pH = $q4[0];
echo "pH saat ini : $pH <br>";

//TDS
$query4 = mysqli_query($link, "SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='TDS' and moni.sn='2021040001' ORDER BY moni_detail.id DESC limit 1");
$q5 = mysqli_fetch_row($query4);
$TDS = $q5[0];
echo "TDS saat ini : $TDS<br>";

//Oxygen
//Water Level
$query5 = mysqli_query($link, "SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='oksigen' and moni.sn='2021040001' ORDER BY moni_detail.id DESC limit 1");
$q6 = mysqli_fetch_row($query5);
$DO = $q6[0];
echo "Kadar oksigen saat ini : $DO<br>";

//suhu nutrisi
$query6 = mysqli_query($link, "SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='reservoir_temp' and moni.sn='2021040001' ORDER BY moni_detail.id DESC limit 1");
$q7 = mysqli_fetch_row($query6);
$sunut = $q7[0];
echo "Suhu nutrisi saat ini : $sunut<br>";

$fieldsList = [];


//date
$e = date("H:i:s");
$jame = explode(':', $e);
$jam_end = $jame[0];

$fieldsList = [];

$date = date("Y-m-d");
//notif alat mati
if ($delay > 1) {
	$b = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2021040001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'alat' order by no desc limit 1");
	$bb = mysqli_fetch_row($b);

	if (empty($bb)) {
		// prep the bundle
		mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2021040001','$date', '$e', 'alat', 'Alat mati yo!')");
		$msg = array(
			'message' 	=> 'Please check your device and your wireless connection !!!',
			'title'		=> 'Warning !!!',
			'vibrate'	=> 1,
			'sound'		=> 1,
			'largeIcon'	=> 'large_icon',
			'smallIcon'	=> 'small_icon'
		);
		$fields = array(
			'to'  => '/topics/systempakren',
			'data'	=> $msg
		);
		$fieldsList[] = $fields;
	} else {
		$a = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2021040001' and tipe='alat' ORDER BY no DESC limit 1");
		$a2 = mysqli_fetch_row($a);
		$tgl = $a2[0];
		$pkl = $a2[1];
		$tipe = $a2[2];

		echo "<br> $tgl | $pkl | $tipe<br>";

		function datediffe($awal_a, $akhir_a)
		{
			$awal_a = strtotime($awal_a);
			$akhir_a = strtotime($akhir_a);
			$diff_secs_a = abs($awal_a - $akhir_a);
			return array("Secon_total" => floor($diff_secs_a));
		}
		//monik
		$awal_a  = ("$tgl $pkl");
		$akhir_a = date("Y-m-d H:i:s");
		$selisih_a = datediffe($awal_a, $akhir_a);
		$delay_a  = $selisih_a['Secon_total'] / 60;
		$delay_a  = round($delay_a, 1);
		echo "selisih notif1111 : $delay_a menit<br>";

		if ($delay_a >= 720) {
			mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2021040001','$date', '$akhir', 'alat', 'Alat mati yo!')");

			$msg = array(
				'message' 	=> 'Please check your device and your wireless connection !!!',
				'title'		=> 'Warning !!!',
				'vibrate'	=> 1,
				'sound'		=> 1,
				'largeIcon'	=> 'large_icon',
				'smallIcon'	=> 'small_icon'
			);
			$fields = array(
				'to'  => '/topics/systempakren',
				'data'	=> $msg
			);
			$fieldsList[] = $fields;
		};
	};
};

echo "$pH | $thd_ph2 || $pH | $thd_ph1";
//notif pH lebih dari thd
if ($pH > $thd_ph2 || $pH < $thd_ph1) {
	$c = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2021040001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'pH' order by no desc limit 1");
	$cc = mysqli_fetch_row($c);

	if (empty($cc)) {
		mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2021040001','$date', '$e', 'pH', 'pH is abnormal')");
		$msg = array(
			'message' 	=> 'Current pH : ' . $pH,
			'title'		=> 'Warning !!!',
			'vibrate'	=> 1,
			'sound'		=> 1,
			'largeIcon'	=> 'large_icon',
			'smallIcon'	=> 'small_icon'
		);
		$fields = array(
			'to'  => '/topics/pHpakrenald',
			'data'	=> $msg
		);
		$fieldsList[] = $fields;
	} else {
		$b = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2021040001' and tipe='pH' ORDER BY no DESC limit 1");
		$b2 = mysqli_fetch_row($b);
		$tgl = $b2[0];
		$pkl = $b2[1];
		$tipe = $b2[2];

		echo "<br> $tgl | $pkl | $tipe<br>";

		function datediffee($awal_aa, $akhir_aa)
		{
			$awal_aa = strtotime($awal_aa);
			$akhir_aa = strtotime($akhir_aa);
			$diff_secs_aa = abs($awal_aa - $akhir_aa);
			return array("Secon_total" => floor($diff_secs_aa));
		}
		//monik
		$awal_aa  = ("$tgl $pkl");
		$akhir_aa = date("Y-m-d H:i:s");
		$selisih_aa  = datediffee($awal_aa, $akhir_aa);
		$delay_aa  = $selisih_aa['Secon_total'] / 60;
		$delay_aa  = round($delay_aa, 1);
		echo "selisih notif2222 : $delay_aa menit<br>";

		if ($delay_aa >= 720) {
			mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2021040001','$date', '$akhir', 'pH', 'pH is abnormal!')");
			$msg = array(
				'message' 	=> 'Current pH : ' . $pH,
				'title'		=> 'Warning !!!',
				'vibrate'	=> 1,
				'sound'		=> 1,
				'largeIcon'	=> 'large_icon',
				'smallIcon'	=> 'small_icon'
			);
			$fields = array(
				'to'  => '/topics/pHpakrenald',
				'data'	=> $msg
			);
			$fieldsList[] = $fields;
		};
	};
};

echo "$TDS | $thd_tds2 || $TDS | $thd_tds1";
//notif TDS lebih dari thd
if ($TDS > $thd_tds2 || $TDS < $thd_tds1) {
	$d = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2021040001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'TDS' order by no desc limit 1");
	$dd = mysqli_fetch_row($d);

	if (empty($dd)) {
		mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2021040001','$date', '$e', 'TDS', 'TDS is abnormal')");
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
			'to'  => '/topics/TDSpakrenald',
			'data'	=> $msg
		);
		$fieldsList[] = $fields;
	} else {
		$c = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2021040001' and tipe='TDS' ORDER BY no DESC limit 1");
		$c2 = mysqli_fetch_row($c);
		$tgl = $c2[0];
		$pkl = $c2[1];
		$tipe = $c2[2];

		echo "<br> $tgl | $pkl | $tipe<br>";

		function datediff2($awal_b, $akhir_b)
		{
			$awal_b = strtotime($awal_b);
			$akhir_b = strtotime($akhir_b);
			$diff_secs_b = abs($awal_b - $akhir_b);
			return array("Secon_total" => floor($diff_secs_b));
		}
		//monik
		$awal_b  = ("$tgl $pkl");
		$akhir_b = date("Y-m-d H:i:s");
		$selisih_b  = datediff2($awal_b, $akhir_b);
		$delay_b  = $selisih_b['Secon_total'] / 60;
		$delay_b  = round($delay_b, 1);
		echo "selisih notif333 : $delay_b menit<br>";

		if ($delay_b >= 720) {
			mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2021040001','$date', '$akhir', 'TDS', 'TDS is abnormal!')");
			$msg = array(
				'message' 	=> 'Current TDS :' . $TDS,
				'title'		=> 'Warning !!!',
				'vibrate'	=> 1,
				'sound'		=> 1,
				'largeIcon'	=> 'large_icon',
				'smallIcon'	=> 'small_icon'
			);
			$fields = array(
				'to'  => '/topics/TDSpakrenald',
				'data'	=> $msg
			);
			$fieldsList[] = $fields;
		};
	};
};


//notif Oksigen kurang dari thd
echo "$DO | $thd_ox2 || $DO | $thd_ox1";

if ($DO > $thd_ox2 || $DO < $thd_ox1) {
	$n = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2021040001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'oksigen' order by no desc limit 1");
	$nn = mysqli_fetch_row($n);

	if (empty($nn)) {
		// prep the bundle
		mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2021040001','$date', '$e', 'oksigen', 'Oxygen Level is abnormal')");
		$msg = array(
			'message' 	=> 'Current oxygen level :' . $DO,
			'title'		=> 'Warning !!!',
			'vibrate'	=> 1,
			'sound'		=> 1,
			'largeIcon'	=> 'large_icon',
			'smallIcon'	=> 'small_icon'
		);
		$fields = array(
			'to'  => '/topics/oksigenpakrenald',
			'data'	=> $msg
		);
		$fieldsList[] = $fields;
	} else {
		$d = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2021040001' and tipe='oksigen' ORDER BY no DESC limit 1");
		$d2 = mysqli_fetch_row($d);
		$tgl = $d2[0];
		$pkl = $d2[1];
		$tipe = $d2[2];

		echo "<br> $tgl | $pkl | $tipe<br>";

		function datediff3($awal_x, $akhir_x)
		{
			$awal_x = strtotime($awal_x);
			$akhir_x = strtotime($akhir_x);
			$diff_secs_x = abs($awal_x - $akhir_x);
			return array("Secon_total" => floor($diff_secs_x));
		}
		//monik
		$awal_x  = ("$tgl $pkl");
		$akhir_x = date("Y-m-d H:i:s");
		$selisih_x  = datediff3($awal_x, $akhir_x);
		$delay_x  = $selisih_x['Secon_total'] / 60;
		$delay_x  = round($delay_x, 1);
		echo "selisih notif444 : $delay_x menit<br>";

		if ($delay_x >= 720) {
			mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2021040001','$date', '$akhir', 'oksigen', 'Oxygen level is abnormal!')");
			$msg = array(
				'message' 	=> 'Current oxygen level :' . $DO,
				'title'		=> 'Warning !!!',
				'vibrate'	=> 1,
				'sound'		=> 1,
				'largeIcon'	=> 'large_icon',
				'smallIcon'	=> 'small_icon'
			);
			$fields = array(
				'to'  => '/topics/oksigenpakrenald',
				'data'	=> $msg
			);
			$fieldsList[] = $fields;
		};
	};
};

//notif Suhu Nutrisi lebih dari thd
echo "$Sn | $thd_sn2 || $Sn | $thd_sn1";

if ($sunut > $thd_sn2 || $sunut < $thd_sn1) {
	$f = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2021040001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'Sn' order by no desc limit 1");
	$ff = mysqli_fetch_row($f);

	// prep the bundle
	if (empty($ff)) {
		mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2021040001','$date', '$e', 'Sn', 'Water Temperature is abnormal')");
		$msg = array(
			'message' 	=> 'Current Water Temperature :' . $sunut,
			'title'		=> 'Warning !!!',
			'vibrate'	=> 1,
			'sound'		=> 1,
			'largeIcon'	=> 'large_icon',
			'smallIcon'	=> 'small_icon'
		);
		$fields = array(
			'to'  => '/topics/nutrisikoipakrenald',
			'data'	=> $msg
		);
		$fieldsList[] = $fields;
	} else {
		$z = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2021040001' and tipe='Sn' ORDER BY no DESC limit 1");
		$z2 = mysqli_fetch_row($z);
		$tgl = $z2[0];
		$pkl = $z2[1];
		$tipe = $z2[2];

		echo "<br> $tgl | $pkl | $tipe<br>";

		function datediff4($awal_z, $akhir_z)
		{
			$awal_z = strtotime($awal_z);
			$akhir_z = strtotime($akhir_z);
			$diff_secs_z = abs($awal_z - $akhir_z);
			return array("Secon_total" => floor($diff_secs_z));
		}
		//monik
		$awal_z  = ("$tgl $pkl");
		$akhir_z = date("Y-m-d H:i:s");
		$selisih_z  = datediff4($awal_z, $akhir_z);
		$delay_z  = $selisih_z['Secon_total'] / 60;
		$delay_z  = round($delay_z, 1);
		echo "selisih notif555 : $delay_z menit<br>";

		if ($delay_z >= 720) {
			mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2021040001','$date', '$akhir', 'Sn', 'Water Temperature is abnormal'");
			// prep the bundle
			$msg = array(
				'message' 	=> 'Current Water Temperature :' . $Sn,
				'title'		=> 'Warning !!!',
				'vibrate'	=> 1,
				'sound'		=> 1,
				'largeIcon'	=> 'large_icon',
				'smallIcon'	=> 'small_icon'
			);
			$fields = array(
				'to'  => '/topics/nutrisikoipakrenald',
				'data'	=> $msg
			);
			$fieldsList[] = $fields;
		};
	};
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
