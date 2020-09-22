<?php

namespace App\Controller;

use DateTime;
use App\Entity\Post;
use App\Entity\Topic;
use App\Form\PostType;
use App\Form\TopicType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ForumController extends AbstractController
{
    /**
     * @Route("/", name="forum_home")
     */
    public function home()
    {
        return $this->render('forum/home.html.twig');

    }

    /**
    * @Route("/list", name="list_topics")
    */
    public function listTopics()
    {       
        $topics = $this->getDoctrine()
        ->getRepository(Topic::class)
        ->getAll();

    return $this->render('forum/topics/list.html.twig', [
        'topics' => $topics,
    ]);  

    }
    /**
     * @Route("/add", name="add_topic")
     * @Route("/edit/{id}", name="edit_topic")
     */
    public function add(Topic $topic = null, Request $request, EntityManagerInterface $manager){
    // just setup a fresh $task object (remove the example data)
        if(!$topic){
        $topic = new Topic();
    }

        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        $topic->setUser($this->getUser());
        $date = new \DateTime('now');
        $topic->setDate($date);
    // $form->getData() holds the submitted values
    // but, the original `$task` variable has also been updated
        $manager->persist($topic);
        $manager->flush();
    // ... perform some action, such as saving the task to the database
    // for example, if Task is a Doctrine entity, save it!
    // $entityManager = $this->getDoctrine()->getManager();
    // $entityManager->persist($task);
    // $entityManager->flush();

        return $this->redirectToRoute('list_topics');
    }
        return $this->render('forum/topics/add.html.twig', [
        'FormTopic' => $form->createView(),
        'editMode' => $topic->getId() !==null
    ]);
    }

    /**
     * @Route("/show/{id}", name="topic_show", methods="GET", requirements={"id":"\d+"})
     */   
    public function show(Topic $topic): Response {


    return $this->render('forum/topics/show.html.twig', ['topic' => $topic,
                                                        ]); 
    }

        /**
     * @Route("/response/{topic_id}", name="add_post")
     * @Route("/edit/{post_id}", name="edit_post")
     * @ParamConverter("topic", options={"id" = "topic_id"})
     * @ParamConverter("post", options={"id" = "post_id"})
     */
    public function repondre(Post $post = null,Topic $topic, Request $request, EntityManagerInterface $manager){
        // just setup a fresh $task object (remove the example data)
            if(!$post){
            $post = new Post();
        }
    
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {

            $post->setTopic($topic);

            $post->setUser($this->getUser());

            $date= new \DateTime('now');

            $post->setDate($date);
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
            $manager->persist($post);
            $manager->flush();
        // ... perform some action, such as saving the task to the database
        // for example, if Task is a Doctrine entity, save it!
        // $entityManager = $this->getDoctrine()->getManager();
        // $entityManager->persist($task);
        // $entityManager->flush();
    
            return $this->redirectToRoute('list_topics');
        }
            return $this->render('forum/posts/add.html.twig', [
            'FormPost' => $form->createView(),
            'editMode' => $post->getId() !==null
        ]);
        }
        /**
         * @Route("/delete/{id}", name="post_delete")
         */
        public function deletePost (Post $post, EntityManagerInterface $manager){

            $manager->remove($post);
            $manager->flush();

            return $this->redirectToRoute('list_topics');
            }
        /**
         * @Route("/flush/{id}", name="topic_delete")
         */
        public function deleteTopic (Topic $topic, EntityManagerInterface $manager){

            $post = $topic->getPosts();

            foreach ($post as $posts) {
                $manager->remove($post);

            }


            $manager->remove($topic);
            $manager->flush();

            return $this->redirectToRoute('list_topics');
            }
    
}

