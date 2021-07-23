layui.use(['form','layer','jquery'],function(){
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer,
        element = layui.element;
        $ = layui.jquery;

    $.get(pc_file+"statics/css/font-awesome/css/font.min.css",function(data){
        var iconHtml = '';
        for(var i=1;i<data.split(".fa-").length;i++){
            iconHtml += "<li class='layui-col-xs4 layui-col-sm3 layui-col-md2 layui-col-lg1'>"+
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