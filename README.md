# jushuitan-sdk（ 聚水潭SDK-PHP）

!!!非官方SDK，用于PHP对接 [聚水潭开放平台](http://open.jushuitan.com/document.html) 相关接口，完成需要的业务逻辑。

# 聚水潭通用测试账号
```
沙箱测试登录地址: https://c.jushuitan.com/login.aspx;
沙箱测试API地址: https://c.jushuitan.com/api/open/query.aspx;
线上环境API地址: https://open.erp321.com/api/open/query.aspx;
线上环境接口参数申请文档：https://open.jushuitan.com/document/2122.html

在线API测试工具（正式和沙箱都可用）：https://open.jushuitan.com/tools.aspx

测试环境授权申请QQ群：10784273、226257083

企业版客户(有精细化库存管理)： 
帐号: kfcs@jst.com
密码: qwe123qwe!
PartnerId：ywv5jGT8ge6Pvlq3FZSPol345asd 
PartnerKey：ywv5jGT8ge6Pvlq3FZSPol2323 
TOKEN：181ee8952a88f5a57db52587472c3798 
---------------------------------------------------- 
专业版客户（无精细化库存管理）： 
帐号: kfcszy@jst.com
密码: qwe123qwe!
PartnerId：ywv5jGT8ge6Pvlq3FZSPol345asd 
PartnerKey：ywv5jGT8ge6Pvlq3FZSPol2323 
TOKEN：3f723a3e4bd178cdc7070b48641c5707
```

# 使用示例
```php
require "vendor/autoload.php";

use JushuitanSdk\Client;
use JushuitanSdk\Config;
use JushuitanSdk\Services\BaseService;

$config = [
    'shop_id' => 0,
    'sandbox' => true,
    'partnerid' => 'ywv5jGT8ge6Pvlq3FZSPol345asd',
    'partnerkey' => 'ywv5jGT8ge6Pvlq3FZSPol2323',
    'token' => '181ee8952a88f5a57db52587472c3798',
];

$jstConfig = new Config($config);
$jstClient = new Client($jstConfig);
$jstService = new BaseService($jstClient);

$params = [
    "page_index" => 1,
    "page_size" => 100,
];

var_dump($jstService->shopsQuery($params));

OR 

var_dump($jstClient->request("shops.query", $params));
```

# 注意事项
需要自行处理Token一年有效期问题，接口文档：[刷新token(必接)](https://open.jushuitan.com/document/2135.html)

MIT, see [LICENSE](/LICENSE)