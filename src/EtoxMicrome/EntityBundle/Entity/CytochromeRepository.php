<?php

namespace EtoxMicrome\EntityBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CytochromeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CytochromeRepository extends EntityRepository
{
    public function getEntityFromId($entityId)
    {
        $message="Inside getEntityFromId at CytochromeRepository";
        $query = $this->_em->createQuery("
            SELECT a
            FROM EtoxMicromeEntityBundle:Cytochrome a
            WHERE a.id= :entityId
        ");
        $query->setParameter('entityId', $entityId);
        $query->setMaxResults(2);
        $cytochrome=$query->getResult();
        if(count($cytochrome)==0){
            $errorMessage="There is no entity with that name ('$entityName')";
            ldd($errorMessage);
        }
        if(count($cytochrome)!=1){
            $errorMessage="There are more than one entityName for '$entityName'";
            //ld($errorMessage);
            return $cytochrome[0];
        }
        //We return all the Compounds with the entityName given. By now we supose its only one entity!!!
        $entity=$cytochrome[0];
        return $entity;
    }

    public function getEntityFromName($entityName)
    {
        $message="Inside getEntityFromName at CytochromeRepository";
        $query = $this->_em->createQuery("
            SELECT a
            FROM EtoxMicromeEntityBundle:Cytochrome a
            WHERE LOWER(a.name) = :entityName
        ");
        $query->setParameter('entityName', strtolower($entityName));
        $query->setMaxResults(2);
        $cytochrome=$query->getResult();
        if(count($cytochrome)==0){
            $errorMessage="There is no entity with that name ($entityName)";
            $entity=array();
            return $entity;
        }
        if(count($cytochrome)!=1){
            $errorMessage="There are more than one entityName for '$entityName'";
            //ld($errorMessage);
            return $cytochrome[0];
        }
        //We return only one entity because we will do a query expansion later on.
        $entity=$cytochrome[0];
        return $entity;
    }

    public function getIdFromGenericField($key, $value, $arrayEntityId)
    {
        $message="Inside getIdFromGenericField at CytochromeRepository";
        $query = $this->_em->createQuery("
            SELECT c
            FROM EtoxMicromeEntityBundle:Cytochrome c
            WHERE c.$key= :value
        ");
        $query->setParameter('value', $value);
        $compounds=$query->getResult();
        if(count($compounds)==0){
            return $arrayEntityId;
        }
        else{
            $errorMessage="There are at least one Compound for $key = $value";
            //ld($errorMessage);
            foreach($compounds as $compound){
                $arrayEntityId[]=$compound->getId();
            }
        }
        //We return all the Compounds with the entityName given. By now we supose its only one entity!!!
        return $arrayEntityId;
    }

    public function searchEntityGivenAnId($entityId)
    {
        $message="Inside searchEntityGivenAnId at CytochromeRepository";
        //ldd($message);
        $query = $this->_em->createQuery("
            SELECT c
            FROM EtoxMicromeEntityBundle:Cytochrome c
            where c.entityId= :entityId
        ");
        $query->setParameter('entityId', $entityId);
        $query->setMaxResults(2);
        $compound=$query->getResult();
        if(count($compound)==0){
            $errorMessage="There is no entity with that entityId ($entityId)";
            $entity=array();
            return $entity;
        }
        if(count($compound)!=1){
            $errorMessage="There are more than one entityId for '$entityId'";
            //ld($errorMessage);
            return $compound[0];
        }
        //We return only one entity. Later on we will make the query expansion so we will collect all of them
        $entity=$compound[0];
        return $entity;
    }

    public function searchEntityGivenACanonical($canonical, $taxId='9606')
    {
        $message="Inside searchEntityGivenACanonical at CytochromeRepository";
        $query = $this->_em->createQuery("
            SELECT c
            FROM EtoxMicromeEntityBundle:Cytochrome c
            where c.canonical= :canonical
            and c.tax= :taxId
        ");
        $query->setParameter('canonical', $canonical);
        $query->setParameter('taxId', $taxId);
        $query->setMaxResults(2);
        $compound=$query->getResult();
        if(count($compound)==0){
            $errorMessage="There is no entity with that canonical ($canonical)";
            $entity=array();
            return $entity;
        }
        if(count($compound)!=1){
            $errorMessage="There are more than one canonical for '$canonical'";
            //ld($errorMessage);
            return $compound[0];
        }
        //We return only one entity. Later on we will make the query expansion so we will collect all of them
        $entity=$compound[0];
        return $entity;
    }

    public function findOneByCanonicalTax($canonical,$ncbiTaxId)
    {
        $query = $this->_em->createQuery("
            SELECT c
            FROM EtoxMicromeEntityBundle:Cytochrome c
            where c.canonical= :canonical
            and c.tax= :ncbiTaxId
        ");
        $query->setParameter('canonical', $canonical);
        $query->setParameter('ncbiTaxId', $ncbiTaxId);
        $query->setMaxResults(2);
        $cytochrome=$query->getResult();
        if(count($cytochrome)==0){
            $errorMessage="There is no entity with that canonical ($canonical) and ncbiTaxId ($ncbiTaxId)";
            //ld($errorMessage);
            $entity=array();
            return $entity;
        }
        if(count($cytochrome)!=1){
            $errorMessage="There are more than one entity for that canonical ($canonical) and ncbiTaxId ($ncbiTaxId)";
            //We return the first one until change of strategy
            //ld($errorMessage);
            return $cytochrome[0];

        }
        //We return only one entity. Later on we will make the query expansion so we will collect all of them
        $entity=$cytochrome[0];
        return $entity;
    }

    public function taxIdIsInArray($ncbiTaxId, $arraycytochromes){
        $isInArray=false;
        foreach($arraycytochromes as $cytochrome){
            if($ncbiTaxId==$cytochrome->getTax()){
                $isInArray==true;
            }
        }
        return $isInArray;
    }

    public function getCytochromeSummary($initial, $orderBy)
    {
        $message="Here";
        if($initial=="0"){

        }elseif($initial=="-"){

        }else{
            $initialUpper=strtoupper($initial);
            $query = $this->_em->createQuery("
                SELECT c
                FROM EtoxMicromeEntityBundle:Cytochrome c
                WHERE c.name like  :initial
                OR c.name like  :initialUpper
                ORDER BY c.$orderBy
            ");
            $query->setParameter("initial", $initial."%");
            $query->setParameter("initialUpper", $initialUpper."%");
        }

        $arrayCytochromes=$query->getResult();
        return $arrayCytochromes;
    }

     public function getCytochromeList($initial, $specie)
    {
        //Extracts a list of cytochrome2document, of the initial given and a given specie
        $em = $this->getEntityManager();
        //To find out if we are searching for CYP...:
        $isCyp=substr_compare($initial,"cyp",0,3,true);
        $isP=substr_compare($initial,"p",0,1,true);
        if($initial=="0"){
            $query = $em->createQuery('
                SELECT c
                FROM EtoxMicromeEntity2DocumentBundle:Cytochrome2Document c2d
                JOIN EtoxMicromeEntityBundle:Cytochrome c
                WHERE (c2d.cypsMention like \'1%\' AND c.tax = :specie AND c2d.cypsMention = c.name)
                OR (c2d.cypsMention like \'2%\' AND c.tax = :specie AND c2d.cypsMention = c.name)
                OR (c2d.cypsMention like \'3%\' AND c.tax = :specie AND c2d.cypsMention = c.name)
                OR (c2d.cypsMention like \'4%\' AND c.tax = :specie AND c2d.cypsMention = c.name)
                OR (c2d.cypsMention like \'5%\' AND c.tax = :specie AND c2d.cypsMention = c.name)
                OR (c2d.cypsMention like \'6%\' AND c.tax = :specie AND c2d.cypsMention = c.name)
                OR (c2d.cypsMention like \'7%\' AND c.tax = :specie AND c2d.cypsMention = c.name)
                OR (c2d.cypsMention like \'8%\' AND c.tax = :specie AND c2d.cypsMention = c.name)
                OR (c2d.cypsMention like \'9%\' AND c.tax = :specie AND c2d.cypsMention = c.name)
                OR (c2d.cypsMention like \'0%\' AND c.tax = :specie AND c2d.cypsMention = c.name)
                order by c.name
            ');
        }elseif($isCyp==0){
            $query = $em->createQuery('
                SELECT c
                FROM EtoxMicromeEntity2DocumentBundle:Cytochrome2Document c2d
                JOIN EtoxMicromeEntityBundle:Cytochrome c
                WHERE (c2d.cypsMention like :initial or c2d.cypsMention like :initialUpper)
                AND c.tax = :specie
                AND c2d.cypsMention = c.name
                order by c.name
            ');
            $initialUpper=strtoupper($initial);
            $query->setParameter('initial', $initial."%");
            $query->setParameter('initialUpper', $initialUpper."%");
        }else{
            $query = $em->createQuery('
                SELECT c
                FROM EtoxMicromeEntity2DocumentBundle:Cytochrome2Document c2d
                JOIN EtoxMicromeEntityBundle:Cytochrome c
                WHERE (c2d.cypsMention like :initial or c2d.cypsMention like :initialUpper)
                AND c.tax = :specie
                AND c2d.cypsMention = c.name
                order by c.name
            ');
            $initialUpper=strtoupper($initial);
            $query->setParameter('initialUpper', $initialUpper."%");
            $query->setParameter('initial', $initial."%");
        }
        $query->setParameter('specie', $specie);

        return $query->execute();
    }
}