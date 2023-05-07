<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function drop(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "DROP TABLE IF EXISTS book;";

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function create(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            CREATE TABLE book (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                title VARCHAR(255) NOT NULL,
                description CLOB NOT NULL,
                isbn CHAR(13) NOT NULL,
                author VARCHAR(255) NOT NULL,
                image_url VARCHAR(255) NOT NULL
            );
        ";

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function insert(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            INSERT INTO book VALUES
            (4,'The Joy of Debugging','\"The Joy of Debugging\" is a fun and motivational guide to the often-frustrating process of debugging code. The book is filled with humorous anecdotes, practical tips, and inspiring stories from experienced developers who have overcome even the most stubborn bugs. With a focus on positive thinking and a can-do attitude, \"The Joy of Debugging\" encourages readers to embrace the challenge of debugging and approach it with enthusiasm and creativity. Whether you are a seasoned developer or just starting out, \"The Joy of Debugging\" will help you unlock the joy and satisfaction that comes from successfully fixing a stubborn bug.','3333333333333','Haskell Backslash','https://ucarecdn.com/df36fae6-bdd3-4d1c-b36d-f6d521efd2e9/the_joy_of_debugging.png'),
            (5,'The Phantom Programmer: A D.B. Webber Adventure','A Shadowy Software Thriller and the first book of the D.B. Webber series. A software developer named Ada Lovejoy, is tasked with creating a new, cutting-edge program for a major tech company. But as she delves deeper into the project, strange things begin to happen. Code appears in the program that she does not remember writing, and her coworkers start to disappear one by one. Ada soon realizes that she''s being targeted by a mysterious hacker, known only as the Phantom Programmer, who seems to be able to manipulate the code itself. D.B. Webber is called in to solve the case. As he races to uncover the identity of the Phantom Programmer and stop them before they can cause any more harm, he realizes that he may be in over his head in a world of high-tech intrigue and danger.','2222222222222','Pythonia Jones','https://ucarecdn.com/e8bcdb1f-f42e-4876-aeb4-f0b7246876ed/d_b_webber.png'),
            (6,'When Code Breaks','\"When Code Breaks\" is a lighthearted, humorous look at the life of a programmer. The book follows the misadventures of Jake, a quirky and somewhat clumsy programmer, as he navigates the highs and lows of the tech industry. From debugging seemingly impossible code errors to dealing with difficult clients, his life is never dull. But despite the challenges he faces, Jake is always able to find humor in even the most frustrating situations. With plenty of jokes and relatable anecdotes, \"When Code Breaks\" is the perfect read for anyone who has ever spent hours staring at a screen trying to fix their broken code.','1111111111111','Bob Rossbach ','https://ucarecdn.com/c0894e3e-3694-4771-aabf-83a7f5a59770/when_code_breaks.png'),
            (7,'The History of Naming Variables','\"The History of Naming Variables\" is a hilarious take on the often-overlooked skill of naming variables. The book starts with a tongue-in-cheek examination of the ancient art of naming variables, including examples from Babylonian cuneiform tablets and Egyptian hieroglyphics. From there, it moves on to the more recent history of variable naming, including the evolution of programming languages and their associated naming conventions. Along the way, the book pokes fun at some of the more ridiculous variable names that have been used over the years. \"The History of Naming Variables\" is a must-read for anyone who has ever struggled to come up with the perfect variable name.','4444444444444','Dr. Hax','https://ucarecdn.com/1cfb66cd-77d2-4808-8aa3-89780239c885/history_of_naming_variables.png');
        ";

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
