var aid=null
const app=getApp()
Page({

  data: {
    list:null
  },

  onLoad: function (options) {
    
   aid=options.aid
   this.fetch()
  },
  fetch(){
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getTeamsByAid',
      data: {
        aid: aid,
        token: '',
        uid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        for (let i in data) {
          data[i].num1 = 0
          data[i].num2 = 0
          data[i].num3 = 0
         
        }
        that.setData({
          list: data
        })
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  addAuctionScore(e){
    let id = e.currentTarget.id
    let data=this.data.list[id]
    console.log(data)
    wx.navigateTo({
      url: 'addAuctionScore?aid='+aid+'&ops='+JSON.stringify(data),
    })
  }
})