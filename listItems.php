<?php

//INCLUDES
include_once('header.php');
include_once('config.php');

//CONNECT TO DATABASE
$connection = mysql_connect($host, $user, $pass) or die ("unable to connect");
mysql_select_db($db) or die ("unable to select database!");


//GET URL VARIABLES
$type=$_GET["type"]{0};
$pType=$_GET["pType"]{0};
if ($pType!="s") $pType="p";
if ($_GET['contextId']>0) $contextId=(int) $_GET['contextId'];
else $contextId=(int) $_POST['contextId'];
if ($_GET['categoryId']>0) $categoryId=(int) $_GET['categoryId'];
else $categoryId=(int) $_POST['categoryId'];
	
if ($ptype=='s') $ptypequery='y';
else $ptypequery='n';

//Set page titles
if ($type=="a") {
	$typename="Actions";
	$typequery="a";
	}
elseif ($type=="n") {
	$typename="Next Actions";
	$display="nextonly";
	$typequery="a";
	}
elseif ($type=="r") {
	$typename="References";
	$typequery="r";
	}
elseif ($type=="w") {
	$typename="Waiting On";
	$typequery="w";
	}
else {
	$typename="Items";
	$typequery="a";
	}
	
//SQL CODE
//select all contexts for dropdown list
$query = "SELECT contextId, name, description  FROM context ORDER BY name ASC";
$result = mysql_query($query) or die("Error in query");
$cshtml="";
while($row = mysql_fetch_assoc($result)) {
        $cshtml .= '<option value="'.$row['contextId'].'" title="'.htmlspecialchars(stripslashes($row['description'])).'"';
        if($row['contextId']==$contextId) $cshtml .= ' SELECTED';
        $cshtml .= '>'.stripslashes($row['name']).'</option>\n';
	}
mysql_free_result($result);

//select all nextactions for test
$query = "SELECT projectId, nextaction FROM nextactions";
$result = mysql_query($query) or die ("Error in query");
$nextactions = array();
while ($nextactiontest = mysql_fetch_assoc($result)) {
	//populates $nextactions with itemIds using projectId as key
	$nextactions[$nextactiontest['projectId']] = $nextactiontest['nextaction'];
	}

//select all categories for dropdown list
$query = "SELECT categories.categoryId, categories.category, categories.description from categories ORDER BY categories.category ASC";
$result = mysql_query($query) or die("Error in query");
$cashtml="";
while($row = mysql_fetch_assoc($result)) {
        $cashtml .= '<option value="'.$row['categoryId'].'" title="'.htmlspecialchars(stripslashes($row['description'])).'"';
        if($row['categoryId']==$categoryId) $cashtml .= ' SELECTED';
        $cashtml .= '>'.stripslashes($row['category']).'</option>\n';
        }
mysql_free_result($result);


//Select items
$catquery = "";
$contextquery = "";
if ($contextId != NULL) $contextquery = "AND itemattributes.contextId = '$contextId'";
if ($categoryId != NULL) $catquery = " AND projectattributes.categoryId = '$categoryId'";

		$query = "SELECT itemattributes.projectId, projects.name AS pname, items.title, items.description, itemstatus.dateCreated, 
			context.contextId, context.name AS cname, items.itemId, itemstatus.dateCompleted, itemattributes.deadline, 
			itemattributes.repeat, itemattributes.suppress, itemattributes.suppressUntil 
			FROM items, itemattributes, itemstatus, projects, projectattributes, projectstatus, context 
			WHERE itemstatus.itemId = items.itemId AND itemattributes.itemId = items.itemId 
			AND itemattributes.contextId = context.contextId AND itemattributes.projectId = projects.projectId 
			AND projectattributes.projectId=itemattributes.projectId AND projectstatus.projectId = itemattributes.projectId 
			AND itemattributes.type = '$typequery' " .$catquery.$contextquery. " AND projectattributes.isSomeday='$ptypequery'
			AND (itemstatus.dateCompleted IS NULL OR itemstatus.dateCompleted = '0000-00-00')
			AND (projectstatus.dateCompleted IS NULL OR projectstatus.dateCompleted = '0000-00-00') 
			AND ((CURDATE() >= DATE_ADD(itemattributes.deadline, INTERVAL -(itemattributes.suppressUntil) DAY))
				OR itemattributes.suppress='n'
				OR ((CURDATE() >= DATE_ADD(projectattributes.deadline, INTERVAL -(projectattributes.suppressUntil) DAY))))
			ORDER BY projects.name, itemattributes.deadline, items.title";


$result = mysql_query($query) or die ("Error in query");

//PAGE DISPLAY CODE
        echo '<h2><a href="item.php?type='.$type.'" title="Add new '.str_replace("s","",$typename).'">'.$typename.'</a></h2>';
	echo '<form action="listItems.php?type='.$type.'" method="post">';
	echo '<p>Category:';
        echo '&nbsp;<select name="categoryId" title="Filter items by project category">';
	echo '<option value="">All</option>';
	echo $cashtml;
	echo '</select>';
	echo '&nbsp;&nbsp;&nbsp;Context:';
        echo '&nbsp;<select name="contextId" title="Filter items by context">';
	echo '<option value="">All</option>';
	echo $cshtml;
	echo '</select>';
	echo '<input type="submit" class="button" value="Filter" name="submit" title="Filter '.$typename.' by category and/or context"></p></form>';

	if (mysql_num_rows($result) > 0) {
		$tablehtml="";		
		while($row = mysql_fetch_assoc($result)){

			$showme="y";
			//filter out all but nextactions if $display=nextonly
			if (($display=='nextonly')  && !($key=array_search($row['itemId'],$nextactions))) $showme="n";

			if($showme=="y") {
			
				$tablehtml .= "<tr>";
				$tablehtml .= '<td><a href = "projectReport.php?projectId='.$row['projectId'].'"title="Go to '.htmlspecialchars(stripslashes($row['pname'])).' project report">'.stripslashes($row['pname']).'</a></td>';

				//if nextaction, add icon in front of action (* for now)
				if ($key = array_search($row['itemId'],$nextactions)) $tablehtml .= '<td><a href = "item.php?itemId='.$row['itemId'].'" title="Edit '.htmlspecialchars(stripslashes($row['title'])).'">*&nbsp;'.stripslashes($row['title']).'</td>';
				else $tablehtml .= '<td><a href = "item.php?itemId='.$row['itemId'].'" title="Edit '.htmlspecialchars(stripslashes($row['title'])).'">'.stripslashes($row['title']).'</td>';
				$tablehtml .= '<td>'.nl2br(stripslashes($row['description'])).'</td>';
				$tablehtml .= '<td><a href = "reportContext.php?contextId='.$row['contextId'].'" title="Go to '.htmlspecialchars(stripslashes($row['cname'])).' context report">'.stripslashes($row['cname']).'</td>';
				$tablehtml .= "<td>";

				if(($row['deadline']) == "0000-00-00" || $row['deadline'] ==NULL) $tablehtml .= "&nbsp;";
				elseif(($row['deadline']) < date("Y-m-d")) $tablehtml .= '<font color="red"><strong title="Item overdue">'.date("D M j, Y",strtotime($row['deadline'])).'</strong></font>';  //highlight overdue actions
				elseif(($row['deadline']) == date("Y-m-d")) $tablehtml .= '<font color="green"><strong title="Item due today">'.date("D M j, Y",strtotime($row['deadline'])).'</strong></font>'; //highlight actions due today
				else $tablehtml .= date("D M j, Y",strtotime($row['deadline']));
				
				$tablehtml .= "</td>";
				if ($row['repeat']=="0") $tablehtml .= "<td>--</td>";
				else $tablehtml .= "<td>".$row['repeat']."</td>";
	            $tablehtml .= '<td align="center">  <input type="checkbox" align="center" title="Complete '.htmlspecialchars(stripslashes($row['title'])).'" name="completedNas[]" value="';
                $tablehtml .= $row['itemId'];
                $tablehtml .= '" /></td>';
				$tablehtml .= "</tr>\n";
			}
		}

		if ($tablehtml!="") {
			echo '<form action="processItemUpdate.php" method="post">';
			echo "<table>\n";
			echo '<tr>';
			echo '<th>Project</th>';
			echo '<th>'.$typename.'</th>';
			echo '<th>Description</th>';
			echo '<th>Context</th>';
			echo '<th>Deadline</th>';
			echo '<th>Repeat</th>';
			echo '<th>Completed</th>';
			echo "</tr>\n";
			echo $tablehtml;
			echo "</table>\n";
			echo '<input type="hidden" name="type" value=".$type." />';
			echo '<input type="hidden" name="contextId" value=".$contextId." />';
			echo '<input type="hidden" name="referrer" value="i" />';
        	        echo '<input type="submit" class="button" value="Complete '.$typename.'" name="submit"></form>';
			}

		else echo "<h4>Nothing was found</h4>";
		}

	else echo "<h4>Nothing was found</h4>";

	mysql_free_result($result);
	mysql_close($connection);
	include_once('footer.php');
?>
