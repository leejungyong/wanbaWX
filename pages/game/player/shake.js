var audioCtx,aid;
const app=getApp()
Page({

  data: {
    open:true,
    flag:0,
    todoFlag:-1,
    posid:1,
    txt:'',
    hasResult: -1,
    bar_state: 0,
    winWidth: 0,
    winHeight: 0,
    img_url: "https://www.wondfun.com/wanba/img/img_yaoyiyao.png",
    loading: "https://www.wondfun.com/wanba/img/small_loading.gif",
    audio1:'https://img.wondfun.com/wanba/api/audio/shake1.mp3',
    audio2: 'https://img.wondfun.com/wanba/api/audio/shake2.mp3'
  },


  onLoad: function (options) {
   aid=options.aid
   this.fetch()
  },
  fetch(){
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getRedbagTodo',
      data: {
        aid: aid,
        openid:wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
       console.log(data)
       if(data){
       if(data.status==0){
         that.setData({
           todoFlag:0,
           posid:data.displayorder,
           txt: '请前往' + data.displayorder +'号点位完成任务获得红包',
           hasResult:1,
           flag:1,
           open:true
         })
       }else if(!data.open){
         that.setData({
           todoFlag: 0,
           posid: 1,
           txt: '客官过会再来瞧瞧吧',
           hasResult: 1,
           flag: 1,
           open:false
         })
       }
       }
      },
      fail: (err) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
   // console.log(this.data)
  },
  onReady: function () {
    audioCtx = wx.createAudioContext('myAudio') 
    // var context = wx.createContext()
    // context.rect(5, 5, 25, 15)
    // context.stroke()
    // context.drawImage()
    // wx.drawCanvas({
    //   canvasId: 'myCanvas',
    //   actions: context.getActions()
    // })
  },
  //重力加速度
  onAccelerometerChange(res) {
    let that = this
   
      if (res.x > .7 && res.y > .7 && that.data.flag < 1) {
        
        that.playAudio(that.data.audio1)
        that.setData({
          flag: 1
        })
        that.startAnimation();
       
      }
   
  },
  
  getSysInfo(){
    var that = this;
    //获取系统信息 
    wx.getSystemInfo({
      success: function (res) {
        that.setData({
          winWidth: res.windowWidth,
          winHeight: res.windowHeight
        });
      }
    });
  },
  download(){
    var that = this    
    wx.downloadFile({
      url: that.data.img_url,
      success: function (sres) {
        console.log(sres);
      }, fail: function (fres) {

      }
    })
  },
  onShow: function () {
    var that = this;
    that.initAnimation();
    that.getSysInfo()
    that.download()
  },
  rand(n){
    return Math.floor(Math.random() * n + 1)
  },
  playAudio(mp3){
    
    audioCtx.setSrc(mp3) //音频文件，第三方的可自行选择
    audioCtx.play() //播发音频
  },
  initAnimation: function () {
    var that = this;
    //实例化一个动画
    this.animation1 = wx.createAnimation({
      // 动画持续时间，单位ms，默认值 400
      duration: 400,
      /**
      * http://cubic-bezier.com/#0,0,.58,1 
      * linear 动画一直较为均匀
      * ease 从匀速到加速在到匀速
      * ease-in 缓慢到匀速
      * ease-in-out 从缓慢到匀速再到缓慢
      * 
      * http://www.tuicool.com/articles/neqMVr
      * step-start 动画一开始就跳到 100% 直到动画持续时间结束 一闪而过
      * step-end 保持 0% 的样式直到动画持续时间结束 一闪而过
      */
      timingFunction: 'ease',
      // 延迟多长时间开始
      // delay: 100,
      /**
      * 以什么为基点做动画 效果自己演示
      * left,center right是水平方向取值，对应的百分值为left=0%;center=50%;right=100%
      * top center bottom是垂直方向的取值，其中top=0%;center=50%;bottom=100%
      */
      transformOrigin: 'left top 0',
      success: function (res) {
        console.log(res)

      }
    })
    //实例化一个动画
    this.animation2 = wx.createAnimation({
      // 动画持续时间，单位ms，默认值 400
      duration: 400,
      /**
      * http://cubic-bezier.com/#0,0,.58,1 
      * linear 动画一直较为均匀
      * ease 从匀速到加速在到匀速
      * ease-in 缓慢到匀速
      * ease-in-out 从缓慢到匀速再到缓慢
      * 
      * http://www.tuicool.com/articles/neqMVr
      * step-start 动画一开始就跳到 100% 直到动画持续时间结束 一闪而过
      * step-end 保持 0% 的样式直到动画持续时间结束 一闪而过
      */
      timingFunction: 'ease',
      // 延迟多长时间开始
      // delay: 100,
      /**
      * 以什么为基点做动画 效果自己演示
      * left,center right是水平方向取值，对应的百分值为left=0%;center=50%;right=100%
      * top center bottom是垂直方向的取值，其中top=0%;center=50%;bottom=100%
      */
      transformOrigin: 'left top 0',
      success: function (res) {
        console.log(res)
      }
    })
  },
  /**
  *位移
  */
  startAnimation: function () {
    var that = this
    //x轴位移100px
    var h1 = "35%";
    var h2 = "65%";
    
    if (this.data.bar_state == 1) {
      h1 = "39.9%";
      h2 = "39.9%";
      setTimeout(function () {
        that.setData({
          //输出动画
          bar_state: 0,
          hasResult: 0
        })
        let r = that.rand(49)
        setTimeout(function () {
           
          that.setData({
            hasResult: 1,
            posid:r,
            txt: '请前往' + r+ '号点位完成任务获得红包',
            flag:0
          })
          that.playAudio(that.data.audio2)
        }, 4000)
      }, 400)
    } else {
      h1 = "25%";
      h2 = "55%";
      this.setData({
        bar_state: 1
      })
      setTimeout(function () {
        that.startAnimation();
      }, 600)
    }
    
    this.animation1.height(h1).step()
    this.animation2.top(h2).step()
    this.setData({
      //输出动画
      animation1: that.animation1.export(),
      animation2: that.animation2.export()
    })

  },

  todo(){
    let data={
      aid:aid,
      openid:wx.getStorageSync('openid'),
      displayorder:this.data.posid
    }
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=insertRedbagTodo',
      data:{
        data:data
        },
      method:'POST',
      success:(res)=>{
        let data=res.data
         if(data.status){
           this.setData({
             todoFlag:0,
             hasResult:1,
             flag:1
           })
         }
       
      },
      fail:(err)=>{
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },

//  shakeFun() { // 摇一摇方法封装

//     let that=this
//     var numX = 1 //x轴
//     var numY = 1 // y轴
//     var numZ = 0 // z轴
//     var stsw = true // 开关，保证在一定的时间内只能是一次，摇成功
//     var positivenum = 0 //正数 摇一摇总数
//     var audioCtx = wx.createAudioContext('myAudio') //音频，用于摇成功提示
//     wx.onAccelerometerChange(function (res) {  //小程序api 加速度计
//     console.log(res)
//       if (numX < res.x && numY < res.y) {  //个人看法，一次正数算摇一次，还有更复杂的
//         positivenum++
//         setTimeout(() => { positivenum = 0 }, 2000) //计时两秒内没有摇到指定次数，重新计算
//       }
//       if (numZ < res.z && numY < res.y) { //可以上下摇，上面的是左右摇
        
//         positivenum++
//         setTimeout(() => { positivenum = 0 }, 2000) //计时两秒内没有摇到指定次数，重新计算
//       }
//       if (positivenum == 2 && stsw) { //是否摇了指定的次数，执行成功后的操作
//         stsw = false
//         audioCtx.setSrc(that.data.audio2) //音频文件，第三方的可自行选择
//         audioCtx.play() //播发音频
//         console.log('摇一摇成功')
//         setTimeout(() => {
//           positivenum = 0 // 摇一摇总数，重新0开始，计算
//           stsw = true
//         }, 2000)
//       }
//     })
//   }
})