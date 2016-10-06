<?php
// The following page was created by Roger L. Smith (roger@ERC.MsState.Edu), 
// copyright July 2003. Used by permission.
$textpart = "stats";
include("tng_begin_front.php");

$search_url = getURL( "search", 1 );
$surnames_oneletter_url = getURL( "surnames-oneletter", 1 );
$surnames_all_url = getURL( "surnames-all", 1 );
$getperson_url = getURL( "getperson", 1 );
$showtree_url = getURL( "showtree", 1 );
$statistics_url = getURL( "statistics", 1 );

?>

<?php

if($sitever != "standard") {
	if($tabletype == "toggle") $tabletype = "columntoggle";
	$header = "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"500\" class=\"table table-hover\" data-tablesaw-mode=\"$tabletype\"{$headerr}>\n";
} else {
	$header = "<table cellpadding=\"0\" cellspacing=\"1\" border=\"0\" width=\"100%\" class=\"table table-hover\">";
}
echo $header;
?>
	<thead>
		<tr>
		<th data-tablesaw-priority="persist" class="fieldnameback fieldname">&nbsp;<?php echo $text['description']; ?>&nbsp;</th>
		<th data-tablesaw-priority="1" class="fieldnameback align-right fieldname" width="30%" align="center">&nbsp;<?php echo $text['quantity']; ?>&nbsp;</th>
		</tr>
	</thead>
<?php

$query = "SELECT count(id) as pcount FROM $people_table $wherestr";
$result =  tng_query($query);
$row = tng_fetch_assoc( $result );
$totalpeople = $row['pcount'];
tng_free_result($result);

$query = "SELECT count(id) as fcount FROM $families_table $wherestr";
$result = tng_query($query);
$row = tng_fetch_assoc( $result );
$totalfamilies = $row['fcount'];
tng_free_result($result);

$query = "SELECT count(DISTINCT ucase(lastname)) as lncount
   FROM $people_table $wherestr";
$result =  tng_query($query);
$row = tng_fetch_array($result);
$uniquesurnames = number_format($row['lncount']);
tng_free_result($result);

$query = "SELECT count(mediaID) as pcount FROM $media_table $wherestr";
$result =  tng_query($query);
$row = tng_fetch_assoc( $result );
$medias = $row['pcount'];
tng_free_result($result);

$query = "SELECT count(id) as scount FROM $sources_table $wherestr";
$result =  tng_query($query);
$row = tng_fetch_assoc( $result );
$totalsources = number_format($row['scount']);
tng_free_result($result);

$query = "SELECT count(id) as pcount FROM $people_table WHERE sex = 'M' $wherestr2";
$result =  tng_query($query);
$row = tng_fetch_assoc( $result );
$males = $row['pcount'];
tng_free_result($result);

$query = "SELECT count(id) as pcount FROM $people_table WHERE sex = 'F' $wherestr2";
$result =  tng_query($query);
$row = tng_fetch_assoc( $result );
$females = $row['pcount'];
tng_free_result($result);

$unknownsex = $totalpeople - $males - $females;

$query = "SELECT count(id) as pcount FROM $people_table WHERE firstname = 'Unbekannt' $wherestr2";
$result =  tng_query($query);
$row = tng_fetch_assoc( $result );
$unbekannt = $row['pcount'];
tng_free_result($result);

$query = "SELECT count(id) as pcount FROM $people_table WHERE living != 0 $wherestr2";
$result =  tng_query($query);
$row = tng_fetch_assoc( $result );
$numliving = number_format($row['pcount']);
tng_free_result($result);

$query = "SELECT personID, firstname, lnprefix, lastname, birthdate, altbirthdate, gedcom, living, private, branch
    FROM $people_table 
    WHERE (YEAR(birthdatetr) != '0' OR YEAR(altbirthdatetr) != '0') $wherestr2
    ORDER BY IF(birthdatetr != '0000-00-00',birthdatetr,altbirthdatetr) LIMIT 1";
$result =  tng_query($query);
$firstbirth = tng_fetch_array($result);
$firstbirthpersonid = $firstbirth['personID'];
$firstbirthfirstname = $firstbirth['firstname'];
$firstbirthlnprefix = $firstbirth['lnprefix'];
$firstbirthlastname = $firstbirth['lastname'];
$firstbirthdate = $firstbirth['birthdate'] ? $firstbirth['birthdate'] : $firstbirth['altbirthdate'];
$firstbirthgedcom = $firstbirth['gedcom'];

$rights = determineLivingPrivateRights($firstbirth);
$firstallowed = $rights['both'];

tng_free_result($result);

$query = "SELECT YEAR( deathdatetr ) - YEAR(IF(birthdatetr !='0000-00-00',birthdatetr,altbirthdatetr)) 
	AS yearsold, DAYOFYEAR( deathdatetr ) - DAYOFYEAR(IF(birthdatetr !='0000-00-00',birthdatetr,altbirthdatetr)) AS daysold,
	IF(DAYOFYEAR(deathdatetr) and DAYOFYEAR(IF(birthdatetr !='0000-00-00',birthdatetr,altbirthdatetr)),TO_DAYS(deathdatetr) - TO_DAYS(IF(birthdatetr !='0000-00-00',birthdatetr,altbirthdatetr)),(YEAR(deathdatetr) - YEAR(IF(birthdatetr !='0000-00-00',birthdatetr,altbirthdatetr))) * 365) as totaldays
    FROM $people_table
    WHERE (birthdatetr != '0000-00-00' OR altbirthdatetr != '0000-00-00') AND deathdatetr != '0000-00-00'
		AND (birthdate not like 'AFT%' OR altbirthdate not like 'AFT%') AND deathdate not like 'AFT%'
		AND (birthdate not like 'BEF%' OR altbirthdate not like 'BEF%') AND deathdate not like 'BEF%'
		AND (birthdate not like 'ABT%' OR altbirthdate not like 'ABT%') AND deathdate not like 'ABT%'
		AND (birthdate not like 'BET%' OR altbirthdate not like 'BET%') AND deathdate not like 'BET%'
		AND (birthdate not like 'CAL%' OR altbirthdate not like 'CAL%') AND deathdate not like 'CAL%'
		$wherestr2
    ORDER BY totaldays DESC";
$result =  tng_query($query);
$numpeople = tng_num_rows($result);
$avgyears = 0;
$avgdays = 0;
$totyears = 0;
$totdays = 0;

while( $line = tng_fetch_array($result, 'assoc') )
{
	$yearsold = $line['yearsold'];
	$daysold = $line['daysold'];

	if( $daysold < 0 ) {
		if ($yearsold > 0) {
			$yearsold--;
			$daysold = 365 + $daysold;
		}
	}
	$totyears += $yearsold;
	$totdays += $daysold;
}
$avgyears = $numpeople ? $totyears / $numpeople : 0;

// convert the remainder from $avgyears to days
$avgdays = ($avgyears - floor($avgyears)) * 365;  

// add the number of averge days calculated from $totdays
$avgdays += $numpeople ? $totdays / $numpeople : 0;                 

// if $avgdays is more than a year, we've got to adjust things! 
if ($avgdays > 365) {
    // add the number of additional years $avgdaysgives us
	$avgyears += floor($avgdays/365);  

    //change $avgdays to days left after removing multiple 
    //years' worth of days.
	$avgdays = $avgdays - (floor($avgdays/365) * 365); 
}
$avgyears = floor($avgyears);
$avgdays = floor($avgdays);

tng_free_result($result);

$percentmales = $totalpeople ? round(100 * $males / $totalpeople, 2) : 0;
$percentfemales = $totalpeople ? round(100 * $females / $totalpeople, 2) : 0;
$percentunknownsex = $totalpeople ? round(100 * $unknownsex / $totalpeople, 2) : 0;

$totalpeople = number_format($totalpeople);
$totalfamilies = number_format($totalfamilies);
$males = number_format($males);
$females = number_format($females);
$unknownsex = number_format($unknownsex);
$unbekannt = number_format($unbekannt);

echo "<tr><td class=\"databack\"><span class=\"normal\">{$text['totindividuals']}</span></td>\n";
echo "<td class=\"databack\" align=\"right\"><span class=\"normal\">$totalpeople &nbsp;</span></td></tr>\n";

echo "<tr><td class=\"databack\"><span class=\"normal\">{$text['totmales']}</span></td>\n";
echo "<td class=\"databack\" align=\"right\"><span class=\"normal\">$males ($percentmales%) &nbsp;</span></td></tr>\n";

echo "<tr><td class=\"databack\"><span class=\"normal\">{$text['totfemales']}</span></td>\n";
echo "<td class=\"databack\" align=\"right\"><span class=\"normal\">$females ($percentfemales%) &nbsp;</span></td></tr>\n";

echo "<tr><td class=\"databack\"><span class=\"normal\">{$text['totunknown']}</span></td>\n";
echo "<td class=\"databack\" align=\"right\"><span class=\"normal\">$unknownsex ($percentunknownsex%) &nbsp;</span></td></tr>\n";

echo "<tr><td class=\"databack\"><span class=\"normal\">Personen mit unbekanntem Vornamen</span></td>\n";
echo "<td class=\"databack\" align=\"right\"><span class=\"normal\">$unbekannt &nbsp;</span></td></tr>\n";

echo "<tr><td class=\"databack\"><span class=\"normal\">{$text['totliving']}</span></td>\n";
echo "<td class=\"databack\" align=\"right\"><span class=\"normal\">$numliving &nbsp;</span></td></tr>\n";

echo "<tr><td class=\"databack\"><span class=\"normal\">{$text['totfamilies']}</span></td>\n";
echo "<td class=\"databack\" align=\"right\"><span class=\"normal\">$totalfamilies &nbsp;</span></td></tr>\n";

echo "<tr><td class=\"databack\"><span class=\"normal\">{$text['totuniquesn']}</span></td>\n";
echo "<td class=\"databack\" align=\"right\"><span class=\"normal\">$uniquesurnames &nbsp;</span></td></tr>\n";

echo "<tr><td class=\"databack\"><span class=\"normal\">Anzahl der Medien (Photo, Video, Dokumente)</span></td>\n";
echo "<td class=\"databack\" align=\"right\"><span class=\"normal\">$medias &nbsp;</span></td></tr>\n";

echo "<tr><td class=\"databack\"><span class=\"normal\">{$text['totsources']}</span></td>\n";
echo "<td class=\"databack\" align=\"right\"><span class=\"normal\">$totalsources &nbsp;</span></td></tr>\n";

echo "<tr><td class=\"databack\"><span class=\"normal\">{$text['avglifespan']}<sup><font size=\"1\"></font></sup></span></td>\n";
echo "<td class=\"databack\" align=\"right\"><span class=\"normal\">$avgyears {$text['years']}, $avgdays {$text['days']} &nbsp;</span></td></tr>\n";

echo "<tr><td class=\"databack\"><span class=\"normal\">" . $text['earliestbirth'];
if($firstallowed)
	echo " (<a href=http://ahnen.ollinet.org/getperson.php?personID=$firstbirthpersonid&amp;tree=$firstbirthgedcom>$firstbirthfirstname $firstbirthlnprefix $firstbirthlastname</a>)";
echo "&nbsp;</span></td>\n";
echo "<td class=\"databack\" align=\"right\"><span class=\"normal\">" . displayDate( $firstbirthdate ) . " &nbsp;</span></td></tr>\n";

?>
</table>
<br />
