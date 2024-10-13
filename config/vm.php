<?php

return [
    'api_key' => env('DIGITALOCEAN_API_KEY'),
    'region' => 'blr1',
    'size' => 's-2vcpu-4gb-120gb-intel',
    'snapshot' => env('DIGITALOCEAN_VM_SNAPSHOT'),
    'tags' => ['expolt_vm'],
    'ssh_keys' => []
];
