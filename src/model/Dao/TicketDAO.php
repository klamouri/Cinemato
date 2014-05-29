<?php

namespace model\Dao;

use \DateTime;

use \PDO;

use model\Entite\Seance;
use model\Entite\Ticket;

class TicketDAO
{
    private $dao;

    public function __construct($dao) {
        $this->dao = $dao;
    }

    private function getDao() {
        return $this->dao;
    }

    public function find($id) {
    	$ticket = null;
    	
    	$query = "SELECT * FROM tticket WHERE pk_id_ticket = :id";
    	$connection = $this->getDao()->getConnexion();
    	
    	if (is_null($connection)) {
    		return;
    	}
    	
    	$statement = $connection->prepare($query);
    	$statement->execute(array(
    			'id' => $id
    	));
    	
    	if ($donnees = $statement->fetch(PDO::FETCH_ASSOC)) {
    		$ticket = self::bind($donnees);
    	}
    	
    	return $ticket;
    }
    
    public function findAll() {
    	$tickets = array ();
    	$query = 'SELECT * FROM tticket';
    	$connection = $this->getDao ()->getConnexion ();
    	
    	if (! is_null ( $connection )) {
    		try {
    			$statement = $connection->prepare ( $query );
    			$statement->execute ( array () );
    			while ( $donnees = $statement->fetch ( PDO::FETCH_ASSOC ) ) {
    				$ticket = self::bind ( $donnees );
    				array_push ( $tickets, $ticket );
    			}
    		} catch ( \PDOException $e ) {
    			throw $e;
    		}
    	}
    	return $tickets;
    }
    
    public function create($ticket) {
    	$query = 'INSERT INTO tticket(pk_id_ticket, timestamp_vente, note, fk_timestamp_seance, fk_nom_salle_seance, fk_id_personne_abonne, fk_id_personne_vendeur, fk_nom_tarif) VALUES(nextval(sequence_ticket), :dateVente, :note, :dateSeance, :salle, :abonne, :vendeur, :tarif)';
    	$connection = $this->getDao ()->getConnexion ();
    	
    	if (! is_null ( $connection )) {
    		try {
    			$statement = $connection->prepare ( $query );
    			$statement->execute ( array (
    					'dateVente' => $ticket->getDateDeVente(),
    					'note' => $ticket->getNote(),
    					'dateSeance' => $ticket->getSeance()->getDateSeance(),
    					'salle' => $ticket->getSeance()->getSalle(),
    					'abonne' => $ticket->getAbonne()->getId(),
    					'vendeur' => $ticket->getVendeur()->getId(),
    					'tarif' => $ticket->getTarif()->getNom()
    			) );
    		} catch ( \PDOException $e ) {
    			throw $e;
    		}
    	}
    }
    
    public function update($ticket){
    	$query = 'UPDATE tticket SET timestamp_vente = :dateVente, note = :note, fk_timestamp_seance = :dateSeance, fk_nom_salle_seance = :salle, fk_id_personne_abonne = :abonne, fk_id_personne_vendeur = :vendeur, fk_nom_tarif = :tarif WHERE pk_id_ticket = :id';
    	$connection = $this->getDao ()->getConnexion ();
    	
    	if (! is_null ( $connection )) {
    		try {
    			$statement = $connection->prepare ( $query );
    			$statement->execute ( array (
    					'id' => $ticket->getId(),
    					'dateVente' => $ticket->getDateDeVente(),
    					'note' => $ticket->getNote(),
    					'dateSeance' => $ticket->getSeance()->getDateSeance(),
    					'salle' => $ticket->getSeance()->getSalle(),
    					'abonne' => $ticket->getAbonne()->getId(),
    					'vendeur' => $ticket->getVendeur()->getId(),
    					'tarif' => $ticket->getTarif()->getNom()
    			) );
    		} catch ( \PDOException $e ) {
    			throw $e;
    		}
    	}
    }
    public function delete($ticket){
    	$query = 'DELETE FROM tticket WHERE pk_id_ticket = :id ';
    	$connection = $this->getDao ()->getConnexion ();
    	
    	if (! is_null ( $connection )) {
    		try {
    			$statement = $connection->prepare ( $query );
    			$statement->execute ( array (
					'id' => $ticket->getId()
    			) );
    		} catch ( \PDOException $e ) {
    			throw $e;
    		}
    	}
    }
    
    public static function bind($donnees){
    	$ticket = new Ticket();
    	$ticket->setId($donnees['pk_id_ticket']);
    	$ticket->setDateDeVente($donnees['timestamp_vente']);
    	$ticket->setNote($donnees['note']);
    	$ticket->setSeance($this->getDao()->getSeanceDAO()->find($donnees['fk_timestamp_seance'], $donnees['fk_nom_salle_seance']));
    	$ticket->setAbonne($this->getDao()->getPersonneDAO()->find($donnees['fk_id_personne_abonne']));
    	$ticket->setVendeur($this->getDao()->getPersonneDAO()->find($donnees['fk_id_personne_vendeur']));
    	$ticket->setTarif($this->getDao()->getTarifDAO()->find($donnees['fk_nom_tarif']));
    }
    
}