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
        return $this->render('index.html.twig');
    }

    public function latestRuns()
    {
        return $this->render('ajax/latest_runs.html.twig', ['runs' => $this->srcomAPIHelper->getLatestRuns()]);
    }

    public function worldRecords()
    {
        return $this->render('ajax/world_records.html.twig', ['runs' => $this->srcomAPIHelper->getWorldRecords()]);
    }
}
