window.addEvent("domready", function() {
    var fx=new Fx.Style($("consoleContainer"), "background-color", {duration:2000});
    $("ajaxLink").addEvent("click", function() {
        $("fieldsContainer").empty().addClass("ajax-loading").setHTML("<img src='"+assetsBase+"/images/ajax-loader.gif' border='0'> "+ _LOADING_ );
        var url="index.php?option=_ECR_COM_COM_NAME_&task=getrandom&format=raw&justforrandom="+Math.floor(Math.random()*99999);
        var a=new Ajax(url,{
            method:"get",
            onComplete: function(response) {
                var resp=Json.evaluate(response);
                $("fieldsContainer").removeClass("ajax-loading").setHTML(resp.html);
                $("consoleContainer").setHTML(resp.msg);
                fx.set("#fff").start("#f60").chain(function() {
                    this.start.delay(2000,this,"#FFF");
                });
            }
        }).request();
    });
});
