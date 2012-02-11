<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- <meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/> -->
<title><?php echo $site_name; ?></title>
<?php
  echo html::stylesheet(url::file_loc('css').'themes/default/css/style','',true);
  echo html::stylesheet(url::file_loc('css').'plugins/mobile/views/css/styles','',true);
  echo html::stylesheet(url::file_loc('css').'media/css/openlayers','',true);
  echo html::script(url::file_loc('js').'media/js/OpenLayers', true);
  echo html::script(url::file_loc('js').'media/js/OpenStreetMap', true);
  echo "<script type=\"text/javascript\">
    OpenLayers.ImgPath = '".url::file_loc('img').'media/img/openlayers/'."';
    </script>";
	echo html::script(url::file_loc('js').'media/js/jquery', true);
?>
<script type="text/javascript"> <?php echo $js ?> </script>
</head>
<body>
  <div id="page">
  <div id="block">
    <div id="map_canvas" class="smallMap"> </div>
  </div>
  </div>
</body>
</html>
