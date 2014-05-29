TRUNCATE tfilm * CASCADE;
TRUNCATE tgenre * CASCADE;
TRUNCATE tdistributeur * CASCADE;
TRUNCATE tseance * CASCADE;
TRUNCATE tsalle * CASCADE;
TRUNCATE tpersonne * CASCADE;
TRUNCATE tabonne * CASCADE;
TRUNCATE tvendeur * CASCADE;
TRUNCATE tproducteurs_film * CASCADE;
TRUNCATE trealisateurs_film * CASCADE;
TRUNCATE tticket * CASCADE;
TRUNCATE trechargement * CASCADE;
TRUNCATE ttarif * CASCADE;
TRUNCATE tproduit * CASCADE;
TRUNCATE tproduit_boisson * CASCADE;
TRUNCATE tproduit_alimentaire * CASCADE;
TRUNCATE tproduit_autre * CASCADE;
TRUNCATE tvente_produit * CASCADE;

DROP TABLE IF EXISTS tfilm CASCADE;
DROP TABLE IF EXISTS tgenre CASCADE;
DROP TABLE IF EXISTS tdistributeur CASCADE;
DROP TABLE IF EXISTS tseance CASCADE;
DROP TABLE IF EXISTS tsalle CASCADE;
DROP TABLE IF EXISTS tpersonne CASCADE;
DROP TABLE IF EXISTS tabonne CASCADE;
DROP TABLE IF EXISTS tvendeur CASCADE;
DROP TABLE IF EXISTS tproducteurs_film CASCADE;
DROP TABLE IF EXISTS trealisateurs_film CASCADE;
DROP TABLE IF EXISTS tticket CASCADE;
DROP TABLE IF EXISTS trechargement CASCADE;
DROP TABLE IF EXISTS ttarif CASCADE;
DROP TABLE IF EXISTS tproduit CASCADE;
DROP TABLE IF EXISTS tproduit_boisson CASCADE;
DROP TABLE IF EXISTS tproduit_alimentaire CASCADE;
DROP TABLE IF EXISTS tproduit_autre CASCADE;
DROP TABLE IF EXISTS tvente_produit CASCADE;

DROP SEQUENCE IF EXISTS sequence_film;
DROP SEQUENCE IF EXISTS sequence_distributeur;
DROP SEQUENCE IF EXISTS sequence_ticket;
DROP SEQUENCE IF EXISTS sequence_personne;
DROP SEQUENCE IF EXISTS sequence_rechargement;

CREATE SEQUENCE sequence_film;
CREATE SEQUENCE sequence_distributeur;
CREATE SEQUENCE sequence_ticket;
CREATE SEQUENCE sequence_personne;
CREATE SEQUENCE sequence_rechargement;

CREATE TABLE tfilm(
	pk_id_film				integer,
	titre					varchar(255),
	date_sortie				date,
	age_min 				integer,
	fk_nom_genre			varchar(255),
	fk_id_distributeur		integer
	);
CREATE TABLE tgenre(
	pk_nom_genre			varchar(255)
	);
CREATE TABLE tdistributeur(
	pk_id_distributeur 		integer,
	nom						varchar(255),
	prenom					varchar(255),
	adresse					varchar(255),
	tel						varchar(20)
	);
CREATE TABLE tseance(
	pk_timestamp_seance		timestamp,
	pkfk_nom_salle			varchar(255),
	pkfk_id_film			integer,
	doublage				varchar(255)
	);
CREATE TABLE tsalle(
	pk_nom_salle			varchar(255),
	nb_place				integer
	);
CREATE TABLE tpersonne(
	pk_id_personne			integer,
	nom						varchar(255),
	prenom					varchar(255)
	);
CREATE TABLE tabonne(
	pkfk_id_personne		integer,
	place_restante			integer
);

CREATE TABLE tvendeur(
	pkfk_id_personne		integer
);

CREATE TABLE tproducteurs_film(
	pkfk_id_film			integer,
	pkfk_id_personne		integer
);

CREATE TABLE trealisateurs_film(
	pkfk_id_film			integer,
	pkfk_id_personne		integer
);

CREATE TABLE tticket(
	pk_id_ticket			integer,
	timestamp_vente			timestamp,
	note					float,
	fk_timestamp_seance		timestamp,
	fk_nom_salle_seance		varchar(255),
	fk_id_film_seance		integer,
	fk_id_personne_abonne	integer,
	fk_id_personne_vendeur	integer,
	fk_nom_tarif			varchar(255)
);

CREATE TABLE trechargement(
	pk_id_rechargement		integer,
	pkfk_id_personne_abonne	integer,
	nombre_place			integer,
	prix_unitaire			float
);

CREATE TABLE ttarif(
	pk_nom_tarif			varchar(255),
	tarif					float
);

CREATE TABLE tproduit(
	pk_code_barre_produit	integer,
	nom_produit				varchar(255),
	prix					float
);

CREATE TABLE tproduit_boisson(
	pkfk_code_barre_produit	integer
);

CREATE TABLE tproduit_alimentaire(
	pkfk_code_barre_produit	integer
);

CREATE TABLE tproduit_autre(
	pkfk_code_barre_produit	integer
);

CREATE TABLE tvente_produit(
	pkfk_code_barre			integer,
	pkfk_id_personne_vendeur	integer
);

ALTER TABLE tfilm
ADD CONSTRAINT pk_tfilm PRIMARY KEY (pk_id_film);

ALTER TABLE tgenre
ADD CONSTRAINT pk_tgenre PRIMARY KEY (pk_nom_genre);

ALTER TABLE tdistributeur
ADD CONSTRAINT pk_tdistributeur PRIMARY KEY (pk_id_distributeur);

ALTER TABLE tseance
ADD CONSTRAINT pk_tseance PRIMARY KEY (pk_timestamp_seance, pkfk_nom_salle, pkfk_id_film);

ALTER TABLE tsalle
ADD CONSTRAINT pk_tsalle PRIMARY KEY (pk_nom_salle);

ALTER TABLE tpersonne
ADD CONSTRAINT pk_tpersonne PRIMARY KEY(pk_id_personne);

ALTER TABLE tabonne
ADD CONSTRAINT pk_tabonne PRIMARY KEY(pkfk_id_personne);

ALTER TABLE tvendeur
ADD CONSTRAINT pk_tvendeur PRIMARY KEY (pkfk_id_personne);

ALTER TABLE tproducteurs_film
ADD CONSTRAINT pk_tproducteurs_film PRIMARY KEY (pkfk_id_film,pkfk_id_personne);

ALTER TABLE trealisateurs_film
ADD CONSTRAINT pk_trealisateurs_film PRIMARY KEY (pkfk_id_film,pkfk_id_personne);

ALTER TABLE tticket
ADD CONSTRAINT pk_tticket PRIMARY KEY (pk_id_ticket);

ALTER TABLE trechargement
ADD CONSTRAINT pk_trechargement PRIMARY KEY (pk_id_rechargement, pkfk_id_personne_abonne);

ALTER TABLE ttarif
ADD CONSTRAINT pk_ttarif PRIMARY KEY (pk_nom_tarif);

ALTER TABLE tproduit
ADD CONSTRAINT pk_tproduit PRIMARY KEY (pk_code_barre_produit);

ALTER TABLE tproduit_boisson
ADD CONSTRAINT pk_tproduit_boisson PRIMARY KEY (pkfk_code_barre_produit);

ALTER TABLE tproduit_alimentaire
ADD CONSTRAINT pk_tproduit_alimentaire PRIMARY KEY (pkfk_code_barre_produit);

ALTER TABLE tproduit_autre
ADD CONSTRAINT pk_tproduit_autre PRIMARY KEY (pkfk_code_barre_produit);

ALTER TABLE tvente_produit
ADD CONSTRAINT pk_tvente_produit PRIMARY KEY (pkfk_code_barre, pkfk_id_personne_vendeur);


ALTER TABLE tfilm
ADD CONSTRAINT fk_tfilm_tgenre FOREIGN KEY(fk_nom_genre) REFERENCES tgenre(pk_nom_genre),
ADD CONSTRAINT fk_tfilm_distributeur FOREIGN KEY(fk_id_distributeur) REFERENCES tdistributeur(pk_id_distributeur);

ALTER TABLE tseance
ADD CONSTRAINT fk_tseance_tsalle FOREIGN KEY (pkfk_nom_salle) REFERENCES tsalle(pk_nom_salle),
ADD CONSTRAINT fk_tseance_tfilm FOREIGN KEY (pkfk_id_film) REFERENCES tfilm(pk_id_film);

ALTER TABLE tabonne
ADD CONSTRAINT fk_tabonne_tpersonne FOREIGN KEY (pkfk_id_personne) REFERENCES tpersonne(pk_id_personne);

ALTER TABLE tvendeur
ADD CONSTRAINT fk_tvendeur_tpersonne FOREIGN KEY (pkfk_id_personne) REFERENCES tpersonne(pk_id_personne);

ALTER TABLE tproducteurs_film
ADD CONSTRAINT fk_tproducteurs_film_tfilm FOREIGN KEY  (pkfk_id_film) REFERENCES tfilm(pk_id_film),
ADD CONSTRAINT fk_tproducteurs_film_trealisateur FOREIGN KEY (pkfk_id_personne) REFERENCES tpersonne(pk_id_personne);

ALTER TABLE trealisateurs_film
ADD CONSTRAINT fk_trealisateurs_film_tfilm FOREIGN KEY  (pkfk_id_film) REFERENCES tfilm(pk_id_film),
ADD CONSTRAINT fk_trealisateurs_film_trealisateur FOREIGN KEY (pkfk_id_personne) REFERENCES tpersonne(pk_id_personne);

ALTER TABLE tticket
ADD CONSTRAINT fk_tticket_tseance FOREIGN KEY (fk_timestamp_seance, fk_nom_salle_seance, fk_id_film_seance) REFERENCES tseance(pk_timestamp_seance, pkfk_nom_salle, pkfk_id_film),
ADD CONSTRAINT fk_tticket_tabonne FOREIGN KEY (fk_id_personne_abonne) REFERENCES tabonne(pkfk_id_personne),
ADD CONSTRAINT fk_tticket_tvendeur FOREIGN KEY (fk_id_personne_vendeur) REFERENCES tvendeur(pkfk_id_personne),
ADD CONSTRAINT fk_tticket_ttarif FOREIGN KEY (fk_nom_tarif) REFERENCES ttarif(pk_nom_tarif);

ALTER TABLE trechargement
ADD CONSTRAINT fk_trechargement FOREIGN KEY (pkfk_id_personne_abonne) REFERENCES tabonne(pkfk_id_personne);



ALTER TABLE tproduit_boisson
ADD CONSTRAINT fk_tproduit_boisson_tproduit FOREIGN KEY (pkfk_code_barre_produit) REFERENCES tproduit(pk_code_barre_produit);

ALTER TABLE tproduit_alimentaire
ADD CONSTRAINT fk_tproduit_alimentaire_tproduit FOREIGN KEY (pkfk_code_barre_produit) REFERENCES tproduit(pk_code_barre_produit);

ALTER TABLE tproduit_autre
ADD CONSTRAINT fk_tproduit_autre_tproduit FOREIGN KEY (pkfk_code_barre_produit) REFERENCES tproduit(pk_code_barre_produit);


ALTER TABLE tvente_produit
ADD CONSTRAINT fk_tvente_produit_tproduit FOREIGN KEY (pkfk_code_barre) REFERENCES tproduit(pk_code_barre_produit),
ADD CONSTRAINT fk_tvente_produit_tvendeur FOREIGN KEY (pkfk_id_personne_vendeur) REFERENCES tvendeur(pkfk_id_personne);




CREATE OR REPLACE VIEW vvendeur AS
SELECT v.pkfk_id_personne, p.nom, p.prenom
FROM tpersonne p, tvendeur v
WHERE p.pk_id_personne = v.pkfk_id_personne;

CREATE OR REPLACE VIEW vabonne AS
SELECT a.pkfk_id_personne, p.nom, p.prenom , a.place_restante
FROM tpersonne p, tabonne a
WHERE p.pk_id_personne = a.pkfk_id_personne;

CREATE OR REPLACE VIEW vtproduit_boisson AS
SELECT p.pk_code_barre_produit, p.nom_produit, p.prix
FROM tproduit p, tproduit_boisson b
WHERE p.pk_code_barre_produit = b.pkfk_code_barre_produit;

CREATE OR REPLACE VIEW vproduit_alimentaire AS
SELECT p.pk_code_barre_produit, p.nom_produit, p.prix
FROM tproduit p, tproduit_alimentaire a
WHERE p.pk_code_barre_produit = a.pkfk_code_barre_produit;

CREATE OR REPLACE VIEW vproduit_autre AS
SELECT p.pk_code_barre_produit, p.nom_produit, p.prix
FROM tproduit p, tproduit_autre a
WHERE p.pk_code_barre_produit = a.pkfk_code_barre_produit;

INSERT INTO tgenre(pk_nom_genre)
VALUES('Horreur');
INSERT INTO tdistributeur(pk_id_distributeur, nom, prenom, adresse, tel) 
VALUES (nextval('sequence_distributeur'), 'KarimCorp', 'KarimCorpi', 'PARIS', '06LOL');
INSERT INTO tfilm(pk_id_film, titre, date_sortie, age_min,fk_nom_genre,fk_id_distributeur)
VALUES (nextval('sequence_film'),'Karim a Compiegne',TIMESTAMP '2011-05-16 15:36:38', 18, 'Horreur', 1);