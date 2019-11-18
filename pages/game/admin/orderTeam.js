import regeneratorRuntime from '../../../utils/runtime.js'
import {
  wxRequest
} from '../../../utils/wxrequest.js'
import {
  createTimeStamp,
  randomString,
  getXMLNodeValue
} from '../../../utils/util.js'
import {
  md5
} from '../../../utils/md5.js'
const app = getApp()
var aid,order = null
var teamAdd = 1;
Page({


  data: {
    limit: 6,
    teamNum: 1,
    maxNum:6,
    teamTotal: 7,
    pay: 100
  },
  sliderchange(e) {
    teamAdd = e.detail.value
    let teamNum = teamAdd + this.data.maxNum
    let pay = teamAdd * 100
    this.setData({
      teamTotal: teamNum,
      teamNum: teamAdd,
      pay: pay
    })
    console.log(this.data)
  },
  async _updateTeamNum() {
    let that = this
    return await wxRequest(
      app.globalData.config.apiUrl + 'index.php?act=updateTeamNum', {
        data: {
          openid: wx.getStorageSync('openid'),
          aid: aid,
          teamnum: that.data.teamTotal,
          order:order
        }
      }
    )
  },
  updateTeamNum() {
    
    this._updateTeamNum()
      .then((res) => {
        console.log(res)
      })
      .catch((err) => {
        console.log(err)
      })
  },
  random_No(j) {
    var random_no = "";
    for (var i = 0; i < j; i++) //j位随机数，用以加在时间戳后面。
    {
      random_no += Math.floor(Math.random() * 10);
    }
    random_no = 'T_' + new Date().getTime() + random_no
    return random_no;
  },
  goWxPay: function(num) {

    let title = '门派特权卡'
    let price = this.data.pay
    let openid = wx.getStorageSync('openid')
    this.unitedPayRequest(openid, title, price, num);
  },
  /*统一支付接口*/
  unitedPayRequest: function(openid, title, price, num) {
    var that = this;
    //统一支付签名
    var appid = 'wx13a69e8d86bf8f77'; //appid必填
    var body = title; //商品名必填
    var attach = num;
    var mch_id = '1440256202'; //商户号必填
    var nonce_str = randomString(); //随机字符串，不长于32位。  
    var notify_url = 'https://www.wondfun.com/wanba/api/wxpay/biz/notify.php'; //通知地址必填
    var total_fee = parseInt(price * 100); //价格，这是一分钱
    var trade_type = "JSAPI";
    var key = 'e11b1fde966544580537a80da61b549d'; //商户key必填，在商户后台获得
    var out_trade_no = that.random_No(3); //自定义订单号必填
    var spbill_create_ip = '112.17.125.37'
    var unifiedPayment = 'appid=' + appid + '&attach=' + attach + '&body=' + body + '&mch_id=' + mch_id + '&nonce_str=' + nonce_str + '&notify_url=' + notify_url + '&openid=' + openid + '&out_trade_no=' + out_trade_no + '&spbill_create_ip=' + spbill_create_ip + '&total_fee=' + total_fee + '&trade_type=' + trade_type + '&key=' + key;
    console.log("unifiedPayment", unifiedPayment);
    var sign = md5(unifiedPayment).toUpperCase();
    console.log("签名md5", sign);
    //封装订单数据
    order = {
      orderno: out_trade_no,
      openid: wx.getStorageSync('openid'),
      eventtype: 2,
      amount: price,
      eventid: aid
    }
    //封装统一支付xml参数
    var formData = "<xml>";
    formData += "<appid>" + appid + "</appid>";
    formData += "<attach>" + attach + "</attach>";
    formData += "<body>" + body + "</body>";
    formData += "<mch_id>" + mch_id + "</mch_id>";
    formData += "<nonce_str>" + nonce_str + "</nonce_str>";
    formData += "<notify_url>" + notify_url + "</notify_url>";
    formData += "<openid>" + openid + "</openid>";
    formData += "<out_trade_no>" + out_trade_no + "</out_trade_no>";
    formData += "<spbill_create_ip>" + spbill_create_ip + "</spbill_create_ip>";
    formData += "<total_fee>" + total_fee + "</total_fee>";
    formData += "<trade_type>" + trade_type + "</trade_type>";
    formData += "<sign>" + sign + "</sign>";
    formData += "</xml>";
    console.log("formData", formData);
    //统一支付
    wx.request({
      url: 'https://api.mch.weixin.qq.com/pay/unifiedorder', //别忘了把api.mch.weixin.qq.com域名加入小程序request白名单，这个目前可以加
      method: 'POST',
      head: 'application/x-www-form-urlencoded',
      data: formData, //设置请求的 header
      success: function(res) {
        console.log("返回商户", res.data);
        var result_code = getXMLNodeValue('result_code', res.data.toString("utf-8"));
        var resultCode = result_code.split('[')[2].split(']')[0];
        if (resultCode == 'FAIL') {
          var err_code_des = getXMLNodeValue('err_code_des', res.data.toString("utf-8"));
          var errDes = err_code_des.split('[')[2].split(']')[0];
          wx.showToast({
            title: errDes,
            icon: 'none',
            duration: 3000
          })
        } else {
          //发起支付
          var prepay_id = getXMLNodeValue('prepay_id', res.data.toString("utf-8"));
          var tmp = prepay_id.split('[');
          var tmp1 = tmp[2].split(']');
          //签名  
          var key = 'e11b1fde966544580537a80da61b549d'; //商户key必填，在商户后台获得
          var appId = 'wx13a69e8d86bf8f77'; //appid必填
          var timeStamp = createTimeStamp();
          var nonceStr = randomString();
          var stringSignTemp = "appId=" + appId + "&nonceStr=" + nonceStr + "&package=prepay_id=" + tmp1[0] + "&signType=MD5&timeStamp=" + timeStamp + "&key=" + key;
          console.log("签名字符串", stringSignTemp);
          var sign = md5(stringSignTemp).toUpperCase();
          console.log("签名", sign);
          var param = {
            "timeStamp": timeStamp,
            "package": 'prepay_id=' + tmp1[0],
            "paySign": sign,
            "signType": "MD5",
            "nonceStr": nonceStr
          }
          console.log("param小程序支付接口参数", param);
          that.processPay(param, num);
        }

      },
    })

  },

  /* 小程序支付 */
  processPay: function(param, num) {
    let that = this
    wx.requestPayment({
      timeStamp: param.timeStamp,
      nonceStr: param.nonceStr,
      package: param.package,
      signType: param.signType,
      paySign: param.paySign,
      success: function(res) {
        // success
        that.updateTeamNum()
        let pages = getCurrentPages()
        let prepage = pages[pages.length - 2]
        prepage.setData({
          teamNum: that.data.teamTotal,
          maxNum: that.data.teamTotal
        })
        console.log("wx.requestPayment返回信息", res);
        wx.showModal({
          title: '支付成功',
          content: '您将在“微信支付”中收到支付凭证通知',
          showCancel: false,
          success: function(res) {
            if (res.confirm) {

              setTimeout(() => {
                wx.navigateBack()
              }, 200)

            } else if (res.cancel) {

            }
          }
        })
      },
      fail: function() {
        console.log("支付失败");
      },
      complete: function() {
        console.log("支付完成(成功或失败都为完成)");
      }
    })
  },
  getPhoneNumber: function(e) {
    // console.log(e.detail.errMsg)
    // console.log(e.detail.iv)
    // console.log(e.detail.encryptedData)
    // console.log(wx.getStorageSync('session_key'))
    let that = this
    if (e.detail.errMsg == 'getPhoneNumber:ok') {
      const encryptedData = e.detail.encryptedData
      let data = {
        iv: e.detail.iv,
        encryptedData: encryptedData,
        session_key: wx.getStorageSync('session_key')
      }
      wx.request({
        url: app.globalData.config.apiUrl + 'decrypt/decrypt.php',
        data: data,
        method: 'POST',
        success(res) {
          console.log(wx.getStorageSync('session_key'))
          console.log(res)
          let data = JSON.parse(res.data)
          // console.log(data)
          if (data.phoneNumber) {
            let num = data.phoneNumber
            that.goWxPay(num)
          }

        },
        fail(err) {
          console.log(err)
        }
      })

    } else {
      wx.showModal({
        title: '',
        showCancel: false,
        content: '请允许授权获取您绑定的手机号码，以便客服能联系到您提供服务。',
      })

    }
  },
  onLoad: function(options) {
    console.log(options)
    aid = options.aid
    let maxNum = parseInt(options.maxNum)
    let limit = maxNum > 6 ? 12 - maxNum : 6
    let teamNum = maxNum > 6 ? maxNum :6
    let teamTotal = teamNum+1
    this.setData({
      limit: limit,
      maxNum: maxNum > 6 ? maxNum :6,
      teamTotal: teamTotal
    })
    //console.log(this.data)
  },

})