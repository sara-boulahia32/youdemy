<?php

namespace Models;


use Database\Database;
use Interfaces\ICreateCourse;
use Models\Course;

class CoursVideo extends Course implements ICreateCourse {

    public function __construct($title, $description, $category, $price, $status, $media_path, $is_approved, $id_author, $content_type) {
        parent::__construct($title, $description, $category, $price, $status, $media_path, $is_approved, $id_author, $content_type);
    }

    public function create() {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("INSERT INTO Courses (title, content, category, description, price, media_path, content_type, is_approved, id_author) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$this->title, $this->media_path, $this->category, $this->description, $this->price, $this->media_path, "video", $this->is_approved, $this->id_author]);
        $course_id = $pdo->lastInsertId();

    }

    // public function ajouter() {
    //     $type = 'video';
    //     $this->setType($type);
    //     $pdo = Database::getInstance()->getConnection();
    //     $stmt = $pdo->prepare("INSERT INTO Cours (titre, description, categorie_id, image_path, video_url, contenu_type, enseignant_id) VALUES (:titre, :description, :id_categorie, :image_path, :video_url, :type, :enseignant_id)");
    //     $stmt->bindParam(':titre', $this->titre);
    //     $stmt->bindParam(':description', $this->description);
    //     $stmt->bindParam(':id_categorie', $this->id_categorie, PDO::PARAM_INT);
    //     $stmt->bindParam(':image_path', $this->image_path);
    //     $stmt->bindParam(':video_url', $this->video_url);
    //     $stmt->bindParam(':type', $this->type);
    //     $stmt->bindParam(':enseignant_id', $this->enseignant_id, PDO::PARAM_INT);

    //     if ($stmt->execute()) {
    //         $this->id = $pdo->lastInsertId();
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    // public function afficherCours() {
    //     return "<video src='" . $this->video_url . "'></video>";
    // }

    // public function setVideo_url($video_url) {
    //     $this->video_url = $video_url;
    // }
}
