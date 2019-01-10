<?php
namespace App\Controller;

use App\Helper\APIHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function index()
    {
        // @TODO Dependency injection
        $stack = \GuzzleHttp\HandlerStack::create();
        $stack->push(new \Kevinrob\GuzzleCache\CacheMiddleware(
            new \Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy(
                new \Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage(
                    new \Doctrine\Common\Cache\FilesystemCache(sys_get_temp_dir())
                ),
                1800
            )
        ), 'greedy-cache');
        $httpClient = new \GuzzleHttp\Client(['base_uri' => \Dsinn\SrcomApi\Client::DEFAULT_BASE_URI, 'handler' => $stack]);
        $apiClient = new \Dsinn\SrcomApi\Client($httpClient);
        $apiHelper = new APIHelper($apiClient);

        return $this->render('index.html.twig', [
            'latestRuns' => $apiHelper->getLatestRuns(),
            'worldRecords' => $apiHelper->getWorldRecords(),
        ]);
    }
}
