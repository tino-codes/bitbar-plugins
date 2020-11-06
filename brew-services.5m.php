#!/usr/bin/php
<?php
$brew         = '/usr/local/bin/brew';
$brewLink     = 'http://brew.sh/';
$brewServices = '/usr/local/Homebrew/Library/Taps/homebrew/homebrew-services/cmd/services.rb';
$servicesLink = 'https://github.com/Homebrew/homebrew-services';
$refresh      = "---\nRefresh | refresh=true";
$started      = 0;
$stopped      = 0;

$darkMode = trim(shell_exec('defaults read -g AppleInterfaceStyle 2> /dev/null')) === 'Dark';

if (!file_exists($brew)) {
    echo 'Homebrew not installed | color=red' . PHP_EOL;
    echo '---' . PHP_EOL;
    echo 'Install Homebrew | href=' . $brewLink . PHP_EOL;
    echo $refresh;

    return;
}

if (!file_exists($brewServices)) {
    echo 'Homebrew Services not installed | color=red' . PHP_EOL;
    echo '---' . PHP_EOL;
    echo 'Install Homebrew Services | href=' . $servicesLink . PHP_EOL;
    echo $refresh;

    return;
}


$services = [];
exec($brew . ' services', $services);

// remove header line
array_shift($services);

$output = [];

foreach ($services as $service) {
    // parse columns
    $data = explode(' ', $service);
    $data = array_filter($data, function ($item) {
        return trim($item) !== '';
    });
    $data = array_values($data);

    [$name, $status, $user] = $data;


    if ($status === 'started') {
        $started++;

        $output[] = "\e[1m\e[32m" . $name . ($darkMode ? "\e[37m" : "\e[30m");
        $output[] = '--Restart | ' . service('restart', $name);
        $output[] = '--Stop | ' . service('stop', $name);
        $output[] = '-----';
        $output[] = '--Status: ' . $status;
        $output[] = '--User: ' . $user;

    } else {
        $stopped++;
        $output[] = $name;
        $output[] = '--Start | ' . service('start', $name);
        $output[] = '-----';
        $output[] = '--Status: ' . $status;
    }
}

echo 'SVC ' . $started . '/' . ($started + $stopped) . PHP_EOL;
echo '---' . PHP_EOL;
echo implode(PHP_EOL, $output);
echo PHP_EOL;
echo $refresh;

function service(string $command, string $name): string
{
    global $brew;

    return 'bash=' . $brew . ' param1=services param2=' . $command . ' param3=' . $name .
        ' terminal=false refresh=true';
}
