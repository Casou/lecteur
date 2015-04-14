<?php

class MetierUser {
	
	public static function getAllUser() {
		return Database::getResultsObjects("select * from ".User::getTableName()." order by login asc", "User");
	}
	
	public static function getAllUserDTO() {
		$users = MetierUser::getAllUser();
		$usersDTO = array();
		foreach($users as $user) {
			$usersDTO[] = MetierUser::getUserDTO($user);
		}
		
		return $usersDTO;
	}
	
	private static function getUserDTO($user) {
		$dto = new UserDTO();
		$dto->user = $user;
		$dto->droits = MetierDroit::getDroitsByUser($user->id);
		foreach($dto->droits as $droit) {
			$dto->droitsNom[] = $droit->nom;
		}
		$dto->profils = MetierProfil::getProfilByUser($user->id);
		$dto->logConnexion = MetierLog::getAllConnexionForLogin($user->login);
		return $dto;
	}
	
	public static function getAllAdminUser() {
		return Database::getResultsObjects(
			"select * from ".User::getTableName()." u ".
			"inner join ".User::getJoinProfilTableName()." up on u.id = up.id_user ".
			"inner join ".Profil::getTableName()." p on p.id = up.id_profil ".
			"where p.is_admin = 1 ".
			"order by login asc", 
			"User");
	}
	
	public static function isUserAdmin($id_user) {
		$results = Database::getResultsObjects(
			"select * from ".User::getTableName()." u ".
			"inner join ".User::getJoinProfilTableName()." up on u.id = up.id_user ".
			"inner join ".Profil::getTableName()." p on p.id = up.id_profil ".
			"where p.is_admin = 1 and u.id = $id_user ".
			"order by login asc", 
			"User");
		
		return count($results) > 0;
	}
	
	public static function getUserById($id) {
		$sql = "select * from ".User::getTableName()." where id = $id";
		$results = Database::getResultsObjects($sql, "User");
		if (count($results) == 0) {
			return null;
		}
		$user = $results[0];
		
		$dto = new UserDTO();
		$dto->user = $user;
		$dto->droits = MetierDroit::getDroitsByUser($user->id);
		foreach($dto->droits as $droit) {
			$dto->droitsNom[] = $droit->nom;
		}
		
		$dto->profils = MetierProfil::getProfilByUser($user->id);
		
		return $dto;
	}
	
	public static function getUserByLogin($login) {
		$sql = "select * from ".User::getTableName()." where login = '".escapeString($login)."'";
		$results = Database::getResultsObjects($sql, "User");
		if (count($results) == 0) {
			return null;
		}
		return $results[0];
	}
	
	public static function getUserAllowedForVideo($ids_video, $buildObjects = false) {
		$ids = "";
		foreach($ids_video as $id) {
			if ($ids != "") {
				$ids .= ', ';
			}
			$ids .= $id;
		}
		$sql = "select id_user, count(*) as compte from ".Video::getJoinAllowedManualTableName().
			" where id_video in ($ids) ".
			" group by id_user";
		$results = Database::getResults($sql);
		$ids_user = array();
		foreach($results as $result) {
			// On ne renvoie que les utilisateurs qui dont toutes les vidéos sont associées
			if ($result['compte'] == count($ids_video)) {
				if (!$buildObjects) {
					$ids_user[] = $result['id_user'];
				} else {
					$ids_user[] = MetierUser::getUserById($result['id_user']);
				}
			}
		}
		
		return $ids_user;
	}
	
	public static function saveUserAllowedForVideo($ids_video, $users, $delete_existing = true) {
		Database::beginTransaction();
		
		$in_clause_video = "";
		foreach($ids_video as $id_video) {
			if ($in_clause_video != "") {
				$in_clause_video .= ", ";
			}
			$in_clause_video .= $id_video;
		}
		
		if ($delete_existing) {
			// On supprime les droits des utilisateurs concernés (remis par la suite si besoin)
			$users_allowed = MetierUser::getUserAllowedForVideo($ids_video);
			if ($users_allowed != null) {
				$in_clause_user = "";
				foreach($users_allowed as $user_allowed) {
					if ($in_clause_user != "") {
						$in_clause_user .= ", ";
					}
					$in_clause_user .= $user_allowed;
				}
				
				$sql = "DELETE FROM ".Video::getJoinAllowedManualTableName()." where id_video in ($in_clause_video) AND id_user in ($in_clause_user)";
				Database::executeUpdate($sql);
			}
		}
		
		if ($users != null) {
			$in_clause_user = "";
			foreach($users as $user) {
				if ($in_clause_user != "") {
					$in_clause_user .= ", ";
				}
				$in_clause_user .= $user;
			}
			
			foreach($users as $user) {
				foreach($ids_video as $id_video) {
					$sql = "DELETE FROM ".Video::getJoinAllowedManualTableName()." where id_video = $id_video and id_user = $user";
					Database::executeUpdate($sql);
					
					$sql = "INSERT INTO ".Video::getJoinAllowedManualTableName()." (id_video, id_user) values ($id_video, $user)";
					Database::executeUpdate($sql);
				}
			}
		}
		
		Database::commit();
	}
	
	
	
	
	
	public static function login($login, $password) {
		$user = MetierUser::getUserByLogin($login);
		if ($user == null) {
			Logger::warn("[KO] Tentative de login : login inconnu $login (".Fwk::getIp().")");
			return null;
		}
		
		if ($user->password != $password) {
			Logger::warn("[KO] Tentative de login : mauvais password $login / $password (".Fwk::getIp().")");
			return null;
		}
		
		$_SESSION["user"] = $_POST["login"];
		$_SESSION["userId"] = $user->id;
		$_SESSION["userLogged"] = $user->id;
		
		if ($user->log_level == null || $user->log_level == "") {
			$_SESSION["log_level"] = $user->log_level;
		} else {
			$_SESSION["log_level"] = FwkParameter::getParameter(PARAM_CONTEXT_LOG, PARAM_ID_LOG_DEFAULT_LEVEL);
		}
		
		$user = MetierUser::getUserDTO($user);
		foreach($user->droits as $droit) {
			$_SESSION[$droit->nom] = $droit->label;
		}
		
		MetierCritere::calculateAllowedVideos();
		
		Logger::info("Utilisateur loggé : $login (".Fwk::getIp().")");
		return $user;
	}
	
	
	public static function logAs($id) {
		$user = MetierUser::getUserById($id);
		if ($user == null) {
			Logger::error("[KO] Tentative de login : id inconnu $id (".Fwk::getIp().")");
			throw new Exception("[KO] Tentative de login : login inconnu $id", 400);
		}
		
		$droits = MetierDroit::getAllDroit();
		foreach ($droits as $droit) {
			if (isset($_SESSION[$droit->nom])) {
				unset($_SESSION[$droit->nom]);
			}
		}
		
		$_SESSION["user"] = $user->user->login;
		$_SESSION["userId"] = $user->user->id;
		foreach($user->droits as $droit) {
			$_SESSION[$droit->nom] = $droit->label;
		}
		
		MetierCritere::calculateAllowedVideos();
		
		Logger::info("Utilisateur loggé en tant que utilisateur $id (".Fwk::getIp().")");
	}
	

	
	
	public static function saveUser($formulaire) {
		parse_str($formulaire);
		
		Database::beginTransaction();
		
		$log_level_sql = ($log_level == '') ? "null" : "'$log_level'";
		
		if ($id == '') {
			
			$sql = "INSERT INTO ".User::getTableName()." (login, password, log_level) VALUES ";
			$sql .= "('$login', '$password', $log_level_sql)";
			Database::executeUpdate($sql);
			$id = Database::getMaxId(User::getTableName());
			
			$sql = "INSERT INTO ".Danse::getJoinUserTableName()." (id_user, id_danse) ".
					"SELECT $id, id FROM ".Danse::getTableName();
			Database::executeUpdate($sql);
			
		} else {
			
			$sql = "UPDATE ".User::getTableName()." SET login='".$login."', ";
			$sql .= "password='".$password."', log_level=$log_level_sql WHERE id=$id";
			Database::executeUpdate($sql);
			
			$sql = "DELETE FROM ".Droit::getJoinUserTableName()." WHERE id_user=$id";
			Database::executeUpdate($sql);
			
			$sql = "DELETE FROM ".User::getJoinProfilTableName()." WHERE id_user=$id";
			Database::executeUpdate($sql);
		}
		
		if (isset($droits)) {
			foreach ($droits as $droit) {
				$sql = "INSERT INTO ".Droit::getJoinUserTableName()." (id_user, id_droit) ".
						"VALUES ($id, $droit)";
				Database::executeUpdate($sql);
			}
		}
			
		if (isset($profils)) {
			foreach ($profils as $profil) {
				$sql = "INSERT INTO ".User::getJoinProfilTableName()." (id_user, id_profil) ".
						"VALUES ($id, $profil)";
				Database::executeUpdate($sql);
			}
		}
		
		Database::commit();
		
		return $id;
	}
	
	public static function deleteUser($id) {
		Database::beginTransaction();
		
		$sql = "DELETE FROM ".Droit::getJoinUserTableName()." WHERE id_user=$id;";
		Database::executeUpdate($sql);
		
		$sql = "DELETE FROM ".User::getTableName()." WHERE id=$id;";
		Database::executeUpdate($sql);
		
		$sql = "DELETE FROM ".Danse::getJoinUserTableName()." WHERE id_user=$id";
		Database::executeUpdate($sql);
		
		Database::commit();
	}
	
}

?>