<?php
require_once("config.php");
function makemenu($list) {
	global $acckey;
	$class='';
	foreach ($list as $info) {
        if ($info==='separator') {
            $class=" class='menuseparator' ";
            continue;
        }
		if (empty($acckey[$info['link']])) {
            $key='';
            $accesskey='';
        } else {
            $key=' ('.$acckey[$info['link']].')';
            $accesskey=" accesskey='".$acckey[$info['link']]."'";
        }

		echo "<li $class>\n"
            ,"<a href='{$info['link']}' title='{$info['title']}' $accesskey>\n"
            ,"{$info['label']}$key</a></li>\r\n";
		$class='';
	}
}
?>
<div id="header">
	<h1 id='sitename'><a href='index.php'><?php echo $config['title'];?></a></h1>
</div>
<div id="menudiv">
	<ul id="menulist">
	 	<li>Capture
			<ul>
				<?php 
				$thismenu[] = array("link"=>"item.php?type=i", 'title'=>"Drop an item into the inbox", 'label' => "Inbox item");
				$thismenu[] = array("link"=>"item.php?type=a&amp;nextonly=true", 'title'=>"Create a new next action", 'label' => "Next Action");
				$thismenu[] = array("link"=>"item.php?type=a", 'title'=>"Create a new action", 'label' => "Action");
				$thismenu[] = array("link"=>"item.php?type=w", 'title'=>"Create a new waiting on item", 'label' => "Waiting On");
				$thismenu[] = array("link"=>"item.php?type=r", 'title'=>"Create a reference", 'label' => "Reference");
				$thismenu[] = array("link"=>"item.php?type=p", 'title'=>"Create a new project", 'label' => "Project");
				$thismenu[] = array("link"=>"item.php?type=p&amp;someday=true", 'title'=>"Create a future project", 'label' => "Someday/Maybe");
				$thismenu[] = 'separator';
				$thismenu[] = array("link"=>"item.php?type=g", 'title'=>"Define a new goal", 'label' => "Goal");
                $thismenu[] = array("link"=>"item.php?type=o", 'title'=>"Define a new role", 'label' => "Role");
				$thismenu[] = array("link"=>"item.php?type=v", 'title'=>"Define a new vision", 'label' => "Vision");
				$thismenu[] = array("link"=>"item.php?type=m", 'title'=>"Define a new value", 'label' => "Value");
				makemenu($thismenu);
				unset($thismenu);
				?>
			</ul>
        </li>
		<li>Process
			<ul>
				<?php 
				$thismenu[] = array("link"=>"listItems.php?type=i", 'title'=>"Inbox", 'label' => "Inbox");
				$thismenu[] = array("link"=>"listItems.php?type=a&amp;nextonly=true", 'title'=>"Next actions", 'label' => "Next Actions");
				$thismenu[] = array("link"=>"listItems.php?type=a", 'title'=>"Actions", 'label' => "Actions");
				$thismenu[] = array("link"=>"listItems.php?type=w", 'title'=>"Waiting On", 'label' => "Waiting On");
				$thismenu[] = array("link"=>"listItems.php?type=r", 'title'=>"References", 'label' => "References");
                $thismenu[] = array("link"=>"listItems.php?type=p", 'title'=>"Projects", 'label' => "Projects");
				$thismenu[] = array("link"=>"listItems.php?type=p&amp;someday=true", 'title'=>"Someday projects", 'label' => "Someday/Maybes");
				$thismenu[] = 'separator';
				$thismenu[] = array("link"=>"reportContext.php", 'title'=>"Active items sorted by space context", 'label' => "Actions in context");
				if ($config['debug'] & _GTD_DEBUG) {
                    $thismenu[] = array("link"=>"reportTimeContext.php", 'title'=>"Active items sorted by time context", 'label' => "Time Contexts");
    				$thismenu[] = array("link"=>"reportCategory.php", 'title'=>"Active items sorted by category", 'label' => "Categories");
				}
				$thismenu[] = array("link"=>"index.php", 'title'=>"Summary view", 'label' => "Summary");
				$thismenu[] = array("link"=>"listItems.php?quickfind", 'title'=>'find an item based on text in its title, description or outcome', 'label'=>'Quick Find');
				makemenu($thismenu);
				unset($thismenu);
				?>
			</ul>
        </li>
		<li>Review
			<ul>
				<?php
				$thismenu[] = array("link"=>"weekly.php", 'title'=>"Steps in the Weekly Review", 'label' => "Weekly Review");
				$thismenu[] = array("link"=>"orphans.php", 'title'=>"List items without a parent item", 'label' => "Orphaned Items");
				$thismenu[] = array("link"=>"listItems.php?type=a&amp;tickler=true", 'title'=>"Hidden items and reminders", 'label' => "Tickler File");
				$thismenu[] = 'separator';
				$thismenu[] = array("link"=>"listItems.php?type=g", 'title'=>"Goals", 'label' => "Goals");
				$thismenu[] = array("link"=>"listItems.php?type=o", 'title'=>"Roles / Areas of Responsibility", 'label' => "Roles");
				$thismenu[] = array("link"=>"listItems.php?type=v", 'title'=>"Visions", 'label' => "Visions");
				$thismenu[] = array("link"=>"listItems.php?type=m", 'title'=>"Values / Mission", 'label' => "Values");
				if ($config['debug'] & _GTD_DEBUG) {
    				$thismenu[] = 'separator';
                    $thismenu[] = array("link"=>"leadership.php", 'title'=>"Leadership view", 'label' => "Leadership");
				    $thismenu[] = array("link"=>"management.php", 'title'=>"Management view", 'label' => "Management");
				    $thismenu[] = array("link"=>"achievements.php", 'title'=>"Notable Achievements", 'label' => "Achievements");
                }
				makemenu($thismenu);
				unset($thismenu);
				?>

			</ul>
        </li>
        <li>Lists
			<ul>
				<?php
				$thismenu[] = array("link"=>"newList.php", 'title'=>"Create a general purpose list", 'label' => "New List");
				$thismenu[] = array("link"=>"listList.php", 'title'=>"General-purpose lists", 'label' => "Show Lists");
				$thismenu[] = 'separator';
				$thismenu[] = array("link"=>"newChecklist.php", 'title'=>"Create a reusable list", 'label' => "New Checklist");
				$thismenu[] = array("link"=>"listChecklist.php", 'title'=>"Reusable checklists", 'label' => "Show Checklists");
				makemenu($thismenu);
				unset($thismenu);
				?>
			</ul>
        </li>
        <li>Configure
    		<ul>
				<?php
				$thismenu[] = array("link"=>"editCat.php?field=category", 'title'=>"Meta-categories", 'label' => "Categories");
				$thismenu[] = array("link"=>"editCat.php?field=context", 'title'=>"Spatial contexts", 'label' => "Space Contexts");
				$thismenu[] = array("link"=>"editCat.php?field=time-context", 'title'=>"Time contexts", 'label' => "Time Contexts");
				$thismenu[] = 'separator';
				$thismenu[] = array("link"=>"preferences.php", 'title'=>"User preferences", 'label' => "User Preferences");
                if ($config['showAdmin'])
                    $thismenu[] = array("link"=>"admin.php", 'title'=>"Administration", 'label' => "Admin");
				makemenu($thismenu);
				unset($thismenu);
				?>
			</ul>
        </li>
		<li>Help
			<ul>
				<?php
				$thismenu[] = array("link"=>"http://www.gtd-php.com/Users/Documentation", 'title'=>"Documentation", 'label' => "Helpfile Wiki");
				$thismenu[] = array("link"=>"https://www.hosted-projects.com/trac/toae/gtdphp/newticket", 'title'=>"Report a bug on the gtd-php trac system", 'label' => "Report a bug");
				$thismenu[] = array("link"=>"http://toae.org/boards", 'title'=>"Help and development discussions", 'label' => "Support Forum");
				$thismenu[] = array('link'=>'http://www.gtd-php.com/Developers/Contrib','title'=>'User-contributed enhancements','label'=>'Themes and add-ons');
				$thismenu[] = array("link"=>"https://www.hosted-projects.com/trac/toae/gtdphp", 'title'=>"Bug tracking and project development", 'label' => "Developers' wiki");
				$thismenu[] = array("link"=>"http://www.frappr.com/gtdphp", 'title'=>"Tell us where you are", 'label' => "Frappr Map");
				if ($config['debug'] & _GTD_DEBUG) {
				    $thismenu[] = array("link"=>"https://www.hosted-projects.com/trac/toae/gtdphp/log?action=stop_on_copy&amp;rev="
                        ._GTD_REVISION."&amp;stop_rev=411&amp;mode=follow_copy&amp;verbose=on"
                        ,'title'=>'Changelog (requires trac login)', 'label'=>'Changelog');
                }
				$thismenu[] = 'separator';
				$thismenu[] = array("link"=>"donate.php", 'title'=>"Help us defray our costs", 'label' => "Donate");
				$thismenu[] = array("link"=>"credits.php", 'title'=>"The GTD-PHP development team", 'label' => "Credits");
				$thismenu[] = array("link"=>"license.php", 'title'=>"The GTD-PHP license", 'label' => "License");
				$thismenu[] = array("link"=>"version.php", 'title'=>"Version information", 'label' => "Version");
				makemenu($thismenu);
				unset($thismenu);
				?>
			</ul>
        </li>
	</ul>
</div>
