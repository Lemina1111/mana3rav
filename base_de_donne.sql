

CREATE TABLE client (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prénom VARCHAR(100),
    nom VARCHAR(100),
    email VARCHAR(100),
    mot_de_passe VARCHAR(255),
    tel VARCHAR(20)
    
);

CREATE TABLE commercant (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prénom VARCHAR(100),
    nom VARCHAR(100),
    email VARCHAR(100),
    mot_de_passe VARCHAR(255),
    tel VARCHAR(20)
   
);

CREATE TABLE administrateur (
    id  INT PRIMARY KEY AUTO_INCREMENT,
    prénom VARCHAR(100),
    nom VARCHAR(100),
    email VARCHAR(100),
    mot_de_passe VARCHAR(255),
     tel VARCHAR(20)
    
);

CREATE TABLE Role (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50),
    is_Active BOOLEAN
);

CREATE TABLE Produit (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100),
    prix DECIMAL(10,2),
    stock INT,
    imagee VARCHAR(255)
);

CREATE TABLE Commande (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_produit INT,
    Quantite INT,
    prix float,
    date_commande DATE,
    id_client INT,
    id_commercant INT,
    FOREIGN KEY (id_client) REFERENCES client(id),
     FOREIGN KEY (id_commercant) REFERENCES commercant(id)
);

CREATE TABLE Paiement (
    id INT PRIMARY KEY AUTO_INCREMENT,
    Capture_banlky VARCHAR(100),
    Montant DECIMAL(10,2),
    Date_paiement DATE,
    ID_commande INT,
    FOREIGN KEY (ID_commande) REFERENCES Commande(id)
);

CREATE TABLE Chat (
    id_chat INT PRIMARY KEY AUTO_INCREMENT,
    contenu_Chat TEXT,
    date_Chat DATETIME,
    type_Chat VARCHAR(50),
    id_client INT,
    id_commercant INT,
    id_commande INT,
    FOREIGN KEY (id_client) REFERENCES client(id),
    FOREIGN KEY (id_commercant) REFERENCES commercant(id),
    FOREIGN KEY (id_commande) REFERENCES commande(id)

);

-- -- Table d'association Commande et Produit (relation Contient)
CREATE TABLE Contient (
    ID_commande INT,
    ID_produit INT,
    Quantite INT,
    PRIMARY KEY (ID_commande, ID_produit),
    FOREIGN KEY (ID_commande) REFERENCES commande(id),
    FOREIGN KEY (ID_produit) REFERENCES produit(id)
);

-- -- Table d'association Créer (Client -> Commande)
-- -- Déjà gérée par Commande.ID_Utilisateurs

-- -- Table d'association Modifier (Commande <-> Paiement) déjà intégrée par la clé étrangère

-- -- Table d'association Discuter (Client <-> Commercant) déjà représentée par Chat

-- -- Table d’association Utilisateurs <-> Role
-- CREATE TABLE Possede (
--     ID_Utilisateurs INT,
--     id_role INT,
--     PRIMARY KEY (ID_Utilisateurs, id_role),
--     FOREIGN KEY (ID_Utilisateurs) REFERENCES Utilisateurs(ID_Utilisateurs),
--     FOREIGN KEY (id_role) REFERENCES Role(id)
-- );
