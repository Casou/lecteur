<form action="login.php?message=2" method="POST">
	<input type="hidden" name="url" value="<?= Fwk::getCurrentGetUrl(); ?>" />
</form>

<script>
document.forms[0].submit();
</script>

Vous allez être redirigé vers la page de connexion d'ici quelques secondes. 
Si ce n'est pas le cas, cliquez <a href="login.php">ici</a> mais vous devrez saisir de 
nouveau l'adresse une fois connecté. 