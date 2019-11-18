var aid,openid=null
const app = getApp()
Page({

 
  data: {

  },
  onLoad: function (options) {
      aid=options.aid
       openid=wx.getStorageSync('openid')
      if(openid && aid){
        let that = this
        wx.request({
          url: app.globalData.config.apiUrl+'index.php?act=promoteManager',
          data: {
            aid: aid,
            openid: openid
          },
          method: 'POST',
          success: (res) => {
            let data = res.data
            console.log(data)
            wx.showToast({
              title: data.msg,
              success: (res) => {
                setTimeout(() => {
                  wx.redirectTo({
                    url: '/pages/game/splash?aid=' + aid,
                  })
                  
                }, 2000)
              }
            })
            
          },
          fail: (res) => {
            wx.showToast({
              title: '网络错误',
              icon: 'none'
            })
          }
        })
      }else{
        wx.showToast({
          title: '身份验证错误，未获得管理员权限',
          success: (res) => {
            setTimeout(() => {
              wx.redirectTo({
                url: '/pages/game/splash?aid=' + aid,
              })
            }, 2000)
          }
        })
      }
  },

})