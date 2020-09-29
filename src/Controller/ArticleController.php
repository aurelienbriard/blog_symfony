<?php

namespace App\Controller;

// Injection de mes dépendances
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;
use App\Entity\Article;

/**
 * @Route("/article", name="article_")
 */
class ArticleController extends AbstractController {

  /**
   * @var EntityManagerInterface
   */
  private $em;

  /**
   * @var ArticleRepository
   */
  private $repo;

  public function __construct(EntityManagerInterface $entityManager, ArticleRepository $ArticleRepository)
  {
    $this->em = $entityManager;
    $this->repo = $ArticleRepository;
  }

  /**
   * @Route("/", name="index")
   */
  public function index() {
    $articles = $this->repo->findAll();
    return $this->render( 'article/index.html.twig', ['articles'=>$articles] );
  }

  /**
   * @Route("/new", name="new")
   */
  public function new(Request $request) {

    // creation d'un objet article
    $newArticle = new Article();
    // creation d'un formulaire
    $form = $this -> createForm(ArticleType::class, $newArticle);

    $form -> handleRequest($request);

    // action déclenchée si le formulaire est soumis
    if ( $form -> isSubmitted() ){
      $this->em->persist($newArticle); // je persiste en base
      $this->em->flush();
      return $this->redirectToRoute('article_index');  // je redirige vers l'index après soumission du form
    }

    return $this->render('article/new.html.twig', [
      'article' => $newArticle,
      'form' => $form->createView(),
    ]);

  }

  /**
   * @Route("/edit/{id}", name="edit")
   */
  public function edit(Request $request, Article $article) {

    // creation d'un formulaire
    $form = $this -> createForm(ArticleType::class, $article);

    $form -> handleRequest($request);

    // action déclenchée si le formulaire est soumis
    if ( $form -> isSubmitted() ){
      $this->em->flush();
      return $this->redirectToRoute('article_index'); // je redirige vers l'index après soumission du form

    }

    return $this->render('article/edit.html.twig', [
      'article' => $article,
      'form' => $form->createView(),
    ]);

  }

  /**
   * @Route("/{id}", name="show", requirements={"id"="\d+"})
   */
  public function show(Article $article){
    return $this->render( 'article/show.html.twig', ['article' => $article] );
  }

  /**
   * @Route("/delete/{id}", name="delete", requirements={"id"="\d+"})
   */
  public function delete(Article $article){
    $this->em->remove($article);
    $this->em->flush();
    return $this->redirectToRoute('article_index'); // je redirige vers l'index après soumission du form
  }

}
