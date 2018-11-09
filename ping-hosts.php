<?php

require __DIR__.'/vendor/autoload.php';
use JJG\Ping as Ping;

$config = json_decode(file_get_contents(__DIR__.'/config.json'), TRUE);
use Elasticsearch\ClientBuilder;

$esClient = ClientBuilder::create()->setHosts($config['elasticsearch']['hosts'])->build();


$now_timestamp = new DateTime();
$timestamp = $now_timestamp->format(DateTime::ISO8601);

$params = [
  'index' => $config['elasticsearch']['index'],
  'type' => $config['elasticsearch']['index'],
  'id' => hash('sha256',$now_timestamp->format(DateTime::ISO8601)),
  'body' => []
];

$params['body']['timestamp'] = $now_timestamp->format(DateTime::ISO8601);

foreach ($config['hosts'] as $k=>$v) {

  $host = $v['host'];
  $ttl = 128;
  $timeout = 5;

  $ping = new Ping($host, $ttl, $timeout);
  $latency = $ping->ping();

  if ($latency !== false) {
    $params['body'][$k]['latency'] = $latency;
    $params['body'][$k]['status'] = 'ok';
    $params['body'][$k]['status_val'] = 1;
  }
  else {
    $params['body'][$k]['latency'] = -99;
    $params['body'][$k]['status'] = 'error';
    $params['body'][$k]['status_val'] = -1;
  }

}

$indexResponse = $esClient->index($params);
echo "[".$timestamp."][PING-MONEYPIT]=>".json_encode($indexResponse)."\n";
