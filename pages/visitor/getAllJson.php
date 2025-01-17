<?php
require_once('../../src/config/autoloader.php');
use Models\Tag;

header('Content-Type: application/json');

$query = $_GET['query'] ?? '';

try {
    $tags = Tag::search($query);
    echo json_encode(array_map(function($tag) {
        return [
            'id' => $tag->getId(),
            'name' => $tag->getName()
        ];
    }, $tags));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
