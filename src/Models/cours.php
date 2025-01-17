<?php

require_once 'database.php';

class Cours {
    protected $id;
    protected $titre;
    protected $description;
    protected $id_categorie;
    protected $image_path;
    protected $enseignant_id;
    protected $type;

    public function __construct($id = null, $titre = null, $description = null, $id_categorie = null, $image_path = null, $enseignant_id = null, $type = null) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->id_categorie = $id_categorie;
        $this->image_path = $image_path;
        $this->enseignant_id = $enseignant_id;
        $this->type = $type;
    }

    public function addTag($tag_id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO Course_Tags (id_course, id_tags) VALUES (:id_course, :id_tags)");
        $stmt->bindParam(':id_course', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':id_tags', $tag_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function setType($type) {
        $this->type = $type;
    }
}
