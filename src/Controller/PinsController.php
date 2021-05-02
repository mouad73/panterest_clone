<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(PinRepository $pinRepository, EntityManagerInterface $em, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $pinRepository->createQueryBuilder('a');
        // dd($query);
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            Pin::NUM_ITEMS_PER_PAGE /*limit per page*/
        );
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        // dd($pagination);

        return $this->render('pins/index.html.twig', compact('pagination', 'pins'));
    }

    /**
     * @Route("/pins/create", name="app_pins_create", methods={"GET","POST"})
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {

        $pin = new Pin;
        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // dd($form->getData(), $pin);
            // $data = $form->getData();
            
            // $pin = new Pin;
            // $pin->setTitle($data['title']);
            // $pin->setDescription($data['description']);
            
            // $pin = $form->getData();
            // $mouad = $userRepo->findOneBy(['email' => 'john.bro@exampe.com']);
            
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin successfully created!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/create.html.twig',[
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show", methods="GET")
     */
    public function show(Pin $pin, PinRepository $pinRepository): Response
    {
        $pin = $pinRepository->find($pin->getId());

        return $this->render('pins/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="app_pins_edit", methods="GET|PUT")
     */
    public function edit(Pin $pin, Request $request, EntityManagerInterface $em): Response
    {
        // $form = $this->createFormBuilder($pin)
        //     ->add('title')
        //     ->add('description')
        //     ->getForm()
        // ;

        
        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();

            $this->addFlash('success', 'Pin successfully updated!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/edit.html.twig', [
            'form'  => $form->createView(),
            'pin'   => $pin
        ]);
    }


    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_delete", methods="DELETE")
     */
    public function delete(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {

        
        if($this->isCsrfTokenValid('pins_deletion_' . $pin->getId(), $request->request->get('csrf_token'))){
            $em->remove($pin);
            $em->flush();
            $this->addFlash('info', 'Pin successfully deleted!');
        }
        

        return $this->redirectToRoute('app_home');
    }
}
