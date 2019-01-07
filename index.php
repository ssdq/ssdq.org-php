<?php
use function htmlspecialchars as e;
require 'vendor/autoload.php';
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
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Serious Sam Done Quick</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.1.3/slate/bootstrap.min.css" />
        <link rel="stylesheet" href="bfe.css" />
    </head>
    <body>
        <main class="container">
            <h1 class="mt-5"><img height="48" src="logo.svg" alt /> Serious&nbsp;Sam Done&nbsp;Quick</h1>
            <p class="lead">
                We're currently under construction, but in the meantime, here are some links that you may find useful:
            </p>
            <div class="list-group list-group-flush mb-5">
                <a class="list-group-item list-group-item-action" href="https://www.speedrun.com/serious_sam" target="_blank"><img height="32" src="speedrun_com.png" alt="speedrun.com" /></a>
                <a class="list-group-item list-group-item-action" href="https://discord.gg/0vKwJa1xQoCpw7vY" target="_blank"><img height="32" src="discord.svg" alt="Discord" /></a>
            </div>

            <h2>Latest runs</h2>
            <p class="mt-4">
                The latest runs in the main games of the series. See the full leaderboards at <a href="https://www.speedrun.com/serious_sam">speedrun.com</a>.
            </p>

            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Game</th>
                        <th scope="col">Category</th>
                        <th scope="col">Level</th>
                        <th scope="col">Player(s)</th>
                        <th scope="col">Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ([
                        'vo6g4962', //sstfe
                        '576re5d8', //sstse
                        'm9do9edp', //ss2
                        'n268oo1p', //ss3bfe
                        'nj1nkx1p', //sshdtfe
                        'xldeoe63', //sshdtse
                    ] as $gameId) {
                        try {
                            $runs = array_merge($runs ?? [], $apiClient->runs()->getList(
                                [\Dsinn\SrcomApi\Client\Getters\Runs::FILTER_GAME => $gameId],
                                \Dsinn\SrcomApi\Client\Getters\Runs::ORDER_BY_DATE,
                                \Dsinn\SrcomApi\Client\Getters\Runs::ORDER_DIRECTION_DESC
                            ));
                            usort($runs, function (\Dsinn\SrcomApi\Client\DataTypes\Run $r1, \Dsinn\SrcomApi\Client\DataTypes\Run $r2) {
                                return ($r2->getSubmitted() ? $r2->getSubmitted()->getTimestamp() : 0) <=> ($r1->getSubmitted() ? $r1->getSubmitted()->getTimestamp() : 0);
                            });
                        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                        }
                    }
                    ?>
                    <? if ($runs): ?>
                        <?php /** @var \Dsinn\SrcomApi\Client\DataTypes\Run $run */ ?>
                        <?php foreach (array_slice($runs, 0, 10) as $run): ?>
                            <tr style="cursor: pointer" onclick="window.location = '<?= "https://www.speedrun.com/{$run->getGame()->getAbbreviation()}/run/{$run->getId()}"; ?>';">
                                <td><?= $run->getSubmitted() ? e($run->getSubmitted()->format('Y-m-d')) : null; ?></td>
                                <td><?= e($run->getGame()->getNames()->getInternational()); ?></td>
                                <td><?= e($run->getCategory()->getName()); ?></td>
                                <td><?= $run->getLevel() ? e($run->getLevel()->getName()) : null; ?></td>
                                <td><?= implode('<br />', array_map(function (\Dsinn\SrcomApi\Client\DataTypes\User $player) {
                                    return $player->getNameStyle()->getDarkHTML($player->getNames()->getInternational());
                                }, $run->getPlayers())) ?></td>
                                <td><?= e($run->getTimes()->getLowestTime()->getString()); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <th scope="colgroup" colspan="6" class="text-center">Error connecting to speedrun.com</th>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h2>World records</h2>
            <p class="mt-4">
                A selective listing of records. See the full leaderboards at <a href="https://www.speedrun.com/serious_sam">speedrun.com</a>.
            </p>
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Game</th>
                        <th scope="col">Category</th>
                        <th scope="col">Player(s)</th>
                        <th scope="col">Time</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
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
                            $leaderboard = $apiClient->leaderboards()->getByGameCategory(...$params[0]);
                    ?>
                            <?php if ($leaderboard->getRuns()): ?>
                                <?php $run = reset($leaderboard->getRuns())->getRun(); ?>
                                <tr style="cursor: pointer" onclick="window.location = '<?= $run->getWeblink(); ?>';">
                                    <td><?= e($leaderboard->getGame()->getNames()->getInternational()); ?></td>
                                    <td>
                                        <?= e($leaderboard->getCategory()->getName()); ?>
                                        <?= isset($params[1]) ? e($leaderboard->getVariables()[$params[1]]->getValues()->getValues()[$params[0][2]["var-{$params[1]}"]]->getLabel()) : ''; ?>
                                    </td>
                                    <td><?= implode('<br />', array_map(function (\Dsinn\SrcomApi\Client\DataTypes\User $player) use ($leaderboard) {
                                        return ($realPlayer = $leaderboard->getPlayers()[$player->getId()]) && $realPlayer->getNameStyle()
                                            ? $realPlayer->getNameStyle()->getDarkHTML($realPlayer->getNames()->getInternational())
                                            : $player->getName();
                                    }, $run->getPlayers())) ?></td>
                                    <td><?= e($run->getTimes()->getLowestTime()->getString()); ?></td>
                                    <td><?= $run->getSubmitted() ? e($run->getSubmitted()->format('Y-m-d')) : null; ?></td>
                                </tr>
                            <?php endif; ?>
                    <?php
                        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                        }
                    }
                    ?>
                </tbody>
            </table>

            <h2>Contact</h2>
            <p>Please contact <em>mr.deagle#6969</em> on our <a href="https://discord.gg/0vKwJa1xQoCpw7vY" target="_blank">Discord server</a>.</p>

            <p>For the old site that was maintained by TheVoiid, click <a href="legacy.html">here</a>.</p>
        </main>
    </body>
</html>
