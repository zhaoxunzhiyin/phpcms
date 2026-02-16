/**
 * 插入音乐命令
 * @file
 */
UE.plugin.register('music', function (){
    var me = this;
    function creatInsertStr(url,width,height,align,toEmbed){
        return  !toEmbed ?
                (align=='center' ? '<p style="text-align: ' + align + '">' : '') + '<img ' +
                    (align || width || height ? 'style="'+ (align && align!='center' ? 'float:' + align + ';' : '') + (width ? 'width:' + width + 'px;' : '') + (height ? 'height:' + height + 'px;' : '') +
                '"' : '') +
                    ' _url="'+url+'" class="edui-faked-music"' +
                    ' src="'+me.options.langPath+me.options.lang+'/images/music.png" />' + (align=='center' ? '</p>' : '')
            :
            (align=='center' ? '<p style="text-align: ' + align + '">' : '') + '<audio class="edui-faked-music"' +
                ' src="' + url + '" ' +
                    (align || width || height ? 'style="'+ (align && align!='center' ? 'float:' + align + ';' : '') + (width ? 'width:' + width + 'px;' : '') + (height ? 'height:' + height + 'px;' : '') +
                '"' : '') +
                    ' controls=""></audio>' + (align=='center' ? '</p>' : '');
    }
    return {
        outputRule: function(root){
            utils.each(root.getNodesByTagName('img'),function(node){
                var html;
                if(node.getAttr('class') == 'edui-faked-music'){
                    html =  creatInsertStr(node.getAttr("_url"), node.getStyle('width').replace('px', ''), node.getStyle('height').replace('px', ''), node.getStyle('float'), true);
                    var audio = UE.uNode.createElement(html);
                    node.parentNode.replaceChild(audio,node);
                }
            })
        },
        inputRule:function(root){
            utils.each(root.getNodesByTagName('audio'),function(node){
                if(node.getAttr('class') == 'edui-faked-music'){
                    html =  creatInsertStr(node.getAttr("src"), node.getStyle('width').replace('px', ''), node.getStyle('height').replace('px', ''), node.getStyle('float'),false);
                    var img = UE.uNode.createElement(html);
                    node.parentNode.replaceChild(img,node);
                }
            })

        },
        commands:{
            /**
             * 插入音乐
             * @command music
             * @method execCommand
             * @param { Object } musicOptions 插入音乐的参数项， 支持的key有： url=>音乐地址；
             * width=>音乐容器宽度；height=>音乐容器高度；align=>音乐文件的对齐方式， 可选值有: left, center, right, none
             * @example
             * ```javascript
             * //editor是编辑器实例
             * //在编辑器里插入一个“植物大战僵尸”的APP
             * editor.execCommand( 'music' , {
             *     align: "center",
             *     url: "音乐地址"
             * } );
             * ```
             */
            'music':{
                execCommand:function (cmd, musicObj) {
                    musicObj = utils.isArray(musicObj)?musicObj:[musicObj];

                    if(me.fireEvent('beforeinsertmusic', musicObj) === true){
                        return;
                    }

                    var html = [];
                    for(var i=0,vi,len = musicObj.length;i<len;i++){
                        vi = musicObj[i];
                        html.push(creatInsertStr(vi.url, vi.width || 400,  vi.height || 95, vi.align, false));
                    }
                    me.execCommand("inserthtml",html.join(""),true);

                    me.fireEvent('afterinsertmusic', musicObj);
                },
                queryCommandState:function () {
                    var me = this,
                        img = me.selection.getRange().getClosedNode(),
                        flag = img && (img.className == "edui-faked-music");
                    return flag ? 1 : 0;
                }
            }
        }
    }
});