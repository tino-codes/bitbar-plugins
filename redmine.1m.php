#!/usr/bin/php
<?php

$redmineUrl   = 'https://redmine.domain.de/';
$startUrl     = $redmineUrl . 'issues';
$redmineToken = 'thetoken';

$ticketsUrl = $redmineUrl . 'issues.json?key=' . $redmineToken . '&limit=100&status_id=open&assigned_to_id=me';
$json       = file_get_contents($ticketsUrl);

if (!$json) {
    echo '🆘';
    exit;
}

$data = json_decode($json, true);

if (!$data || !is_array($data)) {
    echo '🆘';
    exit;
}

echo 'RDM ' . $data['total_count'] . PHP_EOL;
echo '---' . PHP_EOL;
echo 'Redmine | href=' . $startUrl . PHP_EOL;
echo '---' . PHP_EOL;

$projects = [];
$issues   = [];

foreach ($data['issues'] as $issue) {
    $projects[$issue['project']['id']] = $issue['project']['name'];

    $issues[$issue['project']['id']][$issue['id']] =
        '#' . $issue['id'] . ' ' . $issue['subject'] .
        ' (' . $issue['status']['name'] . ' / ' . $issue['tracker']['name'] . ')';
}

foreach ($projects as $projectId => $projectName) {
    echo $projectName . ' (' . count($issues[$projectId]) . ') | size=14 href=' .
        $redmineUrl . 'projects/' . $projectId . PHP_EOL;
    
    foreach ($issues[$projectId] as $issueId => $issue) {
        echo $issue . ' | size=12 href=' . $redmineUrl . 'issues/' . $issueId . PHP_EOL;
    }
    echo '---' . PHP_EOL;
}

echo 'Refresh | refresh=true' . PHP_EOL;
echo '---' . PHP_EOL;
