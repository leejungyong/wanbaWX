const app = getApp()
Page({


  data: {

  },
  auth: function (e) {
    //console.log(e)
    let that = this
    let location = e.detail.authSetting["scope.userLocation"]
    if (location) {
      app.globalData.getLocation = true
    }

    else {
      wx.authorize({
        scope: 'scope.userLocation',
        success: (res) => {
          app.globalData.getLocation = true
        },
        fail: (res) => {
          //console.log(res)
          app.globalData.getLocation = false
          that.auth()
        }
      })
    }
  },
  onHide() {
    wx.navigateBack({
      delta: 2
    })
  },

})