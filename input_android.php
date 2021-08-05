<?php
//jangan diubah yang dari sini

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

//sampek sini jangan diubah

                                                    /** ini contoh yang bener dari sini */

$date = date("Y-m-d");
if ($delay>1){
    $b = mysqli_query($link, "SELECT tgl, pkl from notif where sn ='2020110001' and hour(pkl)='$jam_end' and tgl = '$date' order by no desc limit 1");
    $bb = mysqli_fetch_row($b);
    
    if(empty($bb)){
        mysqli_query($link, "insert into notif(sn, tgl, pkl, ket) values ('2020110001','$date', '$e', 'Alat mati yo!')");
        $msg = array(
        'message' 	=> 'Please check your device and your wireless connection !!!',
        'title'		=> 'Warning !!!',
        'vibrate'	=> 1,
        'sound'		=> 1,
        'largeIcon'	=> 'large_icon',
        'smallIcon'	=> 'small_icon');
        $fields = array
        (
        	'to'  => '/topics/systemkoi',
        	'data'	=> $msg
        );
        $fieldsList[] = $fields;
    } else {
        $a = mysqli_query($link, "SELECT tgl, pkl FROM notif WHERE sn='2020110001' ORDER BY no DESC limit 1");
        $a2 = mysqli_fetch_row($a);
        $tgl = $a2[0];
        $pkl = $a2[1];
        
        echo "<br> $tgl | $pkl";
        
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
        $selisih_a   = datediffe($awal_a, $akhir_a);
        $delay_a  = $selisih_a['Secon_total'] / 60;
        $delay_a  = round ($delay_a, 1);
        echo "selisih notif1111 : $delay_a menit<br>";
        
        if($delay_a >= 60){
            mysqli_query($link, "insert into notif(sn, tgl, pkl, ket) values ('2020110001','$date', '$akhir', 'Alat mati yo!')");
            
            $msg = array(
            'message' 	=> 'Please check your device and your wireless connection !!!',
            'title'		=> 'Warning !!!',
            'vibrate'	=> 1,
            'sound'		=> 1,
            'largeIcon'	=> 'large_icon',
            'smallIcon'	=> 'small_icon');
            $fields = array
            (
            	'to'  => '/topics/systemkoi',
            	'data'	=> $msg
            );
            $fieldsList[] = $fields;
        }
    }
}
                                                                /** sampek sini */

                        /**terus ini kebawah ganti bentuknya kayak contoh diatas tapi jangan ubah bagian msgnya sampek field list */

//notif pH lebih dari thd monik
if ($pH > $thd_ph2 || $pH < $thd_ph1){
    /**terus kan ada query yang gini mysqli_query($link, "insert into notif(sn, tgl, pkl, ket) values ('2020110001','$date', '$e', 'Alat mati yo!')"); nah dibagian keterangan diisi sesuai parameternya misal pH ya pH melebihi batas gitu keteranganya */
mysqli_query($link, " insert into dami(id) values ('2021', '$pH')");

// prep the bundle ini jangan diubah sampek field list ok
$msg = array
(
	'message' 	=> 'Current pH : '.$pH,
	'title'		=> 'Warning !!!',
	'vibrate'	=> 1,
	'sound'		=> 1,
	'largeIcon'	=> 'large_icon',
	'smallIcon'	=> 'small_icon'
);
$fields = array
    (
    	'to'  => '/topics/kadar_pH',
    	'data'	=> $msg
    );
$fieldsList[] = $fields;
}

//notif TDS lebih dari thd monik
if ($TDS > $thd_tds2 || $TDS < $thd_tds1){
mysqli_query($link, " insert into dami(id) values ('2021', '$TDS')");
// prep the bundle
$msg = array
    (
	'message' 	=> 'Current TDS :'.$TDS,
	'title'		=> 'Warning !!!',
	'vibrate'	=> 1,
	'sound'		=> 1,
	'largeIcon'	=> 'large_icon',
	'smallIcon'	=> 'small_icon'
	);
$fields = array
    (
    	'to'  => '/topics/kadar_TDS',
    	'data'	=> $msg
    );
$fieldsList[] = $fields;
}

//notif WL kurang dari thd
if ($WL < $thd_wl1){
    mysqli_query($link, " insert into dami(id) values ('2021', '$WL')");
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
$fields = array
    (
    	'to'  => '/topics/takaran_water',
    	'data'	=> $msg
    );
$fieldsList[] = $fields;
}

//notif Suhu nutrisi lebih dari thd monik
if ($Sn > $thd_sn2 || $Sn < $thd_sn1){
mysqli_query($link, " insert into dami(id) values ('2021', '$Sn')");
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
}

//notif cahaya lebih dari thd monik
if ($ch > $thd_ch2 || $ch < $thd_ch1){
mysqli_query($link, " insert into dami(id) values ('2021', '$ch')");
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
$fields = array
    (
    	'to'  => '/topics/lightning',
    	'data'	=> $msg
    );
$fieldsList[] = $fields;
}

//notif Suhu greenhouse lebih dari thd monik
if ($sgh > $thd_sgh2 || $sgh < $thd_sgh1){
mysqli_query($link, " insert into dami(id) values ('2021', '$sgh')");
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
$fields = array
    (
    	'to'  => '/topics/greenhouse_temp',
    	'data'	=> $msg
    );
$fieldsList[] = $fields;
}

//notif Kelembapan lebih dari thd monik
if ($kgh > $thd_kgh2 || $kgh < $thd_kgh1){
mysqli_query($link, " insert into dami(id) values ('2021', '$kgh')");
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
}

/** ini kebawah jangan di ubah */

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

    /**pokoknya sesuaiin sama contoh tapi ya ga merubbah $msg nya juga disesuaiin parameternya ok 감사 해요 */
 }
?>