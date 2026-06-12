<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, maximum-scale=1.0, initial-scale=1.0, user-scalable=no">
<title><?php echo L('preview')?></title>
</head>
<body>
<div style="text-align:center;"><?php if($r['type']=='code') { echo $data; } else {?><script language='javascript' src='<?php echo $path;?>'></script><?php }?></div>
</body>
</html>