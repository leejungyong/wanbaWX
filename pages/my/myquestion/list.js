const app = getApp()

Page({
  data: {
    swiper: null,
    list: null,
    circular: true,
    indicatorDots: false,
    indicatorcolor: "#000",
    vertical: false,
    autoplay: true,
    interval: 2500,
    duration: 100,
    imgheights: [],
    imgwidth: 750,
    current: 0,
    imgwidth: 0,
    imgheight: 160,
    page: 0,
    pages: 0,
    pagesize: 10,
    init: true
  },
  stopLoadMoreTiem: false,
  new() {

    wx.navigateTo({
      url: './question',
    })
  },
  to: function (e) {
    let that = this
    let id = e.currentTarget.id
    let url = that.data.swiper[id].url
    if (url.indexOf('/pages/') > -1) {
      wx.navigateTo({
        url: url
      })
    } else {
      wx.navigateTo({
        url: '/pages/index/showWx?url=' + url
      })
    }
  },
  exportData(e) {
    let id = e.currentTarget.id
    let temp = []
    let data = this.data.list[id]
    temp.push(data)
    console.log(temp)
    wx.request({
      url: app.globalData.config.apiUrl + 'exportq.php',
      data: {
        data: temp
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        wx.setClipboardData({
          data: '导出数据下载地址：' + app.globalData.config.apiUrl + res.data,
          success(res) {
            wx.getClipboardData({
              success(res) {
                console.log(res.data) // data
              }
            })
          }
        })
        wx.showModal({
          title: '提示',
          content: '导出成功，数据下载地址已复制到剪贴板',
          showCancel: false,
          success(res) {

          }
        })
        // let data=res.data
        // if(data.status){
        //   wx.setClipboardData({
        //     data: '导出数据下载地址：'+app.globalData.config.apiUrl+'export.xlsx',
        //     success(res) {
        //       wx.getClipboardData({
        //         success(res) {
        //           console.log(res.data) // data
        //         }
        //       })
        //     }
        //   })
        //    wx.showModal({
        //      title: '提示',
        //      content: '导出成功，数据下载地址已复制到剪贴板',
        //      showCancel:false,
        //      success(res) {

        //      }


        //    })
        // }else{

        // }
      },
      fail: (err) => {

      }
    })

  },
  postSysQuestion(e) {
    let that = this
    let id = e.currentTarget.id
    let list = that.data.list
    let qid = list[id].questionid
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=postSysQuestionApply',
      data: {
        qid: qid,
        uid: wx.getStorageSync('openid'),
        token: wx.getStorageSync('token')
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
        if (data.status) {
          list[id].applystatus = 0
          that.setData({
            list: list
          })
          wx.showModal({
            title: '感谢您的推荐',
            content: '一经采纳，您的推荐即将进入系统题库',
            showCancel: false,
            success: (res) => {

            }
          })

        } else {
          wx.showToast({
            title: data.msg,
            icon: 'none'
          })
        }
      },
      fail: (err) => {

        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  delQuestion(e) {
    let that = this
    let id = e.currentTarget.id
    let list = that.data.list
    let qid = list[id].questionid
    wx.showModal({
      title: '警告',
      content: '确定要删除此题目吗？',
      success: (res) => {
        if (res.confirm) {
          wx.request({
            url: app.globalData.config.apiUrl + 'index.php?act=delQuestion',
            data: {
              qid: qid,
              openid: wx.getStorageSync('openid')
            },
            method: 'POST',
            success: (res) => {
              console.log(res)
              let data = res.data
              if (data.status) {
                wx.showToast({
                  title: data.msg,
                  icon: 'none'
                })
                list.splice(id, 1)
                that.setData({
                  list: list
                })
              } else {
                wx.showToast({
                  title: data.msg,
                  icon: 'none'
                })
              }
            },
            fail: (err) => {

              wx.showToast({
                title: '网络错误',
                icon: 'none'
              })
            }
          })
        }
      }
    })
  },
  editQuestion(e) {
    let id = e.currentTarget.id
    let ops = this.data.list[id]
    // console.log(qid)
    wx.navigateTo({
      url: './question?ops=' + JSON.stringify(ops),
    })
  },
  imageLoad: function(e) { //获取图片真实宽度  
    var imgwidth = e.detail.width,
      imgheight = e.detail.height,
      //宽高比  
      ratio = imgwidth / imgheight;
    //console.log(imgwidth, imgheight)
    //计算的高度值  
    var viewHeight = 750 / ratio;
    var imgheight = viewHeight;
    var imgheights = this.data.imgheights;
    //把每一张图片的对应的高度记录到数组里  
    imgheights[e.target.dataset.id] = imgheight;
    this.setData({
      imgheights: imgheights
    })
  },



  bindchange: function(e) {
    // console.log(e.detail.current)
    this.setData({
      current: e.detail.current
    })
  },

  fetch() {
    wx.showLoading({
      title: '加载中',
    })
    let that = this
    that.stopLoadMoreTiem = true
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getMyQuestionlist',
      data: {
        openid: wx.getStorageSync('openid'),
        currentpage: that.data.page,
        pagesize: that.data.pagesize
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        that.stopLoadMoreTiem = false
        wx.hideLoading()
        let data = res.data
        let pagenum = Math.ceil(data.total / that.data.pagesize)
        if (that.data.init) {
          that.setData({
            swiper: data.swiper,
            list: data.list,
            pages: pagenum,
            init: false
          })
        } else {
          let newList = that.data.list.concat(data.list)
          that.setData({
            list: newList,
            pages: pagenum,
          })
        }
      },
      fail: (err) => {
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  onReachBottom: function() {
    let that = this

    if (!that.stopLoadMoreTiem && (that.data.page < that.data.pages - 1)) {
      this.setData({
        page: that.data.page + 1
      })
      this.fetch()
    }


  },
  onLoad: function(options) {
    this.fetch()
  }

})