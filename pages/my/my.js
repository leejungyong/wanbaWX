const app = getApp()
Page({

  data: {
    imgUrl:app.globalData.config.imgUrl,
    mypoint:0,
    payfirst:1,
    memberid:null
  },
  myaccount() {
    wx.navigateTo({
      url: './myaccount/myaccount',
    })
  },
  myact(){
    wx.navigateTo({
  url: './myact/list',
})
  },
  pcAdmin() {
    wx.navigateTo({
      url: '/pages/game/admin/pcAdmin',
    })
  },
  userPlan(){
    wx.navigateTo({
      url: '/pages/shop/userPlan',
    })
  },
  suggestion(){
    wx.navigateTo({
      url: './suggestion',
    })
  },
  mypoint() {
    wx.navigateTo({
      url: './mypoint/list',
    })
  },
  cityPartner() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=queryAgentApply',
      method: 'POST',
      data: {
        openid: wx.getStorageSync('openid'),
      },
      success(res) {
        console.log(res)
        if (res.data.mode == false) {
          wx.navigateTo({
            url: '../cityPartner/statuspage?type=1&reason='
          })
        } else if (res.data.status == 0) {
          wx.navigateTo({
            url: "../cityPartner/statuspage?status=0&reason=",
          })
        } else if (res.data.status == 1) {
          wx.navigateTo({
            url: "../cityPartner/statuspage?status=1&reason=&city="+res.data.city,
          })
        } else if (res.data.status == -1) {
          wx.navigateTo({
            url: "../cityPartner/statuspage?status=-1&reason=" + res.data.reason,
          })
        }
      }
    })
  },
  myquestion() {
    wx.navigateTo({
      url: './myquestion/list',
    })
  },
  myLine(){
    wx.navigateTo({
      url: './myline/lineList',
    })
  },
  buykit() {
    wx.navigateTo({
      url: './mypay/buykit'
    })
  }, 
  mypay() {
    let url = this.data.memberid ? './mypay/list' : './mypay/activate' 
    wx.navigateTo({
      url:url
    })
  },
  onPullDownRefresh() {
    wx.showNavigationBarLoading();
    this.fetch();
    wx.hideNavigationBarLoading();
    wx.stopPullDownRefresh()
  },
  fetch(){
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=myPayInfo',
      data: {
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data

        this.setData({

          mypoint: data.point ? data.point : 0,
          payfirst: data.payfirst ? data.payfirst : 1,
          memberid: data.memberid == '' ? null : data.memberid
        })

      },
      fail: (err) => {

        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  onLoad: function (options) {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=myPayInfo',
      data: {
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
       
        this.setData({
          
          mypoint: data.point ? data.point : 0,
          payfirst: data.payfirst ? data.payfirst : 1,
          memberid: data.memberid == '' ? null : data.memberid
        })

      },
      fail: (err) => {

        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  onShareAppMessage: function (ops) {
    let that = this
    //console.log(ops)
    if (ops.from === 'button') {
      // 来自页面内转发按钮
      //console.log(ops.target)
    }
    return {
      title: '玩霸江湖',
      path: '/pages/my/my',
      imageUrl: app.globalData.config.imgUrl + 'wanba/img/sharepic/1.jpg'
    }
  },

})