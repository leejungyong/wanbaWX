const app = getApp()
var catname,allcats=null
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
    btnTxt: '创建点位'
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
  new() {
    wx.navigateTo({
      url: './selectPos?cat='+catname,
    })
  },

  delPos(e) {
    let that = this
    let id = e.currentTarget.id
    let list = that.data.list
    let pointid  = list[id].pointid
    
    wx.showModal({
      title: '警告',
      content: '确定要删除此点位吗？',
      success: (res) => {
        if (res.confirm) {
          wx.request({
            url: app.globalData.config.apiUrl + 'index.php?act=delPos',
            data: {
              pointid: pointid,
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
  editPos(e) {
    let id = e.currentTarget.id
    //let pointid = this.data.list[id].pointid
    let poiInfo = this.data.list[id]
    poiInfo.allcats=allcats
    wx.navigateTo({
      url: './editPos?ops=' + JSON.stringify(poiInfo)+'&index=' +id,
    })
  },
  imageLoad: function (e) { //获取图片真实宽度  
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

map(){
  console.log(this.data.list)
  let list=this.data.list
  wx.navigateTo({
    url: './map?ops=' +JSON.stringify(list)
  })
},

  bindchange: function (e) {
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
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getCatList',
      data: {
        openid: wx.getStorageSync('openid'),
        cat:catname
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
        allcats=data.allcats
        that.setData({
          swiper: data.swiper,
          list: data.list
        })
        wx.hideLoading()
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
  onLoad: function (options) {
    catname=options.cat
    console.log(catname)
    this.fetch()
  }

})