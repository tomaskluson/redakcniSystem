<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Kontroler pro práci s články.
 * @package App\Controller
 */
class ArticleController extends AbstractController
{
    /** @var ArticleRepository Repozitář pro správu článků. */
    private $articleRepository;

    /**
     * Konstruktor kontroleru pro práci s články
     * @param ArticleRepository $articleRepository automaticky injektovaný repositář pro správu článků
     */
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * Načte a předá seznam všech článků do šalbony.
     * @return Response HTTP odpověď
     * @Route("/seznam-clanku", name="article_list")
     */
    public function list(): Response
    {
        return $this->render('article/list.html.twig', ['articles' => $this->articleRepository->findAll()]);
    }

    /**
     * Odstraní článek podle jeho URL.
     * @param string|null $url URL článku
     * @return Response HTTP odpověď
     * @Route("/odstranit/{url}", name="remove_article")
     * @throws ORMException Jestliže nastane chyba při mazání článku.
     */
    public function remove(string $url = null): Response
    {
        $this->articleRepository->removeByUrl($url);
        $this->addFlash('notice', 'Článek byl úspěšně odstraněn.');
        return $this->redirectToRoute('article_list');
    }

    /**
     * Vytváří a zpracovává formulář pro editaci článku podle jeho URL.
     * @param string|null $url URL článku
     * @param Request $request HTTP požadavek
     * @return Response HTTP odpověď
     * @Route("/editor/{url}", name="article_editor")
     * @throws ORMException Jestliže nastane chyba při ukládání článku.
     */
    public function editor(string $url, Request $request): Response
    {
        if($url){ //pokud byla url zadána
            //url článku nenalezena, vytvoření nové url adressy
            if(!($article = $this->articleRepository->findOneByUrl($url))){
                $this->addFlash('warning', 'Článek se zadanou URL nebyl nalezen!');
                $article = (new Article())->setUrl($url);
            }
        } else {
            //nejedná se o edit, vytvoření nového článku
            $article = new Article();
        }

        //Vytváření editačního formuláře
        $editorForm = $this->createFormBuilder($article)
            ->add('title', null, ['label' => 'Titulek'])
            ->add('url', null, ['label' => 'URL'])
            ->add('description', null, ['label' => 'Popisek'])
            ->add('content', null, ['label' => 'Obsah', 'required' => false])
            ->add('submit', SubmitType::class, ['label' => 'Uložit článek'])
            ->getForm();

        // Zpracování editačního formuláře
        $editorForm->handleRequest($request);
        if($editorForm->isSubmitted() && $editorForm->isValid()) {
            $this->articleRepository->save($article);
            $this->addFlash('notice', 'Článek byl úspěšně uložen.');
            return $this->redirectToRoute('article', ['url' => $article->getUrl()]);
        }

        //Předání editačního formuláře do šablony
        return $this->render('article/editor.html.twig', ['editorForm' => $editorForm->createView()]);
    }

    /**
     * * Načte článek podle jeho URL a předá jej do šablony.
     * Pokud není nastavená URL, nastaví se jí hodnota pro výchozí článek.
     * @param Article $article článek
     * @return Response HTTP odpověď
     * @throws NotFoundHttpException Jestliže článek s danou URL nebyl nalezen.
     * @Route("/{url?%default_article_url%}", name="article")
     * @Entity("article", expr="repository.findOneByUrl(url)")
     */
    public function index(Article $article): Response
    {
        return $this->render('article/index.html.twig', [
            'article' => $article,
        ]);
    }
}
