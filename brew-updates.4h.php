#!/usr/bin/php
<?php
// update brew
exec('/usr/local/bin/brew update');

// get pinned packages
$pinned = [];
exec('/usr/local/bin/brew list --pinned', $pinned);

// get outdated packages
$outdated = [];
exec('/usr/local/bin/brew outdated --quiet', $outdated);

// remove pinned from outdated
$updates = array_diff($outdated, $pinned);

// get outdated casks
$outdatedCasks = [];
exec('/usr/local/bin/brew outdated --cask', $outdatedCasks);

$updateCount = count($outdatedCasks) + count($updates);

if ($updateCount > 0) {
    echo 'BRW ' . $updateCount . PHP_EOL;
    echo '---' . PHP_EOL;

    echo count($updates) . ' formulae to update | size=14' . PHP_EOL;
    if (count($updates)) {
        foreach ($updates as $formulae) {
            echo $formulae . ' | terminal=true refresh=true bash=/usr/local/bin/brew param1=upgrade param2=' .
                $formulae . PHP_EOL;
        }

        echo 'update all | terminal=true refresh=true bash=/usr/local/bin/brew param1=upgrade' . PHP_EOL;
    }

    echo '---' . PHP_EOL;

    echo count($outdatedCasks) . ' casks to update | size=14' . PHP_EOL;

    if (count($outdatedCasks)) {
        foreach ($outdatedCasks as $cask) {
            echo $cask . ' | terminal=true refresh=true bash=/usr/local/bin/brew param1=upgrade param2=--cask param3=' .
                $cask . PHP_EOL;
        }

        echo 'update all | terminal=true refresh=true bash=/usr/local/bin/brew param1=upgrade param2=--cask' . PHP_EOL;
    }

    echo '---' . PHP_EOL;

    echo 'Refresh | refresh=true' . PHP_EOL;
}
