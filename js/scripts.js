/*
 * @revised: Apr 01, 2011
 */
function changePage(pageNo) {
    setQryStr('pageNo', pageNo);
}
function setQryStr(varName, varValue, remove) {
    if (typeof(remove) == "undefined") var remove = {};
    var param = new Object();
    var qrystr = location.search.substr(1).split("&");
    for (var t=0; t < qrystr.length; ++t) {
        var arr = qrystr[t].split("=");
        try { param[arr[0]] = arr[1] } catch (e) { }
        //alert(arr[0])
    }
    //alert("1: "+param[varName]);
    if (varName == "sortby" && typeof(param[varName]) != "undefined" && param[varName].indexOf(varValue) == 0) {
        param[varName] = (param[varName] == varValue) ? varValue + "%20DESC" : varValue;
    } else {
        param[varName] = varValue;
    }
    //alert("2: "+param[varName]);
    var arr = new Array();
    for (var key in param) {
        //alert(key)
        if (varName != "delete" && key == "delete") key = "";
        //if (key != "" && param[key] != "" && typeof(remove[key]) == "undefined") arr.push(key + "=" + param[key]);
        if (key != "" && typeof(remove[key]) == "undefined") arr.push(key + "=" + param[key]);
    }
    document.location.href = "?" + arr.join("&");
}
// onMouseOver=\"overItem('{$row['UID']}',1)\" onMouseOut=\"overItem('{$row['UID']}',0)\" onClick=\"alert('{$row['UID']}')\"
function initEditTbl() {
    $("tr.itemRow").each(function() {
        var status = parseInt($(this).attr("status"),10);
        if (status!=1) {
            $(this).addClass("hand");
            $(this).hover(
                function () {
                    $(this).addClass("itemOver");
                }, 
                function () {
                    $(this).removeClass("itemOver");
                }
            );
        }
    });
    $("tr.itemRow").click(function() {

        var id = $(this).attr("rel"),
            qs = $("#QUERY_STRING").val(),
            status = parseInt($(this).attr("status"),10);
        var clearId= id.replace("R","");
            
            if(id!="R"+clearId){
                if (status!=1) {
                    document.location.href = 'submit.php?EDIT='+id+"&qs="+qs;
                }
            }
            else{
                $(this).attr("rel",clearId); 
            }

    });
            
    
}
function editGoBack() {
    var qs = $("#QS").val();
    document.location.href = "report.php?"+qs;
}
function frmClear() {
    $("#FROM,#TO,#RES_ID,#orderID,#GUEST_NAME").val("");
    $("#DATE_FIELD")[0].selectedIndex=0;
    $("#STATUS")[0].selectedIndex=0;
    $("#publisher_name")[0].selectedIndex=0;
}
function GetCookieVal (offset) {
		var endstr = document.cookie.indexOf (";", offset);
		if (endstr == -1)
			endstr = document.cookie.length;
		return unescape(document.cookie.substring(offset, endstr));
}

function GetCookie(name) {
		var arg = name + "=";
		var alen = arg.length;
		var clen = document.cookie.length;
		var i = 0;
		while (i < clen) {
			var j = i + alen;
			if (document.cookie.substring(i, j) == arg)
				return GetCookieVal (j);
			i = document.cookie.indexOf(" ", i) + 1;
			if (i == 0) break; 
		  }
		return "";
}
function sortBy(field) {
    var sortBy = GetCookie("sortBy"),
        sortOrd = parseInt(GetCookie("sortOrd"),10);
        sortOrd = (sortOrd==0 || isNaN(sortOrd)) ? 1 : sortOrd;
    document.cookie = "sortBy="+field+"; path=/;";
    if (sortBy == field) {
        sortOrd *= -1;
    } else {
        sortOrd = 1;
    }
    document.cookie = "sortOrd="+sortOrd+"; path=/;";
    window.location.reload();
}
