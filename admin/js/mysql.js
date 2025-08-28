$.fn.serializeObject = function()
{
   var o = {};
   var a = this.serializeArray();
   $.each(a, function() {
       if (o[this.name]) {
           if (!o[this.name].push) {
               o[this.name] = [o[this.name]];
           }
           o[this.name].push(this.value || '');
       } else {
           o[this.name] = this.value || '';
       }
   });
   return o;
};		



function do_sql(arr,action,Sfunction,Ffunction){


	$.ajax({
            url: action,
            data: arr,
            type:"POST",
            dataType:'html',

            success: function(msg){
              Sfunction(msg);

            },

             error:function(xhr, ajaxOptions, thrownError){ 
                alert(xhr.status); 
                alert(thrownError); 
             }
        });
        
}




function alert_box(custom,msg){

  style=["style-msg"];

  switch(custom){
    case "green":
      style.push("successmsg")
      break;
     
    case "red":
      style.push("errormsg")
      break;

    case "blue":
      style.push("infomsg")
      break;

    case "yellow":
      style.push("alertmsg")
      break;
    case "black":
      style.push("style-msg-light")
      break; 
    default:
      style.push("style-msg-light")    
  }

  classtag="class=\""+style.join(" ")+"\"";


  html='<div '+classtag+'>'+
       '<div class="sb-msg">'+msg+'</div>'+
       '</div>';

  return html;

}


$("#subscribe-form").submit(function(){

    

    $(this).find(".style-msg").remove();

    myform=$(this).serializeObject();
  

    Sfunction=function (i){ 
      switch(i) {
          case "S":
              window.location = "login.php";
              break;
          case "F":
              $("#subscribe-form").prepend(alert_box("red","寫入失敗，可能是資料庫連線有問題!"));
              break;
          default:
              $("#subscribe-form").prepend(alert_box("red","不明原因造成錯誤!"));
      }        

    }

    do_sql(myform,'sqlfunction/insert.php',Sfunction);

    return false;
})