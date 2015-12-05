<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="<?php print base_url('static/foundation/bower_components/foundation-sites/dist/foundation.min.css'); ?>"/>
<link rel="stylesheet" href="<?php print base_url('static/foundation/css/app.css?') . time(); ?>"/>
<body>
	<div class="row">
		<div class="small-12 center columns">
			<h1>Jaguarundi</h1>
			<?php if(isset($index)) : ?>
				<h2>Database Initialization</h2>
			<?php elseif(isset($new_search)) : ?>
				<h2>Search</h2>
			<?php else : ?>
				<h2>Results</h2>
			<?php endif; ?>
		</div>
	</div>
	<?php if(isset($index)) : ?>
		<div class="row">
			<div class="small-4 small-offset-4 columns">
				<form action="<?php print base_url('home/connect'); ?>" method="POST">
					<div class="row">
						<div class="small-12 columns">
							<label>Hostname: <input type="text" name="hostname"/></label>
						</div>
					</div>
					<div class="row">
						<div class="small-12 columns">
							<label>Port: <input type="text" name="port"/></label>
						</div>
					</div>
					<div class="row">
						<div class="small-12 columns">
							<label>Username: <input type="text" name="username"/></label>
						</div>
					</div>
					<div class="row">
						<div class="small-12 columns">
							<label>Password: <input type="text" name="password"/></label>
						</div>
					</div>
					<div class="row">
						<div class="small-12 columns">
							<label>Database: <input type="text" name="database"/></label>
						</div>
					</div>
					<div class="row">
						<div class="small-12 columns center">
							<button type="submit" class="button">Submit</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	<?php elseif(isset($new_search)) : ?>
		<div class="row">
			<div class="small-4 small-offset-4 columns">
				<form action="<?php print base_url('home/search'); ?>" method="POST">
					<div class="row">
						<div class="small-12 columns">
							<label>Table: <input type="text" name="table"/></label>
						</div>
					</div>
					<div class="row">
						<div class="small-12 columns">
							<label>Query: <input type="text" name="word"/></label>
						</div>
					</div>
					<div class="row">
						<div class="small-12 columns center">
							<button type="submit" class="button">Search</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	<?php else : ?>
		<div class="row">
			<div class="small-4 small-offset-4 columns">
				<div class="row">
					<div class="small-12 columns">
						<?php
							function print_r2($value)
							{
								echo '<pre>';
								print_r($value);
								echo '</pre>';
							}
							print_r2($results);
						?>
					</div>
				</div>
				<div class="row">
					<div class="small-12 columns">
						<a href="<?php print base_url('home/new_search'); ?>"><br/>New Search</a>
					</div>
				</div>
				<div class="row">
					<div class="small-12 columns">
						<a href="<?php print base_url('home'); ?>"><br/>Connect to a New Database</a>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<script src="<?php print base_url('static/foundation/bower_components/jquery/dist/jquery.min.js'); ?>"></script>
	<script src="<?php print base_url('static/foundation/bower_components/foundation-sites/dist/foundation.min.js'); ?>"></script>
	<script>
		$(document).foundation();
	</script>
</body>
</html>