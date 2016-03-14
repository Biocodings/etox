<?php

namespace EtoxMicrome\Entity2AbstractBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Gene2AbstractRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Gene2AbstractRepository extends EntityRepository
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


    public function getGene2AbstractFromGeneID_orderby($arrayGeneIds, $orderBy)
    {
        return $this->getGene2AbstractFromGeneIDsDQL($arrayGeneIds, $orderBy)->getResult();
    }

    public function getGene2AbstractsFromGeneIDsDQL($arrayGeneIds, $orderBy)
    {//("hepatotoxicity","CompoundDict",arrayEntityId)
        $message="inside getGene2AbstractFromGeneIDsDQL";
        $field="hepatotoxicity";
        $valToSearch=$this->getValToSearch($field);//"i.e hepval, embval... etc"
        $orderBy_backup=$orderBy;
        if ($orderBy=="hepval" or $orderBy=="score"){
            $orderBy="hepval desc";
        }elseif($orderBy=="svmConfidence"){
            $orderBy="svmConfidence desc";
        }elseif($orderBy=="pattern"){
            $orderBy="patternCount asc";
        }elseif($orderBy=="term"){
            $orderBy="hepTermVarScore asc";
        }elseif($orderBy=="rule"){
            $orderBy="ruleScore asc";
        }elseif($orderBy=="curation"){
            $orderBy="curation desc";
        }
        /*$sql="SELECT e2a,a
            FROM EtoxMicromeEntity2AbstractBundle:Entity2Abstract e2a
            JOIN e2a.abstracts a
            WHERE e2a.name IN (:arrayEntityName)
            AND e2a.qualifier = :entityType
            AND a.$valToSearch is not NULL
            ORDER BY a.$valToSearch desc
            ";
        */

        $sql="SELECT g2a
                FROM EtoxMicromeEntity2AbstractBundle:Gene2Abstract g2a
                WHERE g2a.geneId IN (:arrayGeneIds)
                ORDER BY g2a.$orderBy
                ";

        $query = $this->_em->createQuery($sql);
        $query->setParameter("arrayGeneIds", $arrayGeneIds);

        $query->setMaxResults(10000);
        return $query;

    }

}
