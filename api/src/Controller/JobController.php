<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Job;
use App\Entity\City;
use App\Form\JobType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Form\FormInterface;

class JobController extends AbstractController
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
     * @Route("/jobs", methods={"POST"})
     * @param $request Request
     * @param $validator ValidatorInterface
     */
    public function jobs(Request $request, ValidatorInterface $validator)
    {
        $job = new Job();
        $city = new City();
        // set default values 
        $job->setCreatedDate(new \DateTime());
        $job->setUpdatedDate(new \DateTime());
        $job->setActive(1);

        // decode json data php array
        $data = json_decode(
            $request->getContent(), 
            true
        );
        $zipCodes = $this->entityManager->getRepository(City::class)->getZipCodes();
        
        $form = $this->createForm(JobType::class, $job);
    
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

        if (!in_array($form['zip_code']->getData(), $zipCodes)) {
            return new JsonResponse(
                [
                    'status' => false,
                    'errors' => (object)['zip_code' => 'Invalid data'],
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        // if pass validation then save data
        
        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        // get saved service info
        $savedJob = $this->getDoctrine()
        ->getRepository(Job::class)
        ->find($job->getId());
        
        $data = (object)[
            'id' => $savedJob->getId(),
            'title' => $savedJob->getTitle(),
            'description' => $savedJob->getDescription(),
            'end_date' => $savedJob->getEndDate(),
            'city' => $savedJob->getCityId()->getName(),
            'service' => $savedJob->getServiceId()->getName(),
            'zip_code' => $savedJob->getZipCode()
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