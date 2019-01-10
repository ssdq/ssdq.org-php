<?php
namespace App\Helper;

use Dsinn\SrcomApi\Client\DataTypes\Run;
use Dsinn\SrcomApi\Client\DataTypes\Status;
use Dsinn\SrcomApi\Client\Getters\Runs;

class APIHelper
{
    const LATEST_RUNS_DEFAULT_LIMIT = 10;

    /** @var \Dsinn\SrcomApi\Client */
    private $apiClient;

    public function __construct(\Dsinn\SrcomApi\Client $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function getLatestRuns(int $limit = self::LATEST_RUNS_DEFAULT_LIMIT): array
    {
        try {
            $runs = [];
            foreach ([
                'vo6g4962', //sstfe
                '576re5d8', //sstse
                'm9do9edp', //ss2
                'n268oo1p', //ss3bfe
                'nj1nkx1p', //sshdtfe
                'xldeoe63', //sshdtse
            ] as $gameId) {
                $runs = array_merge($runs, $this->apiClient->runs()->getList(
                    [Runs::FILTER_GAME => $gameId, Runs::FILTER_STATUS => Status::STATUS_VERIFIED],
                    Runs::ORDER_BY_DATE,
                    Runs::ORDER_DIRECTION_DESC
                ));
            }

            usort($runs, function (Run $r1, Run $r2) {
                return ($r2->getSubmitted() ? $r2->getSubmitted()->getTimestamp() : 0)
                        <=> ($r1->getSubmitted() ? $r1->getSubmitted()->getTimestamp() : 0);
            });

            return array_slice($runs, 0, $limit);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return [];
        }
    }

    public function getWorldRecords(): array
    {
        $rows = [];
        // @TODO omg I hate this
        foreach ([
             [['vo6g4962', 'xk90mvk0', ['var-onv6wgr8' => '810xoxp1']], 'onv6wgr8'], // sstfe Any% solo
             [['vo6g4962', 'xk90mvk0', ['var-onv6wgr8' => '9qjg6goq']], 'onv6wgr8'], // sstfe Any% co-op
             [['576re5d8', 'z27gx420', ['var-onv6w708' => '8105d5pq']], 'onv6w708'], // sstse Any% solo
             [['576re5d8', 'z27gx420', ['var-onv6w708' => '9qjz8zo1']], 'onv6w708'], // sstse Any% co-op
             [['m9do9edp', '5dwjz5kg']], // ss2 Any% solo
             [['m9do9edp', 'q254jqjd']], // ss2 Any% co-op
             [['n268oo1p', '7dg8pg24', ['var-yn2v24j8' => '8142ogvl', 'var-6njv235n' => 'p122vm41']], 'yn2v24j8'], // ss3bfe Any% solo
             [['n268oo1p', '7dg8pg24', ['var-yn2v24j8' => 'z19z2y8q', 'var-6njv235n' => 'p122vm41']], 'yn2v24j8'], // ss3bfe Any% co-op
             [['nj1nkx1p', 'mkeomjd6', ['var-kn042mol' => '4lxx6xgl']], 'kn042mol'], // sshdtfe Any% solo
             [['nj1nkx1p', 'mkeomjd6', ['var-kn042mol' => '814xwxkq']], 'kn042mol'], // sshdtfe Any% co-op
             [['xldeoe63', 'wkpjzjkr', ['var-j84k25wn' => '4lxx60gl', 'var-p8592r3n' => 'z194p54l']], 'j84k25wn'], // sshdtfe Any% solo
             [['xldeoe63', 'wkpjzjkr', ['var-j84k25wn' => '814xwkkq', 'var-p8592r3n' => 'z194p54l']], 'j84k25wn'], // sshdtfe Any% co-op
         ] as $params) {
            try {
                $leaderboard = $this->apiClient->leaderboards()->getByGameCategory(...$params[0]);
                if ($runs = $leaderboard->getRuns()) {
                    $run = reset($runs)->getRun();
                    $rows[] = new WorldRecordRow(
                        $leaderboard,
                        $run,
                        isset($params[1]) ? $leaderboard->getVariables()[$params[1]]->getValues()->getValues()[$params[0][2]["var-{$params[1]}"]]->getLabel() : ''
                    );
                }
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            }
        }
        return $rows;
    }
}
