<?php
return [
    'log' => [
        'access' => '',
        'error' => '',
        'loglevel' => ''
    ],
//    "api" => [
//        "tag" => "api",
//        "services" => [
//            "StatsService"
//        ]
//    ],
    'dns' => [
        'hosts' => (object)[],
        'servers' => [],
        'tag' => 'dns'
    ],
    'routing' => [
        'domainStrategy' => 'IPOnDemand',
        'rules' => [
            // API 路由规则
//            [
//                "type" => "field",
//                "inboundTag" => ["api"],
//                "outboundTag" => "api"
//            ],
        ],
//        "balancers" => []
    ],
//    "policy" => [
//        "0" => [
//            "handshake" => 4,
//            "connIdle" => 300,
//            "uplinkOnly" => 2,
//            "downlinkOnly" => 5,
//            "statsUserUplink" => true,
//            "statsUserDownlink" => true,
//            "bufferSize" => 512
//        ],
//        "system" => [
//            "statsInboundUplink" => true,
//            "statsInboundDownlink" => true
//        ]
//    ],
    'inbounds' => [
        // API 入站连接
//        [
//            "port" => 45335,
//            "listen" => "127.0.0.1",
//            "protocol" => "dokodemo-door",
//            "settings" => [
//                "address" => "127.0.0.1",
//            ],
//            "tag" => "api"
//        ]
    ],
    'outbounds' => [
        [
            'protocol' => 'freedom',
            'tag' => 'direct'
        ],
        [
            'protocol' => 'blackhole',
            'tag' => 'block'
        ]
    ],
//    "transport" => (object)[],
//    "stats" => (object)[],
//    "reverse" => (object)[]
];
