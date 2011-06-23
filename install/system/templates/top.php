<!DOCTYPE html>
<html<?php echo $lang; ?>>
<head>
<title><?php echo $name; ?> | <?php echo $title; ?></title>
<?php echo $headers; ?>
<link rel="stylesheet" href="system/templates/stylesheets/style.min.css" />
<script>
	$(document).ready(function() {
		$(':text, :password, textarea, input[type=email]').funtip();
	});
</script>
</head>
<body>
<header>
<h1><?php echo $name; ?></h1>
</header>

<article class="content">
<header>
<h2><?php echo $title; ?></h2>
</header>

