const app = getApp()
Page({
  data: {
    swiper: null,
    cat: null,
    catname: null,
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
    console.log(url)
    wx.navigateTo({
      url: '/pages/index/showWx?url=' + url
    })
  },
  new() {
    wx.navigateTo({
      url: './selectPos',
    })
  },
  exportPos(e) {
    let id = e.currentTarget.id
    let data = this.data.cat[id].posdata
    console.log(data)
    wx.request({
      url: app.globalData.config.apiUrl + 'exportpoint.php',
      data: {
        data: data
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
           wx.setClipboardData({
            data: '导出数据下载地址：'+app.globalData.config.apiUrl+res.data,
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
             showCancel:false,
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
  catList(e) {
    let id = e.currentTarget.id
    let cat = this.data.cat[id].cat

    wx.navigateTo({
      url: './catList?cat=' + cat,
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
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=myPosList',
      data: {
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: function(res) {
        // console.log(res)
        let data = res.data
        let cat = data.cat
        for (let i in cat) {
          cat[i].catname = cat[i].catname.length > 4 ? cat[i].catname.substr(0, 4) + '..' : cat[i].catname
        }
        that.setData({
          swiper: data.swiper,
          cat: cat
        })
        wx.hideLoading()
        console.log(that.data)
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
  onLoad: function(options) {
    let openid = wx.getStorageSync('openid')
    // console.log(openid)
    this.fetch()
  }

})