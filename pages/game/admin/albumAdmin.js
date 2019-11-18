var aid = null
const app = getApp()
Page({


  data: {
    va:null,
    param1:0
  },
  slider1change(e) {
    this.setData({
      param1: e.detail.value
    })
  },

  onLoad: function(options) {
    aid = options.aid
    this.fetch(aid)
  },
  back() {
    wx.navigateBack()
  },
  fetch(aid) {


    wx.showLoading({
      title: '加载中',
    })
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=actSetting',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
       wx.hideLoading()
        that.setData({
          va: data.act.uploadPhotoSetting,
          param1: data.act.uploadPhotoTotal
        })
        
      },
      fail: (err) => {
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },

  updateV(e) {
    let v = e.detail.value
    this.setData({
      va: v
    })
  },

  confirm() {
    let that = this


    let v = this.data.va
    if (v == '' || isNaN(parseInt(v)) || parseInt(v) < 0 || parseInt(v) >20) {
      wx.showToast({
        title: '请正确设置上传奖励',
        icon: 'none'
      })
      return
    }



    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=postPhotoUploadSettting',
      data: {
        va: that.data.va,
        param1:that.data.param1,
        aid:aid
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
        wx.showToast({
          title: data.msg,
        })
        setTimeout(() => {
          wx.navigateBack()
        }, 2000)
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
})