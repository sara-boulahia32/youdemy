-- Create Users table
CREATE TABLE Users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher', 'admin') NOT NULL,
    is_valid BOOLEAN NOT NULL DEFAULT FALSE, -- For teacher account validation
    is_active BOOLEAN NOT NULL DEFAULT TRUE  -- For account activation/suspension
);

-- Create Courses table
CREATE TABLE Courses (
    id_course INT AUTO_INCREMENT PRIMARY KEY,
    id_author INT ,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price FLOAT NOT NULL,
    status ENUM('Draft', 'Published') NOT NULL,
    media_path VARCHAR(255), -- For media files (video, image, document)
    is_approved BOOLEAN NOT NULL DEFAULT FALSE -- For course approval by admin
    content_type ENUM('text', 'video', 'file', 'image');
);

-- Create Tags table
CREATE TABLE Tags (
    id_tags INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Create Categories table
CREATE TABLE Categories (
    id_category INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Create Reservations table
CREATE TABLE Reservations (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_course INT NOT NULL,
    startDate DATE NOT NULL,
    endDate DATE NOT NULL,
    FOREIGN KEY (id_user) REFERENCES Users(id_user),
    FOREIGN KEY (id_course) REFERENCES Courses(id_course)
);

-- Create Notifications table
CREATE TABLE Notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT NOT NULL,
    isRead BOOLEAN NOT NULL DEFAULT FALSE,
    id_user INT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES Users(id_user)
);

-- Create Course_Tags table for many-to-many relationship between Courses and Tags
CREATE TABLE Course_Tags (
    id_course INT NOT NULL,
    id_tags INT NOT NULL,
    name VARCHAR(255) NOT NULL, -- Tag name
    PRIMARY KEY (id_course, id_tags),
    FOREIGN KEY (id_course) REFERENCES Courses(id_course),
    FOREIGN KEY (id_tags) REFERENCES Tags(id_tags)
);

