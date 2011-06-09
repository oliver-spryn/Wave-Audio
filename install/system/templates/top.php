<!DOCTYPE html>
<html>
<head>
<title><?php echo $name; ?> | <?php echo $title; ?></title>
<link rel="stylesheet" type="text/css" href="system/templates/stylesheets/style.css" />
<?php echo $headers; ?>
<script type="text/javascript">
	$(document).ready(function() {
		$(':text, :password, textarea').funtip();
	});
</script>
</head>
<body>
  <h1 class="header"><?php echo $name; ?></h1>
  <div class="content">
    <h2><?php echo $title; ?></h2>
