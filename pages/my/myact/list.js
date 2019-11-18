import regeneratorRuntime from '../../../utils/runtime.js'
const app = getApp()
import {
  wxRequest
} from '../../../utils/wxrequest.js'


Page({
  data: {
    uploadUrl: app.globalData.config.uploadUrl,
    swiper: null,
    actNow: null,
    actFinished: null,
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
    navbar: ['进行中', '已完成'],
    currentTab: 0,
    index: 0,
    hideShareBox: true,
    page: 0,
    actnowpages:0,
    actfinishedpages:0,
    pagesize: 10,
    init: true
  },
  stopLoadMoreTiem: false,
  async updatePoint(openid, aid) {
    return await wxRequest(
      app.globalData.config.apiUrl + 'index.php?act=updatePoint', {
        hideLoading: false,
        data: {
          aid: aid,
          openid: wx.getStorageSync('openid')
        }
      }
    )
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
  postRouteApply(e) {
    let that = this
    let id = e.currentTarget.id
    let currentTab = that.data.currentTab
    let aid = currentTab == 0 ? this.data.actNow[id].aid : this.data.actFinished[id].aid
    wx.navigateTo({
      url: './routeapply?aid=' + aid + '&id=' + id + '&currentTab=' + currentTab,
    })
  },
  sharePage(id) {
    // console.log(id)
    let act = this.data.currentTab == 0 ? this.data.actNow[id] : this.data.actFinished[id]
  console.log(act)
    wx.navigateTo({
      url: './sharePage?ops=' + JSON.stringify(act),
    })
  },
  beforeShare(e) {
    let that = this
    let openid = wx.getStorageSync('openid')
    let id = e.currentTarget.id
    let aid = that.data.currentTab == 0 ? this.data.actNow[id].aid : this.data.actFinished[id].aid

    //检查点位任务是否都设置完整
    this.checkAct(aid)
      .then((ret) => {
      //  console.log(ret)
        if (ret.taskFlag) {
          //是否分享过了
          if (ret.actIsShared == 1) {
            //console.log('可以进入分享，不扣点')
            that.sharePage(id)
            // that.setData({
            //   index: id,
            //   hideShareBox: false
            // })
          } else {
            if (ret.point && ret.point >= 999) {
            //  console.log('扣点分享后')
              that.updatePoint(openid, aid)
                .then((ret) => {
                  if (ret.status) {
                    //console.log('可以进入分享')
                    that.sharePage(id)
                    // that.setData({
                    //   index: id,
                    //   hideShareBox: false
                    // })
                  } else {
                    wx.showToast({
                      title: ret.msg,
                      icon: 'none'
                    })
                  }
                })
            } else {
             // console.log('先购买')
              wx.showModal({
                title: '提示',
                content: '玩点不够',
                showCancel:false,
                confirmText:'我知道了',
                success(res) {
                    wx.navigateTo({
                      url: '/pages/shop/buyshare?aid='+aid,
                    })
                  
                }
              })
             
            }
          }
        } else {
          wx.showModal({
            title: '提示',
            showCancel: false,
            content: '请先设置完整活动信息，点位任务后才可以进行分享',
            success(res) {

            }
          })
        }
      })
      .catch((err) => {

      })
  },

  async checkAct(aid) {

    return await wxRequest(
      app.globalData.config.apiUrl + 'index.php?act=checkAct', {
        hideLoading: false,
        data: {
          aid: aid,
          openid: wx.getStorageSync('openid')
        }
      }
    )
  },
  newAct() {
    wx.navigateTo({
      url: './newAct',
    })
  },
  delAct(e) {
    let that = this
    let id = e.currentTarget.id
    let actNow = that.data.actNow
    let aid = actNow[id].aid
    wx.showModal({
      title: '警告',
      content: '确定要删除此活动吗？',
      success: (res) => {
        if (res.confirm) {
          wx.request({
            url: app.globalData.config.apiUrl + 'index.php?act=delAct',
            data: {
              aid: aid,
              openid: wx.getStorageSync('openid')
            },
            method: 'POST',
            success: (res) => {
              //console.log(res)
              let data = res.data
              if (data.status) {
                wx.showToast({
                  title: data.msg,
                  icon: 'none'
                })
                actNow.splice(id, 1)
                that.setData({
                  actNow: actNow
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
  editAct(e) {
    let id = e.currentTarget.id

    let aid = this.data.actNow[id].aid
    //console.log(aid)
    wx.navigateTo({
      url: '../../game/admin/setting?aid=' + aid,
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
  navbarTap: function(e) {
    this.setData({
      currentTab: e.currentTarget.dataset.idx,
      page:0
    })
  },
  fetch() {
    wx.showLoading({
      title: '加载中',
    })
    let that = this
    that.stopLoadMoreTiem = true
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=myActData',
      data: {
         currentpage: that.data.page,
        pagesize: that.data.pagesize,
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        that.stopLoadMoreTiem = false
       // console.log(res)
        let data = res.data
        let actnowpagenum = Math.ceil(data.actNowTotal / that.data.pagesize)
        //console.log(actnowpagenum)
        let actfinishedpagenum = Math.ceil(data.actFinishedTotal / that.data.pagesize)
        if (that.data.init) {
        that.setData({
          swiper: data.swiper,
          actNow: data.actNow,
          actFinished: data.actFinished,
          actnowpages: actnowpagenum,
          actfinishedpages: actfinishedpagenum,
          init: false
        })
        console.log(that.data)
        }else{
          if (that.data.currentTab == 0) {
            let newList = that.data.actNow.concat(data.actNow)
            that.setData({
              actNow: newList,
              actnowpages: actnowpagenum,
            })
          //  console.log(that.data)
          } else {
            let newList = that.data.actFinished.concat(data.actFinished)
            that.setData({
              list: newList,
              actfinishedpages: actfinishedpagenum,
            })
          }
        }

        wx.hideLoading()
      },
      fail: (err) => {
        that.stopLoadMoreTiem = false
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  },
  onLoad: function(options) {
    this.fetch()

  },
  hideShareBox() {
    this.setData({
      index: 0,
      hideShareBox: true
    })
  },
  onPullDownRefresh() {
    this.setData({
      page:0,
      init:true
    })
    wx.showNavigationBarLoading();
    this.fetch();
    wx.hideNavigationBarLoading();
    wx.stopPullDownRefresh()
  },
  // onShareAppMessage: function (ops) {
  //   let that = this
  //   console.log(ops)

  //   if (ops.from === 'button') {
  //     let id = ops.target.id
  //     let list = that.data.currentTab == 0 ? that.data.actNow : that.data.actFinished
  //     let data = list[id]
  //     that.hideShareBox()
  //     return {
  //       title: data.title,
  //       path: 'pages/game/splash?aid=' + data.aid,
  //       imageUrl: data.sharepic ? app.globalData.config.apiUrl + 'sharepic/' + data.sharepic : app.globalData.config.apiUrl + 'sharepic/1.jpg',

  //     }

  //   }

  // },
  onReachBottom: function () {
    let that = this
    if (that.data.currentTab == 0) {
      if (!that.stopLoadMoreTiem && (that.data.page < that.data.actnowpages-1)) {
        this.setData({
          page: that.data.page + 1
        })
       // console.log(that.data.page)
        this.fetch()
      }
    } else if (that.data.currentTab == 1) {
      if (!that.stopLoadMoreTiem && (that.data.page < that.data.actfinishedpages - 1)) {
        this.setData({
          page: that.data.page + 1
        })
        this.fetch()
      }
    }

  },
})