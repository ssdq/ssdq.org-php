<?php
namespace App\Helper;

use Dsinn\SrcomApi\Client\DataTypes\Leaderboard;
use Dsinn\SrcomApi\Client\DataTypes\Run;

class WorldRecordRow
{
    /** @var Leaderboard */
    private $leaderboard;
    /** @var Run */
    private $run;
    /** @var string */
    private $subcategory;

    public function __construct(Leaderboard $leaderboard, Run $run, string $subcategory)
    {
        $this->leaderboard = $leaderboard;
        $this->run = $run;
        $this->subcategory = $subcategory;
    }

    public function getLeaderboard(): Leaderboard
    {
        return $this->leaderboard;
    }

    public function getRun(): Run
    {
        return $this->run;
    }

    public function getSubcategory(): string
    {
        return $this->subcategory;
    }
}
