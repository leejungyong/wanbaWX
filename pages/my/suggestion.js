const app = getApp()
Page({


  data: {
    txt: ''
  },
  updateTxt(e) {
    this.setData({
      txt: e.detail.value
    })
  },
  save() {
    let that = this
    let token = new Date().getTime();
    let cache = wx.getStorageSync('lastpost')
    wx.setStorageSync('lastpost', token)
    if (that.data.txt == '') {
      console.log('3')
      wx.showToast({
        title: '请留下墨宝',
        icon: 'none',
        mask: true
      })
      return false
    }
    else if (cache) {
      console.log('1')
      let duration = token - cache
      console.log(duration)
      if (duration < 3000) {
        console.log('2')
        wx.showToast({
          title: '手速有点过快呀，休息下，过几秒再点击吧',
          icon: 'none',
          mask: true
        })
        return false
      } else {
        {
          console.log('4')
          wx.request({
            url: app.globalData.config.apiUrl + 'index.php?act=postSuggestion',
            method: 'POST',
            data: {
              openid: wx.getStorageSync('openid'),
              txt: that.data.txt
            },
            success: (res) => {
              let data = res.data
              //console.log(data)
              if (data.status) {
                wx.showModal({
                  title: '',
                  content: '感谢您的宝贵建议',
                  showCancel: false,
                  success: (res) => {
                    wx.navigateBack()
                  }
                })
              } else {
                wx.showToast({
                  title: '操作失败',
                  icon: 'none',
                  mask: true
                })
              }
            }
          })
        }
      }

    }
  },
  onLoad: function (options) {

  },

})