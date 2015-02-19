<?php

class MetierTag {
	
	public static function getAllTag() {
		return Database::getResultsObjects("select * from ".Tag::getTableName()." order by label asc", "Tag");
	}
	
	public static function getTagById($id) {
		$sql = "select * from ".Tag::getTableName()." where id = $id";
		$tag_array = Database::getResultsObjects($sql, "Tag");
		return $tag_array[0];
	}
	
	public static function getTagByLabel($label) {
		$labelSql = escapeString($label);
		$sql = "select * from ".Tag::getTableName()." where label = '$labelSql'";
		$tag = Database::getResultsObjects($sql, "Tag");
		if (count($tag) == 0) {
			return null;
		}
		return $tag[0];
	}
	
	public static function getTagByVideo($id_video) {
		return Database::getResultsObjects("select t.* from ".Tag::getJoinVideoTableName()." tv ".
			" inner join ".Tag::getTableName()." t on t.id = tv.id_tag ".
			" where tv.id_video = $id_video", "Tag");
	}
	
	public static function getTagObjectByVideo($id_video) {
		$sql = "select t.* from ".Tag::getJoinVideoTableName()." tv ".
			" INNER JOIN ".Tag::getTableName()." t on tv.id_tag = t.id".
			" where id_video = $id_video";
		return Database::getResultsObjects($sql, "Tag");
	}
	
	public static function getTagsForVideos($id_videos_array) {
		$ids = "";
		foreach($id_videos_array as $id) {
			if ($ids != "") {
				$ids .= ', ';
			}
			$ids .= $id;
		}
		// On ne renvoie que les tags auquels toutes les vidéos sont associées
		$sql = "select tv.id_tag, count(*) from ".Tag::getJoinVideoTableName()." tv ".
			" where tv.id_video in ($ids)".
			" group by id_tag ".
			" having count(*) = ".count($id_videos_array);
		$results = Database::getResults($sql);
		$ids_tag = array();
		foreach($results as $result) {
			$ids_tag[] = $result['id_tag'];		
		}
		
		return $ids_tag;
	}
	
	
	
	public static function insertTag($label) {
		if (trim($label) == "") {
			throw new Exception("Le tag ne peut pas être vide.");
		}
		
		$tag = MetierTag::getTagByLabel($label);
		if ($tag != null) {
			throw new Exception("Le tag $label existe déjà.");	
		}
		
		$sql = "INSERT INTO ".Tag::getTableName()."(label) VALUES  ('".escapeString($label)."')";
		Database::executeUpdate($sql);
		
		return Database::getMaxId(Tag::getTableName());
	}
	
	public static function updateLabelTag($id, $label) {
		if (trim($label) == "") {
			throw new Exception("Le tag ne peut pas être vide.");
		}
		
		$tag = MetierTag::getTagByLabel($label);
		if ($tag != null) {
			if ($tag->id == $id) {
				throw new Exception("Même label : $label -> $tag->label.");
				return;
			} else {
				throw new Exception("Le tag $label existe déjà.");	
			}
		}
	
		$tag = MetierTag::getTagById($id);
		if ($tag == null) {
			throw new Exception("Le tag numéro $id n'existe pas en base.");
		}
	
		$sql = "UPDATE ".Tag::getTableName()." SET label='".escapeString($label)."' WHERE id=$id";
		Database::executeUpdate($sql);
	}
	
	
	public static function linkVideoTag($id_video, $id_tags) {
		Database::beginTransaction();
		Database::executeUpdate("DELETE FROM ".Tag::getJoinVideoTableName()." WHERE id_video = $id_video");
		
		foreach ($id_tags as $tag) {
			$sql = "INSERT INTO ".Tag::getJoinVideoTableName()."(id_tag, id_video) VALUES  ($tag, $id_video)";
			Database::executeUpdate($sql);
		}
		Database::commit();
	}
	
	public static function saveAttachedTagsForVideo($id_videos, $tags) {
		Database::beginTransaction();
		$in_clause_video = "";
		foreach($id_videos as $id_video) {
			if ($in_clause_video != "") {
				$in_clause_video .= ", ";
			}
			$in_clause_video .= $id_video;
		}
		
		$in_clause_tag = "";
		foreach($tags as $tag) {
			if ($in_clause_tag != "") {
				$in_clause_tag .= ", ";
			}
			$in_clause_tag .= $tag;
		}
		$sql = "DELETE FROM ".Tag::getJoinVideoTableName()." where id_video in ($in_clause_video) and id_tag in ($in_clause_tag)";
		Database::executeUpdate($sql);
		
		/*
		$tags_allowed = MetierTag::getTagsForVideos($id_videos);
		if ($tags_allowed != null) {
			$in_clause_tag = "";
			foreach($tags_allowed as $tag) {
				if ($in_clause_tag != "") {
					$in_clause_tag .= ", ";
				}
				$in_clause_tag .= $tag;
			}
			$sql = "DELETE FROM ".Tag::getJoinVideoTableName()." where id_video in ($in_clause_video) and id_tag in ($in_clause_tag)";
			Database::executeUpdate($sql);
		}
		*/
		
		if ($tags != null) {		
			foreach($tags as $tag) {
				foreach($id_videos as $id_video) {
					$sql = "INSERT INTO ".Tag::getJoinVideoTableName()." (id_video, id_tag) values ($id_video, $tag)";
					Database::executeUpdate($sql);
				}
			}
		}
		Database::commit();
	}
	
	
	
	
	public static function removeLinkVideoTag($id_video) {
		Database::executeUpdate("DELETE FROM ".Tag::getJoinVideoTableName()." WHERE id_video = $id_video");
	}
	
	public static function deleteTag($id) {
		Database::beginTransaction();
		Database::executeUpdate("DELETE FROM ".Tag::getJoinVideoTableName()." WHERE id_tag = $id");
		Database::executeUpdate("DELETE FROM ".Tag::getTableName()." WHERE id = $id");
		Database::commit();
	}
	
	
	
}

?>