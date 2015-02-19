<?php 
$pathToPhpRoot = "..";
include_once "$pathToPhpRoot/includes.php";

?>
<div id="playerDialog" data-role="dialog" title="Visualisation" style="text-align : center">

	<div data-role="header">
    	<h1>Visualisation</h1>
	</div>
	
	<div data-role="content">
		<?php 
		if(!isset($_GET['id'])) {
			echo "Pas d'identifiant !!!</div></div>";
			exit;
		}
		
		$id = $_GET['id'];
		$video = MetierVideo::getVideoById($id);
		$passes = MetierPasse::getPasseByVideo($id);
		
		?>
		<h1><?= $video->nom_affiche ?></h1>
	
    	<video id="player" title="Prévisualisation" width="90%" controls>
    		<source src="<?= $pathToPhpRoot."/".changeBackToSlash(PATH_CONVERTED_FILE)."/".$video->nom_video ?>"></source>
		</video>
		
		<div id="playerPasses">
			<table>
				<?php foreach ($passes as $passe) { ?>
				<tr>
					<td>
						<?= $NIVEAUX[$passe->niveau] ?> -
						<?= $passe->nom ?>  
					</td>
					<td class="playerPasses_timer">
					<?php if($passe->timer_debut != null) { ?>
						[
						<a class="playerGoto" href="#" onClick="playerGoto('<?= $passe->timer_debut ?>'); return false;"><?= $passe->timer_debut ?></a>
						-<a class="playerGoto" href="#" onClick="playerGoto('<?= $passe->timer_fin ?>'); return false;"><?= $passe->timer_fin ?></a>
						]
					<?php } ?>  
					</td>
				</tr>
				<?php } ?>
			</table>
		</div>
		
		<script type="text/javascript">
			function playerGoto(time) {
				$t = toSeconds(time);
				document.getElementById("player").currentTime = $t;
			}
			function toSeconds(t) {
			    var s = 0.0
			    if(t) {
			      var p = t.split(':');
			      for(i=0;i<p.length;i++)
			        s = s * 60 + parseFloat(p[i].replace(',', '.'))
			    }
			    return s;
			  }
		</script>
	</div>
</div>
