<div class="wrap">
<h1><?php _e('Help') ?></h1>
<div class="tab" style="float: left; width:30%;">

	<a id="nav-overview" onclick="openTab(event, 'overview')">
	<h3  class="nav-tab">Pregled</h3></a>
	<a onclick="openTab(event, 'started')">
	<h3 class="nav-tab">Prvi koraki: Ustvarjanje svoje vprašanje lonce</h3></a>
	<a onclick="openTab(event, 'questions')">
	<h3 class="nav-tab">Ustvarjanje vprašanja</h3></a>
	<a onclick="openTab(event, 'question_feedback')">
	<h3 class="nav-tab">Odziv za vprašanje</h3></a>
	<a onclick="openTab(event, 'insertQuestion')" >
	<h3 class="nav-tab">Dodajanje vprašanj na stran</h3></a>
	<a onclick="openTab(event, 'quiz')" >
	<h3 class="nav-tab">Ustvarjanje Kvizi</h3></a>
	<a onclick="openTab(event, 'insertQuiz')">
	<h3 class="nav-tab">Dodajanje kviz na stran</h3></a>
	<a onclick="openTab(event, 'shortcodes')">
	<h3 class="nav-tab">Kratke kode</h3></a>
	<a onclick="openTab(event, 'showResponse')">
	<h3 class="nav-tab">Prikaz uporabniške odzive</h3></a>
	<a onclick="openTab(event, 'results')" >
	<h3 class="nav-tab">Ogled rezultatov</h3></a>
	<a onclick="openTab(event, 'more_help')">
	<h3 class="nav-tab">Potrebujete pomoč?</h3></a>

</div>
<div id="overview" class="tab-content" style="float: right; width:66%;" >

<h2>Pregled</h2>
<p>Ta plugin bo omogočilo, da ustvarite kviz vprašanja in jih razporedi v eno samo vprašanje, na strani, ali kot celoten kviz.</p>

<p>Vprašanja so shranjeni v "lončkih", ki vam omogočajo, da ustvarite meri kvizi iz različnih vprašanj v vsak lonček. Si lahko zamislite vprašajem pot kot vedro, v katerem ste shranili podobna vprašanja, na primer bi lahko imeli 3 vprašanje lonce imenovane "Easy", "srednje" in "Hard". Nato lahko ustvarite kviz s 5 vprašanj iz "lahko" pot, 5 vprašanj iz "Medium" pot in 5 vprašanj iz lonca na "trdo".</p>

<p>Kviz vprašanja so dodeljene naključno, tako v zgornjem primeru, če bi imeli 10 vprašanj v vsaki pot, s katero bi se prikazuje 15 naključnih vprašanj, 5 iz vsake zahtevnosti vsakega posameznika ob kviz (glej spodaj)</p>

<?php
//echo home_url();
$homeURL =  network_home_url();

if($homeURL =="")
{
	$homeURL = home_url();	
}

$imgSrc =  $homeURL.'/wp-content/plugins/quiz-tool-lite/admin/help/pot-example-sl_SI.jpg';
//echo $imgSrc;

echo '<div style="text-align:center"><img src="'.$imgSrc.'" />';
echo '<br/><span class="greyText">An example showing a quiz made up of 2 questions at random from 3 pots.</span>';
echo '</div>';

?>

</div>
<div id="started" class="tab-content" style="float: right; width:66%;">

<h2>Prvi koraki: Ustvarjanje svoje vprašanje lonce</h2>
<p>Najprej morate ustvariti vprašanje pot, da dodate vprašanja. Kliknite "kviz vprašanja" iz menija in nato 'Ustvari novo vprašanje pot'</p>

<p>Pokličete lahko svoje vprašanje pot karkoli želite na primer "Geografija vprašanja". Ljudje, ki jemljejo kviz nikoli ne bodo videli ime vašega vprašanja lončkih.</p>

</p>Prav tako lahko spremenite svoje vprašanje imena lonec na kateri koli točki
Ko ste ustvarili vprašanje lonec kliknite 'Dodaj / uredi vprašanja' povezavo za začetek dodajanja vprašanja</p>

</div>
<div id="questions" class="tab-content" style="float: right; width:66%;">

<h2>Ustvarjanje vprašanja</h2>
<p>Ko ste ustvarili vprašanje lonec in so kliknili "Dodaj / uredi vprašanja," bi morali videti novo povezavo "Dodaj novo vprašanje". Kliknite to in si vzeti do strani, ki zahteva, da izberete vrsto vprašanje. Trenutno je 4 vprašanja vrste med katerimi lahko izbirate.</p>

<b>Enotni Odgovor (Radio gumbi)</b>
<p>Ta vrsta vprašanje udeležencem omogoča izbiro le en odgovor. To uporabite za ustvarjanje pravilno / napačno vprašanja, ali če je le en odgovor pravilen primer, kaj je glavno mesto Francije. Multiple Odgovor (potrditveni polji)</p>

<p>Ta vrsta vprašanje udeležencem omogoča izbiro več kot enega odgovora na primer kateri od naslednjih so resnični / izberi vse, ki se uporabljajo.</p>

<b>Prosti Besedilo</b><br/>
<p>This question type allows participants to select more than one answer e.g. which of the following are true / select all that apply.</p>

<b>Prosti Besedilo</b><br/>
<p>Ta vrsta vprašanje udeležencem omogoča dodajanje besedila v polje. Prepoznate lahko čim več pravilnih odgovorov, kot si želite. To niso velike in male črke. tj "Bones" bi bila sprejeta tudi "kosti".</p>

<b>Odsev (ne textbox)</b>
<p>Lahko uporabite to vprašanje vrsto, če želite, da bi svojim udeležencem izjavo, da razmišljajo o tem, in nato kliknite na gumb, da razkrije modela odgovor. Ta "kliknite, da razkrije" tipa vprašanje zgolj sedanje podatke z ljudmi in ne daje nobenih sredstev, da bi vstop na odgovor.</p>

<b>Odsev (v okvirčku)</b>
<p>Ta vrsta vprašanja deluje na enak način kot zgoraj (kliknite da razkrije vzorec odgovor / besedilo), ampak tudi omogoča študentom, da tip odgovora, preden razkrivajo odgovor. To lahko uporabite za zbiranje informacij od udeležencev in nato na naslednji strani predstavijo svoje prvotno odziv na njih, da vidim, če se je to spremenilo. Glej <a href="#shortcodes">'kratke kode'</a> poglavje o tem, kako to storiti.</p>

</div>
<div id="question_feedback" class="tab-content" style="float: right; width:66%;">

<h2>Odziv za vprašanje</h2>
<p>Vsako vprašanje ima več možnosti za dajanje povratne informacije. Imajo skupno pravilne in napačne povratne škatlo, vendar je vsaka možnost odziv (če je primerno), lahko tudi povratne informacije za pravilno in nepravilno povratne informacije.</p>

<p>Povratne informacije ni potrebna in se lahko pusti prazno. Povratna informacija je podana samodejno, ko dodate eno vprašanje - ni treba, da "omogočiti" je v vsakem primeru. </p>

</div>
<div id="insertQuestion" class="tab-content" style="float: right; width:66%;">

<h2>Dodajanje vprašanj na stran</h2>
<p>Dodajte vprašanje na stran s pomočjo ikone čarovnika kviz orodje Lite. To lahko našli na vsakem baru strani ali po orodje - poiščite ikono Red "Q" (glej spodaj)</p>

<?php
$imgSrc =  $homeURL.'/wp-content/plugins/quiz-tool-lite/admin/help/question-add-sl_SI.jpg';
echo '<div style="text-align:center"><img src="'.$imgSrc.'" />';
echo '<br/><span class="greyText">Pri urejanju strani, kliknite ikono Q dodati vprašanje ali kviz na stran.</span>';
echo '</div>';
?>

<p>Ko kliknete se bo pojavilo pojavno okno, kjer lahko izberete bodisi kviz ali vprašanje vstavite v stran. Najprej izberite pot, ki vsebuje vprašanja, nato kliknite na vprašanje sama in "vstavite v stran". To bo vstaviti "shortcode" na stran.</p>

</div>
<div id="quiz" class="tab-content" style="float: right; width:66%;">

<h2>Ustvarjanje Kvizi</h2>
<p>Kviz je ustvarjena s potegom po številu X vprašanj iz števila X za vprašanje lončkih. V svoji najbolj preprosti formar, če bi imeli eno vprašanje lonec z deset vprašanj, ki jih lahko ustvarite kviz, ki je potegnil v 10 vprašanj, od tega pota. Da bi ustvarili kviz pri čemer vseh 10 vprašanj prikaže naključno. Vendar pa lahko združite več vprašanj iz ločenih vprašanj loncih, da bi vsak udeleženec videti nekoliko drugačno različico kviza.</p>
<p>Rezultati kvizov za prijavljeni uporabniki shranjujejo in si lahko ogledate na strani "rezultatov". Najvišja ocena se shrani za vsakega udeleženca.</p>

</div>

<div id="insertQuiz" class="tab-content" style="float: right; width:66%;">

<h2>Dodajanje kviz na stran</h2>
<p>Uporabite 'Vstavi čarovnika "v orodni vrstici urejevalnika na enak način, kot ste dodali eno samo vprašanje dodati shortcode za prikaz kviz. Ali lahko uporabite shortcode, kot je prikazano spodaj.</p>

</div>
<div id="shortcodes" class="tab-content" style="float: right; width:66%;">

<h2>Kratke kode</h2>
<p>Če želite stran, ki jih potrebujete za uporabo "kratke kode" dodati vprašanje ali kviz. Kratka koda je preprost malo besedila, ki se doda med na oglatem oklepaju npr [Moj shortcode].</p>
<p>Na splošno vam ne bo treba vedeti, kaj drugega pa Kratke kode dodati vprašanj na stran. Preprosto uporabite "Vstavi vprašanje čarovnika" (glej zgoraj) in kratka koda se generira in dodali na stran za you.I

<p>Primer Kratke kode so prikazani spodaj </p>

<h4>Standardni shortcode Primeri</h4>
<table style="line-height:50px; text-align:left; border-bottom:1px solid #ccc">
<tr>
<th width="350px">Shortcode</th>
<th>Opis</th>
</tr>
<tr>
<td>
<span class="codeExample">[QTL-Question id=25]</span>
</td>
<td>
Vstavite vprašanje ID 25.
</td>
</tr>
<tr>
<td>
<span class="codeExample">[QTL-Question id=25 savedata=true]</span>
</td>
<td>
Vstavite vprašanje ID 25 in shranite odgovor na podatkovno bazo
</td>
</tr>
<tr>
<td>
<span class="codeExample">[QTL-Question id=25 button="Click Here"]</span>
</td>
<td>
Vstavite vprašanje ID 25 in spremeniti besedilo privzeti gumb "Check odgovor" da "Kliknite tukaj"
</td>
</tr>

<tr>
<td>
<span class="codeExample">[QTL-Question id=25 correctfeedback="Well done!"]</span>
</td>
<td>
Vstavite vprašanje ID 25 in spremeniti privzeto pravilne povratne informacije
</td>
</tr>

<tr>
<td><span class="codeExample">[QTL-Response id=25]</span></td>
<td>Prikazuje odgovor na prvo vprašanje, ID 25, ki ga sedanji prijavljeni uporabniki</td>
</tr>
<tr>
<td><span class="codeExample">[QTL-Quiz id=2]</span></td>
<td>Vstavite kviz ID 2 v stran</td>
</tr>
<tr>
<tr>
<td valign="top"><span class="codeExample">[QTL-Score id=2]</span></td>
<td>Prikazuje število poskusov in maksimalno oceno za kviz ID 2 do toka prijavljeni uporabniki <br>
Neobvezno - dodati "ShowAll = true" na SHORTCODE, da si ogledate vse poskušajo rezultate</td>
</tr>
<tr>
<td><span class="codeExample">[QTL-Leaderboard id=2]</span></td>
<td>Sestaviti lestvico, ki prikazuje vse uporabniške ocene za kviz ID 2</td>
</tr>
<tr>
<td><span class="codeExample">[QTL-Leaderboard id=2 anonymous=true]</span></td>
<td>Sestaviti lestvico, ki prikazuje vse uporabniške ocene za kviz ID 2, vendar skriva njihova imena</td>
</tr>

</table>

<h4>Kratke kode na voljo za eno samo vprašanje [QTL-Question]</h4>
<table style="line-height:50px; text-align:left; border-bottom:1px solid #ccc">

<tr>
<td><span class="codeExample">savedata=true</span></td>
<td>Shrani odziv na bazi podatkov, tako da lahko prikaže uporabniku na drugi strani</td>
</tr>

<tr>
<td><span class="codeExample">button="Click here"</span></td>
<td>Spremeni besedilo na privzeti gumb gumba za odgovor oddaji</td>
</tr>

<tr>
<td><span class="codeExample">correctfeedback="Well done!"</span></td>
<td>Spremeni privzeti pravilno sporočilo za uporabnika</td>
</tr>

<tr>
<td><span class="codeExample">incorrectfeedback="Thats wrong"</span></td>
<td>Spremeni privzeti napačno sporočilo za uporabnika</td>
</tr>

<tr>
<td><span class="codeExample">iconset=3</span></td>
<td>Spremeni nabor privzeto ikono. <a href="javascript:toggleLayerVis('iconsets')">Ogled zbirke ikon</a></td>
</tr>
</table>

<div id="iconsets" style="display:none">
<h3>Available Iconsets</h3>
<?php

$iconArray = array();
$iconArray = qtl_utils::getQTL_IconArray();

$correctIconDir = QTL_PLUGIN_URL.'/images/icons/correct/';
$incorrectIconDir = QTL_PLUGIN_URL.'/images/icons/incorrect/';
echo '<table>';
$i=1;
foreach($iconArray as $myIcon)
{
	$currentIconNo = substr($myIcon, 4, -4);
	$correctIconRef = $correctIconDir.'/'.$myIcon;
	$incorrectIconRef = $incorrectIconDir.'/cross'.$currentIconNo.'.png';
	if($i==1){echo '<tr>';}
	echo '<td align="center" style="padding:25px">';
	echo '<img src="'.$correctIconRef.'">';
	echo '<img src="'.$incorrectIconRef.'"><br/>';
	echo 'Iconset '.$currentIconNo;
	if($currentIconNo==1){echo ' (Default)';}
	echo '</td>';
	$i++;
	if($i>=5){$i=1; echo '</tr>';}
}
if($i<>1){echo '</tr>';}
echo '</table>';
?>  

</div>
</div>
<div id="showResponse" class="tab-content" style="float: right; width:66%;">

<h2>Prikaz uporabniške odzive</h2>
<p>Možno je, da predstavi udeležencem odgovor so dali na prejšnje vprašanje. To je posebej uporabno za "Reflective" vrste vprašanj, kjer si želijo, da predstavijo svoje odgovore, ki jim pozneje v njihovi učni poti.</p>

<p>Ali je to, kar morate storiti naslednje:</p>
<p><b>1. Prepričajte se podatki shranjujejo z ročnim spreminjanjem shortcode Tipičen kratka koda za vprašanje je, kot sledi:</p>

<span class="codeExample">[QTL-Question id=25]</span>
<p>Da bi vprašanje rešiti podatke preprosto dodate 'savedata = true' na shoprtcode, kot je prikazano spodaj</p>

<span class="codeExample">[QTL-Question id=25 savedata=true]</span>

<p><b>2. Dodamo shortcode 'Pokaži Response «, da se prikaže odgovor, predložene za vprašanje z ID 25, dodajte naslednje na svojo stran ali objavo</p>

<span class="codeExample">[QTL-Response id=3]</span>

<p><i>Prosimo, upoštevajte, da trenutno je to le ustrezno podprta z odsevni vrste vprašanje (polja z besedilom), in samo z oblikovne vprašanja, tj posameznih vprašanj, ne kvizi.</i></p>

</div>
<div id="results" class="tab-content" style="float: right; width:66%;">

<h2>Ogled rezultatov</h2>
<p>Stran z rezultati vam bo pokazal seznam kvizov, ki ste jih ustvarili. S klikom na povezavo »Ogled rezultatov« bo prikazal vse registriranih uporabnikov na spletnem mestu, skupaj s svojim najboljšim rezultatom dosežen.</p>
<p>Prosimo, upoštevajte, da lahko trenutno kvize treba tolikokrat, kot želijo, in le najboljši rezultat se zabeleži.</p>

</div>
<div id="more_help" class="tab-content" style="float: right; width:66%;">

<h2>Potrebujete pomoč?</h2>
<p>Če potrebujete dodatno pomoč prosim dodajte svoje vprašanje na <a href="http://wordpress.org/support/plugin/quiz-tool-lite
">forumu za podporo</a>, kjer bomo v stiku ASAP.</p>

</div>
</div>

<script>

function openTab(evt, tabName) {

	var i, tabcontent, tablinks;
	tabcontent = document.getElementsByClassName("tab-content");
	for (i = 0; i < tabcontent.length; i++) {
		tabcontent[i].style.display = "none";
	}
	tablinks = document.getElementsByClassName("nav-tab");
	for (i = 0; i < tablinks.length; i++) {
		tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
	}
	document.getElementById(tabName).style.display = "block";
	evt.currentTarget.className += " nav-tab-active";

	//event.preventDefault();
}

// Get the element with id="nav-overview" and click on it
document.getElementById("nav-overview").click();


</script>

<style>
	.nav-tab {
    display: block;
    min-width: 90%;
    margin: 0;
}
</style>