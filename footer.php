		</main>
		<footer>
			<div class="row">
				<div class="col-xs-6 copy">
					<p>&copy; <?=SITE_NAME?> 2010 - <?=date('Y')?></p>
					<p>This site is not affiliated with any of the brands featured.</p>
				</div>
				<div class="col-xs-6">
					<ul class="list-unstyled">
						<li><a href="about">About</a></li>
						<li><a href="contact">Contact</a></li>
					</ul>
				</div>
			</div>
		</footer>
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');ga('create','UA-89612390-1','auto');ga('send','pageview');
		</script>
		<?php $app->get_foot_js();?>
	</body>
</html>