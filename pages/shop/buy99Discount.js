const app = getApp()
var fromid = null
Page({


  data: {
    isNewUser: false
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
            showCancel: false
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
  useDiscount() {
    wx.navigateTo({
      url: '/pages/shop/newActDiscount?from=' + fromid,
    })
  },
  onLoad: function (options) {
    fromid = options.from
    this.fetch()
  },

})