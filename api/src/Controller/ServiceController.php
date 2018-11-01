<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Service;
use App\Form\ServiceType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\FormInterface;

class ServiceController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/services", methods={"POST"})
     * @param $request Request
     * @param $validator ValidatorInterface
     */
    public function services(Request $request, ValidatorInterface $validator)
    {
        $service = new Service();

        // set default values 
        $service->setCreatedDate(new \DateTime());
        $service->setUpdatedDate(new \DateTime());
        $service->setActive(1);

        // decode json data php array
        $data = json_decode(
            $request->getContent(), 
            true
        );
        
        $form = $this->createForm(ServiceType::class, $service);
    
        $form->submit($data);
        
        // check form is valid or not
        if (false === $form->isValid()) {
            $errors = $this->getErrorMessages($form);
            
            return new JsonResponse(
                [
                    'status' => false,
                    'errors' => $errors,
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // if pass validation then save data
        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        // get saved service info
        $savedService = $this->getDoctrine()
        ->getRepository(Service::class)
        ->find($service->getId());
        
        $data = (object)[
            'id' => $savedService->getId(),
            'title' => $savedService->getTitle(),
            'description' => $savedService->getDescription(),
            'end_date' => $savedService->getEndDate(),
        ];
        
        return new JsonResponse(
            [
                'status' => true,
                'data' => $data
            ],
            JsonResponse::HTTP_CREATED
        );
    }
    
    /**
     * generate human readble form errors
     * @param $form FormInterface
     */
    private function getErrorMessages(FormInterface $form)
    {
        $errors = array();
    
        //this part get global form errors (like csrf token error)
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
    
        //this part get errors for form fields
        /** @var Form $child */
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $options = $child->getConfig()->getOptions();
                //there can be more than one field error, that's why implode is here
                $errors[$options['label'] ? $options['label'] : ucwords($child->getName())] = implode('; ', $this->getErrorMessages($child));
            }
        }
    
        return $errors;
    }
}