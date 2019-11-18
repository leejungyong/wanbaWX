const app = getApp()
var selectedType = 0
Page({
  _data: {
    task: null,
    taskid: 0,
    teamid: 0,
    aid: 0,
    pvalue: 0
  },

  data: {
    pics: null,
    videos: null,
    memo: '',
    imgUrl: app.globalData.config.imgUrl
  },

  updateMemo: function(e) {
    this.setData({
      memo: e.detail.value
    })
  },
  selectType() {
    let that = this
    wx.showActionSheet({
      itemList: ['上传照片', '上传视频'],
      success(res) {
        selectedType = res.tapIndex
        switch (selectedType) {
          case 0:
            that.chooseImg()
            break
          case 1:
            wx.showModal({
              title: '提示：',
              content: '支持10秒短视频上传',
              showCancel: false,
              confirmText: '我知道了',
              success:(res)=>{
                that.chooseV()
              }
            })
            
            break
          default:

        }
      }
    })
  },
  chooseV() {
    let that = this
    wx.chooseVideo({
      sourceType: ['album'],
      maxDuration: 10,
      camera: 'back',
      success(res) {
        //console.log(res.tempFilePath)
        that.setData({
          videos: res.tempFilePath
        })

        
      }
    })
  },
  chooseImg() {
    let that = this
    let pics = that.data.pics
    let cnt = pics ? pics.length : 0
    let count = 9 - cnt
    wx.chooseImage({
      count: count,
      sizeType: ['compressed'],
      sourceType: ['album', 'camera'],
      success: function(res) {

        if (pics) {
          let paths = res.tempFilePaths
          for (let i in paths) {
            pics.push(paths[i])
          }

        } else {
          pics = res.tempFilePaths
        }
        that.setData({
          pics: pics
        })
        //console.log(that.data.pics)
      }
    })
  },
  post() {
    let token = new Date().getTime();
    let cache = wx.getStorageSync('lastpost')
    if (cache) {
      let duration = token - cache
      if (duration < 3000) {
        wx.showToast({
          title: '手速有点过快呀，休息下，过几秒再点击吧',
          icon: 'none'
        })
        return
      }

    }
    let taskid = this._data.taskid
    let teamid = this._data.teamid
    let pvalue = this._data.pvalue
    let aid = this._data.aid

    wx.setStorageSync('lastpost', token)
    let paths = this.data.pics
    let memo = this.data.memo
    let video=this.data.videos
    if (memo == '' && !paths && !video)  {
      wx.showToast({
        title: '请输入任务完成情况',
        icon: 'none'
      })
      return
    }
    let that = this

    wx.showLoading({
      title: '数据提交中，请稍候',
      mask: true
    })
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=postAnswer',
      data: {
        teamid: teamid,
        taskid: taskid,
        pvalue: pvalue,
        memo: memo,
        aid: aid,
        token: token,
        openid: wx.getStorageSync('openid'),
        owner: this._data.task.owner.join(','),
        ptype: this._data.task.ptype,
        mine: this._data.task.mine,
        displayorder: this._data.task.displayorder
      },
      method: 'POST',
      success: (res) => {
        let resp = res.data
        //console.log(resp)
        if (resp.status) {
          //上传图片
          if (selectedType == 0) {
            if (paths) {

              wx.showLoading({
                title: '图片上传中',
                mask: true
              })
              for (let i in paths) {
                wx.uploadFile({
                  url: app.globalData.config.apiUrl + 'uploadtask.php',
                  filePath: paths[i],
                  name: 'file',
                  formData: {
                    'logid': resp.id,
                    'aid': aid
                  },
                  success: function(res) {
                    console.log(res)
                  }
                })
              }
              wx.hideLoading()

            }
          }
          //上传视频
           else if (selectedType == 1) {
            let video=that.data.videos
            if(video){
            wx.uploadFile({
              url: app.globalData.config.apiUrl + 'uploadvideo.php',
              filePath: video,
              name: 'file',
              formData: {
                'logid': resp.id,
                'aid': aid
              },
              success: function (res) {
                console.log(res)
              }
            })
            }
            wx.hideLoading()
          }

          wx.showToast({
            title: '提交成功，请等待评判',
            icon: 'none'
          })
          //that.disabled = false
          setTimeout(
            () => {
              wx.reLaunch({
                url: './main?aid=' + aid,
              })
            }, 2000)
        } else {
          wx.showToast({
            title: '操作失败',
            icon: 'none'
          })
        }
      },
      fail: (res) => {
        //console.log(res)
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })


      }
    })
    wx.hideLoading()

  },
  onLoad: function(options) {
    let task = JSON.parse(options.ops)
    this._data.task = task
    this._data.teamid = options.teamid
    //console.log(task)
    this._data.taskid = task.taskid
    this._data.pvalue = task.pvalue
    this._data.aid = task.aid

  },


})