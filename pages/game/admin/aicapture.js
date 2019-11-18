
const app = getApp()
var aid=0;
Page({

 
  data: {

  },

  onLoad: function (options) {
    aid=options.aid
    this.ctx = wx.createCameraContext()
    // const listener = this.ctx.onCameraFrame((frame) => {
    //   console.log(frame.data instanceof ArrayBuffer, frame.width, frame.height)
    // setInterval(() => {
    //   this.takePhoto()
    // }, 10000)
    setTimeout(()=>{
      this.takePhoto()
    },3000)

    // })
    // listener.start()
    // setTimeout(() => {
    //   wx.showToast({
    //     title: '恭喜你获得了一颗灵魂宝石',
    //     icon:'none'
    //   })
    // }, 10000)

  },
  takePhoto() {
    let that=this
    this.ctx.takePhoto({
      quality: 'high',
      success: (res) => {
        //console.log(res.tempImagePath)
        wx.uploadFile({
          url: app.globalData.config.apiUrl + 'uploadaicapture.php',
          filePath: res.tempImagePath,
          name: 'file',
          formData: {
            'openid': wx.getStorageSync('openid'),
            aid:aid
          },
          success: function (res) {
            let data = JSON.parse(res.data) 
           //console.log(data)
           wx.showModal({
             title: '识别结果',
             content: '系统自动识别到这是\"'+data.title+'\",要将此保存到特征图库吗？',
             success:((res)=>{
               if(res.confirm){
                 that.postData(data)
                 setTimeout(() => {
                   that.takePhoto()
                 }, 3000)
               }else{
                 setTimeout(() => {
                   that.takePhoto()
                 }, 3000)
               }
             })
           })
          }
        })
      }
    })
  },

  do() {

  },
   postData(data){
     let that=this
     wx.request({
       url: app.globalData.config.apiUrl + 'index.php?act=postAIPic',
       data: {
         data:data
       },
       method: 'POST',
       success: (res) => {
         let data = res.data
         //console.log(data)
         if (data.status) {
           let pages=getCurrentPages()
           let prepage=pages[pages.length-2]
           let arr = prepage.data.arr
           arr.unshift(data.data)
           prepage.setData({
             arr: arr
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
   }



})