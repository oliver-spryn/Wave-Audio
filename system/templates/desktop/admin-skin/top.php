<!DOCTYPE html>
<html<?php echo $lang; ?>>
<head>
<title><?php echo $siteName; ?> | <?php echo $title; ?></title>
<?php echo $headers; ?>
<link rel="stylesheet" href="<?php echo $templateRoot;?>stylesheets/style.css">
<script src="<?php echo $templateRoot; ?>javascripts/navigation.jquery.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo ROOT; ?>system/stylesheets/universal.css">
</head>
<body>
<header>
<hgroup id="page-top">
<h1 id="name"><?php echo $siteName; ?></h1>
<h2 id="slogan"><?php echo $slogan; ?></h2>
</hgroup>

<nav class="nav">
<ul class="select">
<li><a href="#nogo"><strong>Dashboard</strong></a></li>
<li class="current"><a href="#nogo"><strong>Products</strong></a></li>
</ul>

<ul class="breadcrumb">
<li class="intro"><strong>You are here:</strong></li>
<li><a href="#nogo"><strong>Dashboard</strong></a></li>
<li class="current"><strong>Products</strong></li>
</ul>
</nav>
</header>

<article id="content-outer">
<header id="page-heading">
<h1><?php echo $title; ?></h1>
</header>

<ul class="toolbar">
<li><a href="#nogo" class="new">Create Page</a></li>
<li><a href="#nogo" class="settings">Page Settings</a></li>
<li><a href="#nogo" class="search">Search for Content</a></li>
<li><a href="#nogo" class="back">Back</a></li>
</ul>

<table id="content-table">
<tbody>
<tr>
<th rowspan="3" class="sized"><img src="<?php echo $templateRoot;?>images/shared/side_shadowleft.jpg" alt="" height="300" width="20"></th>
<th class="topleft"></th>
<td id="tbl-border-top">&nbsp;</td>
<th class="topright"></th>
<th rowspan="3" class="sized"><img src="<?php echo $templateRoot;?>images/shared/side_shadowright.jpg" alt="" height="300" width="20"></th>
</tr>

<tr>
<td id="tbl-border-left"></td>
<td>
<div id="content-table-inner">

								<table id="dataTable">
									<tr>
										<th></th>
										<th><a class="sort">Last Name</a></th>
										<th>First Name</th>
										<th>Email</th>
										<th>Due</th>
										<th>Website</th>
										<th></th>
									</tr>
									<tr class="odd">
										<td><a class="dragger"></a><a class="eyeShow"></a></td>
										<td>Sabev</td>
										<td>George</td>
										<td><a href="">george@mainevent.co.za</a>
										</td>
										<td>R250</td>
										<td><a href="">www.mainevent.co.za</a>
										</td>
										<td><a class="info"></a><a class="edit"></a><a class="delete"></a></td>
									</tr>
									<tr class="even">
										<td></td>
										<td>Sabev</td>
										<td>George</td>
										<td><a href="">george@mainevent.co.za</a>
										</td>
										<td>R250</td>
										<td><a href="">www.mainevent.co.za</a>
										</td>
										<td><a class="info"></a><a class="edit"></a><a class="delete"></a></td>
									</tr>
									<tr class="odd">
										<td></td>
										<td>Sabev</td>
										<td>George</td>
										<td><a href="">george@mainevent.co.za</a>
										</td>
										<td>R250</td>
										<td><a href="">www.mainevent.co.za</a>
										</td>
										<td><a class="info"></a><a class="edit"></a><a class="delete"></a></td>
									</tr>
									<tr class="even">
										<td></td>
										<td>Sabev</td>
										<td>George</td>
										<td><a href="">george@mainevent.co.za</a>
										</td>
										<td>R250</td>
										<td><a href="">www.mainevent.co.za</a>
										</td>
										<td><a class="info"></a><a class="edit"></a><a class="delete"></a></td>
									</tr>
									<tr class="odd">
										<td></td>
										<td>Sabev</td>
										<td>George</td>
										<td><a href="">george@mainevent.co.za</a>
										</td>
										<td>R250</td>
										<td><a href="">www.mainevent.co.za</a>
										</td>
										<td><a class="info"></a><a class="edit"></a><a class="delete"></a></td>
									</tr>
									<tr class="even">
										<td></td>
										<td>Sabev</td>
										<td>George</td>
										<td><a href="">george@mainevent.co.za</a>
										</td>
										<td>R250</td>
										<td><a href="">www.mainevent.co.za</a>
										</td>
										<td><a class="info"></a><a class="edit"></a><a class="delete"></a></td>
									</tr>
								</table>
							</div>
						</td>
						<td id="tbl-border-right"></td>
					</tr>

					<tr>
						<th class="sized bottomleft"></th>
						<td id="tbl-border-bottom">&nbsp;</td>
						<th class="sized bottomright"></th>
					</tr>
				</tbody>
			</table>
</article>

		

	<div id="footer">
		<div id="footer-left">Admin Skin © Copyright Internet Dreams
			Ltd. All rights reserved.</div>
	</div>
</body>
</html>