var newaid,  price = 0
var fromid,tradeno,order=null
import {
  createTimeStamp,
  randomString,
  getXMLNodeValue
} from '../../utils/util.js'
import {
  md5
} from '../../utils/md5.js'
const app = getApp()
Page({


  data: {
    imgUrl: app.globalData.config.imgUrl,
    title: '',
    date: '请选择活动时间',
    teamNum: 6,
    maxNum: 6,
    pic: null,
    cat: 0,
   cats: [{
      name: '团建模式',
      value: 0,
      checked: true
    },
    {
      name: '旅游模式',
      value: 1
    }]
  },
  sliderchange(e) {
    this.setData({
      teamNum: e.detail.value
    })
  },
  bindDateChange(e) {
    this.setData({
      date: e.detail.value
    })
  },
  updateTitle(e) {
    this.setData({
      title: e.detail.value
    })
  },
  beforePost() {
    let that = this
    if (that.data.title == '' || that.data.date == '请选择活动时间') {
      wx.showToast({
        title: '星号*为必填项',
        icon: 'none'
      })
      return false
    }
    let token = new Date().getTime();
    let cache = wx.getStorageSync('lastpost')
    wx.setStorageSync('lastpost', token)
    if (cache) {
      let duration = token - cache

      if (duration < 3000) {
        wx.showToast({
          title: '手速有点过快呀，休息下，过几秒再点击吧',
          icon: 'none'
        })
        return false
      }

    }
    return true
  },
  radioChange(e) {
    let v = e.detail.value
    console.log(v)
    this.setData({
      cat: v
    })


  },

  random_No(j) {
    var random_no = "";
    for (var i = 0; i < j; i++) //j位随机数，用以加在时间戳后面。
    {
      random_no += Math.floor(Math.random() * 10);
    }
    // random_no = new Date().getTime() + random_no;
    random_no = 'D99'+ '_' + new Date().getTime() + random_no
    return random_no;
  },
  goWxPay: function (num) {

    let title = '使用99折扣卡创建活动'
    let openid = wx.getStorageSync('openid')
    this.unitedPayRequest(openid, title, price, num);
  },
  /*统一支付接口*/
  unitedPayRequest: function (openid, title, price, num) {
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
    tradeno = out_trade_no 
    var spbill_create_ip = '112.17.125.37'
    var unifiedPayment = 'appid=' + appid + '&attach=' + attach + '&body=' + body + '&mch_id=' + mch_id + '&nonce_str=' + nonce_str + '&notify_url=' + notify_url + '&openid=' + openid + '&out_trade_no=' + out_trade_no + '&spbill_create_ip=' + spbill_create_ip + '&total_fee=' + total_fee + '&trade_type=' + trade_type + '&key=' + key;
    console.log("unifiedPayment", unifiedPayment);
    var sign = md5(unifiedPayment).toUpperCase();
    console.log("签名md5", sign);
    //封装订单数据
    order = {
      orderno: out_trade_no,
      openid: wx.getStorageSync('openid'),
      eventtype: 0,
      amount: price
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
      success: function (res) {
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
  processPay: function (param, num) {
    let that = this
    wx.requestPayment({
      timeStamp: param.timeStamp,
      nonceStr: param.nonceStr,
      package: param.package,
      signType: param.signType,
      paySign: param.paySign,
      success: function (res) {
        // success
        that.save(num)
        console.log("wx.requestPayment返回信息", res);
        wx.showModal({
          title: '恭喜您',
          content: '成功创建了一个活动，您可以“微信支付”中查看支付凭证通知',
          showCancel: false,
          success: function (res) {
            if (res.confirm) {

              setTimeout(() => {
                wx.redirectTo({
                  url: '/pages/my/myact/list',
                })
              }, 500)

            } else if (res.cancel) {

            }
          }
        })
      },
      fail: function () {
        console.log("支付失败");
      },
      complete: function () {
        console.log("支付完成(成功或失败都为完成)");
      }
    })
  },

  getPhoneNumber: function (e) {
    let that = this
    let status = that.beforePost()
    if (status) {
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
            let data = JSON.parse(res.data)
            console.log(data)
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
    }
  },

  save(num) {
    let that = this

    var req = function (obj) {
      return new Promise(function (resolve, reject) {

        wx.request({

          url: obj.url,

          data: obj.data,

          header: obj.header,

          method: obj.method == undefined ? "get" : obj.method,

          success: function (data) {
            resolve(data)

          },

          fail: function (data) {

            if (typeof reject == 'function') {

              reject(data);

            } else {

              console.log(data);

            }

          },

        })

      })

    }
    let req1 = new req({
      url: app.globalData.config.apiUrl + 'index.php?act=newAct',
      data: {
        actdata: that.data,
        openid: wx.getStorageSync('openid'),
        fromid:fromid,
        point:999,
        tel:num,
        tradeno: tradeno,
        order:order
      },
      method: 'POST',
    })
    req1.then((res) => {
      let data = res.data
      let pic = that.data.pic
      if (data.status && data.aid && pic) {
        var aid = data.aid

        if (pic.indexOf('http://tmp/') > -1 || pic.indexOf('wxfile://') > -1) {
          wx.uploadFile({
            url: app.globalData.config.apiUrl + 'uploadactpic.php',
            filePath: pic,
            name: 'file',
            formData: {
              'aid': aid,
              'openid': wx.getStorageSync('openid')
            },
            success: function (res) {
              let data = res.data
              console.log(data)
              if (data) {
                return new req({
                  url: app.globalData.config.apiUrl + 'index.php?act=updateActPic',
                  data: {
                    aid: data,
                    openid: wx.getStorageSync('openid')
                  },
                  method: 'POST',
                })
              }
            }
          })
        }
      }
    })
      .then((res) => {

        wx.showToast({
          title: '创建活动成功',
          icon: 'none'
        })

        // setTimeout(() => {

        //   wx.redirectTo({
        //     url: '/pages/my/myact/list',
        //   })
        // }, 2000)
      })
      .catch((err) => {
        console.log(err)
        wx.showToast({
          title: '操作出错，请重试',
          icon: 'none'
        })
      })

  },
  delPic() {
    this.setData({
      pic: null
    })
  },
  preview() {
    let pics = []
    pics.push(this.data.pic)
    wx.previewImage({
      urls: pics,
    })
  },
  chooseImg() {
    let that = this
    let pic = that.data.pic

    wx.chooseImage({
      count: 1,
      sizeType: ['compressed'],
      sourceType: ['album', 'camera'],
      success: function (res) {


        pic = res.tempFilePaths[0]

        that.setData({
          pic: pic
        })
        console.log(that.data.pic)
      }
    })
  },
  fetch() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=isNewUser',
      data: {
        openid: wx.getStorageSync('openid'),
        fromid: fromid,
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
        let s = data.status ? true : false
        that.setData({
          isNewUser: s
        })
        if (!s) {
          wx.showModal({
            title: '',
            content: '老用户不能享受此优惠折扣',
            showCancel: false,
            success:(res)=>{
              if(res.confirm){
                wx.navigateBack()
              }
            }
          })

        }
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  onLoad: function (options) {
    console.log(options)
    fromid=options.from
    price = 999
    this.fetch()
  }

})