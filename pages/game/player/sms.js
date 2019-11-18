const app = getApp()
Page({


  data: {
    aid: 0,
    tel: '',
    code: ''
  },
  verify() {
    let tel = this.data.tel
    let aid = this.data.aid
    let code = this.data.code
    if (tel.length < 11) {
      wx.showToast({
        title: '请输入11位手机号码',
        icon: 'none'
      })
      return
    }
    if (code.length != 4) {
      wx.showToast({
        title: '请输入4位验证码',
        icon: 'none'
      })
      return
    }
    
    let cookie = wx.getStorageSync('cookieKey');
    let header = {};
    if (cookie) {
      header.Cookie = cookie;
    }
    wx.request({
      url: app.globalData.config.apiUrl+ 'check.php',
      data: {
        tel: tel,
        aid: aid,
        code: code
      },
      header: header,
      method: 'POST',
      success: function (res) {
        let data = res.data
        //console.log(data)
        let status = data.status
        let msg = data.message
        wx.showToast({
          title: msg,
          icon: 'none'
        })
        if (status) {
          //写入缓存 coach=aid
          let key = "isCoach_" + aid
          wx.setStorageSync(key, true)
          
          wx.navigateTo({
            url: './main?aid='+aid,
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
  updateTel(e) {
    //console.log(e.detail.value)
    this.setData({
      tel: e.detail.value
    })
  },
  updateCode(e) {
    //console.log(e.detail.value)
    this.setData({
      code: e.detail.value
    })
  },
  sendCode() {
    let tel = this.data.tel
    let aid = this.data.aid
    if (tel.length < 11) {
      wx.showToast({
        title: '请输入11位手机号码',
        icon: 'none'
      })
      return
    }
    wx.request({
      url: app.globalData.config.apiUrl+'code.php',
      data: {
        tel: tel,
        aid: aid
      },

      method: 'POST',
      success: function (res) {
        if (res && res.header && res.header['Set-Cookie']) {
          wx.setStorageSync('cookieKey', res.header['Set-Cookie']);//保存Cookie到Storage
        }


        let data = res.data
       // console.log(data)
        let msg = data.message
        let status = data.status

        wx.showToast({
          title: msg,
          icon: 'none'
        })
        // if(status){
        //   let sessionid = data.sessionid
        //   wx.setStorageSync('PHPSESSID', sessionid);
        // }
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
   // console.log(options)
    this.setData({
      aid: options.aid
    })
  },


})