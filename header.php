<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<?php $app->get_css();
		$app->get_head_js();
		$app->get_icons();?>
		<title><?=$app->page_title()?></title>
    </head>
    <body id="<?=basename($_SERVER['PHP_SELF'],'.php')?>">
		<header class="cd-auto-hide-header">
			<a class="logo" href="/"><?=SITE_NAME?></a>
			<nav class="cd-primary-nav">
				<a href="#cd-navigation" class="nav-trigger">
					<span>
						<em aria-hidden="true"></em>
						Menu
					</span>
				</a> <!-- .nav-trigger -->
				<ul id="cd-navigation">
					<?php if(is_file(ROOT.'categories.html')){
						include(ROOT.'categories.html');
					} ?>
					<li><a href="/news">News</a></li>
					<?php if(!is_logged_in()){ ?>
						<li><a href="/login">Login</a></li>
					<?php }else{ ?>
						<li>
							<a class="text-truncate"><?=$user->username?></a>
							<ul>
								<li><a href="/users">Account</a></li>
								<li><a href="/u/<?=$user->username?>">Profile</a></li>
								<li><a href="/logout">Logout</a></li>
							</ul>
						</li>
					<?php } ?>
				</ul>
			</nav> <!-- .cd-primary-nav -->
		</header>
		<?php if($hero){ ?>
			<section class="cd-hero" style="background-image:url(<?=$hero['image']?>);">
				<div class="cd-hero-content">
					<?=$hero['content']?>
				</div>
			</section>
		<?php }
		if($page_nav){ ?>
			<nav class="cd-secondary-nav">
				<ul>
					<?php foreach($page_nav as $i=>$nav_item){ ?>
						<li><a<?=$i==0?' class="active"':''?> href="<?=$nav_item['link']?>" title="<?=$nav_item['name']?>"><?=$nav_item['name']?><?=$nav_item['count']?' <span class="badge badge-primary">'.$nav_item['count'].'</span>':''?></a></li>
					<?php } ?>
				</ul>
			</nav>
		<?php }?>
		<main class="cd-main-content<?=($hero?' sub-nav-hero':'').(basename($_SERVER['PHP_SELF'],'.php')!='index'?' container':'')?>" <?=$spy?' data-spy="scroll"':''?>>