<?php

namespace EtoxMicrome\Entity2DocumentBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Compound2Term2DocumentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Compound2Term2DocumentRepository extends EntityRepository
{
    public function getAllRelationsFromSentenceId($sentenceId)
    {
        $sql="SELECT c2t2d
            FROM EtoxMicromeEntity2DocumentBundle:Compound2Term2Document c2t2d
            WHERE c2t2d.sentenceId = (:sentenceId)
            ";

        //ld($sql);
        $query = $this->_em->createQuery($sql);
        $query->setParameter("sentenceId", $sentenceId);
        return $query;

    }
    public function updateCompound2Term2DocumentCuration($compound2Term2DocumentId, $action)
    {
        //Explain for the homologous updateEntity2DocumentCuration at Entity2DocumentRepository
        /*Here we get the entity2Document and the action to take for the curation value.
        $action can be check or cross.
        If $action==check, then we have to add one to the curation field of the Entity2Document register
        If $action==cross, then we have to substract one to the curation field of the Entity2Document register

        After that, taking into account the curation value, we have to generate the html to render inside the curation
        */

        //ld($entity2DocumentId);
        //ldd($action);

        $em = $this->getEntityManager();
        $compound2Term2Document=$em->getRepository('EtoxMicromeEntity2DocumentBundle:Compound2Term2Document')->findOneById($compound2Term2DocumentId);
        if (!$compound2Term2Document) {
            throw $this->createNotFoundException(
                "Cannot curate this Compound2Term2Document $compound2Term2DocumentId"
            );
        }
        else{
            $curation=$compound2Term2Document->getCuration();
            if ($action=="check"){
                $compound2Term2Document->setCuration($curation + 1);
            }elseif($action=="cross"){
                $compound2Term2Document->setCuration($curation - 1);
            }
            $em->flush();
            $curationReturn=$compound2Term2Document->getCuration();
            return($curationReturn);
        }
        return ($curationReturn);
    }

    public function countCompound2TermRelations($compoundName)
    {
        $message="inside countCompound2TermRelations";
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(c2t2d.id) FROM EtoxMicromeEntity2DocumentBundle:Compound2Term2Document c2t2d where c2t2d.compoundName= :compoundName');
        $query->setParameter("compoundName", $compoundName);
        $count = $query->getSingleScalarResult();

        return $count;

    }

    public function getCuratedTermRelations()
    {
        $message="inside getCuratedTermRelations";
        $sql="SELECT c2t2d
            FROM EtoxMicromeEntity2DocumentBundle:Compound2Term2Document c2t2d
            WHERE c2t2d.curation > 0
            ORDER BY c2t2d.compoundName DESC, c2t2d.curation DESC
            ";
        $query = $this->_em->createQuery($sql);
        $arrayCuratedTermRelations=$query->getResult();
        return $arrayCuratedTermRelations;
    }

    public function findTerm2DocumentFromDocumentId($documentId)
    {
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('
            SELECT c2t2d
            FROM EtoxMicromeEntity2DocumentBundle:Compound2Term2Document c2t2d
            WHERE c2t2d.document = :documentId
        ');
        $consulta->setParameter('documentId', $documentId);
        return $consulta->execute();
    }

    public function findCompounds2Term2DocumentFromTerm($term, $dictionaryCompounds)
    {
        //Attention! This function returns 2 arrays. First with keys = compoundName and values= number of times (weight) of this relation
        //Second array have a dictionary with values = compoundName and values = dictionaryRelations
        //dictionaryRelations is an asociative array with keys = relationType and values = number of times that this type of relation has been established between this compoundName and the Term
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('
            SELECT c2t2d
            FROM EtoxMicromeEntity2DocumentBundle:Compound2Term2Document c2t2d
            WHERE c2t2d.term = :term
        ');
        $consulta->setParameter('term', $term);
        $consulta->setMaxResults(15);
        $arrayCompounds2Term2Documents = $consulta->execute();
        //We search the arrayCompounds2Term2Documents and only return the components that are present in the $dictionaryCompounds argument (compounds that already are part of the interaction network)
        $tmpArray=array();//Associative array with the keys="compoundName" and the values="number of times present"
        $tmpArrayTypeRelations=array();//Associative array with the keys="compoundName" and the values=dictionaryRelations
        foreach($arrayCompounds2Term2Documents as $compound2Term2Document){
            $compoundName=$compound2Term2Document->getCompoundName();
            $relationType=$compound2Term2Document->getRelationType();
            if(array_key_exists($compoundName, $dictionaryCompounds)){
                if (array_key_exists($compoundName, $tmpArray)){
                    $tmpArray[$compoundName]=$tmpArray[$compoundName]+1;
                    //If there is already a tmpArray with this compoundName, there should be already a $tmpArrayTypeRelations that we should update with this new or repeated relationType
                    $dictionaryRelations=$tmpArrayTypeRelations[$compoundName];
                    if (array_key_exists($relationType, $dictionaryRelations)){
                        //We update the entry
                        $dictionaryRelations[$relationType]=$dictionaryRelations[$relationType]+1;
                        $tmpArrayTypeRelations[$compoundName]=$dictionaryRelations;
                    }else{
                        //We create a new entry
                        $dictionaryRelations=$tmpArrayTypeRelations[$compoundName];
                        $dictionaryRelations[$relationType]=1;
                        $tmpArrayTypeRelations[$compoundName]=$dictionaryRelations;
                    }
                }else{
                    $dictionaryRelations=array();
                    $tmpArray[$compoundName]=1;
                    $dictionaryRelations[$relationType]=1;
                    $tmpArrayTypeRelations[$compoundName]=$dictionaryRelations;
                }
            }
        }
        $returnArray=array($tmpArray,$tmpArrayTypeRelations);
        return ($returnArray);
    }
}
