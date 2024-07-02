jQuery(document).ready(function() {
    $.get(pc_file+"statics/css/font-awesome/css/font.min.css",function(data){
        var iconHtml = '';
        for(var i=1;i<data.split(".fa-").length;i++){
            iconHtml += "<li class='col-lg-1 col-sm-2 col-xs-4'>"+
                            "<i class='fa fa-" + data.split(".fa-")[i].split(":before")[0] + "'></i>" +
                            "fa-" + data.split('.fa-')[i].split(':before')[0] +
                        "</li>";
        }
        $(".icons").html(iconHtml);
        $(".iconslength").text(data.split(".fa-").length-1);
    })

    $("body").on("click",".icons li",function(){
        var copyText = document.getElementById("copyText");
        copyText.innerText = 'fa '+$(this).text();
        copyText.select();
        document.execCommand("copy");
        dr_tips(1, '复制成功');
    })
})