var formValid;
var formErrorMessage;

function validate(form) {

    // Ensure validate is being used correctly

    if(form.elements['required'] == null) {
        document.getElementById("errorMessage").innerHTML="Error: Validate function needs 'required' form field.";
        return false;
    }
    if(form.elements['dateformat'] == null) {
        document.getElementById("errorMessage").innerHTML="Error: Validate function needs 'dateformat' form field.";
        return false;
    }
    
    // Init variables
    formValid = true;
    formErrorMessage = "Please correct the following:<br /><br />";
    
    // Parse required list and check each required field
    var requiredList = form.required.value.split(",");
    var requiredItem;
    
    // remove any previous error flags
    for(var i = 0;i<requiredList.length;i++){
        requiredItem = requiredList[i].split(":");
        form.elements[requiredItem[0]].className='';
        if (requiredItem[1]=='depends') form.elements[requiredItem[3]].className='';
    }
    
    for(var i = 0;i<requiredList.length;i++){
        requiredItem = requiredList[i].split(":");
        var thisfield = form.elements[requiredItem[0]];
        var checkType = requiredItem[1];
        var itemErrorMessage = requiredItem[2];
        var itemValue = thisfield.value;
        var passed;
            
        switch (checkType) {
            case "date":
                dateFormat = form.elements['dateformat'].value;
                passed=checkDate(thisfield,dateFormat);
                break;
            case "notnull":
        		passed=!checkForNull(thisfield);
                break;
            case "depends":
            	fieldRequires = form.elements[requiredItem[3]];
            	passed=checkForNull(thisfield) || !checkForNull(fieldRequires);
            	if (!passed) fieldRequires.className='formerror';
				break; 
            default:
                document.getElementById("errorMessage").innerHTML="Error: Required type not valid.";
                return false;                
        }
        markField(thisfield,passed,itemErrorMessage);
    }
        
    if(!formValid) {
        document.getElementById("errorMessage").innerHTML=formErrorMessage;
    }
    return formValid;
}

function markField(thisfield,passed,error) {        
    if (!passed) {
		formErrorMessage += error + "<br />";
    	formValid = false;
    	thisfield.className='formerror';
    }
}

function checkForNull(field) {
    switch (field.type) {
    	case "text":
    		var tst=field.value;
			tst.replace(" ","");
		    var isblank=(tst == "");
		    return isblank;
		case "checkbox":
			return !field.checked;
		default:
			return false;
	}
}

function checkDate(field,dateFormat) {
    if (checkForNull(field)) return true;
    // The validity of the format itself should be checked when set in user preferences.  This function assumes that the format passed in is valid.

    // Build the regular expression
    var format = dateFormat;
    format = format.replace(/([^mcyd])/,'[$1]');         // Any char that is not m, c, y, or d is a literal
    format = format.replace(/dd/,'[0-3][0-9]');          // First char of a day can be 0-3, second 0-9
    format = format.replace(/mm/,'[0-1][0-9]');          // First char of a month can be 0 or 1, second 0-9
    format = format.replace(/yy/,'[0-9][0-9]');          // Year must be two chars
    format = format.replace(/cc/,'[0-9][0-9]');          // Century must be two chars

    var dateRegEx = new RegExp(format);
    var tst=field.value;
    return (tst.match(dateRegEx));
}

function completeToday(datefield) {
	var now=new Date();
	var m  = now.getMonth()+1;
	var d  = now.getDate();
	var y  = now.getFullYear();
	m=(m < 10) ? ("0" + m) : m;
	d=(d < 10) ? ("0" + d) : d;
	var newdate=""+y+"-"+m+"-"+d;
	document.getElementById(datefield).value=newdate;
//	return true;
}
function aps_toggleVis (thisRule) {
	thisRule.style.display=(thisRule.style.display=="none")?"block":"none";
}
var aps_grabKey;
function aps_keyUpHandler(e) {
	if (!e) var e = window.event;
	if (e.target && e.target.nodeName) {
		var targetNodeName = e.target.nodeName.toLowerCase();
		if (targetNodeName == "textarea" || (targetNodeName == "input" && e.target.type && e.target.type.toLowerCase() == "text"))
			return false;
	}
	if (e.keyCode) code = e.keyCode;
	else if (e.which) code = e.which;
	var character = String.fromCharCode(code);
	if (character==aps_grabKey) {
		var thisRule;
		if (document.styleSheets[0].cssRules) {
			aps_toggleVis(document.styleSheets[0].cssRules[0]);
		} else {
			aps_toggleVis(document.styleSheets[0].rules[0]);
			aps_toggleVis(document.styleSheets[0].rules[1]);
		}
		
	}
	return false;
}
function aps_debugInit(keyToCatch) {
	aps_grabKey=keyToCatch;
	if (document.addEventListener)
		document.addEventListener("keyup", aps_keyUpHandler,false);
	else
		document.attachEvent("onkeyup", aps_keyUpHandler);
}
/*
sortTable amended for GTD-PHP

Based on code from: http://kryogenix.org/code/browser/sorttable/
Copyright (c) 1997-date Stuart Langridge

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
addEvent(window, "load", sortables_init);

var SORT_COLUMN_INDEX;

function sortables_init() {
    // Find all tables with class sortable and make them sortable
    if (!document.getElementsByTagName) return;
    tbls = document.getElementsByTagName("table");
    for (ti=0;ti<tbls.length;ti++) {
        thisTbl = tbls[ti];
        if (((' '+thisTbl.className+' ').indexOf("sortable") != -1) && (thisTbl.id)) {
            //initTable(thisTbl.id);
            ts_makeSortable(thisTbl);
        }
    }
}

function ts_makeSortable(table) {
    if (table.rows && table.rows.length > 0) {
        var firstRow = table.rows[0];
    }
    if (!firstRow) return;
    
    // We have a first row: assume it's the header, and make its contents clickable links
    for (var i=0;i<firstRow.cells.length;i++) {
        var cell = firstRow.cells[i];
        var txt = ts_getInnerText(cell);
        cell.innerHTML = '<a href="#" class="sortheader" '+ 
        'onclick="ts_resortTable(this, '+i+');return false;">' + 
        txt+'<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a>';
    }
}

function ts_getInnerText(el) {
	if (typeof el == "string") return el;
	if (typeof el == "undefined") { return el };
	if (el.innerText) return el.innerText;	//Not needed but it is faster
	var str = "";
	
	var cs = el.childNodes;
	var l = cs.length;
	for (var i = 0; i < l; i++) {
		switch (cs[i].nodeType) {
			case 1: //ELEMENT_NODE
				str += ts_getInnerText(cs[i]);
				break;
			case 3:	//TEXT_NODE
				str += cs[i].nodeValue;
				break;
		}
	}
	return str;
}

function ts_resortTable(lnk,clid) {
    // get the span
    var span;
    for (var ci=0;ci<lnk.childNodes.length;ci++) {
        if (lnk.childNodes[ci].tagName && lnk.childNodes[ci].tagName.toLowerCase() == 'span') span = lnk.childNodes[ci];
    }
    var spantext = ts_getInnerText(span);
    var td = lnk.parentNode;
    var column = clid || td.cellIndex;
    var table = getParent(td,'TABLE');
    
    // Work out a type for the column
    if (table.rows.length <= 1) return;
    var itm = ts_getInnerText(table.rows[1].cells[column]);
    sortfn = ts_sort_caseinsensitive;
    if (itm.match(/^\d\d[\/-]\d\d[\/-]\d\d\d\d$/)) sortfn = ts_sort_date;
    if (itm.match(/^\d\d[\/-]\d\d[\/-]\d\d$/)) sortfn = ts_sort_date;
    if (itm.match(/^[£$]/)) sortfn = ts_sort_currency;
    if (itm.match(/^[\d\.]+$/)) sortfn = ts_sort_numeric;
    SORT_COLUMN_INDEX = column;
    var firstRow = new Array();
    var newRows = new Array();
    for (i=0;i<table.rows[0].length;i++) { firstRow[i] = table.rows[0][i]; }
    for (j=1;j<table.rows.length;j++) { newRows[j-1] = table.rows[j]; }

    newRows.sort(sortfn);

    if (span.getAttribute("sortdir") == 'down') {
        ARROW = '&nbsp;&nbsp;&uarr;';
        newRows.reverse();
        span.setAttribute('sortdir','up');
    } else {
        ARROW = '&nbsp;&nbsp;&darr;';
        span.setAttribute('sortdir','down');
    }
    
    // We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
    // don't do sortbottom rows
    for (i=0;i<newRows.length;i++) { if (!newRows[i].className || (newRows[i].className && (newRows[i].className.indexOf('sortbottom') == -1))) table.tBodies[0].appendChild(newRows[i]);}
    // do sortbottom rows only
    for (i=0;i<newRows.length;i++) { if (newRows[i].className && (newRows[i].className.indexOf('sortbottom') != -1)) table.tBodies[0].appendChild(newRows[i]);}
    
    // Delete any other arrows there may be showing
    var allspans = document.getElementsByTagName("span");
    for (var ci=0;ci<allspans.length;ci++) {
        if (allspans[ci].className == 'sortarrow') {
            if (getParent(allspans[ci],"table") == getParent(lnk,"table")) { // in the same table as us?
                allspans[ci].innerHTML = '&nbsp;&nbsp;&nbsp;';
            }
        }
    }
        
    span.innerHTML = ARROW;
}

function getParent(el, pTagName) {
	if (el == null) return null;
	else if (el.nodeType == 1 && el.tagName.toLowerCase() == pTagName.toLowerCase())	// Gecko bug, supposed to be uppercase
		return el;
	else
		return getParent(el.parentNode, pTagName);
}
function ts_sort_date(a,b) {
    // y2k notes: two digit years less than 50 are treated as 20XX, greater than 50 are treated as 19XX
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
    if (aa.length == 10) {
        dt1 = aa.substr(6,4)+aa.substr(3,2)+aa.substr(0,2);
    } else {
        yr = aa.substr(6,2);
        if (parseInt(yr) < 50) { yr = '20'+yr; } else { yr = '19'+yr; }
        dt1 = yr+aa.substr(3,2)+aa.substr(0,2);
    }
    if (bb.length == 10) {
        dt2 = bb.substr(6,4)+bb.substr(3,2)+bb.substr(0,2);
    } else {
        yr = bb.substr(6,2);
        if (parseInt(yr) < 50) { yr = '20'+yr; } else { yr = '19'+yr; }
        dt2 = yr+bb.substr(3,2)+bb.substr(0,2);
    }
    if (dt1==dt2) return (a.rowIndex-b.rowIndex);
    if (dt1<dt2) return -100;
    return 100;
}

function ts_sort_currency(a,b) { 
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).replace(/[^0-9.]/g,'');
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).replace(/[^0-9.]/g,'');
    var retval = parseFloat(aa) - parseFloat(bb);
	if (retval==0) return (a.rowIndex-b.rowIndex); else return retval;
}

function ts_sort_numeric(a,b) { 
    aa = parseFloat(ts_getInnerText(a.cells[SORT_COLUMN_INDEX]));
    if (isNaN(aa)) aa = 0;
    bb = parseFloat(ts_getInnerText(b.cells[SORT_COLUMN_INDEX])); 
    if (isNaN(bb)) bb = 0;
    if (aa==bb) return (a.rowIndex-b.rowIndex); else return aa-bb;
}

function ts_sort_caseinsensitive(a,b) {
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).toLowerCase();
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).toLowerCase();
    if (aa==bb) return (a.rowIndex-b.rowIndex);
    if (aa<bb) return -100;
    return 100;
}

function ts_sort_default(a,b) {
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
    if (aa==bb) return (a.rowIndex-b.rowIndex);
    if (aa<bb) return -100;
    return 100;
}


function addEvent(elm, evType, fn, useCapture)
// addEvent and removeEvent
// cross-browser event handling for IE5+,  NS6 and Mozilla
// By Scott Andrew
{
  if (elm.addEventListener){
    elm.addEventListener(evType, fn, useCapture);
    return true;
  } else if (elm.attachEvent){
    var r = elm.attachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
} 
