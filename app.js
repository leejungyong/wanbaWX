App({
  globalData: {
    getLocation: false,
    getWeRunData: false,
    getUserinfo: false,
    config: {
      cdn:'https://img.wondfun.com/',  
      apiUrl: 'https://www.wondfun.com/wanba/api/',
      //apiUrl: 'https://w.wondfun.com/wanba/api/',
      imgUrl: 'https://img.wondfun.com/',
      uploadUrl: 'https://www.wondfun.com/',
      //uploadUrl: 'https://w.wondfun.com/',
      mapSubkey: 'ND7BZ-DOA3Q-3MV53-G6KUV-2YTXZ-DQBJO',
      //userApiUrl: 'https://w.wondfun.com/wanba/api/getopenid-wanba.php'
       userApiUrl: 'https://www.wondfun.com/wanba/api/getopenid-wanba.php'
    }
 },
  onLaunch: function() {
    this.update()
    this.getUserSettting()
    this.isLogin()
    this.onAccelerometerChange()
  },
  onAccelerometerChange(){
    wx.onAccelerometerChange((e) => {
    var pages = getCurrentPages()
    var currentPage = pages[pages.length - 1]
    if (currentPage.onAccelerometerChange) {
      currentPage.onAccelerometerChange(e)
    }
  })
  },
  

  getUserSettting() {
    var that = this // 获取用户信息
    wx.getSetting({
      success: res => {
        // console.log(res)
        if (res.authSetting['scope.userLocation']) {
          that.globalData.getLocation = true
        }
        // console.log(that.globalData.getLocation)
        if (res.authSetting['scope.werun']) {
          that.globalData.getWeRunData = true
        }
        if (res.authSetting['scope.userInfo']) {
          that.globalData.getUserinfo = true
        }
      }
    })
  },
  wxLogin() {
    let that = this
    wx.login({
      success(res) {
        if (res.code) {

          wx.request({
            url: that.globalData.config.userApiUrl + '?code=' + res.code,
            data: {},

            success: (res) => {
              let data = res.data
              let openid = data.openid
              let unionid = data.unionid
              console.log(data)
              wx.setStorageSync('session_key', data.session_key)
              console.log(wx.getStorageSync('session_key'))
              wx.setStorageSync('unionid', unionid)
              if (openid) {
                wx.setStorageSync('openid', openid)
                //console.log(wx.getStorageSync('openid'))
                that.syncUser()

              } else {
                console.log('网络错误')
              }

            },
            fail: (res) => {
              wx.showToast({
                title: '网络错误',
                icon: 'none'
              })
            }
          })
        } else {
          console.log('登录失败！' + res.errMsg)
        }
      }
    })
  },
  isLogin() {
    //console.log('login')
    let that = this

    let session = wx.getStorageSync('openid')
    //console.log(session)
    //let session_key = wx.getStorageSync('session_key')
    //console.log(session_key)
    if (session) {
      wx.checkSession({
        success: function(res) {
         // console.log(res)

          //let key = wx.getStorageSync('session_key')
          //console.log(key)
        },
        fail: function(res) {
          // console.log(res)
          that.wxLogin()

        },
        complete: function(res) {
          //console.log(res)
        },
      })
    } else {
      //console.log('need login')
      that.wxLogin()
    }

  },
  syncUser() {
    wx.request({
      url: this.globalData.config.apiUrl + 'index.php?act=addUser',
      data: {
        openid: wx.getStorageSync('openid'),
        unionid: wx.getStorageSync('unionid')
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  update() {
    // 获取小程序更新机制兼容
    if (wx.canIUse('getUpdateManager')) {
      const updateManager = wx.getUpdateManager()
      updateManager.onCheckForUpdate(function(res) {
        // 请求完新版本信息的回调
        if (res.hasUpdate) {
          updateManager.onUpdateReady(function() {
            wx.showModal({
              title: '更新提示',
              content: '新版本已经准备好，是否重启应用？',
              success: function(res) {
                if (res.confirm) {
                  // 新的版本已经下载好，调用 applyUpdate 应用新版本并重启
                  updateManager.applyUpdate()
                }
              }
            })
          })
          updateManager.onUpdateFailed(function() {
            // 新的版本下载失败
            wx.showModal({
              title: '发现新版本',
              content: '新版本已经上线，请您删除当前小程序，重新搜索打开',
            })
          })
        }
      })
    } else {
      // 如果希望用户在最新版本的客户端上体验您的小程序，可以这样子提示
      wx.showModal({
        title: '提示',
        content: '当前微信版本过低，无法使用该功能，请升级到最新微信版本后重试。'
      })
    }
  },
  log() {
    // 展示本地存储能力
    var logs = wx.getStorageSync('logs') || []
    logs.unshift(Date.now())
    wx.setStorageSync('logs', logs)
  }
})