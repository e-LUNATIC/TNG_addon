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

$treestr = $tree ? " ({$text['tree']}: $tree)" : "";
$logstring = "<a href=\"$statistics_url" . "tree=$tree\">" . xmlcharacters($text['databasestatistics'] . $treestr) . "</a>";
writelog($logstring);
preparebookmark($logstring);


?>

<div class="cb-layout-cell layout-item-4">

										<div>
											<?php
					$tngquery = "SELECT lastname, firstname, changedate, personID, gedcom, living, private, branch, lnprefix, title, suffix, prefix FROM $people_table ORDER BY changedate DESC LIMIT 15";
					$resulttng = tng_query( $tngquery ) or die( $text['cannotexecutequery'] . ": $tngquery" );

					$found = tng_num_rows( $resulttng );
					while( $dbrow = tng_fetch_assoc( $resulttng ) ) {
						$lastadd .= "<a href=http://ahnen.ollinet.org/getperson.php?personID={$dbrow['personID']}&amp;tree={$dbrow['gedcom']}>";

						$dbrights = determineLivingPrivateRights($dbrow);
						$dbrow['allow_living'] = $dbrights['living'];
						$dbrow['allow_private'] = $dbrights['private'];

						$lastadd .= getNameRev($dbrow);
						$lastadd .= "</a><br />\n";
					}
					tng_free_result($resulttng);
					echo $lastadd
											?>
										</div>
									</div>
</table>
<br />
