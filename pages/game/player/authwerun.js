const app = getApp()
Page({


  data: {

  },
  auth: function (e) {
    //console.log(e)
    let that = this
    let location = e.detail.authSetting["scope.   werun"]
    if (location) {
      app.globalData.getWeRunData = true
    }

    else {
      wx.authorize({
        scope: 'scope.werun',
        success: (res) => {
          app.globalData.getWeRunData = true
        },
        fail: (res) => {
          //console.log(res)
          app.globalData.getWeRunData = false
          that.auth()
        }
      })
    }
  },
  onHide() {
    wx.navigateBack({
      delta: 1
    })
  },

})