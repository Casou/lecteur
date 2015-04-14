<?php

$pathToPhpRoot = './';
$pathToHtmlRoot = "./";
include_once $pathToPhpRoot."includes.php";
Logger::init($pathToPhpRoot);

$users = MetierUser::getAllUser();


?>
<div class="ui-widget ui-corner-all block">
	<div class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all blockHeader">
		Se connecter à un profil différent
	</div>
	<div class="blockContent">
		<ul>
			<?php foreach($users as $user) { ?>
				<li>
					<a href="#" onClick="logAsUrl(<?= $user->id . ", '$pathToHtmlRoot'" ?>); return false;">
						<?= $user->login ?>
					</a>
				</li>
			<?php }?>
		</ul>
	</div>
</div>



<script>


</script>


<?php 
Logger::close();
Database::disconnectDB();
?>