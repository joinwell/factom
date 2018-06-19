<?php
define('FACTOM_NODE_URL', 'courtesy-node.factom.com:80');
define('FACTOM_CHAIN_ID', '90162c4247c4ef5eab7b9161a0a5f56c6db437548453db214413c85ea44c4a53');
define('FACTOM_PUB', 'EC2dnBDuLb9gWLbSLZn8QbAKsox52vq9bCrrZUATqPMttcwVwFdw');

//login
$auth = curl('https://well-api.joinwell.com/api/auth', array(
    'email' => 'provider@demo.com',
    'password' => 'password',
    'partnerid' => '4'
));

//here will parse auth token and send request to data


$json = json_encode('any json data');
$cli_command = 'echo ' . $json . ' | docker exec -i -t factom-all /go/bin/factom-cli -s ' . FACTOM_NODE_URL . ' addentry -c ' . FACTOM_CHAIN_ID . ' ' . FACTOM_PUB;
echo $cli_command;
echo exec($cli_command);