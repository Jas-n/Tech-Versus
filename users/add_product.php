<?php $app_require=array(
	'form.add_product',
	'php.products'
);
require('../init.php');
$add_product=new add_product();
$add_product->process();
require('header.php');?>
<a class="btn btn-success pull-right" href="add_product">Add</a>
<h1>Add Product</h1>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="../">Home</a></li>
	<li class="breadcrumb-item"><a href="./">Dashboard</a></li>
	<li class="breadcrumb-item"><a href="products">Products</a></li>
	<li class="breadcrumb-item active">Add</li>
</ol>
<?php $app->get_messages();
$add_product->get_form();
require('footer.php');