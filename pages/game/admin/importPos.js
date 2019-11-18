const app = getApp()
var catname = null
Page({
  data: {
    swiper: null,
    list: null,
    cat:null,
    catname:null,
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
    imgheight: 160
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
 
  import(e) {
    let id = e.currentTarget.id
    
    let poiInfo = this.data.list[id]

    // console.log(poiInfo)
    let pages=getCurrentPages()
    let prepage=pages[pages.length-2]
    let olddata=prepage.data.poiInfo
    olddata.latlng = poiInfo.latlng
    prepage.setData({
      name:poiInfo.name,
      pmemo:poiInfo.pmemo,
      poi: poiInfo.latlng,
      poiInfo: olddata
    })
    wx.navigateBack()
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

  catList(e) {
    let that=this
    let id = e.currentTarget.id
    let cat = this.data.cat[id].cat
    wx.showLoading({
      title: '加载中',
    })
    //获取某一分类下的点位
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getCatList',
      data: {
        openid: wx.getStorageSync('openid'),
        cat: cat
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
        // allcats = data.allcats
        that.setData({
          // swiper: data.swiper,
          list: data.list
        })

        wx.navigateTo({
          url: './map?ops=' + JSON.stringify(that.data.list)
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

  bindchange: function (e) {
    // console.log(e.detail.current)
    this.setData({
      current: e.detail.current
    })
  },
  fetchData(){
    wx.showLoading({
      title: '加载中',
    })
    let that=this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=myPosList',
      data:{
        openid:wx.getStorageSync('openid')
      },
      method:'POST',
      success:res=>{
        console.log(res)
        let data=res.data
        let cat = data.cat
        for (let i in cat) {
          cat[i].catname = cat[i].catname.length > 4 ? cat[i].catname.substr(0, 4) + '..' : cat[i].catname
        }
        that.setData({
          swiper:data.swiper,
          cat:cat
        })
        wx.hideLoading()

      },
      fail:err=>{
        wx.hideLoading()
        wx.showToast({
          title: '网络错误',
          icon: 'none'
        })
      }
    })
  
  },
  fetch() {
    wx.showLoading({
      title: '加载中',
    })
    let that = this
    wx.request({
      url: app.globalData.config.apiUrl + 'index.php?act=getMyPos',
      data: {
        openid: wx.getStorageSync('openid')
      },
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
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
    this.fetchData()
    this.fetch()
  }

})