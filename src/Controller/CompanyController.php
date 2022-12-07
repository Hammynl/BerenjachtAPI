<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{


    #[Route('/company', name: 'company_route')]
    public function index(Request $request, RateLimiterFactory $companyRouteLimiter, ManagerRegistry $doctrine): Response
    {
        $helper = new Helper();
        $helper->addRateLimiter($companyRouteLimiter, $request);

        $companies = $doctrine->getRepository(Company::class)->findAll();

        $companyCollection = $this->createJsonResponse($companies);

        return $this->json($companyCollection);
    }


    #[Route('/company', name: 'company_route_id')]
    public function show(Request $request, RateLimiterFactory $companyRouteIdLimiter, ManagerRegistry $doctrine, int $id): Response
    {
        $helper = new Helper();
        $helper->addRateLimiter($companyRouteIdLimiter, $request);

        $company = $doctrine->getRepository(Company::class)->find($id);

        if (!$company) {
            throw $this->createNotFoundException(
                'No company found for id '. $id
            );
        }

        $companyCollection = $this->createJsonResponse([$company]);

        return $this->json($companyCollection);
    }


    #[Route('/company/inrange', name: 'company_route_inrange')]
    public function inrange(Request $request, RateLimiterFactory $companyRouteLimiter, ManagerRegistry $doctrine): Response
    {
        $helper = new Helper();
        $helper->addRateLimiter($companyRouteLimiter, $request);

        $companies = $doctrine->getRepository(Company::class)->findAll();

        $lat = $request->get('latitude');
        $long = $request->get('longitude');
        $range = $request->get('range');


        $companiesInRange = array();

        foreach ($companies as $item)
        {
            if($helper->isInRange($item->getLatitude(),$item->getLongitude(),$lat, $long, $range))
            {
                $companiesInRange[] = $item;
            }
        }

        $companyCollection = $this->createJsonResponse($companiesInRange);

        return $this->json($companyCollection);
    }



    function createJsonResponse(array $companies): array
    {

        $companyCollection = array();

        foreach($companies as $item) {
            $companyCollection[] = array(
                'id' => $item->getId(),
                'company_name' => $item->getName(),
                'street' => $item->getStreet(),
                'street_number' => $item->getStreetNumber(),
                'postal_code' => $item->getPostalCode(),
                'city' => $item->getCity(),
                'country' => $item->getCountry(),
                'latitude' => $item->getLatitude(),
                'longitude' => $item->getLongitude(),
                'email' => $item->getEmail(),
            );
        }

        return $companyCollection;
    }

}
