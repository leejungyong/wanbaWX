var aid, teamid,i = 0
var gotStone = false
var openid = wx.getStorageSync('openid')
var audioCtx
// var animation = wx.createAnimation({
//   duration: 3000,
//   delay: 0,
//   timingFunction: 'ease-in-out',
// });
var takePhotoFinished=true
const app = getApp()
Page({


  data: {
    moveData:null,
    showWanba: false,
    showCatchStone: false,
    showMissStone: false,
    stoneSelected: null,
    rewardtype:null,
    timer1: '',
    timer2: '',
    timer3:'',
    tips: [
      '请对准被拍摄物保持三秒以上',
      '万物皆有灵性',
      '奔跑吧兄弟',
      '行万里路，拿万颗宝石'
    ],
    keyword: '',
   audio1: 'https://img.wondfun.com/wanba/img/redbag/redbag.mp3',
    audio2: 'https://img.wondfun.com/wanba/img/redbag/diamond_2.mp3',
    audio3: 'https://img.wondfun.com/wanba/img/redbag/ao.mp3'
  },
  onReady: function () {
    audioCtx = wx.createAudioContext('myAudio')
   
  },
  playAudio(mp3) {

    audioCtx.setSrc(mp3) //音频文件，第三方的可自行选择
    audioCtx.play() //播发音频
  },
  move(){
   
   var that=this
   setTimeout(function(){
     console.log('scan')
     animation.translate(0, 200).step({ duration: 3000 })
     that.setData({ moveData: animation.export() })
     i++
   }.bind(that),3000)
   if(i==2){
     console.log('scan1')
     animation.translate(200, 0).step({ duration: 3000 })
     that.setData({ moveData: animation.export() })
     i=0
   }else{
     console.log('scan2')
     animation.translate(0, 200).step({ duration: 3000 })
     that.setData({ moveData: animation.export() })
   }
    setTimeout(function () {
      console.log(i)
      that.move()

    }.bind(that), 3000)
    
  },
  fetch() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getAITips',
      data: {
        aid: aid
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
        let arr = data.map((item) => {
          return item.tip
        })
        let oldarr = this.data.tips
        let newarr = oldarr.concat(arr)
        console.log(newarr)
        that.setData({
          tips: newarr
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
  hideStone() {
    this.setData({
      showCatchStone: false,
      showMissStone: false
    })
    this.startTimer()
  },
  startTimer() {
    let that = this
    that.data.timer1 = setInterval(() => {

      that.takePhoto()
    }, 10000)
    that.data.timer2 = setInterval(() => {

      that.showTip()
    }, 8000)
    // that.data.timer3 = setInterval(function() {

    //   that.move()
    // }.bind(that), 3000)
  },
  getRandomStone() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getRandomStone',
      data: {
        teamid: teamid,
        aid: aid,
        keyword:that.data.keyword
      },
      method: 'POST',
      success(res) {
        console.log(res.data)
        let data = res.data
        var rewardtype,reward;
        if (data.status) {
          if(data.redbag){
            rewardtype=2
            reward = data.redbag
            that.playAudio(that.data.audio1)
          }else{
           rewardtype = 1
            reward = data.stonetype
            that.playAudio(that.data.audio2)
          }
          that.setData({
            rewardtype: rewardtype,
            stoneSelected: reward,
            showWanba: false,
            showCatchStone: true,
            showMissStone: false
          })
        } else {
          
          that.setData({
            showWanba: false,
            showCatchStone: false,
            showMissStone: true
          })
          that.playAudio(that.data.audio3)
        
        }

      },
      fail(res) {
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  
  closeWanba() {
    let token = new Date().getTime();
    let cache = wx.getStorageSync('lastpost')
    wx.setStorageSync('lastpost', token)
    if (cache) {
      let duration = token - cache

      if (duration < 5000) {
        wx.showToast({
          title: '手速有点过快呀，休息下，过几秒再点击吧',
          icon: 'none'
        })

      } else {
        
          this.getRandomStone()
         
      }

    }

  },
  
  onUnload() {
    
    let that = this
    clearInterval(that.data.timer1)
    clearInterval(that.data.timer2)
    console.log('unload')
    // clearInterval(that.data.timer3)
    if(!takePhotoFinished){
      wx.showToast({
        title: '正在拍照识别，请稍后再退出',
        icon:'none'
      })
      return
    }
  },
 
  onLoad: function(options) {
    aid = options.aid
    teamid = options.teamid
    this.ctx = wx.createCameraContext()
  // this.move()
    this.fetch()
    setTimeout(() => {
      wx.showToast({
        title: this.data.tips[0],
        duration: 2000,
        icon: 'none'
      })
    }, 3000)

    this.startTimer()

  },
  showTip() {

    let arr = this.data.tips

    let n = Math.floor(Math.random() * arr.length + 1) - 1
    console.log(arr[n])
    wx.showToast({
      title: arr[n],
      duration: 2000,
      icon: 'none'
    })
  },
  takePhoto() {
    let that = this
    takePhotoFinished=false
    this.ctx.takePhoto({
      quality: 'high',
      success: (res) => {
        wx.uploadFile({
          url: app.globalData.config.apiUrl + 'uploadaiphoto.php',
          filePath: res.tempImagePath,
          name: 'file',
          formData: {
            'openid': openid,
            'aid': aid
          },
          success: function(res) {
            //拍照完成后置标记为true，unload事件里才可以退出
            console.log(res)
            takePhotoFinished=true
            let data = JSON.parse(res.data)

            console.log(data)

             
              that.setData({
                keyword:data,
                showWanba: true
              })
              clearInterval(that.data.timer1)
              clearInterval(that.data.timer2)
   }
        })
      }
    })
  },

})