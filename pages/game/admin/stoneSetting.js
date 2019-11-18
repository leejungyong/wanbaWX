var aid=0
const app=getApp()
Page({


  data: {
    param1:10,
    param2: 0,
    array:['0', '1','1,2,3','1,2,3,4','5,10', '5,10,20', '5,10,20,30', '5,10,20,30,50', '5,10,20,30,50,100', '5,10,20,30,50,100,150', '5,10,20,30,50,100,150,200'],
    index:0
  },

  onLoad: function (options) {
    aid=options.aid
    this.fetch()
  },
  onUnload(){
    this.postData()
  },
  bindPickerChange(e){
    console.log(e.detail.value)
    this.setData({
      index:e.detail.value
    })
  },
  postData(){
    let that=this
    let ai_random=that.data.param2
    if (ai_random == '' || isNaN(parseInt(ai_random)) || parseInt(ai_random) < 0 || parseInt(ai_random) > 10000) {
      ai_random =0
    }
    // console.log(that.data.array[that.data.index])
     let redbagTotal=that.data.param1
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=postAIBaseSetting',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid'),
        // ai_duration: ai_duration,
        ai_random: ai_random,
        redBag:that.data.array[that.data.index],
        redbagTotal: redbagTotal
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
       

      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }

    })
  },
  fetch(){
    let that = this
     wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getAIBaseSetting',
      data: {
        aid: aid,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)

        let str=data.redbagrand
        let index=that.data.array.indexOf(str)
        console.log(index)
        that.setData({
          param2: data.ai_random,
          param1:data.redbagtotal,
          index:index
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
  aiPicsSetting() {
    wx.navigateTo({
      url: './aiPicsSetting?aid=' + aid
    })
  },
  tipsSetting() {
    wx.navigateTo({
      url: './tipsSetting?aid=' + aid 
    })
  },
  slider1change(e){
    this.setData({
      param1:e.detail.value
    })
  },
  updateParam2(e) {
    this.setData({
      param2: e.detail.value
    })
  }

})