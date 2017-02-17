<?php

namespace EtoxMicrome\EntityBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * HepatotoxKeywordRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HepatotoxKeywordRepository extends EntityRepository
{
    public function getEntityFromId($entityId)
    {
        $message="Inside getEntityFromId at TermRepository";
        $query = $this->_em->createQuery("
            SELECT a
            FROM EtoxMicromeEntityBundle:HepatotoxKeyword a
            WHERE a.id= :entityId
        ");
        $query->setParameter('entityId', $entityId);
        $query->setMaxResults(2);
        $term=$query->getResult();
        if(count($term)==0){
            $errorMessage="There is no entity with that name ('$entityName')";
        }
        if(count($term)!=1){
            $errorMessage="There are more than one entityName for '$entityName'";
        }
        //We return all the Compounds with the entityName given. By now we supose its only one entity!!!
        $entity=$term[0];
        return $entity;
    }

    public function getEntityFromName($entityName)
    {
        $message="Inside getEntityFromName at TermRepository";
        $query = $this->_em->createQuery("
            SELECT a
            FROM EtoxMicromeEntityBundle:HepatotoxKeyword a
            WHERE LOWER(a.term) = :entityName
        ");
        $query->setParameter('entityName', strtolower($entityName));
        $query->setMaxResults(2);
        $term=$query->getResult();
        if(count($term)==0){
            $errorMessage="There is no entity with that name ($entityName)";
            $entity=array();
            return $entity;
        }
        if(count($term)!=1){
            $errorMessage="There are more than one entityName for '$entityName'";
        }
        //We return all the CompoundDict with the entityName given. By now we supose its only one entity!!!
        $entity=$term[0];
        return $entity;
    }

    public function getIdFromGenericField($key, $value, $arrayEntityId)
    {
        $message="Inside getEntityIdFromName at HepatotoxKeywordRepository";
        $query = $this->_em->createQuery("
            SELECT h
            FROM EtoxMicromeEntityBundle:HepatotoxKeyword h
            WHERE h.$key= :value
        ");
        $query->setParameter('value', $value);
        $query->setMaxResults(2);
        $compounds=$query->getResult();
        if(count($compounds)==0){
            return $arrayEntityId;
        }
        else{
            $errorMessage="There are at least one hepatotoxTerm for $key = $value";
            //ld($errorMessage);
            foreach($compounds as $compound){
                $arrayEntityId[]=$compound->getId();
            }
        }
        //We return all the Compounds with the entityName given. By now we supose its only one entity!!!
        return $arrayEntityId;
    }
}
