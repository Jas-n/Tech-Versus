<?php $app_require=array(
	'form.settings'
);
require('../init.php');
$settings=new settings('name=settings&class=form-horizontal');
$settings->process();
require('header.php');?>
<h1>Settings</h1>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="../">Home</a></li>
	<li class="breadcrumb-item"><a href="./">Dashboard</a></li>
	<li class="breadcrumb-item active">Settings</li>
</ol>
<?php $app->get_messages();
$settings->get_form();
require('footer.php');