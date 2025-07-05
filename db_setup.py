#!/usr/bin/env python3
import mysql.connector
from mysql.connector import errorcode

DB_NAME = 'commerce'
DB_CONFIG = {
    'user': 'root',
    'password': 'yourpassword',
    'host': '127.0.0.1'
}

TABLES = {}
TABLES['client'] = (
    "CREATE TABLE IF NOT EXISTS `client` ("
    "  `id` int(11) NOT NULL AUTO_INCREMENT,"
    "  `prénom` varchar(100) DEFAULT NULL,"
    "  `nom` varchar(100) DEFAULT NULL,"
    "  `email` varchar(100) DEFAULT NULL,"
    "  `mot_de_passe` varchar(255) DEFAULT NULL,"
    "  `tel` varchar(20) DEFAULT NULL,"
    "  PRIMARY KEY (`id`)"
    ") ENGINE=InnoDB")

TABLES['commercant'] = (
    "CREATE TABLE IF NOT EXISTS `commercant` ("
    "  `id` int(11) NOT NULL AUTO_INCREMENT,"
    "  `prénom` varchar(100) DEFAULT NULL,"
    "  `nom` varchar(100) DEFAULT NULL,"
    "  `email` varchar(100) DEFAULT NULL,"
    "  `mot_de_passe` varchar(255) DEFAULT NULL,"
    "  `tel` varchar(20) DEFAULT NULL,"
    "  PRIMARY KEY (`id`)"
    ") ENGINE=InnoDB")

TABLES['administrateur'] = (
    "CREATE TABLE IF NOT EXISTS `administrateur` ("
    "  `id` int(11) NOT NULL AUTO_INCREMENT,"
    "  `prénom` varchar(100) DEFAULT NULL,"
    "  `nom` varchar(100) DEFAULT NULL,"
    "  `email` varchar(100) DEFAULT NULL,"
    "  `mot_de_passe` varchar(255) DEFAULT NULL,"
    "  `tel` varchar(20) DEFAULT NULL,"
    "  PRIMARY KEY (`id`)"
    ") ENGINE=InnoDB")

TABLES['Role'] = (
    "CREATE TABLE IF NOT EXISTS `Role` ("
    "  `id` int(11) NOT NULL AUTO_INCREMENT,"
    "  `nom` varchar(50) DEFAULT NULL,"
    "  `is_Active` tinyint(1) DEFAULT NULL,"
    "  PRIMARY KEY (`id`)"
    ") ENGINE=InnoDB")

TABLES['Produit'] = (
    "CREATE TABLE `Produit` ("
    "  `id` int(11) NOT NULL AUTO_INCREMENT,"
    "  `nom` varchar(100) DEFAULT NULL,"
    "  `prix` decimal(10,2) DEFAULT NULL,"
    "  `stock` int(11) DEFAULT NULL,"
    "  `imagee` LONGTEXT DEFAULT NULL,"
    "  PRIMARY KEY (`id`)"
    ") ENGINE=InnoDB")

TABLES['Commande'] = (
    "CREATE TABLE IF NOT EXISTS `Commande` ("
    "  `id` int(11) NOT NULL AUTO_INCREMENT,"
    "  `nom_produit` int(11) DEFAULT NULL,"
    "  `Quantite` int(11) DEFAULT NULL,"
    "  `prix` float DEFAULT NULL,"
    "  `date_commande` date DEFAULT NULL,"
    "  `id_client` int(11) DEFAULT NULL,"
    "  `id_commercant` int(11) DEFAULT NULL,"
    "  PRIMARY KEY (`id`),"
    "  KEY `id_client` (`id_client`),"
    "  KEY `id_commercant` (`id_commercant`),"
    "  CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id`),"
    "  CONSTRAINT `commande_ibfk_2` FOREIGN KEY (`id_commercant`) REFERENCES `commercant` (`id`)"
    ") ENGINE=InnoDB")

TABLES['Paiement'] = (
    "CREATE TABLE IF NOT EXISTS `Paiement` ("
    "  `id` int(11) NOT NULL AUTO_INCREMENT,"
    "  `Capture_banlky` varchar(100) DEFAULT NULL,"
    "  `Montant` decimal(10,2) DEFAULT NULL,"
    "  `Date_paiement` date DEFAULT NULL,"
    "  `ID_commande` int(11) DEFAULT NULL,"
    "  PRIMARY KEY (`id`),"
    "  KEY `ID_commande` (`ID_commande`),"
    "  CONSTRAINT `paiement_ibfk_1` FOREIGN KEY (`ID_commande`) REFERENCES `Commande` (`id`)"
    ") ENGINE=InnoDB")

TABLES['Chat'] = (
    "CREATE TABLE IF NOT EXISTS `Chat` ("
    "  `id_chat` int(11) NOT NULL AUTO_INCREMENT,"
    "  `contenu_Chat` text,"
    "  `date_Chat` datetime DEFAULT NULL,"
    "  `type_Chat` varchar(50) DEFAULT NULL,"
    "  `id_client` int(11) DEFAULT NULL,"
    "  `id_commercant` int(11) DEFAULT NULL,"
    "  `id_commande` int(11) DEFAULT NULL,"
    "  PRIMARY KEY (`id_chat`),"
    "  KEY `id_client` (`id_client`),"
    "  KEY `id_commercant` (`id_commercant`),"
    "  KEY `id_commande` (`id_commande`),"
    "  CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id`),"
    "  CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`id_commercant`) REFERENCES `commercant` (`id`),"
    "  CONSTRAINT `chat_ibfk_3` FOREIGN KEY (`id_commande`) REFERENCES `Commande` (`id`)"
    ") ENGINE=InnoDB")

TABLES['Contient'] = (
    "CREATE TABLE `Contient` ("
    "  `ID_commande` int(11) NOT NULL,"
    "  `ID_produit` int(11) NOT NULL,"
    "  `Quantite` int(11) DEFAULT NULL,"
    "  PRIMARY KEY (`ID_commande`,`ID_produit`),"
    "  KEY `ID_produit` (`ID_produit`),"
    "  CONSTRAINT `contient_ibfk_1` FOREIGN KEY (`ID_commande`) REFERENCES `Commande` (`id`),"
    "  CONSTRAINT `contient_ibfk_2` FOREIGN KEY (`ID_produit`) REFERENCES `Produit` (`id`)"
    ") ENGINE=InnoDB")

TABLES['Panier'] = (
    "CREATE TABLE `Panier` ("
    "  `id` int(11) NOT NULL AUTO_INCREMENT,"
    "  `id_client` int(11) NOT NULL,"
    "  `id_produit` int(11) NOT NULL,"
    "  `quantite` int(11) NOT NULL,"
    "  PRIMARY KEY (`id`),"
    "  KEY `id_client` (`id_client`),"
    "  KEY `id_produit` (`id_produit`),"
    "  CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id`),"
    "  CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `Produit` (`id`)"
    ") ENGINE=InnoDB")

def main():
    try:
        cnx = mysql.connector.connect(**DB_CONFIG)
        cursor = cnx.cursor()
        print("Successfully connected to MySQL server.")
    except mysql.connector.Error as err:
        print(f"Failed to connect to MySQL: {err}")
        exit(1)

    try:
        cursor.execute(f"CREATE DATABASE IF NOT EXISTS {DB_NAME} DEFAULT CHARACTER SET 'utf8mb4'")
        cursor.execute(f"USE {DB_NAME}")
        print(f"Database '{DB_NAME}' is ready.")
    except mysql.connector.Error as err:
        print(f"Failed to create or select database: {err}")
        cursor.close()
        cnx.close()
        exit(1)

    # Drop all tables for a clean slate
    try:
        print("Dropping all tables...")
        cursor.execute("SET FOREIGN_KEY_CHECKS = 0")
        cursor.execute("SHOW TABLES")
        tables = cursor.fetchall()
        for (table_name,) in tables:
            print(f"Dropping table {table_name}")
            cursor.execute(f"DROP TABLE `{table_name}`")
        cursor.execute("SET FOREIGN_KEY_CHECKS = 1")
        print("All tables dropped.")
    except mysql.connector.Error as err:
        print(f"Error dropping tables: {err.msg}")


    for table_name, table_description in TABLES.items():
        try:
            print(f"Creating table '{table_name}': ", end='')
            cursor.execute(table_description)
            print("OK")
        except mysql.connector.Error as err:
            print(f"ERROR: {err.msg}")

    print("\nDatabase setup process finished.")
    cursor.close()
    cnx.close()

if __name__ == "__main__":
    main()
