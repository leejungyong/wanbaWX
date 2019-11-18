const app=getApp()
var from=null
Page({
data: {

  },
onLoad: function (options) {
   from=options.from
  },
agree(){
  wx.request({
    url: app.globalData.config.apiUrl +'index.php?act=agreement',
    method:'POST',
    data:{
      from:from,
      openid:wx.getStorageSync('openid')
    },
    success:(res)=>{
      let data=res.data
      if(data.status){
        wx.showModal({
          title: '',
          content: data.msg,
          showCancel:false,
          success:(res)=>{
            wx.reLaunch({
              url: '/pages/index/index',
            })
          }
        })

      }else{
        wx.showToast({
          title: data.msg,
          icon:'none',
          mask:true
        })
      }
    }
  })
}
})