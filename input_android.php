<?php
//set waktu 
date_default_timezone_set("Asia/Jakarta");

// API access key from Google API's Console
define( 'API_ACCESS_KEY', 'AAAAYMM_sEc:APA91bE3o5IJ49JN1Fo9d3mODBBHpQ_Ds9I8JMCqal7N4GHxejICZO-lT9iR5aB_R53QhtRGzOucHLUfPPmexNm_1iodrOGp7xsTQrsjlKO4W1dwdAy0EobMQV_0sH9VeoGoHV4OqI2O');
$registrationIds = array( $_GET['id'] );

include "inc/func.php";
$ponik = new hidroponik;
$link = $ponik->koneksi();

//alat mati atau tidak monik
$q = mysqli_query($link, "SELECT dgw, tgw from moni where sn ='2020110001' order by no desc ");
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
//monik
$awal  = ("$dgw $tgw");
$akhir = date("Y-m-d H:i:s");
$selisih   = datediff($awal, $akhir);
$delay  = $selisih['Secon_total'] / 3600;
$delay  = round ($delay, 1);
echo " selisih alat:$delay jam<br>";

//threshold maksimum, maksimum, minimum Monik
$query =mysqli_query($link, "SELECT thd_ph2, thd_tds2, thd_wl1, thd_ph1, thd_tds1, thd_sn1, thd_sn2, thd_ch1, thd_ch2, thd_sgh1, thd_sgh2, thd_kgh1, thd_kgh2 from plant_user where sn ='2020110001' order by no desc ");
$query2 = mysqli_fetch_row($query);
$thd_ph2 = $query2[0];
$thd_tds2 = $query2[1];
$thd_wl1 = $query2[2];
$thd_ph1 = $query2[3];
$thd_tds1 = $query2[4];
$thd_sn1 = $query2[5];
$thd_sn2 = $query2[6];
$thd_ch1 = $query2[7];
$thd_ch2 = $query2[8];
$thd_sgh1 = $query2[9];
$thd_sgh2 = $query2[10];
$thd_kgh1 = $query2[11];
$thd_kgh2 = $query2[12];

echo "threshold atas : $thd_ph1 || $thd_ph2 || $thd_wl1 || $thd_tds1 || $thd_tds2 || $thd_sn1 || $thd_sn2 || $thd_ch1 || $thd_ch2 || $thd_sgh1 || $thd_sgh2 || $thd_kgh1 || $thd_kgh2 <br>";

//pH
$query3 = mysqli_query($link,"SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='pH' and moni.sn='2020110001' ORDER BY moni_detail.id DESC limit 1");
$q4 = mysqli_fetch_row($query3);
$pH = $q4[0];
echo "pH saat ini : $pH <br>";

//TDS
$query4 = mysqli_query($link,"SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='TDS' and moni.sn='2020110001' ORDER BY moni_detail.id DESC limit 1");
$q5 = mysqli_fetch_row($query4);
$TDS = $q5[0];
echo "TDS saat ini : $TDS<br>";

//Water Level
$query5 = mysqli_query($link,"SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='distance' and moni.sn='2020110001' ORDER BY moni_detail.id DESC limit 1");
$q6 = mysqli_fetch_row($query5);
$WL = $q6[0];
echo "Water level saat ini : $WL<br>";

//Suhu Nutrisi
$query6 = mysqli_query($link,"SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='reservoir_temp' and moni.sn='2020110001' ORDER BY moni_detail.id DESC limit 1");
$q7 = mysqli_fetch_row($query6);
$Sn = $q7[0];
echo "Suhu nutrisi saat ini : $Sn<br>";

//cahaya
$query7 = mysqli_query($link,"SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='cahaya' and moni.sn='2020110001' ORDER BY moni_detail.id DESC limit 1");
$q8 = mysqli_fetch_row($query7);
$ch = $q8[0];
echo "Terang cahaya saat ini : $ch<br>";

//suhu greenhouse
$query8 = mysqli_query($link,"SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='temperature' and moni.sn='2020110001' ORDER BY moni_detail.id DESC limit 1");
$q9 = mysqli_fetch_row($query8);
$sgh = $q9[0];
echo "Suhu greenhouse saat ini : $sgh<br>";

//kelembapan
$query9 = mysqli_query($link,"SELECT moni_detail.nilai from moni join moni_detail on moni.no =moni_detail.id where moni_detail.sensor='humidity' and moni.sn='2020110001' ORDER BY moni_detail.id DESC limit 1");
$q10 = mysqli_fetch_row($query9);
$kgh = $q10[0];
echo "Kelembapan saat ini : $kgh<br>";

$e = date("H:i:s");
$jame = explode(':', $e);
$jam_end = $jame[0];

$fieldsList = [];

$date = date("Y-m-d");
if ($delay>1){
    $b = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2020110001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'alat' order by no desc limit 1");
    $bb = mysqli_fetch_row($b);
    
    if(empty($bb)){
        mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$e', 'alat', 'Alat mati yo!')");
        $msg = array(
        'message' 	=> 'Please check your device and your wireless connection !!!',
        'title'		=> 'Warning !!!',
        'vibrate'	=> 1,
        'sound'		=> 1,
        'largeIcon'	=> 'large_icon',
        'smallIcon'	=> 'small_icon');
        $fields = array
        (
        	'to'  => '/topics/system',
        	'data'	=> $msg
        );
        $fieldsList[] = $fields;
    } else {
        $a = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2020110001' and tipe='alat' ORDER BY no DESC limit 1");
        $a2 = mysqli_fetch_row($a);
        $tgl = $a2[0];
        $pkl = $a2[1];
        $tipe = $a2[2];
        
        echo "<br> $tgl | $pkl | $tipe<br>";
        
        function datediffe($awal_a, $akhir_a){
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
        $delay_a  = round ($delay_a, 1);
        echo "selisih notif1111 : $delay_a menit<br>";
        
        if($delay_a >= 720){
            mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$akhir', 'alat', 'Alat mati yo!')");
            
            $msg = array(
            'message' 	=> 'Please check your device and your wireless connection !!!',
            'title'		=> 'Warning !!!',
            'vibrate'	=> 1,
            'sound'		=> 1,
            'largeIcon'	=> 'large_icon',
            'smallIcon'	=> 'small_icon');
            $fields = array(
            	'to'  => '/topics/system',
            	'data'	=> $msg
            );
            $fieldsList[] = $fields;
        };
    };
};

echo "$pH | $thd_ph2 || $pH | $thd_ph1";
//notif pH lebih dari thd monik
if ($pH > $thd_ph2 || $pH < $thd_ph1){
    $c = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2020110001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'pH' order by no desc limit 1");
    $cc = mysqli_fetch_row($c);
    
    if(empty($cc)){
        mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$e', 'pH', 'pH is abnormal')");
        $msg = array(
        	'message' 	=> 'Current pH : '.$pH,
        	'title'		=> 'Warning !!!',
        	'vibrate'	=> 1,
        	'sound'		=> 1,
        	'largeIcon'	=> 'large_icon',
        	'smallIcon'	=> 'small_icon');
        $fields = array(
            'to'  => '/topics/kadar_pH',
            'data'	=> $msg );
        $fieldsList[] = $fields;
    } else {
        $b = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2020110001' and tipe='pH' ORDER BY no DESC limit 1");
        $b2 = mysqli_fetch_row($b);
        $tgl = $b2[0];
        $pkl = $b2[1];
        $tipe = $b2[2];
        
        echo "<br> $tgl | $pkl | $tipe<br>";
        
        function datediffee($awal_aa, $akhir_aa) {
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
        $delay_aa  = round ($delay_aa, 1);
        echo "selisih notif2222 : $delay_aa menit<br>";
        
        if($delay_aa >= 720){
            mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$akhir', 'pH', 'pH is abnormal!')");
            $msg = array(
        	'message' 	=> 'Current pH : '.$pH,
        	'title'		=> 'Warning !!!',
        	'vibrate'	=> 1,
        	'sound'		=> 1,
        	'largeIcon'	=> 'large_icon',
        	'smallIcon'	=> 'small_icon');
        $fields = array(
            'to'  => '/topics/kadar_pH',
            'data'	=> $msg );
        $fieldsList[] = $fields;
        };
    };
};

echo "$TDS | $thd_tds2 || $TDS | $thd_tds1";
//notif TDS lebih dari thd monik

if ($TDS > $thd_tds2 || $TDS < $thd_tds1){
$d = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2020110001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'TDS' order by no desc limit 1");
$dd = mysqli_fetch_row($d);
// prep the bundle
if(empty($dd)){
    mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$e', 'TDS', 'TDS is abnormal')");
    $msg = array(
    	'message' 	=> 'Current TDS :'.$TDS,
    	'title'		=> 'Warning !!!',
    	'vibrate'	=> 1,
    	'sound'		=> 1,
    	'largeIcon'	=> 'large_icon',
    	'smallIcon'	=> 'small_icon');
    $fields = array(
        'to'  => '/topics/kadar_TDS',
        'data'	=> $msg);
    $fieldsList[] = $fields;
    } else {
        $c = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2020110001' and tipe='TDS' ORDER BY no DESC limit 1");
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
        $delay_b  = round ($delay_b, 1);
        echo "selisih notif333 : $delay_b menit<br>";
        
        if($delay_b >= 720){
            mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$akhir', 'TDS', 'TDS is abnormal!')");
            $msg = array
                (
            	'message' 	=> 'Current TDS :'.$TDS,
            	'title'		=> 'Warning !!!',
            	'vibrate'	=> 1,
            	'sound'		=> 1,
            	'largeIcon'	=> 'large_icon',
            	'smallIcon'	=> 'small_icon'
            	);
            $fields = array(
                	'to'  => '/topics/kadar_TDS',
                	'data'	=> $msg );
            $fieldsList[] = $fields;
        };
    };
};

//notif WL kurang dari thd
echo "$WL | $thd_wl1";

if ($WL < $thd_wl1){
    $n = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2020110001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'Water' order by no desc limit 1");
    $nn = mysqli_fetch_row($n);
    if(empty($nn)) {
        mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$e', 'Water', 'Water Level is abnormal')");
    // prep the bundle
    $msg = array
        (
        'message' 	=> 'Current water level :'.$WL,
        'title'		=> 'Warning !!!',
        'vibrate'	=> 1,
        'sound'		=> 1,
        'largeIcon'	=> 'large_icon',
        'smallIcon'	=> 'small_icon'
        );
    $fields = array(
            'to'  => '/topics/takaran_water',
            'data'	=> $msg 
            );
    $fieldsList[] = $fields;
    } else {
        $d = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2020110001' and tipe='Water' ORDER BY no DESC limit 1");
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
        $delay_x  = round ($delay_x, 1);
        echo "selisih notif444 : $delay_x menit<br>";
        
        if($delay_x >= 720){
            mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$akhir', 'Water', 'Water level is abnormal!')");
            $msg = array
            (
                'message' 	=> 'Current water level :'.$WL,
                'title'		=> 'Warning !!!',
                'vibrate'	=> 1,
                'sound'		=> 1,
                'largeIcon'	=> 'large_icon',
                'smallIcon'	=> 'small_icon'
                );
            $fields = array
                (
                    'to'  => '/topics/takaran_water',
                    'data'	=> $msg
                );
            $fieldsList[] = $fields;
        };
    };
};


//notif Suhu nutrisi lebih dari thd monik
echo "$Sn | $thd_sn2 || $Sn | $thd_sn1";

if ($Sn > $thd_sn2 || $Sn < $thd_sn1){
    $f = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2020110001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'Sn' order by no desc limit 1");
    $ff = mysqli_fetch_row($f);
    
    if(empty($ff)) {
        mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$e', 'Sn', 'Reservoir is abnormal')");
        // prep the bundle
        $msg = array
            (
        	'message' 	=> 'Current Reservoir Temperature :'.$Sn,
        	'title'		=> 'Warning !!!',
        	'vibrate'	=> 1,
        	'sound'		=> 1,
        	'largeIcon'	=> 'large_icon',
        	'smallIcon'	=> 'small_icon'
        	);
        $fields = array(
        	'to'  => '/topics/reservoir',
        	'data'	=> $msg );
        $fieldsList[] = $fields;
    } else {
        $z = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2020110001' and tipe='Sn' ORDER BY no DESC limit 1");
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
        $delay_z  = round ($delay_z, 1);
        echo "selisih notif555 : $delay_z menit<br>";
        
        if($delay_z >= 720){
            mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$akhir', 'Sn', 'Reservoir is abnormal!')");
            // prep the bundle
            $msg = array
                (
            	'message' 	=> 'Current Reservoir Temperature :'.$Sn,
            	'title'		=> 'Warning !!!',
            	'vibrate'	=> 1,
            	'sound'		=> 1,
            	'largeIcon'	=> 'large_icon',
            	'smallIcon'	=> 'small_icon'
            	);
            $fields = array
                (
                	'to'  => '/topics/reservoir',
                	'data'	=> $msg
                );
            $fieldsList[] = $fields;
        };
    };
};

//notif cahaya lebih dari thd monik
echo "$ch | $thd_ch2 || $ch | $thd_ch1";

if ($ch > $thd_ch2 || $ch < $thd_ch1){
$g = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2020110001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'Light' order by no desc limit 1");
$gg = mysqli_fetch_row($g);

    if(empty($gg)) {
        mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$e', 'Light', 'Lightning is abnormal')");
        $msg = array
            (
        	'message' 	=> 'Current Lightning meter :'.$ch,
        	'title'		=> 'Warning !!!',
        	'vibrate'	=> 1,
        	'sound'		=> 1,
        	'largeIcon'	=> 'large_icon',
        	'smallIcon'	=> 'small_icon'
        	);
        $fields = array(
            'to'  => '/topics/lightning',
            'data'	=> $msg );
        $fieldsList[] = $fields;
    } else {
        $y = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2020110001' and tipe='Light' ORDER BY no DESC limit 1");
        $y2 = mysqli_fetch_row($y);
        $tgl = $y2[0];
        $pkl = $y2[1];
        $tipe = $y2[2];

        echo "<br> $tgl | $pkl | $tipe<br>";
        
        function datediff5($awal_y, $akhir_y)
        {
            $awal_y = strtotime($awal_y);
            $akhir_y = strtotime($akhir_y);
            $diff_secs_y = abs($awal_y - $akhir_y);
            return array("Secon_total" => floor($diff_secs_y));
        }
        //monik
        $awal_y  = ("$tgl $pkl");
        $akhir_y = date("Y-m-d H:i:s");
        $selisih_y  = datediff5($awal_y, $akhir_y);
        $delay_y  = $selisih_y['Secon_total'] / 60;
        $delay_y  = round ($delay_y, 1);
        echo "selisih notif666 : $delay_y menit<br>";
        
        if($delay_y >= 720){
            mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$akhir', 'Light', 'Lightning is abnormal!')");
            // prep the bundle
            $msg = array
                (
            	'message' 	=> 'Current Lightning meter :'.$ch,
            	'title'		=> 'Warning !!!',
            	'vibrate'	=> 1,
            	'sound'		=> 1,
            	'largeIcon'	=> 'large_icon',
            	'smallIcon'	=> 'small_icon'
            	);
            $fields = array(
                	'to'  => '/topics/lightning',
                	'data'	=> $msg 
                	);
            $fieldsList[] = $fields;
        };
    };
};

//notif Suhu greenhouse lebih dari thd monik
echo "$sgh | $thd_sgh2 || $sgh | $thd_sgh1";

if ($sgh > $thd_sgh2 || $sgh < $thd_sgh1){
$h = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2020110001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'greenhouse' order by no desc limit 1");
$hh = mysqli_fetch_row($h);

if(empty($hh)) {
    mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$e', 'greenhouse', 'Greenhouse temperature is abnormal')");
    $msg = array
        (
    	'message' 	=> 'Current Greenhouse Temperature :'.$sgh,
    	'title'		=> 'Warning !!!',
    	'vibrate'	=> 1,
    	'sound'		=> 1,
    	'largeIcon'	=> 'large_icon',
    	'smallIcon'	=> 'small_icon'
    	);
    $fields = array(
        	'to'  => '/topics/greenhouse_temp',
        	'data'	=> $msg );
    $fieldsList[] = $fields;
} else {
    $w = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2020110001' and tipe='greenhouse' ORDER BY no DESC limit 1");
    $w2 = mysqli_fetch_row($w);
    $tgl = $w2[0];
    $pkl = $w2[1];
    $tipe = $w2[2];

    echo "<br> $tgl | $pkl | $tipe<br>";
        
    function datediff6($awal_w, $akhir_w){
            $awal_w = strtotime($awal_w);
            $akhir_w = strtotime($akhir_w);
            $diff_secs_w = abs($awal_w - $akhir_w);
            return array("Secon_total" => floor($diff_secs_w));
    }
    //monik
    $awal_w  = ("$tgl $pkl");
    $akhir_w = date("Y-m-d H:i:s");
    $selisih_w  = datediff6($awal_w, $akhir_w);
    $delay_w  = $selisih_w['Secon_total'] / 60;
    $delay_w  = round ($delay_w, 1);
    echo "selisih notif777 : $delay_w menit<br>";
        
    if($delay_w >= 720){
        mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$akhir', 'greenhouse', 'Greenhouse temperature is abnormal')");
        // prep the bundle
        $msg = array
        (
            'message' 	=> 'Current Greenhouse Temperature :'.$sgh,
            'title'		=> 'Warning !!!',
            'vibrate'	=> 1,
            'sound'		=> 1,
            'largeIcon'	=> 'large_icon',
            'smallIcon'	=> 'small_icon'
        );
        $fields = array(
            'to'  => '/topics/greenhouse_temp',
            'data'	=> $msg );
        $fieldsList[] = $fields;
        };
    };
};

//notif Kelembapan lebih dari thd monik
echo "$kgh | $thd_kgh2 || $kgh | $thd_kgh1";

if ($kgh > $thd_kgh2 || $kgh < $thd_kgh1){
$j = mysqli_query($link, "SELECT tgl, pkl, tipe from notif where sn ='2020110001' and hour(pkl)='$jam_end' and tgl = '$date' and tipe = 'humidity' order by no desc limit 1");
$jj = mysqli_fetch_row($j);
if(empty($jj)) {
    mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$e', 'humidity', 'Greenhouse humidity is abnormal')");
    $msg = array
        (
    	'message' 	=> 'Current Reservoir Temperature :'.$kgh,
    	'title'		=> 'Warning !!!',
    	'vibrate'	=> 1,
    	'sound'		=> 1,
    	'largeIcon'	=> 'large_icon',
    	'smallIcon'	=> 'small_icon'
    	);
    $fields = array
        (
        	'to'  => '/topics/kelembapan',
        	'data'	=> $msg
        );
    $fieldsList[] = $fields;
    } else {
    $v = mysqli_query($link, "SELECT tgl, pkl, tipe FROM notif WHERE sn='2020110001' and tipe='humidity' ORDER BY no DESC limit 1");
    $v2 = mysqli_fetch_row($v);
    $tgl = $v2[0];
    $pkl = $v2[1];
    $tipe = $v2[2];

        echo "<br> $tgl | $pkl | $tipe<br>";
        
        function datediff7($awal_v, $akhir_v)
        {
            $awal_v = strtotime($awal_v);
            $akhir_v = strtotime($akhir_v);
            $diff_secs_v = abs($awal_v - $akhir_v);
            return array("Secon_total" => floor($diff_secs_v));
        }
        //monik
        $awal_v  = ("$tgl $pkl");
        $akhir_v = date("Y-m-d H:i:s");
        $selisih_v = datediff7($awal_v, $akhir_v);
        $delay_v  = $selisih_v['Secon_total'] / 60;
        $delay_v  = round ($delay_v, 1);
        echo "selisih notif888 : $delay_v menit<br>";
        
        if($delay_v >= 720){
            mysqli_query($link, "insert into notif(sn, tgl, pkl, tipe, ket) values ('2020110001','$date', '$akhir', 'humidity', 'Greenhouse humidity is abnormal')");
            // prep the bundle
            $msg = array
            (
            	'message' 	=> 'Current Reservoir Temperature :'.$kgh,
            	'title'		=> 'Warning !!!',
            	'vibrate'	=> 1,
            	'sound'		=> 1,
            	'largeIcon'	=> 'large_icon',
            	'smallIcon'	=> 'small_icon'
        	);
            $fields = array
                (
                	'to'  => '/topics/kelembapan',
                	'data'	=> $msg
                );
            $fieldsList[] = $fields;
        };
    };
};

for($i=0; $i<count($fieldsList); $i++){
    
    $headers = array
    (
    	'Authorization: key=' . API_ACCESS_KEY,
    	'Content-Type: application/json'
    );
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fieldsList[$i]) );
    $result = curl_exec($ch );
    curl_close( $ch );
    
    echo $result;
 }
?>