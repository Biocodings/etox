<?php

namespace EtoxMicrome\Entity2DocumentBundle\Entity;

use EtoxMicrome\Entity2AbstractBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Cytochrome2DocumentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Cytochrome2DocumentRepository extends EntityRepository
{
    public function getValToSearch($field)
    {
        switch ($field) {
            case "hepatotoxicity":
                $valToSearch="hepval";
                break;
            case "cardiotoxicity":
                $valToSearch="cardval";
                break;
            case "nephrotoxicity":
                $valToSearch="nephval";
                break;
            case "phospholipidosis":
                $valToSearch="phosval";
                break;
        }
        return $valToSearch;
    }

    public function getOrderBy($orderBy, $valToSearch)
    {
        switch ($orderBy) {
            case "score":
                $orderBy=$valToSearch;
                break;
            case "pattern":
                $orderBy="patternCount";
                break;
            case "rule":
                $orderBy="ruleScore";
                break;
            case "term":
                $orderBy="hepTermVarScore";
                break;
        }
        return $orderBy;
    }

    public function getCytochrome2DocumentFromField($field, $typeOfEntity, $arrayNames, $arrayCanonicals, $source, $orderBy)
    {
        return $this->getCytochrome2DocumentFromFieldDQL($field, $typeOfEntity, $arrayNames, $arrayCanonicals, $source, $orderBy)->getResult();
    }

    public function getCytochrome2DocumentFromFieldDQL($field, $entityType, $arrayNames, $arrayCanonicals, $source, $orderBy)
    {
        $message="inside getCytochrome2DocumentFromFieldDQL";
        $valToSearch=$this->getValToSearch($field);//"i.e hepval, embval... etc"
        //We have to create a query that searchs all over the entityIds inside the $arrayEntityId
        $orderBy=$this->getOrderBy($orderBy, $valToSearch);
        //ld($source);
        //ld($valToSearch);
        //ld($arrayNames);
        //ld($arrayCanonicals);
        //$sql="SELECT e2d,d
        //    FROM EtoxMicromeEntity2DocumentBundle:Cytochrome2Document e2d
        //    JOIN e2d.document d
        //    WHERE (e2d.cypsMention IN (:arrayNames) AND d.$valToSearch is not NULL AND d.kind = :source) or ( e2d.cypsCanonical IN (:arrayCanonicals) AND d.$valToSearch is not NULL AND d.kind = :source )
        //    ORDER BY d.$orderBy desc
        //    ";
        if ($source=="all"){ //if source of data is all we dont filter for kind=source
            $sql="SELECT c2d
                FROM EtoxMicromeEntity2DocumentBundle:Cytochrome2Document c2d
                WHERE c2d.cypsMention IN (:arrayNames) AND c2d.$orderBy is not NULL
                ORDER BY c2d.$orderBy desc
                ";
            $query = $this->_em->createQuery($sql);
            $query->setParameter("arrayNames", $arrayNames);
            $query->setMaxResults(1000);
            //$query->setParameter("arrayCanonicals", $arrayCanonicals);
        }
        else{ //we filter to documents of certain kind = source(pubmed, fulltext,...etc)
            $sql="SELECT e2d,d
                FROM EtoxMicromeEntity2DocumentBundle:Cytochrome2Document e2d
                JOIN e2d.document d
                WHERE e2d.cypsMention IN (:arrayNames) AND d.$orderBy is not NULL AND d.kind = :source
                ORDER BY d.$orderBy desc
                ";
            //ld($sql);
            $query = $this->_em->createQuery($sql);
            $query->setParameter("arrayNames", $arrayNames);
            //$query->setParameter("arrayCanonicals", $arrayCanonicals);
            $query->setParameter("source", $source);
            $query->setMaxResults(1000);
        }
        return $query;

    }

    public function findCytochrome2DocumentFromDocument($document)
    {
        //Function to search all the entities involved in a particular sentence in order to highlight them
        $documentId=$document->getId();

        $em = $this->getEntityManager();
        $consulta = $em->createQuery('
            SELECT c2d
            FROM EtoxMicromeEntity2DocumentBundle:Cytochrome2Document c2d
            WHERE c2d.document = :documentId
        ');
        $consulta->setParameter('documentId', $documentId);
        return $consulta->execute();
    }

    public function findCytochrome2DocumentFromDocumentId($documentId)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('
            SELECT c2d
            FROM EtoxMicromeEntity2DocumentBundle:Cytochrome2Document c2d
            WHERE c2d.document = :documentId
        ');
        $consulta->setParameter('documentId', $documentId);
        return $consulta->execute();
    }

    public function updateCytochrome2DocumentCuration($cytochrome2DocumentId, $action)
    {
        /*Here we get the cytochrome2Document and the action to take for the curation value.
        $action can be check or cross.
        If $action==check, then we have to add one to the curation field of the Cytochrome2Document register
        If $action==cross, then we have to substract one to the curation field of the Cytochrome2Document register

        After that, taking into account the curation value, we have to generate the html to render inside the curation
        */

        //ld($entity2DocumentId);
        //ldd($action);

        $em = $this->getEntityManager();
        $cytochrome2Document=$em->getRepository('EtoxMicromeEntity2DocumentBundle:Cytochrome2Document')->findOneById($cytochrome2DocumentId);
        if (!$cytochrome2Document) {
            throw $this->createNotFoundException(
                "Cannot curate this Cytochrome2Document $cytochrome2DocumentId"
            );
        }
        else{
            $curation=$cytochrome2Document->getCuration();
            if ($action=="check"){
                $cytochrome2Document->setCuration($curation + 1);
            }elseif($action=="cross"){
                $cytochrome2Document->setCuration($curation - 1);
            }
            $em->flush();
            $curationReturn=$cytochrome2Document->getCuration();
            return($curationReturn);
        }
        return ($curationReturn);
    }
}