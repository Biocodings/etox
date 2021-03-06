<?php

namespace EtoxMicrome\DocumentBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DocumentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DocumentRepository extends EntityRepository
{
    public function getEvidencesFromDominioOriginMethod($dominio,$origen, $method, $entity)
    {
        return $this->getEvidencesFromDominioOriginMethodDQL($dominio,$origin, $method, $entity)->getResult();
    }

    public function getEvidencesFromDominioOriginMethodDQL($dominio,$origin, $method, $entity)
    {
        $query = $this->_em->createQuery("
            SELECT ee, ev, en, m, d, o
            FROM EvidenciaEntidadBundle:EvidenciaEntidad ee
            JOIN ee.evidencia ev
            JOIN ee.entidad en
            JOIN en.metodo m
            JOIN ev.dominio d
            JOIN ev.origen o
            WHERE o.tipo = :origin
            AND d.tipo = :dominio
            AND m.tipo = :method
        ");
        $query->setParameter('origin', $origin);
        $query->setParameter('dominio', $dominio);
        $query->setParameter('method', $method);

        return $query;
    }

    public function findAllSamePubmed($evidence)
    {
        $evidenceCode=$evidence->getCode();
        $arrayEvidenceCode=explode("_", $evidenceCode);
        $pubmedId=$arrayEvidenceCode[0];
        $searchterm="^".$pubmedId."_";
        $query = $this->_em->createQuery("
            SELECT ev
            FROM EvidenciaBundle:Evidencia ev
            WHERE ev.code LIKE :code
        ");
        $query->setParameter('code', $evidenceCode);

        return $query->getResult();
    }

    public function getDocumentFromDocumentWith($document)
    {
        //Function to search all the entities involved in a particular sentence in order to highlight them
        $sentenceId=$document->getSentenceId();
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('
            SELECT d
            FROM EtoxMicromeDocumentBundle:Document d
            WHERE d.sentenceId = :sentenceId
        ');
        $consulta->setParameter('sentenceId', $sentenceId);
        $consulta->setMaxResults(1);
        return $consulta->execute();
    }

    public function getDocumentFromSentenceId($sentenceId)
    {
        //Function to search a document given the unique sentenceId parameter
        $em = $this->getEntityManager();
        $consulta = $em->createQuery('
            SELECT d
            FROM EtoxMicromeDocumentBundle:Document d
            WHERE d.sentenceId = :sentenceId
        ');
        $consulta->setParameter('sentenceId', $sentenceId);
        $consulta->setMaxResults(1);
        $arrayDocument=$consulta->execute();
        return $arrayDocument[0];
    }
}
