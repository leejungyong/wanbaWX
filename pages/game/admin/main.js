var aid,title,cat=null
const app = getApp()
Page({
  
  data: {
    task: null
  },
  homepage() {
    wx.navigateBack({
      
    })
  
  },
  stone(){
    console.log(title)
    wx.navigateTo({
      url: './stoneList?aid=' + aid+'&act_title='+title+'&cat='+cat
    })
  },
  summary() {
    wx.navigateTo({
      url: './summary?aid=' + aid 
    })
  },
  
  topBoard() {

    wx.navigateTo({
      url: './topBoard?aid=' + aid,
    })
  },

  reload() {
    this.fetchData()
  },
  setting() {

    wx.navigateTo({
      url: './setting?aid=' + aid 
    })
  },

  view: function (e) {
    let id = e.currentTarget.id
    let task = JSON.stringify(this.data.task[id])
     console.log(task)
    task = task.replace(/\?/g, '？')
    task = task.replace(/\&/g, '＆')
    wx.navigateTo({
      url: './view?task=' + task + '&index=' + id
    })
  },
  onLoad: function (options) {
     aid = options.aid
     title=options.title
     cat=options.cat
    // console.log(title)
    this.fetchData()
  },
  fetchData() {
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=allUpload',
      data: {
        aid: aid
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        //console.log(data)
        that.setData({
          task: data
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
  onShareAppMessage: function (ops) {
    // let that = this
    // console.log(ops)
    // if (ops.from === 'button') {
     
    // }
    // return {
    //   title: title,
    //   path: 'pages/game/splash?aid=' + aid,
    //   imageUrl: app.globalData.config.imgUrl + 'wanba/img/sharepic/1.jpg',
    //   success: function (res) {
        
    //   },
    //   fail: function (res) {
        
    //   }
    // }
  },
  onPullDownRefresh() {
    wx.showNavigationBarLoading();
    this.fetchData()
    wx.hideNavigationBarLoading();
    wx.stopPullDownRefresh()
  },
  scan() {
    wx.navigateTo({
      url: './judge',
    })
  }
})