<?php $app_require[]='php.news';
require('../init.php');
require('header.php');
$articles=$news->get_articles();?>
<h1>News</h1>
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="../">Home</a></li>
	<li class="breadcrumb-item"><a href="./">Dashboard</a></li>
	<li class="breadcrumb-item active">News</li>
</ol>
<table class="table table-hover table-sm table-striped">
	<thead>
		<tr>
			<th>Title</th>
			<th>Product</th>
			<th>Category</th>
			<th>Status</th>
			<th>Published</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php if($articles['count']){
			foreach($articles['data'] as $article){?>
				<tr>
					<td><?=$article['title']?></td>
					<td><?=$article['product']?></td>
					<td><?=$article['category']?></td>
					<td><?=$article['status']?></td>
					<td><?=sql_datetime($article['published'])?></td>
					<td><a class="btn btn-sm btn-primary" href="article/<?=$article['id']?>">View</a></td>
				</tr>
			<?php }
		}else{?>
			<tr class="danger"><td colspan="6">No news articles found</td></tr>
		<?php }?>
	</tbody>
</table>
<?php pagination($logs['count']);
require('footer.php');