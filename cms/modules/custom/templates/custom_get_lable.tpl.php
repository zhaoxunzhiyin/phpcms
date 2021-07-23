<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<div style="font-size:14px;line-height:25px;">
<div class="explain-col"> 
温馨提示：您可以手动或者点下面按钮把下面代码复制到模板中。
</div>
<div style="margin-top:6px;"></div>
<textarea name="lable" id="lable" rows="8" cols="75">
{pc:custom  action="content" id="<?php echo $id;?>" siteid="$siteid"}
    {loop $data $r}
    	{$r[content]}
    {/loop}
{/pc}
</textarea>

<p style="margin-top:6px;">
<input type="button" class="button" onclick="copy_clip(document.getElementById('lable').value);Dialog.alert('恭喜，复制成功！');" value="复制标签代码到剪切板"/><span style="font-size:12px;">(提示：仅支持IE内核浏览器，如果是谷歌、火狐浏览器等请手动复制代码。)</span>
</p>
</div>

<script type="text/javascript">
	function copy_clip(txt) {
        if (window.clipboardData) {
                window.clipboardData.clearData();
                window.clipboardData.setData("Text", txt);
        } else if (navigator.userAgent.indexOf("Opera") != -1) {
                window.location = txt;
        } else if (window.netscape) {
                try {
                        netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
                } catch (e) {
                        Dialog.alert("您的firefox安全限制限制您进行剪贴板操作，请在新窗口的地址栏里输入'about:config'然后找到'signed.applets.codebase_principal_support'设置为true'");
                        return false;
                }
                var clip = Components.classes["@mozilla.org/widget/clipboard;1"].createInstance(Components.interfaces.nsIClipboard);
                if (!clip)
                        return;
                var trans = Components.classes["@mozilla.org/widget/transferable;1"].createInstance(Components.interfaces.nsITransferable);
                if (!trans)
                        return;
                trans.addDataFlavor('text/unicode');
                var str = new Object();
                var len = new Object();
                var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
                var copytext = txt;
                str.data = copytext;
                trans.setTransferData("text/unicode", str, copytext.length * 2);
                var clipid = Components.interfaces.nsIClipboard;
                if (!clip)
                        return false;
                clip.setData(trans, null, clipid.kGlobalClipboard);
        }
	}
	</script>

	
</div>
</body>
</html> 