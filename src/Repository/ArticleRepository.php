<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repositář pro správu článků v redakčním systému.
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class ArticleRepository extends ServiceEntityRepository
{
    /** @inheritdoc */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Vrátí článek z databáze podle jeho URL.
     * @param string $url URl článku
     * @return Article|null první článek, který má danou URL nebo null pokud takový neexistuje
     */
    public function findOneByUrl(string $url): ?Article
    {
        return $this->findOneBy(['url' => $url]);
    }

    /**
     * Uloží článek do systému.
     * Pokud není nastaveno ID, vloží nový, jinak provede editaci.
     * @param Article $article článek
     * @throws ORMException Jestliže nastane chyba při ukládání článku.
     */
    public function save(Article $article): void
    {
        $this->getEntityManager()->persist($article);
        $this->getEntityManager()->flush($article);
    }

    /**
     * Odstraní článek s danou URL.
     * @param string $url URL článku
     * @throws ORMException Jestliže nastane chyba při mazání čánku.
     */
    public function removeByUrl(string $url): void
    {
        if(($article = $this->findOneByUrl($url))) {
            $this->getEntityManager()->remove($article);
            $this->getEntityManager()->flush();
        }
    }
}
