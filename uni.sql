 -- =========================================
-- Base de données : LIU-MR
-- =========================================

 CREATE DATABASE IF NOT EXISTS liu_mr
 CHARACTER SET utf8mb4
 COLLATE utf8mb4_general_ci;

 USE liu_mr;

-- =========================================
-- Table : contacts
-- =========================================

CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================================
-- Table : admins
-- =========================================

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL
);
CREATE TABLE IF NOT EXISTS etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    filiere VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(50),
  password VARCHAR(255),
  role ENUM('etudiant','prof')
);

CREATE TABLE IF NOT EXISTS courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS attendance (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  course_id INT,
  date DATE,
  present TINYINT(1),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (course_id) REFERENCES courses(id)
);



-- =========================================
-- Admin de test (mot de passe NON haché)
-- =========================================

INSERT INTO admins (email, password)
VALUES ('admin@liu-mr.edu', 'admin123')
ON DUPLICATE KEY UPDATE password = 'admin123';

-- =========================================
-- Données de test : contacts
-- =========================================

INSERT INTO contacts (name, email, message) VALUES
('Test Étudiant', 'etudiant@liu-mr.edu', 'Bonjour, je souhaite avoir des informations sur les admissions.'),
('Administration', 'admin@liu-mr.edu', 'Message de test pour vérifier le formulaire de contact.'),
('Alice Dupont', 'alice.dupont@example.com', 'J aimerais avoir le lien direct avec vous merci de me répondre'),
('Bob Martin', 'bob.martin@example.com', 'Je voudrais vous rejoindre'),
('Charlie Durand', 'charlie.durand@example.com', 'Bonjour je suis intéressé par votre université');

INSERT IGNORE INTO etudiants (nom, prenom, email, filiere) VALUES
('Diallo', 'Mamadou', 'mamadou.diallo@liu-mr.edu', 'Informatique'),
('Ba', 'Aminata', 'aminata.ba@liu-mr.edu', 'Gestion'),
('Ould Ahmed', 'Mohamed', 'mohamed.ahmed@liu-mr.edu', 'Réseaux & Télécoms'),
('Fall', 'Cheikh', 'cheikh.fall@liu-mr.edu', 'Génie Civil'),
('Sy', 'Fatou', 'fatou.sy@liu-mr.edu', 'Informatique'),
('Kane', 'Ibrahima', 'ibrahima.kane@liu-mr.edu', 'Sciences Économiques'),
('Sow', 'Marième', 'marieme.sow@liu-mr.edu', 'Gestion'),
('Dia', 'Abdoulaye', 'abdoulaye.dia@liu-mr.edu', 'Mathématiques Appliquées');

INSERT INTO courses (name) VALUES
('Recherche Opérationnelle'),
('Programmation Web');

INSERT INTO users (email,password,role)
VALUES
('etu1@mail.com','1234','etudiant'),
('prof1@mail.com','1234','prof');




Select * from contacts;