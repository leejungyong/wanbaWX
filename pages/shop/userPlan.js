const app = getApp()
Page({

 
  data: {
    navbar: ['邀请有礼', '关注有礼','推荐有礼','反馈有礼'],
    currentTab: 0,
    hot: null,
    swiper: null,
    //是否采用衔接滑动  
    circular: true,
    //是否显示画板指示点  
    indicatorDots: false,
    //选中点的颜色  
    indicatorcolor: "#000",
    //是否竖直  
    vertical: false,
    //是否自动切换  
    autoplay: true,
    //自动切换的间隔
    interval: 2500,
    //滑动动画时长毫秒  
    duration: 100,

    //所有图片的高度  （必须）
    imgheights: [],
    //图片宽度 
    imgwidth: 750,
    //默认  （必须）
    current: 0,
    imgwidth: 0,
    imgheight: 160,
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
  navbarTap: function (e) {
    this.setData({
      currentTab: e.currentTarget.dataset.idx
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
      url: app.globalData.config.apiUrl + 'index.php?act=getSwiper',
      data: {},
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
        that.setData({
          swiper: data.swiper
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
  toWX: function (e) {
    let that = this
    let id = e.currentTarget.id
    let url = that.data.swiper[id].url
    if(url.indexOf('/pages/')>-1){
      wx.navigateTo({
        url:  url
      })
    }else{
      wx.navigateTo({
        url: '/pages/index/showWx?url=' + url
      })
    }
    
  },
  postRoute(){
     wx.navigateTo({
       url: '/pages/my/myact/list',
     })
  },
  feedback(){
    wx.navigateTo({
      url: '/pages/my/suggestion',
    })
  },
  onLoad: function (options) {
    wx.hideShareMenu()
    this.fetch()
  },
  onShareAppMessage: function (ops) {
    let that = this
    let t = new Date().getTime();
    if (ops.from === 'button') {
      return {
      title: '@你有一张买一送二优惠卡待领取',
      path: '/pages/shop/buy99Discount?from='+wx.getStorageSync('openid')+'&t='+t,
      imageUrl: 'https://img.wondfun.com/wanba/img/99_share.jpg'
    }
    }
    
  },
})