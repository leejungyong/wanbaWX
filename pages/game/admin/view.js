const app = getApp()
Page({

  data: {
    cdn: app.globalData.config.cdn,
    apiUrl:app.globalData.config.apiUrl,
    flag: true,
    logid: 0,
    passid:0,
    index: 0,
    taskid: 0,
    aid: 0,
    teamid: 0,
    task: null,
    taskArr: null,
    inputTxt: '',
    uploadUrl: app.globalData.config.uploadUrl
  },
  previewImg(e) {
    let id = e.currentTarget.id
    let url = this.data.task.pic[id].url
    let urls = []
    urls.push(url)
    wx.previewImage({
      urls: urls
    })
  },
  preview(e) {
    let id = e.currentTarget.id
    let url = app.globalData.config.apiUrl+'upload/' + this.data.task.uploadpic[id].url
    let urls = []
    urls.push(url)
    wx.previewImage({
      urls: urls
    })
  },
  preventTouchMove: function () {

  },
  updateTxt(e) {
    this.setData({
      inputTxt: e.detail.value
    })
  },
  passTask() {
    let that = this
    let passid = that.data.passid
    let index = that.data.index
    let taskArr = that.data.taskArr
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=pass',
      data: {
        passid: passid,
        taskid: that.data.task.taskid,
        aid: taskArr.aid,
        teamid: that.data.task.teamid,
        pvalue: taskArr.pvalue,
        creator:wx.getStorageSync('openid'),
        owner: taskArr.owner,
        ptype: taskArr.ptype,
        mine: taskArr.mine,
        displayorder: taskArr.displayorder,
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        //console.log(data)
        let msg = data.status ? '操作成功' : '操作失败'
        if (data.status) {
          let pages = getCurrentPages()
          let prePage = pages[pages.length - 2]
          let task = prePage.data.task
          task.splice(index, 1)
          prePage.setData({
            task: task
          })
        }
        wx.showToast({
          title: msg,
          icon: 'none'
        })
        setTimeout(() => {
          wx.navigateBack()
        }, 2000)

      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }

    })
  },
  pass() {
    let that = this
    wx.showModal({
      title: '提示',
      content: '   确定通过审核吗',
      success: function (res) {
        if (res.confirm) {
          that.passTask()
        } else if (res.cancel) {
          console.log('用户点击取消')
        }
      }
    })

  },
  deny() {
    let that = this
    
    that.setData({
      flag: false
    })
  },
  hide() {
    this.setData({
      flag: true
    })
  },
  denyTask() {
    //console.log(this.data.inputTxt)
    if (this.data.inputTxt == '') {
      wx.showToast({
        title: '请填写理由',
        icon: 'none'
      })
      return
    }
    let that = this

    let passid = that.data.passid
    let index = that.data.index
    let taskArr = that.data.taskArr
    let reason = that.data.inputTxt
     //console.log(taskArr)
    // console.log(taskArr.fee)
    //return
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=deny',
      data: {
        passid: passid,
        taskid: that.data.task.taskid,
        aid: taskArr.aid,
        teamid: that.data.task.teamid,
        pvalue: taskArr.pvalue,
        event: reason,
        creator: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        //console.log(data)
        let msg = data.status ? '操作成功' : '操作失败'
        if (data.status) {
          let pages = getCurrentPages()
          let prePage = pages[pages.length - 2]
          let task = prePage.data.task
          task.splice(index, 1)
          prePage.setData({
            task: task
          })
        }
        wx.showToast({
          title: msg,
          icon: 'none'
        })
        setTimeout(() => {
          wx.navigateBack()
        }, 2000)
      },
      fail: (res) => {
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }

    })
  },

  onLoad: function (options) {
   // console.log(options)
   
    let that = this
    let index = options.index
    let taskArr = JSON.parse(options.task)
    console.log(taskArr)
    let logid = taskArr.logid
    let passid=taskArr.passid
    //console.log(id)
    wx.request({
      url: app.globalData.config.apiUrl+'index.php?act=viewUploadDetail',
      data: {
        logid: logid
      },
      method: 'POST',
      success: (res) => {
        let data = res.data
        console.log(data)
        that.setData({
          logid: logid,
          passid:passid,
          index: index,
          task: data,
          taskArr: taskArr
        })
       // console.log(that.data)
       if(data.uploadvideo){
         that.videoContext = wx.createVideoContext('myVideo')
       }
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