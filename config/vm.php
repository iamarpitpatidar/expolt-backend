<?php

return [
    'api_key' => env('DIGITALOCEAN_API_KEY'),
    'region' => 'blr1',
    'size' => 's-2vcpu-4gb-120gb-intel',
    'snapshot' => '167208300',
    'tags' => ['expolt_vm'],
    'ssh_keys' => []
];
