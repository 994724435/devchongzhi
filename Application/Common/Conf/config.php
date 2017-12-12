<?php
return array(
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => '127.0.0.1', // 服务器地址
	'DB_NAME'   => 'devchongzhi', // 数据库名
	'DB_USER'   => 'root', // 用户名
	'DB_PWD'    => 'root', // 密码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => 'p_', // 数据库表前缀
    //支付宝 支付配置
    'ALI_CONFIG'  => array(
        'gatewayUrl'            => 'https://openapi.alipay.com/gateway.do',//支付宝网关（固定)'
        'appId'                 => '2017120200330262',//APPID即创建应用后生成
        //由开发者自己生成: 请填写开发者私钥去头去尾去回车，一行字符串
        'rsaPrivateKey'         =>  'MIIEowIBAAKCAQEA3jJLWC6W92twrg/m3NhIgIPTsRroRvXnvLK/9172LyxWu5TpTf+ODEMEHZmRbIaDSQ5gyOXyEU5BaLLCG9+XH9bTvXej+8q5t/psDp7SKQ+CEjoRnln4R8WKEr0b3s45GuIsmUj4CtQDjJJl5Iwaj8okmtJQMOh4DLuYwWRTQ34PiYRzhVus6iaMrVKXKVWdliPW7lCrWWzlzzQvjiVYi5qwWLzxXykA/grgX1RprOm3ydhU34MR7Q/h3AUK+EHybiqkhba3wScwUrz8ThBTy1RA8T5u3qXzVwJrWnKZbxkzIFg1GaHh9sgBcjCOBKBA5qDPaIJaGyJmmppEsc6mnwIDAQABAoIBAF/nd2vNWC3cutr2VCAYXlrSC4oS4hTWyLpCsObVnw7HZXw7juOynR4fwuP/x6v+9yWORioQTgY4L7VACtY7EKCWeff6btYcL0MAnKlG1dERN4QkejxN+wMHeUwTQbdObYlz++oBe8Y4snt3KPGXo1NS3a/RfPlPLZnZUtqOMf4b2ieo79YKxaV5FJ4cNotRZ5Ei8lWRQfMWxah/jhaGlJlSnDPrr3FHNsdYQU0JEssrAgal1gPtxS/bHgd9WXwVaH9YkXIU7/njbgj/tLdWpTMEaThxYKsbxDFtRqtbgaE7jv6RY/mgCyr1svu66SeLU5fS1+Ms/HSo89I2WymOfkkCgYEA9JETg7X+7cAhhGYlgY3HLSoMQTQfg/Qw4tgulI67t8Z7Xej+Vh/BUjn2SXRWzJ3Yr+89L9yRnU4N47soZHRXDGLxn3soRxcUqLEavXPwEydk3Z7Mm2gcZ23XHoCqCqSZbfTKdaf4yMlWVSy9L78JO6ObAGomn5c183dBaDDlsE0CgYEA6JV+4zQTRZeOL4kd2zGLSB4FeKv/AK0O+LfAMeqVETyEskf/X+pkO+ybP8NNeRwVFVd4r1OCUcEmqipHliq62K3X4LxFBnQ6zazbtQccWNfnfoA0noZOw3DA3scaOdcTuBUiFT9uro7uzyDcsbyadVY5GRrJKmejbeM1Hn9kiJsCgYEAh1a9UNbnI1R+d9E7Ei2OOl0ZeP/KLPB2GSJ+7HDsSq/I11g3XxullMZEl7OM0SDMp9ehqZnK7x0hrJOGr8h933nlslaqHGUWZp/TZ2IASekz9TyKh7medlIfiF61OryJt4KOg3uXvi1E3E+sxf5Wsq0/+oPDqe84yOmGxYzBnsUCgYBMW6hI13PsSRF5Mb8Hk4BysMiDEZSqmCZuKYWD3cwK2J+IGHMS/lTiB5AAoxHwTPPvCcSpavVB+fPDshXGi0jEkm5pbeYLdGVJ2RJYoHkCAgASW+zqjpWVPJNVkHBfxOjIasfByg2AaZSlk9hg0daz5xbf0xdwQI47KXjrBk/vOQKBgEIIihQ0eEQGVxSY8WGbyOzEC6gCovXNUdowa4GguFUZuU4E6OGBD6aiuFLS8WM3+XLczXSRS5eH+OUaNbmqi5f2p53GyZi9+A7ApxBVWNDIbOUV+eqS8jxTrDRM09KePLkapyZdVX/hqmiKjdcz1Nt0b7wOmkYkGZiWqw2Y+ghk',
        //支付宝公钥，由支付宝生成: 请填写支付宝公钥，一行字符串
        'alipayrsaPublicKey'    =>  'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApwsosfUrkB+BHoVMldesa9STLVdl6oyQcWcb8yjCjiWYg/vVQo01cdweSgNXmVFdU0zq7Tdjpcikm1A3pe8TQ+u7t2Cybg1IizIu59nUMgMl8xMUE4J3RzzG6KDgDg2xXiJTsL9HWCG3YGgDW1suvX8GQbsfpMpU4RBOeniU2NHqLUW3U3JLA212LNHbVkgKoFiDpVLYkNyPDskUMEdLlwyXjlqsat1rmTcJzYC/J6MUym4c/1z2hXajDC6dcG6sfK/6t7iHH0+joDo8EVXS1QRt2fLX9lTFRXBHMdJCCCsLpEzBq4h7hM4MHsf3JfAc9rCexn2SpvqxT7vxOhw/AQIDAQAB',
        'notifyUrl'             => 'http://ydc.shuijingjiafang.com', // 支付成功通知地址
        'returnUrl'             => 'http://k36668.cn/index.php/Home/Login/pay', // 支付后跳转地址
        'returnPcUrl'           => 'http://k36668.cn', // PC端扫码支付后跳转地址
    ),
//	'URL_MODEL' => '2',
//	 'DEFAULT_MODULE' => 'Index'
);


