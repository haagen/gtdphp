<?php
//INCLUDES
include_once('header.php');
if ($config['debug'] & _GTD_DEBUG) {
    echo '<pre>POST: ',var_dump($_POST),'</pre>';
}
//page display options array--- can put defaults in preferences table/config/session and load into $show array as defaults...
$show=array();

//GET URL VARIABLES
$values = array();
$filter = array();

// I've used getVarFromGetPost instead of $_REQUEST, because I want $_GET to have higher priority than $_POST.
$filter['type']           =getVarFromGetPost('type','a');
$filter['contextId']      =getVarFromGetPost('contextId',NULL);
if ($filter['contextId']==='0') $filter['contextId']=NULL;
$filter['categoryId']     =getVarFromGetPost('categoryId',NULL);
if ($filter['categoryId']==='0') $filter['categoryId']=NULL;
$filter['timeframeId']    =getVarFromGetPost('timeframeId',NULL);
if ($filter['timeframeId']==='0') $filter['timeframeId']=NULL;
$filter['notcategory']    =getVarFromGetPost('notcategory');
$filter['notspacecontext']=getVarFromGetPost('notspacecontext');
$filter['nottimecontext'] =getVarFromGetPost('nottimecontext');
$filter['tickler']        =getVarFromGetPost('tickler');           //suppressed (tickler file): true/false
$filter['someday']        =getVarFromGetPost('someday');           //someday/maybe:true/empty
$filter['nextonly']       =getVarFromGetPost('nextonly');          //next actions only: true/empty 
$filter['completed']      =getVarFromGetPost('completed');         //status:true/false (completed/pending)
$filter['dueonly']        =getVarFromGetPost('dueonly');           //has due date:true/empty
$filter['repeatingonly']  =getVarFromGetPost('repeatingonly');     //is repeating:true/empty
$filter['parentId']       =getVarFromGetPost('parentId');
$filter['everything']     =getVarFromGetPost('everything');        //overrides filter:true/empty
$filter['parentcompleted']=getVarFromGetPost('parentcompleted');
$filter['needle']         =getVarFromGetPost('needle');            //search string (plain text)

if ($filter['type']==='s') {
    $filter['someday']=true;
    $filter['type']='p';
}
/* end of setting $filter
 --------------------------------------*/
$values['type']           =$filter['type'];
$values['parentId']       =$filter['parentId'];
$values['contextId']      =$filter['contextId'];
$values['categoryId']     =$filter['categoryId'];
$values['timeframeId']    =$filter['timeframeId'];
$values['needle']         =$filter['needle'];

if ($config['debug'] & _GTD_DEBUG) echo '<pre>Filter:',print_r($filter),'</pre>';

//SQL CODE

//create filters for selectboxes
$values['timefilterquery'] = ($config['useTypesForTimeContexts'] && $values['type']!=='*')?" WHERE ".sqlparts("timetype",$config,$values):'';

//create filter selectboxes
$cashtml=str_replace('--','(any)',categoryselectbox   ($config,$values,$options,$sort));
$cshtml =str_replace('--','(any)',contextselectbox    ($config,$values,$options,$sort));
$tshtml =str_replace('--','(any)',timecontextselectbox($config,$values,$options,$sort));

/*
    ===================================================================
    build array of notes
    ===================================================================
*/
//Tickler file header and notes section
$remindertable=array();

if ($filter['tickler']=="true") {
    $values['filterquery'] = '';
    $result = query("getnotes",$config,$values,$options,$sort);
    if ($result!="-1") {
        foreach ($result as $row) {
            $remindertable[]=array(
                'id'=>$row['ticklerId']
                ,'date'=>$row['date']
                ,'title'=>htmlentities(stripslashes($row['title']),ENT_QUOTES)
                ,'note'=>nl2br($row['note'])
            );
        }
    }
}

/*
    ===================================================================
    finished building array of notes
    ===================================================================
*/
// pass filters in referrer
$thisurl=parse_url($_SERVER['PHP_SELF']);
$referrer = basename($thisurl['path']).'?';
foreach($filter as $filterkey=>$filtervalue)
    if ($filtervalue!='') $referrer .= "{$filterkey}={$filtervalue}&amp;";


//Select items

//set default table column display options (kludge-- needs to be divided into multidimensional array for each table type and added to preferences table
$show['parent']=TRUE;
$show['type']=FALSE;
$show['NA']=FALSE;
$show['title']=TRUE;
$show['description']=TRUE;
$show['desiredOutcome']=FALSE;
$show['isSomeday']=FALSE;
$show['suppress']=FALSE;
$show['suppressUntil']=FALSE;
$show['dateCreated']=FALSE;
$show['lastModified']=FALSE;
$show['category']=TRUE;
$show['context']=TRUE;
$show['timeframe']=TRUE;
$show['deadline']=TRUE;
$show['repeat']=TRUE;
$show['dateCompleted']=FALSE;
$show['checkbox']=TRUE;
$show['assignType']=FALSE;
$showalltypes=false;
//determine item and parent labels, set a few defaults
switch ($values['type']) {
    case "*" : $typename="Item"; $parentname=""; $values['ptype']=""; $show['type']=TRUE; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['dateCreated']=TRUE; $show['deadline']=FALSE; $show['desiredOutcome']=TRUE; $show['category']=FALSE; $show['context']=FALSE; $show['timeframe']=FALSE; $checkchildren=FALSE; $showalltypes=TRUE; break;
    case "m" : $typename="Value"; $parentname=""; $values['ptype']=""; $show['parent']=FALSE; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['dateCreated']=TRUE; $show['deadline']=FALSE; $show['desiredOutcome']=TRUE; $show['context']=FALSE; $show['timeframe']=FALSE; $checkchildren=TRUE; break;
    case "v" : $typename="Vision"; $parentname="Value"; $values['ptype']="m"; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['dateCreated']=TRUE; $show['deadline']=FALSE; $show['desiredOutcome']=TRUE; $show['context']=FALSE; $show['timeframe']=FALSE; $checkchildren=TRUE; break;
    case "o" : $typename="Role"; $parentname="Vision"; $values['ptype']="v"; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['deadline']=FALSE; $show['desiredOutcome']=TRUE; $show['context']=FALSE; $show['timeframe']=FALSE; $checkchildren=TRUE; break;
    case "g" : $typename="Goal"; $parentname="Role"; $values['ptype']="o"; $show['desiredOutcome']=TRUE; $show['context']=FALSE; $checkchildren=TRUE; break;
    case "p" : $typename="Project"; $parentname="Goal"; $values['ptype']="g"; $show['context']=FALSE; $show['timeframe']=FALSE; $checkchildren=TRUE; break;
    case "a" : $typename="Action"; $parentname="Project"; $values['ptype']="p"; $show['parent']=TRUE; $show['NA']=TRUE; $show['category']=FALSE; $checkchildren=FALSE; break;
    case "w" : $typename="Waiting On"; $parentname="Project"; $values['ptype']="p"; $show['parent']=TRUE; $checkchildren=FALSE; break;
    case "r" : $typename="Reference"; $parentname="Project"; $values['ptype']="p"; $show['parent']=TRUE; $show['category']=FALSE; $show['context']=FALSE; $show['timeframe']=FALSE; $show['checkbox']=FALSE; $show['repeat']=FALSE; $show['dateCreated']=TRUE; $checkchildren=FALSE; break;
    case "i" : $typename="Inbox Item"; $parentname=""; $values['ptype']=""; $show['parent']=FALSE; $show['category']=FALSE; $show['context']=FALSE; $show['timeframe']=FALSE; $show['deadline']=FALSE; $show['dateCreated']=TRUE; $show['repeat']=FALSE; $show['assignType']=TRUE; $checkchildren=FALSE; break;
    default  : $typename="Item"; $parentname=""; $values['ptype']=""; $checkchildren=FALSE; 
}
$show['flags']=$checkchildren; // temporary measure; to be made user-configurable later


if ($filter['someday']=="true") {
    $show['dateCreated']=TRUE;
    $show['context']=FALSE;
    $show['repeat']=FALSE;
    $show['NA']=FALSE;
    $show['deadline']=FALSE;
    $show['timeframe']=FALSE;
    $checkchildren=FALSE; 
}

if ($filter['tickler']=="true") $show['suppressUntil']=TRUE;

if ($filter['dueonly']=="true") $show['deadline']=TRUE;

if ($filter['repeatingonly']=="true") {
    $show['deadline']=TRUE;
    $show['repeat']=TRUE;
}

if ($filter['completed']=="true") {
    $show['suppress']=FALSE;
    $show['NA']=FALSE;
    $show['flags']=FALSE;
    $show['suppressUntil']=FALSE;
    $show['dateCreated']=TRUE;
    $show['deadline']=FALSE;
    $show['repeat']=FALSE;
    $show['dateCompleted']=TRUE;
    $show['checkbox']=FALSE;
    $checkchildren=FALSE; 
}

if ($filter['everything']=="true") {
    $show['parent']=!$showalltypes;
    $show['NA']=FALSE;
    $show['title']=TRUE;
    $show['description']=TRUE;
    $show['desiredOutcome']=$showalltypes;
    $show['type']=$showalltypes;
    $show['isSomeday']=FALSE;
    $show['suppress']=FALSE;
    $show['suppressUntil']=!$showalltypes;
    $show['dateCreated']=TRUE;
    $show['lastModified']=FALSE;
    $show['category']=!$showalltypes;
    $show['context']=!$showalltypes;
    $show['timeframe']=!$showalltypes;
    $show['deadline']=!$showalltypes;
    $show['repeat']=!$showalltypes;
    $show['dateCompleted']=TRUE;
    $show['checkbox']=FALSE;
}
    
//set query fragments based on filters
$values['childfilterquery'] = "WHERE TRUE";
$values['parentfilterquery'] = "";

//type filter
if ($values['type']!=='*')
    $values['childfilterquery'] .= " AND ".sqlparts("typefilter",$config,$values);

// search string
if ($filter['needle']!=='')
    $values['childfilterquery'] .= " AND ".sqlparts("matchall",$config,$values);

$linkfilter='';

if ($checkchildren) {
    $values['filterquery'] = sqlparts("checkchildren",$config,$values);
    $values['extravarsfilterquery'] = sqlparts("countchildren",$config,$values);;
} else {
    // get next actions array
    $values['extravarsfilterquery'] =sqlparts("getNA",$config,$values);
    if ($filter['nextonly']=='true' && $filter['everything']!="true")
        $values['filterquery'] = sqlparts("onlynextactions",$config,$values);
    else
        $values['filterquery'] = sqlparts("isNA",$config,$values);
}

/*
    Only use filter selections if $filter['everything'] is not true;
    i.e. if we are not forcing the listing of *all* items
*/
if ($filter['everything']!="true") {
    switch ($filter['parentcompleted']) {
        case '*': // don't filter on completion status of parents
            $values['filterquery'] .= ' WHERE TRUE '; // yes, I know this looks odd, but we may be concatenating an "AND {extra condition}" later
            break;

        case 'true': // only select items with completed parents
            $values['filterquery'] .= " WHERE " .sqlparts("completedparents",$config,$values);
            break;

        case 'false': // deliberately flows through to default case
        default:     //Filter out items with completed parents
            $values['filterquery'] .= " WHERE " .sqlparts("pendingparents",$config,$values);
            break;
    }

    //filter box filters
    if ($filter['categoryId'] != NULL && $filter['notcategory']=="true")
        $values['childfilterquery'] .= " AND ".sqlparts("notcategoryfilter",$config,$values);
    elseif($filter['categoryId'] != NULL || $filter['notcategory']=="true") {
        $values['childfilterquery'] .= " AND ".sqlparts("categoryfilter",$config,$values);
        $linkfilter .= '&amp;categoryId='.$values['categoryId'];
    }
    
    if ($filter['contextId'] != NULL && $filter['notspacecontext']=="true")
        $values['childfilterquery'] .= " AND ".sqlparts("notcontextfilter",$config,$values);
    elseif ($filter['contextId'] != NULL || $filter['notspacecontext']=="true") {
        $values['childfilterquery'] .= " AND ".sqlparts("contextfilter",$config,$values);
        $linkfilter .= '&amp;contextId='.$values['contextId'];
    }
    
    if ($filter['timeframeId'] != NULL && $filter['nottimecontext']=="true")
        $values['childfilterquery'] .= " AND ".sqlparts("nottimeframefilter",$config,$values);
    elseif ($filter['timeframeId'] != NULL || $filter['nottimecontext']=="true") {
        $values['childfilterquery'] .= " AND ".sqlparts("timeframefilter",$config,$values);
        $linkfilter .= '&amp;timeframeId='.$values['timeframeId'];
    }
    
    if ($filter['completed']=="true") $values['childfilterquery'] .= " AND ".sqlparts("completeditems",$config,$values);
    else $values['childfilterquery'] .= " AND " .sqlparts("pendingitems",$config,$values);
    
    //problem with project somedays vs actions...want an OR, but across subqueries;
    if ($filter['someday']=="true") {
        $values['isSomeday']="y";
        $values['childfilterquery'] .= " AND " .sqlparts("issomeday",$config,$values);
    } else {
        $values['isSomeday']="n";
        $values['childfilterquery'] .= " AND ".sqlparts("issomeday",$config,$values);
    //    $values['filterquery'] .= " AND " .sqlparts("issomeday-parent",$config,$values);
    }
    
    //problem: need to get all items with suppressed parents(even if child is not marked suppressed), as well as all suppressed items
    if ($filter['tickler']=="true") {
        $linkfilter .='&amp;tickler=true';
        $values['childfilterquery'] .= " AND ".sqlparts("suppresseditems",$config,$values);
    } else {
        $values['childfilterquery'] .= " AND ".sqlparts("activeitems",$config,$values);
        /* TOFIX - suppress untickled parents as well as untickled children
            - commented out for now, else we don't have an easy way to view tickled
                children of untickled parents
        */
        // $values['filterquery'] .= " AND ".sqlparts("activeparents",$config,$values); 
    }
    
    if ($filter['repeatingonly']=="true") $values['childfilterquery'] .= " AND " .sqlparts("repeating",$config,$values);
    
    if ($filter['dueonly']=="true") $values['childfilterquery'] .= " AND " .sqlparts("due",$config,$values);

    if ($values['parentId']!='') $values['filterquery'] .= " AND ".sqlparts("hasparent",$config,$values);

    /*
    $filter['nextonly']
    */   

}
/*
Section Heading
*/
$link="item.php?type=".$values['type'];

if($filter['everything']=="true")
    $sectiontitle = '';
else {
    $link .= $linkfilter;
    if ($filter['completed']=="true")
        $sectiontitle = 'Completed ';
    elseif ($filter['dueonly']=="true")
        $sectiontitle =  'Due ';
    else $sectiontitle ='';

    if ($filter['repeatingonly']=="true")
        $sectiontitle .= 'Repeating ';

    if ($filter['someday']=="true") {
        $sectiontitle .= 'Someday/Maybe ';
        $link.='&amp;someday=true';
    }
    if ($filter['nextonly']=="true") {
        $sectiontitle .= 'Next ';
        $link .='&amp;nextonly=true';
    }
}
$sectiontitle .= $typename;
/*
    ===================================================================
    main query: build array of items
    ===================================================================
*/
$result=query("getitemsandparent",$config,$values,$options,$sort);
$maintable=array();
$thisrow=0;
$allids=array();
if ($result!="-1") {
    $nonext=FALSE;
    $nochildren=FALSE;
    $wasNAonEntry=array();  // stash this in case we introduce marking actions as next actions onto this screen
    foreach ($result as $row) {
        $allids[]=$row['itemId'];
    
        $nochildren=false;
        $nonext=false;
        if ($checkchildren) {
            $nochildren=!$row['numChildren'];
            $nonext=!$row['numNA'];
        }
        if ($row['NA']) array_push($wasNAonEntry,$row['itemId']);
        
        $maintable[$thisrow]=array();
        $maintable[$thisrow]['itemId']=$row['itemId'];
        $maintable[$thisrow]['class'] = ($nonext || $nochildren)?'noNextAction':'';
        $maintable[$thisrow]['NA'] =$row['NA'];

        $maintable[$thisrow]['dateCreated'] = $row['dateCreated'];
        $maintable[$thisrow]['lastModified']= $row['lastModified'];
        $maintable[$thisrow]['dateCompleted']= $row['dateCompleted'];
        $maintable[$thisrow]['isSomeday'] =$row['isSomeday'];
        $maintable[$thisrow]['type'] =$row['type'];

        if ($row['parentId']=='') {
            $maintable[$thisrow]['parent.class']='noparent';
            $maintable[$thisrow]['ptitle']='';
        } else {
            $maintable[$thisrow]['ptitle']=$row['ptitle'];
            $maintable[$thisrow]['parentId']=$row['parentId'];
        }
        // add markers to indicate if this is a next action, or a project with no next actions, or an item with no childern
        if ($nochildren)
            $maintable[$thisrow]['flags'] = 'noChild';
        elseif ($nonext)
            $maintable[$thisrow]['flags'] = 'noNA';
        else
            $maintable[$thisrow]['flags'] = '';

        //item title
        $maintable[$thisrow]['doreport']=!($row['type']=="a" || $row['type']==="r" || $row['type']==="w" || $row['type']==="i");
        
        $cleantitle=makeclean($row['title']);
        $maintable[$thisrow]['title.class'] = 'maincolumn';
        $maintable[$thisrow]['title'] =$row['title'];

        $maintable[$thisrow]['checkbox.title']='Complete '.$cleantitle;
        $maintable[$thisrow]['checkboxname']= 'isMarked[]';
        $maintable[$thisrow]['checkboxvalue']=$row['itemId'];

        $maintable[$thisrow]['description'] = $row['description'];
        $maintable[$thisrow]['desiredOutcome'] = $row['desiredOutcome'];

        $maintable[$thisrow]['category'] =makeclean($row['category']);
        $maintable[$thisrow]['categoryid'] =$row['categoryId'];

        $maintable[$thisrow]['context'] = makeclean($row['cname']);
        $maintable[$thisrow]['timeframe'] = makeclean($row['timeframe']);
        $maintable[$thisrow]['timeframeid'] = $row['timeframeId'];


        $childType=array();
        $childType=getChildType($row['type']);
        if (count($childType)) $maintable[$thisrow]['childtype'] =$childType[0];
        
        if($row['deadline']) {
            $deadline=prettyDueDate($row['deadline'],$config['datemask']);
            $maintable[$thisrow]['deadline'] =$deadline['date'];
            if (empty($row['dateCompleted'])) {
                $maintable[$thisrow]['deadline.class']=$deadline['class'];
                $maintable[$thisrow]['deadline.title']=$deadline['title'];
            }
        } else $maintable[$thisrow]['deadline']='';
             
        $maintable[$thisrow]['repeat'] =((($row['repeat'])=="0")?'&nbsp;':($row['repeat']));

        //tickler date - calculate reminder date as # suppress days prior to deadline
        if ($row['suppress']=="y") {
            $reminddate=getTickleDate($row['deadline'],$row['suppressUntil']);
            $maintable[$thisrow]['suppressUntil']=date($config['datemask'],$reminddate);
        } else
            $maintable[$thisrow]['suppressUntil']= '&nbsp;';
                    
        
        $thisrow++;
    } // end of: foreach ($result as $row)
    
    $dispArray=array(
        'parent'=>'parents'
        ,'type'=>'type'
        ,'flags'=>'!'
        ,'NA'=>'NA'
        ,'title'=>$typename.'s'
        ,'description'=>'Description'
        ,'desiredOutcome'=>'Desired Outcome'
        ,'category'=>'Category'
        ,'context'=>'Space Context'
        ,'timeframe'=>'Time Context'
        ,'deadline'=>'Deadline'
        ,'repeat'=>'Repeat'
        ,'suppressUntil'=>'Reminder Date'
        ,'dateCreated'=>'Date Created'
        ,'lastModified'=>'Last Modified'
        ,'dateCompleted'=>'Date Completed'
        ,'checkbox'=>'Complete'
        ,'assignType'=>'Assign'
        );
    if ($config['debug'] & _GTD_DEBUG) echo '<pre>values to print:',print_r($maintable,true),'</pre>';
} // end of: if($result!="-1")
/*
    ===================================================================
    end of main query: finished building array of items
    ===================================================================
*/
$_SESSION['idlist-'.$values['type']]=$allids;
$numrows=count($maintable);
if ($numrows!==1) $sectiontitle.='s';
if ($filter['tickler']=="true" && $filter['everything']!="true") {
    $sectiontitle .= ' in Tickler File';
    $link .= '&amp;suppress=true';
}

if($filter['everything']=="true")
    switch ($numrows) {
        case 0:
            $sectiontitle = 'There are no '.$sectiontitle;
            break;
        case 1:
            $sectiontitle = 'There is one '.$sectiontitle;
            break;
        default:
            $sectiontitle = "All $numrows $sectiontitle";
            break;
    }
else
    $sectiontitle = $numrows.' '.$sectiontitle;

if($numrows)
    $endmsg='';
else {
    $endmsg=array('header'=>"You have no {$typename}s remaining.");
    if ($filter['completed']!="true" && $values['type']!="t") {
        $endmsg['prompt']="Create a new {$typename}";
        $endmsg['link']=$link;
    }
}
if (($filter['completed']!="true" || $filter['everything']=="true") && $filter['type']!=='*')
    $sectiontitle = "<a title='Add new' href='$link'>$sectiontitle</a>";

// php closing tag has been omitted deliberately, to avoid unwanted blank lines being sent to the browser
