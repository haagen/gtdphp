<?php

//INCLUDES
include_once('header.php');
include_once('config.php');

//RETRIEVE URL VARIABLES
$pId = (int) $_GET['projectId'];
$pName = (string) $_GET['projectName'];
 
//SQL CODE AREA
$connection = mysql_connect($host, $user, $pass) or die ("Unable to connect");
mysql_select_db($db) or die ("Unable to select database!");

//obtain all project names
$query = "SELECT projectId, name FROM projects";
$result = mysql_query($query) or die ("Error in query");
if(mysql_num_rows($result) < 1) {
	echo "No project names";
	}
$projectNames = array();
while($row = mysql_fetch_assoc($result)) {
	$projectNames[$row[projectId]]=stripslashes($row[name]);
	}

//obtain all contexts
$query = "SELECT contextId, name FROM context";
$contextResults = mysql_query($query) or die ("Error in query");
$contextNames=array();
while($row = mysql_fetch_assoc($contextResults)) {
	$contextNames[$row[contextId]]=stripslashes($row[name]);
	}

//obtain all timeframes
$query = "SELECT timeframeId, timeframe, description FROM timeitems";
$timeframeResults = mysql_query($query) or die ("Error in query");
$timeframeNames=array();
while($row = mysql_fetch_assoc($timeframeResults)) {
	$timeframeNames[$row[timeframeId]]=stripslashes($row[timeframe]);
	$timeframeDesc[$row[timeframeId]]=htmlspecialchars(stripslashes($row[timeframe]));
	}

//select all nextactions for test
$query = "SELECT projectId, nextaction FROM nextactions";
$result = mysql_query($query) or die ("Error in query");
$nextactions = array();
while ($nextactiontest = mysql_fetch_assoc($result)) {
        //populates $nextactions with itemIds using projectId as key
        $nextactions[$nextactiontest['projectId']] = $nextactiontest['nextaction'];
        }

//obtain all active item timeframes and count instances of each
$query="SELECT itemattributes.contextId, itemattributes.timeframeId, COUNT(*) AS count
	FROM itemattributes, itemstatus, projectattributes, projectstatus, nextactions
	WHERE itemstatus.itemId=itemattributes.itemId AND projectattributes.projectId=itemattributes.projectId
    AND nextactions.nextaction = itemstatus.itemId
	AND projectstatus.projectId=projectattributes.projectId AND itemattributes.type='a' AND projectattributes.isSomeday='n'
	AND (itemstatus.dateCompleted is null OR itemstatus.dateCompleted='0000-00-00')
	AND (projectstatus.dateCompleted is null OR projectstatus.dateCompleted='0000-00-00') 
	AND ((CURDATE() >= DATE_ADD(itemattributes.deadline, INTERVAL -(itemattributes.suppressUntil) DAY))
		OR projectattributes.suppress='n'
		OR (CURDATE() >= DATE_ADD(projectattributes.deadline, INTERVAL -(projectattributes.suppressUntil) DAY))) 
	GROUP BY itemattributes.contextId, itemattributes.timeframeId";

$itemResults = mysql_query($query) or die ("Error in query");

for ($j=0;$contextRow=mysql_fetch_assoc($itemResults);$j++) {
	$contextArray[$contextRow[contextId]][$contextRow[timeframeId]] = $contextRow[count];
	}


//PAGE DISPLAY CODE

    echo "<h2>Contexts Summary</h2>";
    echo "<h3>Spatial Context (row), Temporal Context (column)</h3>";

//context table
echo "<table>";
echo "<tr>";
echo "<th>Context</th>";
foreach ($timeframeNames as $tcId => $tname) {
	echo '<th><a href="editTimeContext.php?tcId='.$tcId.'" title="Edit the '.htmlspecialchars(stripslashes($tname)).' time context">'.stripslashes($tname).'</a></th>';
	}
echo "<th>Total</th>";
echo "</tr>";
$contextTotal=0;
$timeframeTotal=0;
foreach ($contextNames as $contextId => $cname) {
	$contextCount=0;
	echo '<tr>';
	echo '<td><a href="editContext.php?contextId='.$contextId.'" title="Edit the '.htmlspecialchars($cname).' context">'.$cname.'</a></td>';
	foreach ($timeframeNames as $timeframeId => $tname) {
		if ($contextArray[$contextId][$timeframeId]!="") {
			$count=$contextArray[$contextId][$timeframeId];
			$contextCount=$contextCount+$count;
			echo '<td><a href="#'.$cname.'_'.$timeframeId.'">'.$count.'</a></td>';
			}
		else echo "<td>0</td>";
		}
	echo '<td><a href="#'.htmlspecialchars($cname).'">'.$contextCount.'</a></td>';
	$contextTotal=$contextTotal+$contextCount;
	echo '</tr>';
	}
echo "<tr><td>Total</td>";
foreach ($timeframeNames as $timeframeId => $tname) {
	$timeframeCount=0;
	foreach ($contextNames as $contextId => $cname) {
		if ($contextArray[$contextId][$timeframeId]!="") {
			$count=$contextArray[$contextId][$timeframeId];
			$timeframeCount=$timeframeCount+$count;
			}		
		}
	echo "<td>".$timeframeCount."</td>";
	}
echo "<td>".$contextTotal."</td>";
echo "</tr>";
echo "</table>";

echo " <p>To move to a particular space-time context, select the number.<br />To edit a context select the context name.</p>";

//Item listings by context and timeframe

foreach ($contextArray as $contextId => $timeframe) {

	echo '<a name="'.$contextNames[$contextId].'"></a>';
	echo '<h2>Context:&nbsp;'.$contextNames[$contextId].'</h2>';

	foreach ($timeframe as $timeframeId => $itemCount) {
		echo '<a name="'.$contextNames[$contextId].'_'.$timeframeId.'"></a>';
		echo '<h3>Time Context:&nbsp;'.$timeframeNames[$timeframeId]."</h3>";

                $query = "SELECT itemattributes.projectId, projects.name AS pname, items.title, items.description, itemstatus.dateCreated,
                        items.itemId, itemstatus.dateCompleted, itemattributes.deadline,
                        itemattributes.repeat, itemattributes.suppress, itemattributes.suppressUntil
                        FROM items, itemattributes, itemstatus, projects, projectattributes, projectstatus, nextactions
                        WHERE itemstatus.itemId = items.itemId AND itemattributes.itemId = items.itemId
                        AND itemattributes.projectId = projects.projectId
                        AND projectattributes.projectId=itemattributes.projectId AND projectstatus.projectId = itemattributes.projectId
                        AND nextactions.nextaction=items.itemId
                        AND itemattributes.type = 'a' AND itemattributes.timeframeId='$timeframeId' AND projectattributes.isSomeday='n'
                        AND itemattributes.contextId='$contextId' AND (itemstatus.dateCompleted IS NULL OR itemstatus.dateCompleted = '0000-00-00')
                        AND (projectstatus.dateCompleted IS NULL OR projectstatus.dateCompleted = '0000-00-00')
			AND ((CURDATE() >= DATE_ADD(itemattributes.deadline, INTERVAL -(itemattributes.suppressUntil) DAY))
				OR projectattributes.suppress='n'
				OR (CURDATE() >= DATE_ADD(projectattributes.deadline, INTERVAL -(projectattributes.suppressUntil) DAY))) 
                        ORDER BY projects.name";


		$result = mysql_query($query) or die ("Error in query");

                $tablehtml="";
                while($row = mysql_fetch_assoc($result)) {
                        $tablehtml .= "<tr>";
                        $tablehtml .= '<td><a href = "projectReport.php?projectId='.$row['projectId'].'" title="Go to '.htmlspecialchars(stripslashes($row['pname'])).' project report">'.stripslashes($row['pname']).'</a></td>';
                        //if nextaction, add icon in front of action (* for now)
                        if ($key = array_search($row['itemId'],$nextactions)) $tablehtml .= '<td><a href = "item.php?itemId='.$row['itemId'].'" title="Edit '.htmlspecialchars($row['title']).'">*&nbsp;'.stripslashes($row['title']).'</td>';
                        else $tablehtml .= '<td><a href = "item.php?itemId='.$row['itemId'].'" title="Edit '.htmlspecialchars(stripslashes($row['title'])).'">'.stripslashes($row['title']).'</td>';
                        $tablehtml .= '<td>'.nl2br(stripslashes($row['description'])).'</td>';
                        $tablehtml .= "<td>";
                        if(($row['deadline']) == "0000-00-00") $tablehtml .= "&nbsp;";
                        elseif(($row['deadline']) < date("Y-m-d")) $tablehtml .= '<font color="red"><strong title ="Overdue">'.$row['deadline'].'</strong></font>';  //highlight overdue actions
                        elseif(($row['deadline']) == date("Y-m-d")) $tablehtml .= '<font color="green"><strong title="Due today">'.$row['deadline'].'</strong></font>'; //highlight actions due today
                        else $tablehtml .= $row['deadline'];
                        $tablehtml .= "</td>";
                        if ($row['repeat']=="0") $tablehtml .= "<td>--</td>";
                        else $tablehtml .= "<td>".$row['repeat']."</td>";
	 	        $tablehtml .= '<td align="center"><input type="checkbox" align="center" name="completedNas[]" title="Complete '.htmlspecialchars(stripslashes($row['title'])).'" value="';
                	$tablehtml .= $row['itemId'];
                	$tablehtml .= '"';
                        $tablehtml .= "</tr>\n";
			}

                if ($tablehtml!="") {
                        echo '<form action="processItemUpdate.php?type='.$type.'&contextId='.$contextId.'&referrer=c" method="post">';
                        echo "<table>\n";
                        echo '<tr>';
                        echo '<th>Project</th>';
                        echo '<th>Action</th>';
                        echo '<th>Description</th>';
                        echo '<th>Deadline</th>';
                        echo '<th>Repeat</th>';
                        echo '<th>Completed</th>';
                        echo "</tr>\n";
                        echo $tablehtml;
                        echo "</table>\n";
                        echo '<input type="submit" class="button" value="Update Actions" name="submit"></form>';
                        }

                	else echo "<h4>Nothing was found</h4>";
                	}
		}

	mysql_close($connection);
	include_once('footer.php');
?>
