const app = getApp()
Page({
  _data: {

  },
  data: {
    list: null,
    imgUrl: app.globalData.config.imgUrl,
    currentTab:0
  },
  navbarTap: function (e) {
    this.setData({
      currentTab: e.currentTarget.dataset.idx
    })
  },
  onLoad: function (options) {
   
    let that = this
    //console.log(ops)
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=adminViewTeam',
      data: {
        aid: options.aid
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
        if (data) {
          that.setData({
            list: data.teams

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


})