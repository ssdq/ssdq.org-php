<?php
namespace App\Controller;

use App\Helper\APIHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /** @var APIHelper */
    private $srcomAPIHelper;

    public function __construct(APIHelper $srcomAPIHelper)
    {
        $this->srcomAPIHelper = $srcomAPIHelper;
    }

    public function index()
    {
        return $this->render('index.html.twig', [
            'latestRuns' => $this->srcomAPIHelper->getLatestRuns(),
            'worldRecords' => $this->srcomAPIHelper->getWorldRecords(),
        ]);
    }
}
