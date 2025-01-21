<?php

require_once __DIR__ . '/../../src/config/autoloader.php';

use Models\Tag;

// session_start(); if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'teacher') { header('Location: login.php'); exit; }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const courseTagsInput = document.getElementById('courseTags');
    const tagsList = document.getElementById('tagsList');
    const selectedTagsContainer = document.getElementById('selectedTags');
    let selectedTags = [];

    function fetchTags(query) {
        fetch(`../../app/actions/tag/getAllJson.php?query=${query}`)
            .then(response => response.json())
            .then(tags => {
                tagsList.innerHTML = '';
                tags.forEach(tag => {
                    const tagItem = document.createElement('div');
                    tagItem.classList.add('px-4', 'py-2', 'cursor-pointer', 'hover:bg-gray-100');
                    tagItem.textContent = tag.name;
                    tagsList.appendChild(tagItem);
                    tagItem.addEventListener('click', function () {
                        addTag(tag);
                    });
                });
                tagsList.classList.remove('hidden');
            });
    }

    function addTag(tag) {
        if (!selectedTags.some(t => t.id === tag.id || t.name.toLowerCase() === tag.name.toLowerCase())) {
            selectedTags.push(tag);
            updateSelectedTags();
        }
        courseTagsInput.value = ''; 
        tagsList.classList.add('hidden'); 
    }

    function removeTag(tag) {
        selectedTags = selectedTags.filter(t => t.id !== tag.id);
        updateSelectedTags();
    }

    function updateSelectedTags() {
        selectedTagsContainer.innerHTML = '';
        selectedTags.forEach(tag => {
            const tagItem = document.createElement('div');
            tagItem.classList.add('px-4', 'py-2', 'bg-gray-200', 'rounded', 'mr-2', 'mb-2', 'inline-block');
            tagItem.textContent = tag.name;
            const removeButton = document.createElement('button');
            removeButton.classList.add('ml-2', 'text-red-500');
            removeButton.textContent = 'x';
            removeButton.addEventListener('click', function () {
                removeTag(tag);
            });
            tagItem.appendChild(removeButton);
            selectedTagsContainer.appendChild(tagItem);
        });
    }

    courseTagsInput.addEventListener('input', function () {
        const query = courseTagsInput.value;
        if (query.length >= 2) {
            fetchTags(query);
        } else {
            tagsList.classList.add('hidden');
        }
    });

    courseTagsInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && courseTagsInput.value.trim() !== '') {
            e.preventDefault(); 
            const newTag = { id: 'new' + selectedTags.length, name: courseTagsInput.value.trim() };
            addTag(newTag);
        }
    });

    document.getElementById('addCourseForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('selectedTags', JSON.stringify(selectedTags));
        fetch('/Youdemy/app/actions/cours/add.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json(); 
        })
        .then(data => {
            console.log(data); 
            window.location.href = 'cours.php';
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});

    </script>
</head>
<body>
    <h1>Add Course</h1>
    <form id="addCourseForm" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <br>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <br>
        <label for="courseType">Course Type:</label>
        <select id="courseType" name="courseType" onchange="toggleContentField()" required>
            <option value="text">Text</option>
            <option value="video">Video</option>
        </select>
        <br>
        <div id="textContentField" class="hidden">
            <label for="content">Content:</label>
            <textarea id="content" name="content"></textarea>
        </div>
        <div id="videoContentField" class="hidden">
            <label for="video">Video:</label>
            <input type="file" id="video" name="video" accept="video/*">
        </div>
        <br>
        <label for="courseTags">Tags:</label>
        <input type="text" id="courseTags" name="courseTags">
        <div id="tagsList" class="hidden"></div>
        <div id="selectedTags"></div>
        <br>
        <button type="submit">Add Course</button>
    </form>
</body>
</html>
