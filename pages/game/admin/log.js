const app = getApp()
Page({

  data: {
    list: null
  },


  onLoad: function (options) {
    let that = this
    wx.showLoading({
      title: '加载中',
      mask: true
    })
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=getLogs',
      data: {
        aid: options.aid,
        teamid: -99
      },
      method: "POST",
      success: (res) => {
        let data = res.data
        //console.log(data)
        wx.hideLoading()
        that.setData({
          list: data
        })
      },
      fail: (res) => {
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },


})