<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;    
use Symfony\Component\Form\Extension\Core\Type\TextareaType; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 
use Doctrine\Common\Persistence\ObjectManager; 
use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;   
    
  
  

class BlogController extends AbstractController
{
    /**  
     * @Route("/blog", name="blog")
     */    
    public function index(ArticleRepository $repo)  // Avec cette injection de dépendance on a plus besoin  de la ligne commentée ci dessous 
    {

      //  $repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();
         

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles 
        ]); 

    }
   
              
     /**
     * @Route("/", name="home")   
     */  
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }
// lorsque l'utilisateur tape "127.0.0.1:8000/" c'est la page home.html.twig qui s'affiche

     /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null,Request $request, EntityManagerInterface $manager)
    {  
       // $article = new Article();  

        if(!$article){
             
            $article = new Article();  
         
        }    

        /*$form = $this->createFormBuilder($article)  
                     ->add('title')   
                     ->add('content')  
                     ->add('image')  
                     ->getForm();     */
        $form = $this->createForm(ArticleType::class, $article);    
    
        $form->handleRequest($request); 

        if($form->isSubmitted() && $form->isValid()){

           if(!$article->getId()){

            $article->setCreatedAt(new \DateTime());

           }
            
 
            $manager->persist($article); 
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);     


        }

        return $this->render('blog/create.html.twig', [ 
            'formArticle' => $form->createView() ,
            'editMode' => $article->getId() !== null  
        ]);         
    }

     /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show($id)
    {
        $repo = $this->getDoctrine()->getRepository(Article::class); 

        $article = $repo->find($id);

        return $this->render('blog/show.html.twig', ['article' => $article ]); 
    }



}

