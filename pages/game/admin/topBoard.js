const app = getApp()
Page({

 
  data: {
    list: null,
    imgUrl:app.globalData.config.imgUrl
  },


  onLoad: function (options) {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=topBoard',
      data: {
        aid: options.aid
      },
      method: "POST",
      success: (res) => {
        let data = res.data.list
       // console.log(data)
        let rank = 1
        let count = 1
        data[0].rank = 1
        for (let i = 0; i < data.length - 1; i++) {

          if (data[i + 1].score == data[i].score) {
            data[i + 1].rank = rank
            count++
          }
          else {
            //rank++
            rank += count
            count = 1
            data[i + 1].rank = rank
          }
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


})