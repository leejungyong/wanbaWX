const app = getApp()
const util = require('../../utils/util.js')
var aid=0
Page({

  data: {
    btnShow: true,
    getUserinfo: false,
    imgUrl: app.globalData.config.imgUrl
    
  },
  init(aid, getUserinfo) {
    wx.showLoading({
      title: '',
    })
    let that = this
    console.log(aid)
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=actMyInfo',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        console.log(res.data)
        let data = res.data

        let currentaid = data.currentaid
         //console.log(currentaid)
        wx.hideLoading()
        if (currentaid > 0) {
          that.setData({
            getUserinfo: getUserinfo,
            btnShow: true
          })
          setTimeout(() => {
            wx.reLaunch({
              url: '/pages/game/player/main?aid=' + aid,
            })
          }, 2000)
        } else {
          that.setData({
            getUserinfo: getUserinfo,
            btnShow: false
          })
        }
        //   console.log(that.data)
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
  coach(){
    let key = 'isCoach_' + aid
    if (wx.getStorageSync(key)) {

      wx.navigateTo({
        url: './player/judge',
      })
    } else {
      wx.navigateTo({
        url: './player/sms?aid=' + aid,
      })
    }
  },

  onLoad: function(options) {
   
    //console.log(options)
    
    let getUserinfo = app.globalData.getUserinfo
     aid = options.aid
    //console.log(aid)
    if(aid>0){
    this.init(aid, getUserinfo)
    }else{
      aid=options.scene
      
      //console.log(scene)
      this.init(aid, getUserinfo)
    }

  },

  auth: function(e) {
    //console.log(util.base64_encode(e.detail.userInfo.nickName));
    // console.log(e)
    if (!e.detail.userInfo) {
      wx.showToast({
        title: '程序需要您授权后才可正常运行',
        icon: 'none'
      })
    } else {
      wx.request({
        url: app.globalData.config.apiUrl+'index.php?act=syncUser',
        data: {
          openid: wx.getStorageSync('openid'),
          unionid: wx.getStorageSync('unionid'),
          avatar: e.detail.userInfo.avatarUrl,
          nick: e.detail.userInfo.nickName
         // nick: util.base64_encode(e.detail.userInfo.nickName)
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

      wx.navigateTo({
        url: './player/join?aid=' + aid,
      })
    }


  },
  start() {
    console.log('start')
    return
    wx.navigateTo({
      url: './player/join?aid=' + aid,
    })
  },


})