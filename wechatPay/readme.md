官方文档 https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=7_3&index=1

微信小程序支付实现流程


首先确定小程序与商户号配置，绑定正常。


然后实现微信支付的统一下单接口 看文档https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_1
请求统一支付接口成功并获取 预支付交易会话标识 	prepay_id 字段之后则才是实现微信小程序支付流程


实际操作支付则是调用wx.faceVerifyForPay(Object object)这个微信小程序接口实现 ，
详见文档https://developers.weixin.qq.com/miniprogram/dev/api/open-api/payment/wx.faceVerifyForPay.html

此微信小程序支付demo 实际上 只是在请求 PayController::index() 方法生成 wx.faceVerifyForPay(Object object) 需要的参数,由用户确认并进行支付

而 PayController::notify() 方法则是在支付成功之后微信支付服务器会携带相关数据请求此方法时执行的接口  , 回调地址在预支付请求中配置 ,
