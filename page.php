<?php
	require_once "../config.php";
	//echo $server_host;
	$author_name = "Jan Henrik Levertand";
	$full_time_now = date("d.m.Y H:i:s");
	$weekday_now = date("N");
	//echo $weekday_now;
	$weekdaynames_et = ["esmaspäev", "teisipäev", "kolmapäev", "neljapäev", "reede", "laupäev", "pühapäev"];
	//echo $weekdaynames_et[$weekday_now-1];
	$hours_now = date("H");
	//echo $hours_now;
	$part_of_day = "suvaline päeva osa";
	if($weekday_now < 5) 
	{
	if($hours_now < 7){$part_of_day = "uneaeg";}
	if($hours_now >= 8 and $hours_now< 18){$part_of_day = "koolipäev";}
	if($hours_now >= 18) {$part_of_day = "vabaaeg" ;}
	}
	else {$part_of_day = "puhkus";}
	//uurime semestri kestmist
	$semester_begin = new DateTime("2022-9-5");
	$semester_end = new DateTime ("2022-12-18");
	$semester_duration = $semester_begin->diff($semester_end);
	$semester_duration_days = $semester_duration->format("%r%a");
	$from_semester_begin = $semester_begin->diff(new DateTime("now"));
	$from_semester_begin_days = $from_semester_begin->format("%r%a");
	
	$proverbs_et = ["pada sõimab katelt", "amet kõik mis leiba annab", "vahepeal võida, vahepeal kaota", "andja käsi ei alane", "ega amet leiba küsi"];
	$random_proverb = $proverbs_et[mt_rand(0, count($proverbs_et) -1)];
	
	
	//juhuslik arv
	//küsin massiivi pikkust
	//echo count($weekdaynames_et);
	//echo mt_rand(0, count($weekdaynames_et) -1);
	
	//juhuslik foto
	$photo_dir = "photos";
	//loen kataloogi sisu
	//$all_files = scandir($photo_dir);
	//var_dump($all_files);
	$all_files = array_slice(scandir($photo_dir), 2);
	//kontrollin kas ikka foto
	$allowed_photo_types = ["image/jpeg", "image/png"];
	//tsükkel
	//muutuja väärtuse suurendamine  $muutuja = $muutuja + 5
	//muutuja += 5
	//kui on vaja liita 1
	//muutuja ++
	//samamoodi $muutuja -=5   $muutuja -- 
	/*for($i = 0;$i < count($all_files); $i ++){
		echo $all_files[$i];
	}*/
	$photo_files = [];
	foreach($all_files as $filename) {
		//echo $filename;
		$file_info = getimagesize($photo_dir ."/" .$filename);
		//var_dump($file_info);
		//kas on lubatud tüüpide nimekirjas
		if(isset($file_info["mime"])){
				if(in_array($file_info["mime"], $allowed_photo_types)){
					array_push($photo_files, $filename);
				}
			}				
	}
	
	
	//var_dump($photo_files);
	//   <img src ="kataloog/fail" alt="tekst">
	$photo_html = '<img src ="' .$photo_dir ."/" .$photo_files[mt_rand(0, count($photo_files) -1)]. '"';
	$photo_html .=  ' alt="Pilt Tallinnast">';
	//vaatame mida vormis sisestati
	//var_dump($_POST);
	//echo $_POST["todays_adjective_input"];
	$todays_adjective = "pole midagi sisestatud";
	if(isset($_POST["todays_adjective_input"]) and !empty($_POST["todays_adjective_input"]))
		$todays_adjective = $_POST["todays_adjective_input"];
	
	//<option value="0">tln_1.JPG</option>
		
		//loome rippmenüü valikud
		$select_html ='<option value="0" selected disabled>Vali pilt</option>';
		for($i = 0;$i < count($photo_files); $i ++){
			$select_html .= '<option value="' .$i .'">';
			$select_html .= $photo_files[$i];
			$select_html .= "</option>";
		}
		
		if(isset($_POST["photo_select"]) and $_POST["photo_select"] >= 0){
			echo "Valiti pilt nr:" .$_POST["photo_select"];
		}	
		
		$comment_error= null;
		//kas klikiti päeva kommentaari nuppu
		if(isset($_POST["comment_submit"]))
		if(isset($_POST["comment input"]) and !empty ($_POST["comment_input"])){
					$comment = $_POST["comment_input"];
			}	else	{
				$comment_error= "Kommentaar jäi kirjutamata";
			}
			$grade = $_POST["grade_input"];
			
			if(empty("$comment_error")){
			
			//loon andmebaasiga ühenduse
			//server, kasutaja, parool, andmebaas
			$conn = new mysqli($server_host, $server_user_name, $server_password, $database);
			//määran suhtlemisel kasutatava kooditabeli
			$conn->set_charset("utf8");
			//valmistame ette andmete saatmise SQL käsu
			$stmt = $conn->prepare("INSERT INTO vp_daycomment (comment, grade) values(?,?)");
			echo $conn->error;
			//seome SQL käsu õigete andmetega
			//andmetüübid	i - integer		d - decimal		s - string
			$stmt->bind_param("si", $comment, $grade);
			if($stmt->execute()){
				$grade = 7;
				$comment = null;
			//sulgeme käsu
			$stmt->close();
			//sulgeme andmebaasiühenduse
			$conn->close();
		}
			}
?>
<!DOCTYPE html>
<html lang="et">
<head>  
<img src="https://greeny.cs.tlu.ee/~rinde/vp_2022/vp_banner_gs.png">
	<meta charset="utf-8">
	<title> <?php echo $author_name;?> programmeerib veebi</title>
</head>
<body>
<h1> Jan Henrik Levertand programmeerib veebi</h1>
<p> See leht on loodud õppetöö raames ja ei sisalda tõsiselt võetavat sisu!</p>
<p> Õppetöö toimus <a href="https://www.tlu.ee" target="_blank">Tallinna Ülikoolis</a> Digitehnoloogia Instituudis.</p>
<p>Lehe avamise hetk: <?php echo $weekdaynames_et [$weekday_now-1] .", " .$full_time_now;?></p>
<p>Praegu on <?php echo $part_of_day;?>.</p>
<p>TÄNANE TARKUSETERA: <?php echo $random_proverb; ?>.</p>
<p>Semestri pikkus on <?php echo $semester_duration_days;?> päeva. See on kestnud juba <?php echo $from_semester_begin_days; ?> päeva.</p>
<a href="https://www.tlu.ee" target="_blank"><img src="pildid/tlu_16.jpg" alt="Tallinna Ülikooli imeline garde"></a>
<p> Mul on haigelt lahe toyota corolla mille kytusetoru lekib.   UPDATE: ostsin uue toru!!!</p>  
<hr>
<form method="POST">
	<label for="comment_input">Kommentaar tänase päeva kohta (140 tähte)</label>
	<br>
	<textarea id="comment_input" name="comment_input" cols="35" rows="4" placeholder="kommentaar"></textarea>
	</br>
	<label for="grade_input">Hinne tänasele päevale (0-10)</label>
	<input type="number" id="grade_input" name="grade_input" min="0" max="10" step="1" value="7">
	<br>
	<input type="submit" id="comment_submit" name="comment_submit" value="Salvesta">
	<span><?php echo $comment_error;?></span>
</form>
</hr>

<form method="POST">
	<input type="text" id="todays_adjective_input" name="todays_adjective_input" placeholder="Kirjuta siia omadussõna tänase päeva kohta">
	<input type="submit" id=todays_adjective_submit" name="todays_adjective_submit" value="Saada omadussõna">
</form>
<p>Omadussõna tänase kohta: <?php echo $todays_adjective;?></p>
<hr>
<form method="POST">
	<select id="photo_select" name="photo_select">
		<?php echo $select_html; ?>
	</select>
	<input type="submit" id="photo_submit" name="photo_submit" value="Määra foto">
</form>
<?php
	if(isset($_POST["photo_select"]) and ($_POST["photo_select"] >=0))
	{
		$photo_html ='<img src="' .$photo_dir . "/" . $photo_files[$_POST["photo_select"]] .'"alt="Tallinna pilt">';
		echo $photo_html;
	}
	else
	{
	echo $photo_html;
	}
	?>
		
</body>
</html>