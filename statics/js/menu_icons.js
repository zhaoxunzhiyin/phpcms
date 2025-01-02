jQuery(document).ready(function() {
    $.get(pc_file+"statics/css/font-awesome/css/font.min.css",function(data){
        var iconHtml = '';
        for(var i=1;i<data.split(".fa-").length;i++){
                iconHtml += "<li class='col-lg-1 col-sm-2 col-xs-4";
                if ("fa fa-" + data.split(".fa-")[i].split(":before")[0] == icon) {iconHtml += " active";}
                iconHtml += "'>"+
                            "<i class='fa fa-" + data.split(".fa-")[i].split(":before")[0] + "'></i>" +
                            "fa-" + data.split('.fa-')[i].split(':before')[0] +
                        "</li>";
        }
        $(".icons").html(iconHtml);
    })

    $("body").on("click",".icons li",function(){
        dialogOpener.$S('menu_icon').value = 'fa '+$(this).text();
        ownerDialog.close();
    })
})