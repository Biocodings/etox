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
}