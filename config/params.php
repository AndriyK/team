<?php

return [
    'adminEmail' => 'admin@example.com',

    'securityToken.defaultExpire' => 60*60, // 1 hour
    'securityToken.weekExpire' => 60*60*24*7, // 1 week
    'securityToken.prolongInterval' => 60*10, // 10 min
    'securityToken.host' => 'http://localhost',
    'securityToken.signStr' => 'tst',
];
