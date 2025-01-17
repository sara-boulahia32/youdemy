<?php

require_once 'cours.php';

class CoursVideo extends Cours {
    private $video_url;

    public function __construct($id = null, $titre = null, $description = null, $id_categorie = null, $image_path = null, $enseignant_id = null, $video_url = null, $type = null) {
        parent::__construct($id, $titre, $description, $id_categorie, $image_path, $enseignant_id, $type);
        $this->video_url = $video_url;
    }

    public function ajouter() {
        $type = 'video';
        $this->setType($type);
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO Cours (titre, description, categorie_id, image_path, video_url, contenu_type, enseignant_id) VALUES (:titre, :description, :id_categorie, :image_path, :video_url, :type, :enseignant_id)");
        $stmt->bindParam(':titre', $this->titre);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':id_categorie', $this->id_categorie, PDO::PARAM_INT);
        $stmt->bindParam(':image_path', $this->image_path);
        $stmt->bindParam(':video_url', $this->video_url);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':enseignant_id', $this->enseignant_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $this->id = $pdo->lastInsertId();
            return true;
        } else {
            return false;
        }
    }

    public function afficherCours() {
        return "<video src='" . $this->video_url . "'></video>";
    }

    public function setVideo_url($video_url) {
        $this->video_url = $video_url;
    }
}
