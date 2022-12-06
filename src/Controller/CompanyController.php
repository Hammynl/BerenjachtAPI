<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class CompanyController
{
    public function number(): Response
    {


        return new Response(
            '<html><body>Company ID: '. 5 .'</body></html>'
        );
    }
}