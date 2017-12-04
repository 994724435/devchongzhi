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
        'appId'                 => '2017xxxxxxxx9',//APPID即创建应用后生成
        //由开发者自己生成: 请填写开发者私钥去头去尾去回车，一行字符串
        'rsaPrivateKey'         =>  '6u0+qaC5LiHfpT1o6ancbw==',
        //支付宝公钥，由支付宝生成: 请填写支付宝公钥，一行字符串
        'alipayrsaPublicKey'    =>  'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApwsosfUrkB+BHoVMldesa9STLVdl6oyQcWcb8yjCjiWYg/vVQo01cdweSgNXmVFdU0zq7Tdjpcikm1A3pe8TQ+u7t2Cybg1IizIu59nUMgMl8xMUE4J3RzzG6KDgDg2xXiJTsL9HWCG3YGgDW1suvX8GQbsfpMpU4RBOeniU2NHqLUW3U3JLA212LNHbVkgKoFiDpVLYkNyPDskUMEdLlwyXjlqsat1rmTcJzYC/J6MUym4c/1z2hXajDC6dcG6sfK/6t7iHH0+joDo8EVXS1QRt2fLX9lTFRXBHMdJCCCsLpEzBq4h7hM4MHsf3JfAc9rCexn2SpvqxT7vxOhw/AQIDAQAB',
        'notifyUrl'             => 'http://www.xxx.com/m/cartpay/notify_ali', // 支付成功通知地址
        'returnUrl'             => 'http://www.xxx.com', // 支付后跳转地址
        'returnPcUrl'           => 'http://www.xxx.com/Home', // PC端扫码支付后跳转地址
    ),
//	'URL_MODEL' => '2',
//	 'DEFAULT_MODULE' => 'Index'
);


