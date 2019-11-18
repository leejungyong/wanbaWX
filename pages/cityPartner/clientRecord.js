// pages/cityPartner/clientRecord.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
    saleList:[],
    // indicatorDots: false,//是否显示面板指示点
    // vertical: false,  //滑动方向是否为纵向
    // autoplay: true,   //是否自动播放
    // duration: 100,    //动画滑动时长
    // interval: 2500,   //自动切换时间间隔
    // circular: true,   //是否衔接滑动
    // imgheights: [],
    // current:0,

    swiper:[]

  },
  to: function (e) {
    let that = this
    let id = e.currentTarget.id
    let url = that.data.swiper[id].url
    console.log(url)
    wx.navigateTo({
      url: 'pages/index/showWx?url=' + url
    })
  },
  bindchange: function (e) {
    // console.log(e.detail.current)
    this.setData({
      current: e.detail.current
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

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    let that=this
    let saleList=options.saleList
    let swiper=options.swiper
    that.setData({
      saleList:JSON.parse(saleList),
      swiper:JSON.parse(swiper)
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})