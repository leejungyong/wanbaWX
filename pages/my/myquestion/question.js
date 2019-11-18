const app = getApp()
Page({


  data: {
    imgUrl: app.globalData.config.imgUrl,
    uploadUrl: app.globalData.config.uploadUrl,
    qid: 0,
    qarr: ['人工判定', '回答精确匹配答案', '回答模糊匹配答案', '答案包含在回答里', '回答包含在答案里', '教练提交管理员判定', 'N选3'],
    index: 0,
    qtype: '',
    memo: '',
    pics: null,
    photos: null,
    media: 0,
    url: '',
    answer: '',
    cat: '',
    tag: '',
    videos:null
  },

  bindPickerChange(e) {
    let index = e.detail.value
     if(index==0 || index==5){
    this.setData({
      index: index,
      qtype: this.data.qarr[index],
      answer:''
    })
  }else{
       this.setData({
         index: index,
         qtype: this.data.qarr[index]
       })
  }

  },
  updateAnswer(e) {
    this.setData({
      answer: e.detail.value
    })
  },
  updateMemo(e) {
    this.setData({
      memo: e.detail.value
    })
  },
  beforePost() {
    let that = this
    if (that.data.memo == ''   || that.data.qtype == '') {
      wx.showToast({
        title: '星号*为必填项',
        icon: 'none'
      })
      return false
    }
    if ((that.data.index != 0 && that.data.index != 5) && that.data.answer == ''){
      wx.showToast({
        title: '请设置答案',
        icon: 'none'
      })
      return false
    }
    let token = new Date().getTime();
    let cache = wx.getStorageSync('lastpost')
    wx.setStorageSync('lastpost', token)
    if (cache) {
      let duration = token - cache
      
      if (duration < 10000) {
        wx.showToast({
          title: '手速有点过快呀，休息下，过几秒再点击吧',
          icon: 'none'
        })
        return false
      }

    }
    return true
  },
  delPic(e) {
    let that = this
    let id = e.currentTarget.id
    console.log(id)
    let pics = this.data.pics
    console.log(pics[id])
    let pic = pics[id]
    if (pic.indexOf('http://tmp/') > -1 || pic.indexOf('wxfile://') > -1) {
       pics.splice(id,1)
    this.setData({
      pics:pics
    })
    } else {
      wx.request({
        url: app.globalData.config.apiUrl + 'index.php?act=delQustionPic',
        data: {
          index:id,
          picurl: pics[id],
          questionid: that.data.qid,
          openid: wx.getStorageSync('openid')
        },
        method: 'POST',
        success: (res) => {

          let data = res.data
          console.log(data)
           if(data.status){
             pics.splice(id, 1)
             this.setData({
               pics: pics
             })
             wx.showToast({
               title: data.msg,
               icon:'none'
             })
           }else{
             wx.showToast({
               title: '删除图片失败，请重试',
               icon: 'none'
             })
           }
        },
        fail: (err) => {
          wx.showToast({
            title: '   网络错误',
            icon: 'none'
          })
        }
      })
    }
    
  },
  preview(e) {
    let id = e.currentTarget.id
    let pics = this.data.pics
    console.log(id)
    wx.previewImage({
      current: pics[id],
      urls: pics
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
              success: (res) => {
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
        console.log(that.data.pics)
      }
    })
  },
  save() {

    let that = this
    let status = that.beforePost()
    let paths = that.data.pics
    if (status) {
      // console.log(that.data)
      new Promise(that.postData).then(function(data) {
        console.log(data)
        if (data.status) {
          if (paths && paths.length>0) {

           
            for (let i in paths) {
              if (paths[i].indexOf('http://tmp/') > -1 || paths[i].indexOf('wxfile://') > -1) {
                wx.uploadFile({
                  url: app.globalData.config.apiUrl + 'uploadquestionpic.php',
                  filePath: paths[i],
                  name: 'file',
                  formData: {
                    'questionid': data.questionid,
                    'openid': wx.getStorageSync('openid'),
                    'index': i
                  },
                  success: function(res) {
                    //console.log(res)
                    
                  }
                })
              }
            }
           
          }
          let video = that.data.videos
          if (video && (video.indexOf('http://tmp/') > -1 || video.indexOf('wxfile://') > -1)) {
            wx.uploadFile({
              url: app.globalData.config.apiUrl + 'uploadquestionvideo.php',
              filePath: video,
              name: 'file',
              formData: {
                'questionid': data.questionid,
                'openid': wx.getStorageSync('openid')
              },
              success: function (res) {
                console.log(res)
              }
            })
          }
          wx.hideLoading()
           
            let pages = getCurrentPages()
            let prePage = pages[pages.length - 2]
            prePage.fetch()

            setTimeout(() => {
              wx.showToast({
                title: '   操作成功',
                icon: 'none',
                mask: true
              })
              wx.navigateBack()
            }, 1500)
          

        } else {
          wx.showToast({
            title: data.msg,
            icon: 'none'
          })
        }

      }).catch(function(err) {
        console.log(err)
        // wx.showToast({
        //   title: reason,
        //   icon: 'none'
        // })
      });
    }
  },
  postData(resolve, reject) {
    let that = this
    let act = 'newQuestion'
    
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=' + act,
      data: {
        questiondata: that.data,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        // console.log(res)
        let data = res.data
        resolve(data);

      },
      fail: (err) => {
        reject('网络错误');
      }
    })
  },

  onLoad: function(options) {
    if (options.ops) {
      let ops = JSON.parse(options.ops);
      console.log(ops)
      let index = ops.qtype
      let qtype = this.data.qarr[index]
      wx.request({
        url: app.globalData.config.apiUrl + 'index.php?act=getQuestionPics',
        data: {
          qid: ops.questionid,
          openid: wx.getStorageSync('openid')
        },
        method: 'POST',
        success: (res) => {
           console.log(res.data)
          let data = res.data
          let video=data.video
          let temp = []

          let photos = data.pics
          for (let i in photos) {
            temp.push(photos[i].url)
          }
          this.setData({
            qid: ops.questionid,
            index: index,
            qtype: qtype,
            memo: ops.memo,
            answer: ops.answer,
            media: ops.media,
            cat: ops.cat,
            tag: ops.tag,
            url: ops.url,
            photos: data,
            videos: video.url ? video.url :'',
            pics: temp
          })
        },
        fail: (err) => {
          console.log('网络错误');
        }
      })

    }
    //console.log(this.data)
  }

})