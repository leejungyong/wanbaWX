var questionid,aid,taskid,rnd
const app = getApp()
Page({
 

  data: {
    imgUrl: app.globalData.config.imgUrl, 
    question:'',
    answer:'',
    rnd:'',
    slogan:'让世界更好玩'
  },
  random(n) {
    return Math.floor(Math.random() * n )
  },
post(){
  let that=this

  let arr=rnd.split('|')
  let n=arr.length
  //console.log(n)
  let r=that.random(n)
  let score=arr[r]
 // console.log(score)
  wx.request({
    url: app.globalData.config.apiUrl + 'index.php?act=checkRedbagAnswer',
    data: {
      questionid: questionid,
      taskid: taskid,
      openid: wx.getStorageSync('openid'),
      aid:aid,
      answer:that.data.answer,
      score:score
    },
    method: 'POST',
    success: (res) => {
      let data = res.data
      console.log(data)
      wx.showModal({
        title: '',
        content: data.msg,
        showCancel:false,
        success:(res)=>{
          if(res.confirm){
            wx.navigateBack({
           delta:2
         })
          }
        }

      })
      //  wx.showToast({
      //    title: data.msg,
      //    icon:'none'
      //  })
      //  setTimeout(()=>{
      //    wx.navigateBack({
      //      delta:2
      //    })
      //  },2000)
    },
    fail: (err) => {
      wx.showToast({
        title: '网络错误',
        icon: 'none'
      })
    }
  })
},
  checkAnswer() {
    let that=this
    console.log(this.data.answer)
    console.log(questionid)
    if (this.data.answer==''){
        wx.showToast({
          title: '请输入回答',
          icon:'none'
        })
    }else{
      let token = new Date().getTime();
      let cache = wx.getStorageSync('lastpost_regbag')
wx.setStorageSync('lastpost_regbag', token)
      if (cache) {
        let duration = token -cache
        
        if (duration < 3000) {
          wx.showToast({
            title: '手速有点过快呀，休息下，过几秒再点击吧',
            icon: 'none'
          })
          return false
        }else{
          that.post()
        }

      }else{
        that.post()
        }
    }
  },
  updateAnswer(e) {
    this.setData({
      answer: e.detail.value
    })
  },


  randomQuestion() {
     wx.request({
       url: app.globalData.config.apiUrl +'index.php?act=getRandomQuestion',
       data:{
         aid:aid,
         taskid:taskid,
         openid:wx.getStorageSync('openid')
       },
       method:'POST',
       success:(res)=>{
         let data=res.data
         console.log(data)
         questionid = data.question.id
         rnd=data.config.redbagrand
          this.setData({
            question:data.question.question,
            slogan: data.config.slogan ? data.config.slogan :'让世界更好玩'
          })
       },
       fail:(err)=>{
         wx.showToast({
           title: '网络错误',
           icon: 'none'
         })
       }
     })
  },
 
  onLoad: function(options) {
       console.log(options)
       aid=options.aid
       taskid=options.taskid
       this.randomQuestion()
  },

})