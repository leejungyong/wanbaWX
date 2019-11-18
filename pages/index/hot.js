const app = getApp()

Page({
  data: {
    goodsList: [
      
      {
        pic:'https://img.wondfun.com/wanba/img/shapan.jpg',
        title: '沙盘套装',
        price: 888,
        'url': '../my/mypay/buysandbox'
      }
    ],
    navbar: ['道具',  '   线路'],
    // navbar: ['古镇系列', '   影城系列', '乐园系列', '都市系列'],
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
  navbarTap: function (e) {
    this.setData({
      currentTab: e.currentTarget.dataset.idx
    })
  },
  view: function(e) {
    let that = this
    let id = e.currentTarget.id

    let aid = that.data.hot[id].from ? that.data.hot[id].from :1
    console.log(aid)
   
    wx.navigateTo({
       url: '../game/splash?aid='+aid
       })
  },
  goUrl(e){
    let that = this
    let id = e.currentTarget.id
    let url = that.data.goodsList[id].url
    wx.navigateTo({
      url: url
    })
  },
  to: function(e) {
    let that = this
    let id = e.currentTarget.id
    let aid = that.data.hot[id].aid
    let to = that.data.hot[id].from
    wx.navigateTo({
      url: './showContent?aid=' + aid+'&to='+to,
    })
  },
  picLoad: function(e) {
    var _this = this;
    var $width = e.detail.width, //获取图片真实宽度
      $height = e.detail.height,
      ratio = $width / $height; //图片的真实宽高比例
    var viewWidth = 640, //设置图片显示宽度，
      viewHeight = 640 / ratio; //计算的高度值   
    this.setData({
      imgwidth: viewWidth,
      imgheight: viewHeight
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
      url: app.globalData.config.apiUrl+'index.php?act=recommandData',
      data: {},
      method: 'POST',
      success: (res) => {
        console.log(res)
        let data = res.data
        that.setData({
          hot: data.hot,
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
  onPullDownRefresh() {
    wx.showNavigationBarLoading();
    this.fetch();
    wx.hideNavigationBarLoading();
    wx.stopPullDownRefresh()
  },
  onShareAppMessage: function (ops) {
    let that = this
    //console.log(ops)
    if (ops.from === 'button') {
      // 来自页面内转发按钮
      //console.log(ops.target)
    }
    return {
      title: '玩霸江湖',
      path: '/pages/index/hot',
      imageUrl: app.globalData.config.imgUrl + 'wanba/img/sharepic/1.jpg'
    }
  },
  onLoad: function() {
    let that = this
    // let openid=wx.getStorageSync('openid')
    // console.log(openid)
    that.fetch()
  }
})