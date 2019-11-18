const app=getApp()
var aid,title,sharepic=null
Page({


  data: {
     img:''
  },


  onLoad: function (options) {
    let that=this
    let ops=JSON.parse(options.ops)
   // console.log(ops)
    aid=ops.aid
    title=ops.title
    sharepic=ops.sharepic
    //console.log(aid)
    let data = {
      page: 'pages/game/splash',
      scene: aid
    }
   // console.log(data)
    wx.request({
      url: app.globalData.config.apiUrl+'makeQrcode.php',
      data:data,
     method:'POST',
      success:(res)=>{
       console.log(res.data)
          that.setData({
            img:res.data
          })
      }
    })

  },
  onShareAppMessage: function (ops) {
    let that = this
   // console.log(ops)

    if (ops.from === 'button') {
      
   

    }
    return {
      title: title,
      path: 'pages/game/splash?aid=' + aid,
      imageUrl: sharepic ? app.globalData.config.apiUrl + 'sharepic/' + sharepic : app.globalData.config.apiUrl + 'sharepic/1.jpg',

    }
  },

})