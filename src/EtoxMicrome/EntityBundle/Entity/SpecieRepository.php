<?php

namespace EtoxMicrome\EntityBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SpecieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SpecieRepository extends EntityRepository
{
    public function getEntityFromId($entityId)
    {
        $message="Inside getEntityFromId at SpecieRepository";
        $query = $this->_em->createQuery("
            SELECT a
            FROM EtoxMicromeEntityBundle:Specie a
            WHERE a.id= :entityId
        ");
        $query->setParameter('entityId', $entityId);
        $query->setMaxResults(2);
        $specie=$query->getResult();
        if(count($specie)==0){
            $errorMessage="There is no entity with that name ('$entityName')";
            ldd($errorMessage);
        }
        if(count($specie)!=1){
            $errorMessage="There are more than one entityName for '$entityName'";
            ldd($errorMessage);
        }
        //We return all the Compounds with the entityName given. By now we supose its only one entity!!!
        $entity=$specie[0];
        return $entity;
    }

    public function getEntityFromName($entityName)
    {
        $message="Inside getEntityFromName at SpecieRepository";
        $query = $this->_em->createQuery("
            SELECT a
            FROM EtoxMicromeEntityBundle:Specie a
            WHERE a.name= :entityName
        ");
        $query->setParameter('entityName', $entityName);
        $query->setMaxResults(2);
        $specie=$query->getResult();
        if(count($specie)==0){
            $errorMessage="There is no entity with that name ($entityName)";
            ldd($errorMessage);
        }
        if(count($specie)!=1){
            $errorMessage="There are more than one entityName for '$entityName'";
            ldd($errorMessage);
        }
        //We return all the CompoundDict with the entityName given. By now we supose its only one entity!!!
        $entity=$specie[0];
        return $entity;
    }
}
